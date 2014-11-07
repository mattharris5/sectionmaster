<?
$title = "Section > Add User";
require "../pre.php";
$user->checkPermissions("users", 5);

// update product info
if ($_REQUEST['submitted']) {

	// validate username
	$section = $_REQUEST['new_section'] != '' ? $_REQUEST['new_section'] : $user->info->section;
	$new_username = $_REQUEST['new_username'];
	$check = $user->db->query("SELECT * FROM users WHERE username = '$new_username " . "@" . "$section'");
	if ($check->numRows() != 0) {
		$ERROR['new_username'] = "<div class=\"error_summary\"><h4>Username $new_username@$section already exists</h4>
								Please try a different username.</div>";
	}
	
	// validate password
	if ($_REQUEST['new_password1'] == '' 
			|| $_REQUEST['new_password1'] != $_REQUEST['new_password2']
			|| strlen($_REQUEST['new_password1']) < 8) {
		$ERROR['password'] = "<div class=\"error_summary\"><h4>Problem with Password</h4>
								Sorry, but your new password could not be saved.  This could be due to the following reasons:
								<ul><li>Passwords must be at least 8 characters long
									<li>The two passwords entered do not match</ul></div>";
	}
	
	// insert the data
	if (!$ERROR['new_username'] && !$ERROR['password']) {

		$sql = "INSERT INTO users SET
					username = '$new_username" . "@" . "$section',
					section = '$section',
					member_id = '{$_REQUEST['member_id']}',
					current_event = '{$_REQUEST['current_event']}',
					password = '" . md5($_REQUEST['new_password1']) . "'";
		$res = $user->db->query($sql);
			if (DB::isError($res)) dbError("Could not add new user in $title", $res, $sql);
					
		// get uid
		$uid = mysql_insert_id();

		// add permission record
		$sql = "INSERT INTO permissions SET user_id = '$uid'";
		$res = $user->db->query($sql);
			if (DB::isError($res)) dbError("Could not add permission record in $title", $res, $sql);

		// redirect
		redirect("section/edit_user", "uid=$uid");
	}
}

?>

<h1>Add User</h1>

<form action="section/add_user.php">

	<fieldset>
	<legend>User Details</legend>

		<?= $ERROR['new_username'] ?>

		<label for="new_username">Username:</label>
		<input name="new_username" id="new_username" value="<?= $_REQUEST['new_username'] ?>"><br />

		<? if ($user->peekPermissions("superuser") > 0): ?>
		<label for="new_section">Section:</label>
		<select name="new_section">
			<option value=""> </option>
			<? 
			$sql = "SELECT DISTINCT LOWER(REPLACE(section_name, '-', '')) AS id, section_name FROM events ORDER BY section_name";
			$sections = $sm_db->query($sql);
				if (DB::isError($sections)) dbError("Could not get list of sections in $title", $sections, $sql);
			while ($section = $sections->fetchRow()) {
				print "<option value=\"$section->id\"";
				print ">$section->section_name</option>";
			}
			?>
		</select><br />
		<? endif; ?>
		
		<label for="member_id">Link to Member:</label>
		<input type=button class="default" onClick="window.open('/sectionmaster.org/register/admin/section/find_member.php','find_member','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=400,height=500,top=20'); return true;" value="Select a Member">
		<input name="member_id" id="member_id" value="<?= $_REQUEST['member_id'] ?>" class="default" size=4 locked readonly>
		<input type=hidden name="do_not_redirect" id="do_not_redirect" value="true">
		<br>
	
		<?= $ERROR['password'] ?>
	
		<label for="new_password1">New Password:</label>
		<input name="new_password1" id="new_password1" type=password><br />

		<label for="new_password2">Retype New Password:</label>
		<input name="new_password2" id="new_password2" type=password><br />


		<label for="current_event">Current Event:</label>
		<select name="current_event">
			<option value=""></option>
			<? 
			if ($user->peekPermissions("superuser") > 0)
				$sql = "SELECT * FROM events ORDER BY section_name ASC, year DESC";
			else
				$sql = "SELECT * FROM events
						WHERE LOWER(REPLACE(section_name, '-', '')) ='{$user->info->section}'
						ORDER BY section_name ASC, year DESC";
			$events = $sm_db->query($sql);
				if (DB::isError($events)) dbError("Could not get list of events in $title", $events, $sql);
			while ($event = $events->fetchRow()) {
				print "<option value=\"$event->id\"";
				print ">$event->section_name $event->formal_event_name</option>";
			}
			?>
		</select><br />

	</fieldset>

	<input type="submit" id="submit_button" name="submitted" value="Save Changes"><br>

	
</form>

<? require "../post.php"; ?>