<?
/*	SM-Online Registration System                      www.sectionmaster.org
	------------------------------------------------------------------------

	EVENT SIGNUP PAGE (html/eventreg.php)

	------------------------------------------------------------------------										*/

	# Validate Data
//		validate("emer_contact_name","","Please provide an emergency contact name.");
        
	# Post-Validate
		if (postValidate()) {
/*			$update = $db->query("UPDATE members SET
									firstname = '". get('firstname') . "',
								  WHERE member_id = {$_SESSION['member_id']}");
									 
*/			gotoNextPage("eventreg");
		}

?>

<h2>Activity Signup</h2>

	<p>How bout some activities?</p>

	<? print $ERROR['error_summary']; ?>
	
	<center>
	<form name="form1" action="<? print $_GLOBALS['form_action']; ?>" method="<? print $_GLOBALS['form_method']; ?>">

		<!-- Emergency Contact Name -->
<!--		<label for="emer_contact_name">In the event of emergency, notify:</label>
				<input name="emer_contact_name" id="emer_contact_name" value="<? req('emer_contact_name') ?>">
-->

		<!-- Hiddens -->
		<input type="hidden" name="section" value="<? print $SECTION ?>">
		<input type="hidden" name="page" value="eventreg">
		
		<input id="submit_button" name="submitted" type="submit" value="Continue >">

	</form>