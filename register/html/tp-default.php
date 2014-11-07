<? 
/*	SM-Online Registration System                      www.sectionmaster.org
	------------------------------------------------------------------------

	"DEFAULT" PAGE (html/default.php)

	------------------------------------------------------------------------
	This page is the default handler page for the system, so it usually
	displays a welcome and login screen.										*/
	
	validate("email", "validEmail()", "You need to enter a valid e-mail address in order to register.  A valid e-mail address includes your login name, the '<code>@</code>' symbol, and the domain name of your ISP or business.  For example, <code>steve@apple.com</code> is valid but <code>bill@microsoft</code> is not valid.<br>", false, true);

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
							your e-mail address below to continue the process where you
							left off.</font></div>";
	} 

	# Registration Closed
	if ((time() < strtotime($EVENT['online_reg_open_date']) || time() > strtotime($EVENT['online_reg_close_date'])) && $_SESSION['backdoor'] != 'open') {
		$registration_open = false;
		$ERROR['login'] = "<div class=\"error_summary\"><font class=\"error_summary\">
							<h3 style='margin:0;'>{$EVENT['casual_event_name']} Closed</h3>
							Sorry, but the {$EVENT['formal_event_name']} is currently closed.   
							If you have any questions, please contact 
							{$EVENT['reg_contact_name']} at 
							<a href=\"mailto:{$EVENT['reg_contact_email']}\">
							{$EVENT['reg_contact_email']}</a>.</font></div>";
	} 

	# Destroy Reg
	if ($ERROR['login']) {
		destroyReg();
	}

?>

<h2><?=$EVENT['formal_event_name']?></h2>

	<? if ($registration_open !== false): ?>
	<p>Welcome!  To order from our Online Trading Post, you need to enter your e-mail address below to get started.  If you've attended any of our events or ordered from our trading post before, you should enter the e-mail address you used in the past <strong>even if you no longer have access to it</strong>.  We will use this address to identify you when registering or ordering with us again.</p>
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
		
		<input type="submit" id="submit_button" name="submitted" value="Enter Trading Post">
		
	</form>
	<? endif; ?>
	</center>