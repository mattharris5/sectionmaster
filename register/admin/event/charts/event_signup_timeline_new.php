<?

require_once "../../../includes/db_connect.php";
include_once "../../includes/charts.php";
require_once "../../../includes/event_info.php";

# connect to DBs
$sm_db =& db_connect();
//$EVENT 	 = event_info($sm_db, $_REQUEST['event']);
//$SECTION = section_id_convert($EVENT['section_name']);
$db =& db_connect(w1a);  // section's unique db

# Get event data
$event_year = $EVENT['year'];

# Get applicable years
$yearsql = "SELECT DISTINCT conclave_year FROM conclave_attendance WHERE reg_date<>'0000-00-00'";
$result = $db->query($yearsql);
	if (DB::isError($result)) dbError("Problem getting applicable years", $result, $yearsql);

# issue query
$sql = "SELECT DATE_FORMAT(reg_date, '%c/%e') AS label, DATE_FORMAT(reg_date, '%c/%e') AS period, conclave_year, \n";

$yearcount=0;
$columns=array();
while ($year = $result->fetchRow())
{
	if ($yearcount>0) $sql.=",\n";
	$sql.="			SUM( IF( conclave_year='$year->conclave_year',1,0)) AS 'year$year->conclave_year'";
	$yearcount++;
	array_push($columns,"$year->conclave_year");
}
$sql.= "
		FROM members, orders, conclave_attendance
		WHERE members.member_id = conclave_attendance.member_id
			AND conclave_attendance.conclave_order_id = orders.order_id
			AND registration_complete = 1
		GROUP BY period
		ORDER BY period";

print "<pre>$sql<br><br>";
$res = $db->query($sql);
	if (DB::isError($res)) dbError("Problem getting event signup timeline data", $res, $sql);

// column headers
$chart['chart_data'][0][0] = "Date";

$i=1;
$running_total = array();
while ($period = $res->fetchRow()) {

	for ($row=1; $row<=$yearcount; $row++)
	{
		$chart['chart_data'][$row][0] = $columns[$row-1];
		
		$running_total[$row] +=  $period->count;
		$chart['chart_data'][0][$i] = "$period->label";
		$chart['chart_data'][$row][$i] = $period->count;
		
		// titles
		$chart['chart_value_text'][0][$i] = "";
		$chart['chart_value_text'][$row][$i] = $period->label . "\r" . $running_total[$row];
		
		$i++;
		
	}
}
                                
$chart['chart_type'] = "line";
$chart['axis_category']['skip'] = 7;
$chart['chart_pref']['point_shape'] = 'none';
$chart['chart_value']['position'] = "cursor";
$chart['chart_value']['color'] = "006699";

SendChartData ( $chart );

?>