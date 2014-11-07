<?
$title = "Training > Sessions";
require "../../pre.php";
$user->checkPermissions("training");

// delete session
if ($_REQUEST['command'] == 'delete' && $_REQUEST['session_id'] != '') {
	$sql = "UPDATE training_sessions SET deleted = '1' WHERE session_id = '{$_REQUEST['session_id']}' LIMIT 1";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not delete training session in $title", $res, "");
	redirect("event/training/sessions");
}

// Get list of sessions
$order_by = $_REQUEST['order_by'] ? $_REQUEST['order_by'] : "college_prefix ASC, course_number ASC";
$sql = "SELECT *, IF(offered='1', 'Y', 'N') AS is_offered
		FROM training_sessions 
		LEFT JOIN colleges ON colleges.college_id = training_sessions.college
		LEFT JOIN training_times ON training_times.time_id = training_sessions.time
		LEFT JOIN training_locations ON training_locations.location_id = training_sessions.location
		WHERE training_sessions.deleted != '1'
		ORDER BY $order_by";
$sessions = $db->query($sql);
	if (DB::isError($sessions)) dbError("Could not get session listing in $title", $sessions, $sql);
?>

<h1>Training Sessions</h1>
<a href="event/training.php">&lt; Back to Training</a><br>

<form action="event/training/edit_session.php" class="default">
	<input type=hidden name="command" value="add_new">
	<input type=submit value="Add New Session">
</form><br>

<table class="cart">
	<tr>
		<th colspan=3>Session</th>
		<th>Trainer</th>
		<th>Location</th>
		<th>Time</th>
		<th>Length</th>
		<th>Max Size</th>
		<th>Offered</th>
		<th colspan=2>Edit</th>
	</tr>

	<?
	while ($session = $sessions->fetchRow()) {

		if ($session->college_prefix != $current_college) {
			print "<tr class=\"separator\">
						<td colspan=10>$session->college_name ($session->college_prefix)</td>
					</tr>";
			$current_college = $session->college_prefix;
		}

		print "<tr class=\"sub1\" id=\"link\">
					<td> &nbsp; </td>
					<td>$session->college_prefix$session->course_number$session->time_slot</td>
					<td style=\"width:25%\">$session->session_name</td>
					<td>".stripslashes($session->trainer_name)."</td>
					<td>$session->training_location</td>
					<td>$session->time</td>
					<td id='count'>$session->session_length</td>
					<td id='count'>$session->max_size</td>
					<td id='count'>$session->is_offered</td>";
			
		
		// edit
		print "<td><input class=\"default\" type=button
					 onClick=\"
			window.location='/sectionmaster.org/register/admin/event/training/edit_session.php?session_id=$session->session_id';\"
					value=\"Edit\"></td>";

		// delete
		print "<td><input class=\"default\" type=button
					 onClick=\"if (confirm('Are you SURE you want to PERMANENTLY delete " 
						. addslashes($session->college_prefix . " " . $session->course_number) . "?'))
		window.location='/sectionmaster.org/register/admin/event/training/sessions.php?command=delete&session_id=$session->session_id';\"
					value=\"Delete\"></td>";

					
		print "</tr>";
	}
	?>
			
</table><br>

<? require "../../post.php"; ?>