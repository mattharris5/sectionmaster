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
$event_date = $EVENT['start_date'];
$year_ago = strtotime($EVENT['start_date']) - (365*24*60*60);
require_once "../../event/reports/report_data.php";

# issue query
$sql = "SELECT SUM(new_ordeal) AS new_ordeal_count
		FROM ( $all_registered ) AS temp1";
$count = $db->getRow($sql);
	if (DB::isError($count)) dbError("Problem getting chart data", $count, $sql);

// column headers
$chart['chart_data'][0][0] = "";
$chart['chart_data'][1][0] = "";
$chart['chart_data'][0][1] = "New Ordeal";
$chart['chart_data'][1][1] = "$count->new_ordeal_count";
$chart['chart_data'][0][2] = "Regular Members";
$chart['chart_data'][1][2] = "" . ($total_count - $count->new_ordeal_count);
                                
$chart['chart_type'] = "3d pie";
$chart['chart_value']['as_percentage'] = true;
$chart['chart_value']['suffix'] = "%";
$chart['legend_rect']['fill_alpha'] = 0;
//$chart['legend_rect']['x'] = -1000;
$chart['chart_value']['position'] = "inside";
$chart['chart_value']['size'] = 11;

$chart [ 'draw' ] = array (
                            array ( 'type'       => "text",
                                    'width'      => 250,  
                                    'h_align'    => "center", 
                                    'v_align'    => "top", 
                                    'text'       => "New Ordeal Ratio",
									'size'		 => 11
                                  )
                          );


SendChartData ( $chart );

?>