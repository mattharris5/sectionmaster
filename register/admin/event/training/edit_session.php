<?
$title = "Event > Training > Edit Session";
require "../../pre.php";
$user->checkPermissions("training", 3);

// check that there's a session_id or we're adding new
if (!$_REQUEST['session_id'] && $_REQUEST['command'] != 'add_new')
	redirect("event/training/sessions");

// update session info
if ($_REQUEST['submitted'] && $_REQUEST['session_id'] != '') {
	$sql = "UPDATE training_sessions SET
				college = '" . $_REQUEST['college'] . "',
				course_number = '" . addslashes($_REQUEST['course_number']) . "',
				session_name = '" . addslashes($_REQUEST['session_name']) . "',
				trainer_name = '" . addslashes($_REQUEST['trainer_name']) . "',
				description = '" . addslashes($_REQUEST['description']) . "',
				time = '" . $_REQUEST['time'] . "',
				session_length = '" . $_REQUEST['session_length'] . "',
				location = '" . $_REQUEST['location'] . "',
				offered = '" . $_REQUEST['offered'] . "',
				max_size = '" . $_REQUEST['max_size'] . "'
			WHERE session_id = '{$_REQUEST['session_id']}'";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not update session info in $title", $res, $sql);
	redirect("event/training/sessions");
}

// add new session!
if ($_REQUEST['submitted'] && $_REQUEST['command'] == 'insert') {
	$sql = "INSERT INTO training_sessions SET
				college = '" . $_REQUEST['college'] . "',
				course_number = '" . addslashes($_REQUEST['course_number']) . "',
				session_name = '" . addslashes($_REQUEST['session_name']) . "',
				trainer_name = '" . addslashes($_REQUEST['trainer_name']) . "',
				description = '" . addslashes($_REQUEST['description']) . "',
				time = '" . $_REQUEST['time'] . "',
				session_length = '" . $_REQUEST['session_length'] . "',
				location = '" . $_REQUEST['location'] . "',
				offered = '" . $_REQUEST['offered'] . "',
				max_size = '" . $_REQUEST['max_size'] . "'";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not add new session in $title", $res, $sql);
	
	// redirect
	if ($_REQUEST['after'] == 'another')
		redirect("event/training/edit_session", "command=add_new&added=" . $_REQUEST['course_number']);
	else
		redirect("event/training/sessions");
}

// get session data
$sql = "SELECT * FROM training_sessions
 		LEFT JOIN colleges ON colleges.college_id = training_sessions.college
		LEFT JOIN training_times ON training_times.time_id = training_sessions.time
		WHERE session_id='{$_REQUEST['session_id']}'";
$session = $db->getRow($sql);
	if (DB::isError($session)) dbError("Could not get session info in $title", $session, $sql);

?>

<h1>Edit Training Session</h1>
<h2><?= $session->college_prefix . " " . $session->course_number . " " . $session->session_name ?></h2>
<? if ($_REQUEST['command'] == 'add_new') print "<h2>(New Session)</h2>"; ?>
<? if ($_REQUEST['added'] != '') print "<h3>Successfully added session {$_REQUEST['added']}.</h3>"; ?>

