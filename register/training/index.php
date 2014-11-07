<?
error_reporting(0);
/* Includes */
	require_once "../includes/db_connect.php";
	require "../includes/globals.php";
	require "../includes/validate.php";
	require "../includes/req.php";
	require "../includes/event_info.php";
	require "../includes/member_functions.php";
	include "../includes/time_until.php";

/* Connect to SM-Online DB */
	$sm_db =& db_connect("sm-online");	// Global SM-Online db


/* Event Info */
	if ($_REQUEST['event']) {
		$EVENT 	 = event_info($sm_db, $_REQUEST['event']);
		$SECTION = section_id_convert($EVENT['section_name']);
		$db =& db_connect($SECTION);		// Section's unique db
	}

# Check for event
if (!$EVENT) die("Choose event.");

# Current Year variable
$curr_year = $EVENT['year'];
$event_year = $curr_year;
$year_ago = strtotime($EVENT['start_date']) - (365*24*60*60);
if ($EVENT['training_staff_college']!=0) $staff = "AND college<>".$EVENT['training_staff_college'];

include "../admin/event/reports/report_data.php";

# Get chapter counts
$order_by = $_REQUEST['order_by'] == '' ? "course_number" : $_REQUEST['order_by'];
$sql = "SELECT training_sessions.session_id, college_name, college_prefix, time_slot, training_times.time AS train_time, course_number, session_name, trainer_name, max_size, description, trainer_name, training_location, college_desc, 
				IFNULL(session_count, '<strong><font color=red>0</font></strong>') AS session_count,
				IFNULL(IF(session_count >= max_size, '<font color=red>FULL</font>', max_size - session_count), max_size) AS remaining,
				max_size - session_count AS remaining_count
		FROM training_sessions
		LEFT JOIN (
				SELECT session_id, COUNT(session_id) AS session_count
				FROM training_sessions, ( $all_registered ) AS temp1
				WHERE (time = 1 AND session_1 = session_id)
					OR (time = 2 AND session_2 = session_id)
					OR (time = 3 AND session_3 = session_id)
					OR (time = 4 AND session_4 = session_id)
				GROUP BY session_id
		) AS temp2 USING (session_id)
		LEFT JOIN training_locations ON training_locations.location_id = training_sessions.location
		LEFT JOIN colleges ON colleges.college_id = training_sessions.college
		LEFT JOIN training_times ON training_times.time_id = training_sessions.time
		WHERE training_sessions.deleted<>1 $staff
		AND offered=1
		ORDER BY college, $order_by, time_slot";

$sessions = $db->query($sql);
	if (DB::isError($sessions)) dbError("Could not retrieve session stat list in $title", $sessions, $sql);

$sql = "SELECT location FROM training_sessions WHERE location<>0";
$locations = $db->query($sql);
$numlocs = $locations->numRows();

?>
<title><?=$curr_year." ".$EVENT['section_name']." ".$EVENT['training_university']." Schedule"?></title>
<base href="http://www.sectionmaster.org/">
<link rel="stylesheet" type="text/css" href="../admin/admin.css">
<link href="../../templates/default/pcdtr/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../../templates/default/pcdtr/headings.css" rel="stylesheet" type="text/css" media="screen, projection" />

<div id="container">
<h1 style="background-image:url(../../templates/default/pcdtr/image.php?text=<?=urlencode($curr_year." ".$EVENT['section_name']." ".$EVENT['training_university']." Schedule")?>&amp;tag=h1);"></h1>


<!-- TABLE -->
<table class="cart" align="center">

	<tr>
		<th width=60>Time</th>
		<th width=80>Session ID</th>
		<?=($numlocs!=0)?"<th width=90>Location</th>":""?>
		<th>Title</th>
	</tr>

	<?
	while ($session = $sessions->fetchRow()) {

		if ($session->college_prefix != $current_college) {
			print "<tr class=\"separator\">
						<td colspan="; print ($numlocs!=0)?"4":"3"; print"><font size=+1>$session->college_name ($session->college_prefix)</font>
							<br>$session->college_desc</td>
					</tr>";
			$current_college = $session->college_prefix;
		}

		print "<tr class=\"sub1\" id=\"link\">
					<td>$session->train_time</td>
					<td>$session->college_prefix$session->course_number$session->time_slot</td>";
		print ($numlocs!=0)?"<td>$session->training_location</td>":"";
		print "		<td><b>$session->session_name</b>";
					print ($session->remaining=='<font color=red>FULL</font>') ? " <i><font color=red>(Session Full)</font></i>" : "";
					print "<br><span id=desc>".nl2br($session->description)."</span></td>
				</tr>";
	}
	?>
			<tr class=separator><td colspan=3></td></tr>
			<tr><td colspan=<?=($numlocs!=0)?"4":"3"?>><? include "../html/footer.php"; ?></td></tr>
</table>

</div>

