<?
$title = "Section > Edit User";
require "../pre.php";
$user->checkPermissions("users", 3);

// check that there's a product_id
if (!$_REQUEST['uid'])
	redirect("section/users");

// update product info
if ($_REQUEST['submitted']) {

	// update member_id and current_event
	$sql = "UPDATE users SET
				member_id = '{$_REQUEST['member_id']}',
				current_event = '{$_REQUEST['current_event']}'
			WHERE id = '{$_REQUEST['uid']}'";
	$res = $user->db->query($sql);
		if (DB::isError($res)) dbError("Could not update user info in $title", $res, $sql);

	// reset password
	if ($_REQUEST['new_password1'] != '' 
		&& $_REQUEST['new_password1'] == $_REQUEST['new_password2']
		&& strlen($_REQUEST['new_password1']) >= 8) {
			$sql = "UPDATE users 
					SET password = '" . md5($_REQUEST['new_password1']) . "' 
					WHERE id = '{$_REQUEST['uid']}'";
			$res = $user->db->query($sql);
				if (DB::isError($res)) dbError("Could not update password in $title", $res, $sql);	
	} elseif ($_REQUEST['new_password1'] != '') {
		$ERROR['password'] = "<div class=\"error_summary\"><h4>Problem with Password</h4>
								Sorry, but your new password could not be saved.  This could be due to the following reasons:
								<ul><li>Passwords must be at least 8 characters long
									<li>The two passwords entered do not match</ul></div>";
	}
	
	// update permissions
	$sql = "UPDATE permissions SET ";
		foreach ($_REQUEST['permission'] as $perm => $value) {
			$sql .= "\n\t $perm = '$value',";
		}
		$sql .= "\n\t user_id = '{$_REQUEST['uid']}' 
					WHERE user_id = '{$_REQUEST['uid']}'";
	$res = $user->db->query($sql);
		if (DB::isError($res)) dbError("Could not update permissions in $title", $res, $sql);	

	// redirect
/*	if ($_REQUEST['do_not_redirect'] == 'false' && !$ERROR['password'])
		redirect("section/users");
*/}

// get user data
$this_section = $user->db->getOne("SELECT section FROM users WHERE id = '{$_REQUEST['uid']}'");
$sql = "SELECT * FROM users
		LEFT JOIN $this_section.members USING (member_id)
		LEFT JOIN $this_section.chapters ON $this_section.chapters.chapter_id = $this_section.members.chapter
		LEFT JOIN lodges ON $this_section.members.lodge = lodges.lodge_id
		WHERE id = '{$_REQUEST['uid']}'";
$this_user = $user->db->getRow($sql);
	if (DB::isError($this_user)) dbError("Could not get user info in $title", $this_user, $sql);

?>

<h1>Edit User: <?= $this_user->username ?></h1>
<ul>
	<li>Last activity: <?= doRelativeDate(strtotime($this_user->last_activity)) ?>
	<li>Last connected from: <? print ($this_user->ip=='') ? "<i>Never logged in</i>" : gethostbyaddr($this_user->ip)." ($this_user->ip)"; ?>
</ul>

<form action="section/edit_user.php" method=get>

	<input type="hidden" class="hidden" name="uid" value="<?= $this_user->id ?>">

	<fieldset class="default">
	<legend>Member Link</legend>

		<h4>Linked to Member ID: <?= $this_user->member_id ?></h4>
		<ul><li>Name: <?= $this_user->firstname ?> <?= $this_user->lastname ?>
			<li>Chapter: <?= $this_user->chapter_name ?>
			<li>Lodge: <?= $this_user->lodge_name ?>
			<li>E-mail: <a href="mailto:<?= $this_user->email ?>"><?= $this_user->email ?></a>
		</ul>
		<input type=hidden name="member_id" id="member_id" value="<?= $this_user->member_id ?>">
		<input type=hidden name="do_not_redirect" id="do_not_redirect" value="false">
		<input type=hidden name="submitted" value="true">
	
		<input type=button onClick="window.open('/sectionmaster.org/register/admin/section/find_member.php','find_member','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=400,height=500,top=20'); return true;" value="Link to a Different Member">
		<input type=button onClick="window.location='/sectionmaster.org/register/admin/section/members.php?member_id=<?= $this_user->member_id ?>';" value="Edit Member <?= $this_user->firstname ?> <?= $this_user->lastname ?>">

	</fieldset>	
	
		
	<fieldset>
		<legend>Change Password</legend>
	
		<?= $ERROR['password'] ?>
	
		<label for="new_password1">New Password:</label>
		<input name="new_password1" id="new_password1" type=password><br />

		<label for="new_password2">Retype New Password:</label>
		<input name="new_password2" id="new_password2" type=password><br />

	</fieldset>
	
	
	<fieldset>
	<legend>Current Event</legend>

		<label for="current_event">Current Event:</label>
		<select name="current_event">
			<? 
			if ($user->peekPermissions("superuser") > 0)
				$sql = "SELECT * FROM events WHERE type = 'event'";
			else
				$sql = "SELECT * FROM events
						WHERE section_name ='$this_user->section'
						AND type = 'event'";
			$events = $sm_db->query($sql);
				if (DB::isError($events)) dbError("Could not get list of events in $title", $events, $sql);
			while ($event = $events->fetchRow()) {
				print "<option value=\"$event->id\"";
				if ($this_user->current_event == $event->id) print " selected";
				print ">$event->section_name $event->formal_event_name</option>";
			}
			?>
		</select><br />
		
	</fieldset>

	<input type="submit" id="submit_button" name="submitted" value="Save Changes"><br>
	
	<fieldset>
	<legend>Permissions for <?= $this_user->username ?></legend>
	
		<center><div style="margin-bottom: 10px; padding: 5px; background-color: #dadada; border: 1px dotted black;">
			<strong>Permissions Guide:</strong>
			<strong>0</strong> = none &bull;
			<strong>1</strong> = view (restricted) &bull; 
			<strong>2</strong> = view (all) &bull; 
			<strong>3</strong> = edit (restricted) &bull; 
			<strong>4</strong> = edit (all) &bull; 
			<strong>5</strong> = all
		</div></center>
	
		<?
		$disabled_perms=array('orders','shipping','request_refund');
		$perms = $user->db->tableInfo("permissions");
		$my_perms = $user->db->getRow("SELECT * FROM permissions WHERE user_id={$_REQUEST['uid']}");
			if (DB::isError($my_perms)) dbError("Error getting user's permissions from DB", $my_perms);
		
		$start = $user->peekPermissions("superuser") > 0 ? 1 : 2;
		for ($i=$start; $i<count($perms); $i++) {
			print "<label for='permission[$i]'>{$perms[$i]['name']}</label>\n";
			print "<div class='checkboxes'>";
	        
			for ($j=0; $j<=5; $j++) {
	            print "<span style='width: 4em; min-width: 3em;'>
	                   <input type='radio'";
				if (in_array($perms[$i]['name'],$disabled_perms)) print " disabled ";
				print "	name='permission[{$perms[$i]['name']}]' id='permission[{$perms[$i]['name']}]'
							  value=\"$j\"";
					if ($my_perms->{$perms[$i]['name']} == $j) print " checked";
				print "> $j</span>";
	        }
	        print "</div><br>";
		}
	    ?>
	
	</fieldset>
		
	<input type="submit" id="submit_button" name="submitted" value="Save Changes"><br>

	
</form>
<?/*<pre><? print_r($_REQUEST); ?></pre>*/?>
<? require "../post.php"; ?>