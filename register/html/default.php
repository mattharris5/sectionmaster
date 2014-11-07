<? 
/*	SM-Online Registration System                      www.sectionmaster.org
	------------------------------------------------------------------------

	"DEFAULT" PAGE (html/default.php)

	------------------------------------------------------------------------
	This page is the default handler page for the system, so it usually
	displays a welcome and login screen.										*/
	
	validate("email", "validEmail()", "You need to enter a valid e-mail address that accepts mail in order to continue.  A valid e-mail address includes your login name, the '<code>@</code>' symbol, and the domain name of your ISP or business.  For example, <code>steve@apple.com</code> is valid but <code>bill@microsoft</code> is not valid.<br>", false, true);

	if (postValidate()) {
		// turn over control to the login handler
		gotoPage("", "email=" . $_REQUEST['email'] . "&command=check_address&event_id=" . $_REQUEST['event_id']);
		exit;
	}
	
####################
# Errors
####################
	
	# Already Registered
	if ($_REQUEST['login_error'] == "already_registered") {
		$ERROR['login'] = "<div class=\"error_summary\"><font class=\"error_summary\">
										Sorry, but that member is already registered for
										{$EVENT['year']}.  To register as someone new, enter your e-mail
										address in the box below.</font></div>";
	} 
	
	# Page Not Found
	elseif ($_REQUEST['login_error'] == "404") {
		$ERROR['login'] = "<div class=\"error_summary\"><font class=\"error_summary\">
							<h3 style='margin:0;'>Page Not Found</h3>Could not find {$_REQUEST['page_not_found']} page.
							Please try again.</font></div>";
	} 
	
	# No Session
	elseif ($_REQUEST['login_error'] == "no_session_record") {
		$ERROR['login'] = "<div class=\"error_summary\"><font class=\"error_summary\">
							<h3 style='margin:0;'>Invalid Session</h3>Sorry, but that ID and passkey pair
							is invalid.  Please try again.</font></div>";
	} 
	
	# Expired Session
	elseif ($_REQUEST['login_error'] == "session_expired") {
		$ERROR['login'] = "<div class=\"error_summary\"><font class=\"error_summary\">
							<h3 style='margin:0;'>Time Expired</h3>For security reasons, your
							registration session has ended because your time expired.  Please enter
							your e-mail address below to continue the registration process where you
							left off.</font></div>";
	} 

	# Registration Closed
	if ((time() < strtotime($EVENT['online_reg_open_date']) 
			|| time() > strtotime($EVENT['online_reg_close_date'])
			|| $_REQUEST['login_error'] == 'registration_closed') 
			&& $_SESSION['backdoor'] != 'open') {
		$registration_open = false;
		$ERROR['login'] = "<div class=\"error_summary\"><font class=\"error_summary\">
							<h3 style='margin:0;'>Online Registration Closed</h3>Sorry, but Online
							Registration is closed.  You can still come to {$EVENT['casual_event_name']}
							and register onsite.  If you have any questions, please contact 
							{$EVENT['reg_contact_name']} at 
							<a href=\"mailto:{$EVENT['reg_contact_email']}\">
							{$EVENT['reg_contact_email']}</a>.</font></div>";
	} 

	# Destroy Reg
	if ($ERROR['login']) {
		destroyReg();
	}

?>