<form action="event/training/edit_session.php">

	<input type="hidden" class="hidden" name="session_id" value="<?= $session->session_id ?>">

	<label for="college">College:</label>
	<select name="college" id="college">
		<?
		$colleges = $db->query("SELECT * FROM colleges WHERE deleted != '1' ORDER BY college_name");
		while ($college = $colleges->fetchRow()) {
			print "<option value=\"$college->college_id\"";
			if ($session->college == $college->college_id) print " selected";
			print ">$college->college_name ($college->college_prefix)</option>";
		}
		?>
	</select><br />
	
	<label for="course_number">Course Number:</label>
	<input name="course_number" id="course_number" value="<?= stripslashes($session->course_number) ?>" size="5"><br />
	<span id="comment">This number should be unique to the courses within this particular college.  However, if you have
		multiple courses with the same name and content offered during different time slots, you should give them both
		the same course number.  This will prevent people from signing up for the same session more than once.</span><br />

	<label for="session_name">Session Name:</label>
	<input name="session_name" id="session_name" value="<?= stripslashes($session->session_name) ?>"><br />
	
	<label for="description">Description:</label>
	<textarea name="description" id="description" style="height: 100px;"><?= stripslashes($session->description) ?></textarea><br />
	<span id="comment">The description will appear below the session name, and should be a couple sentences designed to give a
		brief synopsis of the session itself.</span><br />
	
	<label for="time">Time Slot:</label>
	<select name="time" id="time">
		<?
		$times = $db->query("SELECT * FROM training_times ORDER BY time_slot");
		while ($time = $times->fetchRow()) {
			print "<option value=\"$time->time_id\"";
			if ($session->time_id == $time->time_id) print " selected";
			print ">$time->time_slot: $time->time</option>";
		}
		?>
	</select><br />
	
	<label for="session_length">Session Length:</label>
	<select name="session_length" id="session_length">
		<? $selected[$session->session_length] = "selected"; ?>
		<option value="1" <?= $selected['1'] ?>>1 time slot</option>
		<option value="2" <?= $selected['2'] ?>>2 time slots</option>
		<option value="3" <?= $selected['3'] ?>>3 time slots</option>
		<option value="4" <?= $selected['4'] ?>>4 time slots</option>
	</select><br />
	<span id="comment">Making a session last for more than one time slot will block out the full amount of time on the person's
		training schedule and will prevent them from signing up for a session during a time slot in which they'll already
		be busy.</span><br />

	<label for="max_size">Maximum Enrollment:</label>
	<input name="max_size" id="max_size" value="<?= stripslashes($session->max_size) ?>" size="5"><br />
	<span id="comment">This is required.  Once the given maximum of people have registered for this session, it will no longer
		appear as a selection available for additional people to register for.  If you do not know the capacity of the training
		venue for this particular session, do your best to estimate or make allocations.</span><br />
	
	<label for="trainer_name">Trainer Name:</label>
	<input name="trainer_name" id="trainer_name" value="<?= stripslashes($session->trainer_name) ?>"><br />
	
	<label for="location">Location:</label>
	<select name="location" id="location">
		<option value="">(Not yet assigned)</option>
		<?
		$locations = $db->query("SELECT * FROM training_locations WHERE deleted != '1' ORDER BY training_location");
		while ($location = $locations->fetchRow()) {
			print "<option value=\"$location->location_id\"";
			if ($session->location == $location->location_id) print " selected";
			print ">$location->training_location</option>";
		}
		?>
	</select><br />
	<span id="comment">Choose the specific location or venue to be used for this particular training session.
		This will appear on the participant's printed schedule when they arrive at the event, and will also be helpful for
		creating room location signs and other miscellaneous reports using the SM-Desktop software.</span><br />

	<label for="offered">Currently Offered?</label>
	<input type=hidden name="offered" value="0">
	<input type=checkbox name="offered" id="offered" value="1" 
		<?= ($session->offered=='1' || $_REQUEST['command']=='add_new') ? "checked" : "" ?>><br>
	<span id="comment">Only uncheck this if you are not having this session at your event.  If you intend to stop people from
		registering for this session due to space constraints, change the maximum enrollment number above instead.</span><br />
	
	<input type="submit" id="submit_button" name="submitted" value="Save Changes"><br />
	
	<? if ($_REQUEST['command'] == 'add_new'): ?>
		<div class="default">
		<input type=hidden name="command" value="insert">
		<b>After adding this session: </b>
			<input type=radio name="after" value="another" checked> Add another <b>- OR -</b>
			<input type=radio name="after" value="continue"> Go back to session list
		</div>
	<? endif; ?>
	
</form>

<? require "../../post.php"; ?>