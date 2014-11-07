<?
if ($EVENT['training_staff_college']!=0) $staff = "AND college<>".$EVENT['training_staff_college'];
$order_by = $_REQUEST['order_by'] == '' ? "course_number" : $_REQUEST['order_by'];
$sql = "SELECT training_sessions.session_id as sess_id, college_name, college_prefix, time_slot, training_times.time AS train_time, course_number, session_name, trainer_name, max_size, description,
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
		LEFT JOIN colleges ON colleges.college_id = training_sessions.college
		LEFT JOIN training_times ON training_times.time_id = training_sessions.time
		WHERE training_sessions.deleted<>1 $staff
		ORDER BY college, $order_by, time_slot";
//print "<pre>$sql</pre>";		
$colleges_sql = "
SELECT
    college_id, colleges.college_name AS college_name
    , count(conclave_attendance.college_major) AS count
FROM
    colleges
    INNER JOIN conclave_attendance 
        ON (colleges.college_id = conclave_attendance.college_major)
WHERE (conclave_attendance.registration_complete =1
    AND conclave_attendance.conclave_year =".$EVENT['year'].")
GROUP BY college_name
ORDER BY college_name ASC;";

$sessions = $db->query($sql);
	if (DB::isError($sessions)) dbError("Could not retrieve session stat list in $title", $sessions, $sql);
$colleges = $db->query($colleges_sql);
	if (DB::isError($colleges)) dbError("Could not retrieve college stat list in $title", $colleges, $colleges_sql);
?>

<!-- CHART -->
<!--<div style="float:right;">
	<?= InsertSMChart("event/charts/event_signup_timeline.php?time=" . microtime() . "&event={$EVENT['id']}", 400, 300, '', true) ?>
	<br>
	<?= InsertSMChart("event/charts/registrations_by_lodge.php?time=" . microtime() . "&event={$EVENT['id']}", 400, 250, '', true) ?>
</div>-->

<h3>Training College Enrollment Summary</h3>
<!-- TABLE -->
<table class="cart" style="width:40%;">

	<tr>
		<th>College</th>
		<th>Current Count</th>
	</tr>

	<?
	while ($college = $colleges->fetchRow())
	{
		print "<tr class=\"sub1\" id=\"link\">
					<td><a href=\"event/reports.php?report=training_detail&college_id=$college->college_id\">$college->college_name</a></td>
					<td id='count'>$college->count</td>
				</tr>";
	}
	?>
	
</table>
<? if ($EVENT['training_required_classes_for_degree'] != 0): ?>
<h3>Training Session Enrollment Report</h3>

<!--SM:webonly-->
Sort by:
<form action="event/reports.php">
	<select name="order_by" onChange="this.form.submit();">
		<? $selected = array ($_REQUEST['order_by'] => "selected"); ?>
		<option value="course_number" <?=$selected['course_number']?>>Course Number</option>
		<option value="session_name" <?=$selected['session_name']?>>Session Name</option>
		<option value="session_count DESC" <?=$selected['session_count DESC']?>>Current Number Registered</option>
		<option value="remaining_count" <?=$selected['remaining_count']?>>Spots Remaining</option>
	</select>
	<input type=hidden name="report" value="training">
</form>
<!--/SM:webonly-->
<br>
A public listing of your training schedule is available here: 
<a href="http://www.sectionmaster.org/register/training?event=<?=$user->info->current_event?>" target="_blank">http://www.sectionmaster.org/register/training?event=<?=$user->info->current_event?></a>
<br>
<!-- TABLE -->
<table class="cart" style="width:90%;">

	<tr>
		<th colspan=4>Session</th>
		<th>Trainer</th>
		<th>Registered/Max</th>
		<th>Remaining</th>
	</tr>

	<?
	while ($session = $sessions->fetchRow()) {

		if ($session->college_prefix != $current_college) {
			print "<tr class=\"separator\">
						<td colspan=7>$session->college_name ($session->college_prefix)</td>
					</tr>";
			$current_college = $session->college_prefix;
		}

		print "<tr class=\"sub1\" id=\"link\">
					<td> &nbsp; </td>
					<td>$session->college_prefix$session->course_number$session->time_slot</td>
					<td>$session->train_time</td>
					<td><a href=\"event/reports.php?report=training_detail&session_id=$session->sess_id\">".stripslashes($session->session_name)."</a></td>
					<td>$session->trainer_name</td>
					<td id='count'>$session->session_count/$session->max_size</td>
					<td id='count'>$session->remaining</td>
				</tr>";
	}
	?>
	
</table>
<? endif; ?>