<h2>Register for <?=$EVENT['formal_event_name']?></h2>

	<? if ($registration_open !== false): ?>
	<p>Welcome!  To start the registration process, enter your e-mail address below. If you've attended <?=$EVENT['casual_event_name']?> before, you should enter the e-mail address you used in the past <strong>even if you no longer have access to it</strong>.  We will use this address to identify you when registering with us again.</p>
	<? endif; ?>
	
	<? print $ERROR['email']; print $ERROR['login']; ?>

	<center>
	
	<? if ($registration_open !== false): ?>
	<form name="myForm" action="<? print $_GLOBALS['form_action']; ?>" method="<? print $_GLOBALS['form_method']; ?>">
		<label for="email">E-mail Address:</label>
			<input name="email" id="email" value="<? req('email') ?>"><br />
		
		<? $event_id = $EVENT ? $EVENT['id'] : $_REQUEST['event_id']; ?>
		
		<input type="hidden" class="hidden" id="event_id" name="event_id" value="<?=$event_id?>">
		<input type="hidden" class="hidden" id="session" name="session" value="destroy">
		<?
		if ($_REQUEST['backdoor'] == 'open') print "<input type=\"hidden\" class=\"hidden\" id=\"backdoor\" name=\"backdoor\" value=\"open\">";
		?>
		
		<input type="submit" id="submit_button" name="submitted" value="Start my Registration">
		
	</form>
	<? endif; ?>
	</center><br>
	
	<fieldset><legend><?=stripslashes($EVENT['formal_event_name'])?> General Information</legend>
        	
		<center>
		<table border=0 cellpadding=7>
		
		<tr align=left><td valign=top><b>Date:</b></td>
			<td><?=$EVENT['conclave_date']?></td></tr>
		
		<tr align=left><td valign=top><b>Location:</b></td>
			<td><?=stripslashes($EVENT['conclave_location'])?> <? print $EVENT['city_and_state'] ? "(".stripslashes($EVENT['city_and_state']).")" : ""; ?></td></tr>
		
		<tr align=left><td valign=top><b>Cost:</b></td>
			<td><?=$EVENT['event_cost']?></td></tr>

		<tr align=left><td valign=top><b>Online Registration:</b></td>
			<td>Opens on <?=date('l, F j, Y',strtotime($EVENT['online_reg_open_date']))." at ".date('g:i a',strtotime($EVENT['online_reg_open_date']))?> MT<br>
			Closes on <?=date('l, F j, Y',strtotime($EVENT['online_reg_close_date']))." at ".date('g:i a',strtotime($EVENT['online_reg_close_date']))?> MT</td></tr>
			
		<? if (($EVENT['do_training']==1) and ($EVENT['training_required_classes_for_degree']>0)) print "
		<tr align=left><td valign=top><b>Training:</b></td>
			<td><a href=http://www.sectionmaster.org/register/training/?event=$event_id target=_blank>Click here</a> for the {$EVENT['year']} {$EVENT['training_university']} schedule.</td></tr>";
		?>
		
		<tr align=left><td valign=top><b>More Information:</b></td>
			<td><a href="<?=$EVENT['detail_url']?>" target="_blank">Click here</a> for more information.</td></tr>
			
		<tr align=left><td valign=top><b>Tour Plans:</b></td>
			<td>
				National and Regional policy is that tour plans are not required only when traveling by yourself or direct relatives.
				All other travel requires the use of an application filed with the appropriate council office. Tour plans must be used for travel to
				<?=$EVENT['casual_event_name']?>.  Remember, transporting non-family youth members is only permitted by adults over age 21.
				You can direct any questions concerning this policy to the Section Adviser, <?=$EVENT['section_adviser']?>.  Also see the
				<a href="http://www.scouting.org/scoutsource/HealthandSafety/GSS.aspx" target="_blank">Guide to Safe Scouting</a>.<br><br>
				The Region encourages each Section to promote the use of tour plans for all Order of the Arrow events.  However, it is not the
				Region's or Section's role to decide the exact implementation of this National Policy. That implementation is the responsibility
				of the individual Lodge and its Supreme Chief of the Fire.  Each Lodge is responsible for meeting with its Scout Executive and
				setting up their own internal guidelines for implementation of this National BSA policy.<br><br>
				<li><a href="http://www.scouting.org/filestore/pdf/680-014.pdf" target="_blank">Download Tour Plan Application</a></li>
				<li><a href="http://www.scouting.org/scoutsource/HealthandSafety/TourPlanFAQ.aspx" target="_blank">Read the Tour Plan FAQ</a></li>
			</td></tr>
		
		 </table>

	</fieldset>