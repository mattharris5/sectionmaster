<?
$title = "Event > Vicarious Login";
require "../pre.php";
$user->checkPermissions("vicarious_login");


if ($_REQUEST['command']=='login') {

	/* Event Info */
	$EVENT 	 = event_info($sm_db, $_REQUEST['event_id']);

	$newurl = "https://" . $_GLOBALS['secure_base_href']
				. "/register/?id={$_REQUEST['member_id']}&key=" 
				. makeKey(10, $_REQUEST['member_id'], $sm_db);
	header( "Location: $newurl" );
}

?>

	<h1>Login as Another User</h1>
	This feature allows you to enter another user's registration process without needing
	access to their e-mail address.
	
	<p align=center><font color=red>DO NOT USE THIS FEATURE UNLESS YOUR AUTH USER'S SECTION MATCHES THE EVENT RECORD'S SECTION!!</font></p>
	
	<form action="event/vlogin.php" method=get>
		
		<br>
		<label>Member ID: </label>
		<input class="default" id="member_id" name="member_id">
		<input class="default" type=button onClick="window.open('/sectionmaster.org/register/admin/section/find_member.php','find_member','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=400,height=500,top=20'); return true;" value="Find Member">
		<br />
		
		<input type=hidden name="event_id" value="<?= $user->info->current_event ?>">
		<input type=hidden name="command" value="login">
		<input type=submit id="submit_button" value="Start Vicarious Registration"><br />
		
	</form>


<? require "../post.php"; ?>