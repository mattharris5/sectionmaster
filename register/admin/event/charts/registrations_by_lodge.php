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
$sql = "SELECT lodge_name, COUNT(lodge) AS count, section, IF(section='{$EVENT['section_name']}', 'true', 'false') AS in_section
		FROM (
			SELECT 
			members.*,
			conclave_attendance.conclave_year,
			conclave_attendance.reg_date,
			conclave_attendance.check_in_date,
			conclave_attendance.check_out_date,
			conclave_attendance.staff_class,
			conclave_attendance.college_major,
			conclave_attendance.college_degree, 
			conclave_attendance.degree_reqts_complete,
			conclave_attendance.session_1,
			conclave_attendance.session_2,
			conclave_attendance.session_3,
			conclave_attendance.session_4,
			conclave_attendance.current_reg_step,
			conclave_attendance.conclave_order_id,
			conclave_attendance.registration_complete,
			orders.order_id,
			orders.order_timestamp,
			orders.order_year,
			orders.event_id,
			orders.total_collected,
			orders.shipping,
			orders.shipping_method,
			orders.cc,
			orders.exp_month,
			orders.exp_year,
			orders.paid,
			orders.cc_processed,
			orders.order_method,
			orders.payment_method,
			orders.payment_notes,
			orders.complete,
			orders.deleted,
			`sm-online`.lodges.lodge_id,
			`sm-online`.lodges.lodge_name,
			`sm-online`.lodges.section,
			`sm-online`.lodges.council,
			chapters.chapter_id,
			chapters.chapter_name,
			chapters.active_membership,
			chapters.chapter_chief
			FROM 
			 members
			 INNER JOIN conclave_attendance
			            ON members.member_id = conclave_attendance.member_id
			        LEFT JOIN orders
			            ON conclave_attendance.conclave_order_id = orders.order_id 
			        LEFT JOIN `sm-online`.lodges
			            ON members.lodge = `sm-online`.lodges.lodge_id
			        LEFT JOIN chapters
			            ON members.chapter = chapters.chapter_id
			    WHERE 
			        conclave_attendance.conclave_year = '{$EVENT['year']}'
			        AND conclave_attendance.registration_complete = 1
			
			
			
			#SELECT *
			#		FROM members, orders, conclave_attendance
			#		LEFT JOIN `sm-online`.lodges ON `sm-online`.lodges.lodge_id = members.lodge
			#		LEFT JOIN chapters ON chapters.chapter_id = members.chapter
			#		WHERE members.member_id = conclave_attendance.member_id
			#			AND conclave_attendance.conclave_order_id = orders.order_id
			#			AND conclave_year = '{$EVENT['year']}'
			#			AND registration_complete = 1
 		) AS temp1
		GROUP BY lodge
		ORDER BY in_section DESC, section, lodge_name";
$res = $db->query($sql);
	if (DB::isError($res)) dbError("Problem getting event signup timeline data", $res, $sql);

// column headers
$chart['chart_data'][0][0] = "Lodge Name";
$chart['chart_data'][1][0] = "Count";
$chart['chart_value_text'][0][0] = "";
$chart['chart_value_text'][1][0] = "";


$i=1;
while ($count = $res->fetchRow()) {
	
	$this_name = $count->in_section == 'true' ? $count->lodge_name : "Out of Section";
	
	// chart data
	$chart['chart_data'][0][$i] = "$this_name";
	$chart['chart_data'][1][$i] += $count->count;
	
	// titles
	$chart['chart_value_text'][0][$i] = "";
	$chart['chart_value_text'][1][$i] = "$this_name\r({$chart['chart_data'][1][$i]})";
	
	// exploded out of section value
	if ($count->in_section == 'false') $chart['series_explode'][$i] = 35;
	else $chart['series_explode'][$i] = 0;

	if ($count->in_section != 'false') $i++;	
}
                                
$chart['chart_type'] = "3d pie";
$chart['chart_value']['as_percentage'] = true;
$chart['chart_value']['suffix'] = "%";
$chart['legend_rect']['fill_alpha'] = 0;
$chart['legend_rect']['x'] = -1000;
$chart['chart_value']['position'] = "outside";
$chart['chart_value']['size'] = 9;

SendChartData ( $chart );

?>