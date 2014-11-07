<? 
/*	SM-Online Registration System                      www.sectionmaster.org
	------------------------------------------------------------------------

	ID Verification Data Verification Page (html/idverify.php)

	------------------------------------------------------------------------										
																			*/ 
																			
	####################################
	# Checking the submitted values
	####################################
	if ($_REQUEST['submitted']) {
	
		$sql = "SELECT * FROM members
				LEFT JOIN secret_questions ON members.member_id = secret_questions.member_id
				WHERE members.member_id='{$_REQUEST['member_id']}'";
		$res = $db->getRow($sql);
			if (DB::isError($res)) { dbError("Checking idverify data", $res, $sql); }
			
		// check birthdate match
		$birthdate_submitted = date("m-d-Y", mktime(0, 0, 0, get('bday_month'), get('bday_day'), get('bday_year')));
		$birthdate_saved     = date("m-d-Y", strtotime($res->birthdate));
		if ($birthdate_submitted != $birthdate_saved) { $error_count = 5; }
		
		// check answers to questions
		$answer[1] = $res->question1_answer; $answer[2] = $res->question2_answer; $answer[3] = $res->question3_answer;
		for ($k=1; $k<=3; $k++) {
			if (str_replace(" ", "", stripslashes(strtolower($_REQUEST['answer'][$k]))) != str_replace(" ", "", stripslashes(strtolower($answer[$k])))) {
				$error_count++; 
			}
		}
		
		// proceed or give error
		if ($error_count > 2) {
		
			$ERROR['summary'] = "<div class=\"error_summary\"><font class=\"error_summary\">
				<h3 style='margin:0;'>Could not verify identity</h3>
				Sorry, but the data you provided did not match that in our database for your member record.
				Please try again or
				<a href=\"register/?command=new_member&event_id={$EVENT['id']}&email={$_SESSION['email']}\">
				start a new registration</a> instead.</font></div>";
			
		} else {
			
			// success!
			gotoNextPage("idcreate", "id={$_REQUEST['member_id']}&key=" . makeKey(10, $_REQUEST['member_id'], $sm_db));
			
		}
	}


?>


<h2 style="display: inline; float: left;">Verify Your Identity</h2>

<form action="<? print $_GLOBALS['form_action']; ?>" method=post style="display: inline; float: right;">
	<input type=hidden name="session" value="destroy">
	<input type=hidden name="event_id" value="<?=$EVENT['id']?>">
	<input type=submit value="Start a New <?=$EVENT['casual_event_name']?> Registration" style="float: right; width: auto; margin-top: 10px;">
