<?
########################################
#
# Login Link Email
#
########################################

// setup the email headers
$Subject = $EVENT['type']=='tradingpost' ? "Your Trading Post Login Link from SectionMaster" : "Your {$EVENT['casual_event_name']} Registration Link from SectionMaster";
$myFromVar = $EVENT['type']=='tradingpost' ? "Trading Post" : "Online Registration";
$MailHeaders = "From: {$EVENT['org_name']} $myFromVar <$SECTION@sectionmaster.org> \n";
$MailHeaders.= "Reply-To: {$EVENT['reg_contact_email']}";

// special words
$words['action'] = $EVENT['type']=='tradingpost' ? "order from the trading post" : "register";
$words['signature'] = $EVENT['type']=='tradingpost' ? "Trading Post" : "Registration";

	// write the email body
	$Body = "Thank you for visiting the {$EVENT['org_name']} $myFromVar system. ";

	if ($result->numRows() > 1) {
		$Body .= "We found more than one user in the system with this e-mail address.  To {$words['action']}, click the link below your name:\n\n";
	} else {
		$Body .= "To {$words['action']}, click the link below your name:\n\n";
	}
	
	while ($curr_member = $result->fetchRow()) {
		
		$sql = "SELECT conclave_year, registration_complete, current_reg_step
				FROM conclave_attendance 
				WHERE member_id = '$curr_member->member_id'
					AND conclave_year = '{$EVENT['year']}'";
		$res = $db->getRow($sql);
			if (DB::isError($res)) { dbError("Could not check if already registered", $res, $sql); }

		if ($res->registration_complete == 1 && $EVENT['type'] != 'tradingpost') {
			$already_reg_note = " - Already registered for {$EVENT['year']}";
		} else {
			$already_reg_note = "";
		}
		
		$Body .= "$curr_member->firstname $curr_member->lastname (ID Number: $curr_member->member_id$already_reg_note)\n";
		$Body .= "{$_GLOBALS['entry_url']}?id=$curr_member->member_id&key=".makeKey(10, $curr_member->member_id, $sm_db, $_SESSION['session_id'])."\n\n";
	}
	
	$Body .= "If the web address above does not appear as a clickable link, you may need to copy and paste it into a web browser like Internet Explorer. ";
	$Body .= "If you did not request this e-mail or you have a problem with the {$EVENT['org_name']} $myFromVar system, please e-mail {$EVENT['reg_contact_email']}.\n\n";
	$Body .= "Thank you.\n- {$EVENT['org_name']} Online {$words['signature']}\n\n";
	
	$Body .= "* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * \n";
	$Body .= " This e-mail message was requested to be sent from sectionmaster.org \n";
	$Body .= " by ". gethostbyaddr($_SERVER['HTTP_X_FORWARDED_FOR']) ." on ". date("D M j G:i:s T Y");
