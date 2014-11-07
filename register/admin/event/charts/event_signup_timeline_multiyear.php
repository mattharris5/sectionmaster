<?

require_once "../../../includes/db_connect.php";
include_once "../../includes/charts.php";
require_once "../../../includes/event_info.php";

# connect to DBs
$sm_db =& db_connect();
$EVENT 	 = event_info($sm_db, $_REQUEST['event']);
$SECTION = section_id_convert($EVENT['section_name']);
$db =& db_connect($SECTION);  // section's unique db

# Get event data
$event_year = $EVENT['year'];

# issue query
/*$sql = "SELECT DATE_FORMAT(reg_date, '%c/%e') AS label, DATE_FORMAT(reg_date, '%c/%e') AS period, COUNT(*) AS count
		FROM members, orders, conclave_attendance
		WHERE members.member_id = conclave_attendance.member_id
			AND conclave_attendance.conclave_order_id = orders.order_id
			AND conclave_year = '$event_year'
			AND registration_complete = 1
		GROUP BY period
		ORDER BY reg_date ASC"; */
/* 	SELECT DATEDIFF('2007-09-08', reg_date) AS label, conclave_year, DATE_FORMAT(reg_date, '%c/%e') AS period, COUNT(*) AS count
			FROM members, orders, conclave_attendance
	left join `sm-online`.events ON `sm-online`.events.id = orders.event_id
			WHERE w1a.members.member_id = w1a.conclave_attendance.member_id
				AND conclave_attendance.conclave_order_id = orders.order_id
				AND registration_complete = 1
			GROUP BY period
			ORDER BY label, conclave_year */
		
$sql = "SELECT DATEDIFF('{$EVENT['start_date']}', reg_date) AS label, DATE_FORMAT(reg_date, '%c/%e') AS period, COUNT(*) AS count
		FROM members, orders, conclave_attendance
		WHERE members.member_id = conclave_attendance.member_id
			AND conclave_attendance.conclave_order_id = orders.order_id
			AND conclave_year = '$event_year'
			AND registration_complete = 1
		GROUP BY period
		ORDER BY reg_date ASC";
$res = $db->query($sql);
	if (DB::isError($res)) dbError("Problem getting event signup timeline data", $res, $sql);

// column headers
$chart['chart_data'][0][0] = "Days Until";
$chart['chart_data'][1][0] = "Count of Registered Members by Days Until Event";

$i=1;
$running_total = 0;
while ($period = $res->fetchRow()) {
	
	$running_total +=  $period->count;
	$chart['chart_data'][0][$i] = "-$period->label";
	$chart['chart_data'][1][$i] = $running_total;
	
	// titles
	$chart['chart_value_text'][0][$i] = "";
	$chart['chart_value_text'][1][$i] = $period->label . " days until\r$period->period\r" . $running_total;
	
	$i++;
}
                                
$chart['chart_type'] = "line";
$chart['axis_category']['skip'] = 7;
$chart['chart_pref']['point_shape'] = 'none';
$chart['chart_value']['position'] = "cursor";
$chart['chart_value']['color'] = "006699";

SendChartData ( $chart );

?>