</form><br />

	<p>If you no longer have access to your e-mail address, you can still access your member record and register for <?=$EVENT['casual_event_name']?>.  To keep your data safe, we need you to answer a few questions to verify your identity before we can give you access to your record. </p>
	
	<?=$ERROR['summary']?>
	
	<form action="<?=$_GLOBALS['form_action']?>" method="<?=$_GLOBALS['form_method']?>">

	<?
	####################################
	# Displaying the questions
	####################################
	if($_REQUEST['member_id'] || $_REQUEST['submitted']) {
		
		$sql = "SELECT * FROM secret_questions
				WHERE member_id = '{$_REQUEST['member_id']}'
				AND question1 != ''";
		$result = $db->query($sql);
			if (DB::isError($result)) { dbError("Get member idverify data", $questions, $sql); }
		
		
		// Check for no idverify data
		if ($result->numRows() == 0) {
			print "<div class=\"error_summary\"><font class=\"error_summary\">
				<h3 style='margin:0;'>No ID Verification Data on File</h3>
				Unfortunately, we don't have any ID verification data on file for you, so we
				are unable to verify your identity and give you access to your record.  Please
				<a href=\"register/?command=new_member&event_id={$EVENT['id']}&email={$_SESSION['email']}\">
				start a new registration</a> instead.</font></div>";
			$errors_found = true;
		} 
		
		else {

			// Get result
			$questions = $result->fetchRow();

			// Request Birthdate
			print "<label for=\"bday_month\">Birthdate:</label>
					<select name=\"bday_month\" id=\"bday_month\">
						<option value=\"\">--</option>";
						for($i=1; $i<=12; $i++) {
							print "\n\t\t<option value=\"$i\"";
							if (get('bday_month') == $i) {
								print " selected";
							}
							print ">" . date("F", mktime(0,0,0,$i,1,2000)) . "</option>";
						}
				print"	</select>
					<select name=\"bday_day\" id=\"bday_day\">
						<option value=\"\">--</option>";
						for($i=1; $i<=31; $i++) {
							print "\n\t\t<option value=\"$i\"";
							if (get('bday_day') == $i) {
								print " selected";
							}
							print ">$i</option>";
						}
				print "</select>
					<select name=\"bday_year\" id=\"bday_year\">
						<option value=\"\">--</option>";
						for($i=(date("Y")-12); $i>=1915; $i--) {
							print "\n\t\t<option value=\"$i\"";
							if (get('bday_year') == $i) {
								print " selected";
							}
							print ">$i</option>";
						}
				print"</select><br>";
				
			// Randomize order of secret q's
			while ($q[1]==$q[2] || $q[1]==$q[3] || $q[2]==$q[3]) {
				$q[1] = rand(1,3); $q[2] = rand(1,3); $q[3] = rand(1,3);
			}
			$question[1] = $questions->question1;
			$question[2] = $questions->question2;
			$question[3] = $questions->question3;
			
			// Print Questions
			print "<div style=\"margin-left: 90px;\">";
			for($j=1; $j<=1; $j++) {
				
				$num = $q[$j];
				
				print "\n\t\t<label for=\"question{$q[$j]}_answer\" class=\"spanned\">
						Q: $question[$num]</label><br>";
	
				print "<font style=\"float: left; font-weight: bold;\">A: </font>
						<input name=\"answer[$num]\" id=\"answer[$num]\"
						value=\"" . stripslashes($_REQUEST['answer'][$num]) . "\" style=\"width: 400px;\"><br>";
			
			}
			print "</div>
					<input type=\"hidden\" name=\"member_id\" value=\"{$_REQUEST['member_id']}\">
					<input type=\"submit\" id=\"submit_button\" name=\"submitted\" value=\"Submit >\"></form>";
			
		}
	}
	
	####################################
	# Choosing the member
	####################################		
	if ($errors_found || !$_REQUEST['member_id'] && !$_REQUEST['submitted']) {

		$sql = "SELECT * FROM members
				WHERE email = '{$_SESSION['email']}'
				ORDER BY lastname, firstname";
		$result = $db->query($sql);
			if (DB::isError($result)) { dbError("Get members from address", $result, $sql); }
	
				
		// If only ONE record
		if ($result->numRows() == 1) {
			$curr_member = $result->fetchRow();
			gotoPage("idverify", "member_id=$curr_member->member_id");
		}
		
		// If more than one record
		else {
			print "<center><label for=\"member_id\" class=\"spanned\">Which record would you like to attempt to access?</label><br>";
			
			print "<select name=\"member_id\" id=\"member_id\">";
			
				while ($curr_member = $result->fetchRow()) {
					print "<option value=\"$curr_member->member_id\">$curr_member->firstname "
							. substr($curr_member->lastname, 0, 1) . ".";
							$already_reg = $db->getRow("SELECT registration_complete FROM conclave_attendance WHERE member_id=$curr_member->member_id AND conclave_year={$EVENT['year']}");
							if ($already_reg->registration_complete == '1') {
							 	print " - Already registered for {$EVENT['year']} (Choose to view your receipt)";
							 } 
					print "</option>";
				}
				
			print "</select><br>
					<i>If your name does not appear in this list, <a href=\"register/?command=new_member&event_id={$EVENT['id']}&email={$_SESSION['email']}\">click here</a> to create a new member record.<br><input type=\"submit\" id=\"submit_button\" value=\"Continue >\"></form>";
		}
	}