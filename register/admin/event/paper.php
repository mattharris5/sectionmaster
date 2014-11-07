<?
$title = "Event > Input Paper Registration";
require "../pre.php";
$user->checkPermissions("paper_reg");

// login as existing member
if ($_REQUEST['command']=='existing_member') {
	destroySessionDataOnly();
	$_SESSION['type'] = "paper";
	$_SESSION['page'] = "contact-info";
	$EVENT 	 = event_info($sm_db, $_REQUEST['event_id']);
	$newurl = "https://" . $_GLOBALS['secure_base_href']
				. "/register/?id={$_REQUEST['member_id']}&key=" 
				. makeKey(10, $_REQUEST['member_id'], $sm_db);
	header( "Location: $newurl" );
}

// login as new member
else if ($_REQUEST['command'] == 'new_member') {

	// reset what might exist of the session data
	destroySessionDataOnly();
	$_SESSION['type'] = "paper";
	$_SESSION['page'] = "idcreate";
	
	$newurl = "https://" . $_GLOBALS['secure_base_href']
				. "/register/?reason=new_member&event_id="
				. $_REQUEST['event_id'] 
				. "&email=" . $_REQUEST['email'];
	header( "Location: $newurl" ); 
}

?>

<h1>Input Paper Registration</h1>

To input a paper registration, first select the member who has submitted the paper form.  Or, if you cannot find a record for this member, choose "Create New Member Record".
	

	<fieldset><legend>Find a Member</legend>
	<form name="find_member" action="event/paper.php" method=get target="_blank">

	<br>
	<label style="width: 200px;">Search by Member Name or ID: </label>
	<input type="text" id="autocomplete" name="autocomplete_entry"/> &nbsp;
		<span id="indicator1" style="display: none"><img src="../images/default/spinner.gif" alt="Working..." /></span>
		<div id="autocomplete_choices" class="autocomplete"></div>

		<script type="text/javascript" language="javascript">
			new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "section/find_member.php", {afterUpdateElement : getSelectionId, indicator: 'indicator1'});

			function getSelectionId(text, li) {
			    document.getElementById('member_id').value = li.id
				document.find_member.submit()
			}
		</script>


	<br>
	<input class="default" id="member_id" name="member_id" style="visibility: hidden; display: none;">

	<input type=hidden name="event_id" value="<?= $event->id ?>">
	<input type=hidden name="command" value="existing_member">

	<input type=submit id="submit_button" value="View Member Record"><br />

	</form>
	</fieldset>
	
	<center><h3>- OR -</h3></center>

	<fieldset><legend>Create a new member record</legend>
	<form action="event/paper.php" method=get target="_blank">
		
		<br>
		<label>E-mail Address: </label>
		<input id="email" name="email">
		<span id="comment">If no e-mail address was supplied, use <strong>noemail@sectionmaster.org</strong>.</span>
		
		<input type=hidden name="event_id" value="<?= $user->info->current_event ?>">
		<input type=hidden name="command" value="new_member">
		<input type=submit id="submit_button" value="Create New Member Record"><br />
		
	</form>
	</fieldset>





<?
require "../post.php";
?>

