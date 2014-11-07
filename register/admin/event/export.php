<?
$no_header = true;
$title = "Event > Export";
require "../pre.php";
$user->checkPermissions("members");

if ($user->info->current_event == 0)
	redirect("event/select");

$EVENT = event_info($sm_db, $user->info->current_event);

// get event data
$sql = "SELECT
  members.member_id,
  firstname,
  lastname,
  address,
  address2,
  city,
  state,
  zip,
  latitude,
  longitude,
  primary_phone,
  email,
  oahonor,
  gender,
  registered_unit_type,
  registered_unit_number,
  lodge_name,
  chapter_name,
  ordeal_date,
  `position`,
  birthdate,
  emer_contact_name,
  emer_relationship,
  emer_phone_1,
  emer_phone_2,
  food_allergy,
  special_care,
  special_diet,
  special_diet_explain,
  sleeping_device,
  conditions_explanation,
  s1.session_name        AS sess_1,
  s2.session_name        AS sess_2,
  s3.session_name        AS sess_3,
  s4.session_name        AS sess_4,
  custom1,
  custom2,
  custom3,
  (SELECT 
    FORMAT(SUM(quantity * price), 2) 
  FROM
    {$user->info->section}.ordered_items 
  WHERE order_id = {$user->info->section}.orders.order_id) AS order_total,
  FORMAT(total_collected, 2) AS amount_collected,
  FORMAT(
    (SELECT 
      SUM(quantity * price) 
    FROM
      {$user->info->section}.ordered_items 
    WHERE order_id = {$user->info->section}.orders.order_id) - total_collected,
    2
  ) AS amount_due,
  paid,
  payment_method,
  reg_date,
  check_in_date,
  check_out_date,
  order_id,
  staff_classification,
  housing_name
FROM {$user->info->section}.members
  LEFT JOIN {$user->info->section}.conclave_attendance
    USING (member_id)
  LEFT JOIN {$user->info->section}.orders
    ON conclave_attendance.conclave_order_id = orders.order_id
  LEFT JOIN {$user->info->section}.training_sessions s1
    ON session_1 = s1.session_id
  LEFT JOIN {$user->info->section}.training_sessions s2
    ON session_2 = s2.session_id
  LEFT JOIN {$user->info->section}.training_sessions s3
    ON session_3 = s3.session_id
  LEFT JOIN {$user->info->section}.training_sessions s4
    ON session_4 = s4.session_id
  LEFT JOIN `sm-online`.lodges
    ON lodge = `sm-online`.lodges.lodge_id
  LEFT JOIN {$user->info->section}.chapters
    ON chapter = chapters.chapter_id
  LEFT JOIN {$user->info->section}.housing
    USING (housing_id)
  LEFT JOIN {$user->info->section}.staff_classifications
    ON staff_class_id = staff_class
WHERE members.member_id = conclave_attendance.member_id
    AND conclave_attendance.conclave_order_id = orders.order_id
    AND conclave_attendance.conclave_year = {$EVENT['year']}
    AND orders.event_id = {$EVENT['id']}
    AND registration_complete = 1
ORDER BY lastname, firstname";
//die("<pre>".$sql."</pre>");
$result=mysql_query($sql);
$info = $sm_db->getRow($sql);
	if (DB::isError($info)) dbError("Could not get export in $title", $info, $sql);
$fields=mysql_num_fields($result);
$header="
<html>
<head>
<title>Excel Spreadsheet</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
</head>
<body>
<table border=1>
	<tr>
	";
	for ($i = 0; $i < $fields; $i++) {
		$header .= "<td><b>".mysql_field_name($result, $i) . "</b></td>\n";
	}
	$header .= "</tr>\n";
	while($row = mysql_fetch_row($result)) {
	    $line = "<tr>";
	    foreach($row as $value) {                                            
		   if ((!isset($value)) OR ($value == "")) {
			  $value = "<td></td>";
		   } else {
			  $value = str_replace('"', '""', $value);
			  $value = "<td>" . $value . "</td>\n";
		   }
		   $line .= $value."\n";
	    }
	    $data .= "</tr>".trim($line)."\n";
	}
	$data = str_replace("\r","",$data);
	$data.="</table></body></html>";
	if ($data == "") {
	    $data = "\n<font color=red><b>(0) records found to export!</b></font>\n";
	}
	else
	{
		$filename = str_replace(" ","",$EVENT['section_name']."_".$EVENT['formal_event_name']."_DataAsOf_".date('Ymd').".xls");
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		print "$header\n$data"; 
	}

?>