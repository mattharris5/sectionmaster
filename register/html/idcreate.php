<? 
/*	SM-Online Registration System                      www.sectionmaster.org
	------------------------------------------------------------------------

	ID Verification Data Creation Page (html/idcreate.php)

	------------------------------------------------------------------------										*/

	# Get $reason
		$reason = isset($reason) ? $reason : $_REQUEST['reason'];

	# Bypass if this is a tradingpost event
		if ($EVENT['type'] == 'tradingpost') {
			require ("includes/new_tp_order.php");
		}
		if ($reason != "new_member" && $EVENT['type'] == 'tradingpost') {
			gotoPage("tradingpost");
		}

	# Require extras
		include "includes/secret_questions.php";		

	# Special Validate Functions
			$q1 = get('question1');
			$q2 = get('question2');
			$q3 = get('question3');
			
		function diffQuestions($arg) {
			global $q1, $q2, $q3;
			if ($q1==$q2 || $q2==$q3 || $q1==$q3) {
				return false;	
			}
			return true;
		}
		
		function blankQuestions($arg) {
			global $q1, $q2, $q3;
			if ($q1=='' || $q2=='' || $q3=='') {
				return false;
			}
			return true;
		}
		
	# Validate Data
		validate("firstname", "", "Please enter your first name.");
		validate("lastname", "", "Please enter your last name");
	validate("email", "validEmail()", "You need to enter a valid e-mail address in order to register.  A valid e-mail address includes your login name, the '<code>@</code>' symbol, and the domain name of your ISP or business.  For example, <code>steve@apple.com</code> is valid but <code>bill@microsoft</code> is not valid.<br>", false, true);
		validate("birthdate","validBirthdate()","You need to select a valid birthdate.");

	if ($_SESSION['type'] != 'paper') {  // paper reg's dont have to have secret q's
		validate("question1_answer","","You must provide an answer.");
		validate("question2_answer","","You must provide an answer.");
		validate("question3_answer","","You must provide an answer.");
		validate("diffQuestions","diffQuestions()", "You can't use the same question twice!", false);
		validate("blankQuestions","blankQuestions()", "You must choose three questions.", false);
	}
        
	# Post-Validate
		if (postValidate()) {
			
			/* 
			INSERT/UPDATE MEMBER RECORD: 
			*/
			if ($reason == 'new_member') {
			
				// create member record
				$sql1 = "INSERT INTO members 
						SET email = '" . addslashes(get('email')) . "',
							birthdate = '" . addslashes(get('bday_year'))."/".addslashes(get('bday_month'))."/".addslashes(get('bday_day')) . "',
							firstname = '" . addslashes(get('firstname')) . "',
							lastname = '" .  addslashes(get('lastname')) . "'";
				$res1 = $db->query($sql1);
				$member_id = mysql_insert_id();
				if (DB::isError($res1)) {
					dbError("Error adding new member record ID in idcreate post-validate.", $res1, $sql1);
				}

			} else {
				
				// update name and bday
				$sql1 = "UPDATE members 
						SET email = '" . addslashes(get('email')) . "',
							birthdate = '" . addslashes(get('bday_year'))."/".addslashes(get('bday_month'))."/".addslashes(get('bday_day')) . "',
							firstname = '" . addslashes(get('firstname')) . "',
							lastname = '" .  addslashes(get('lastname')) . "'
						WHERE member_id = {$_SESSION['member_id']}
						LIMIT 1";
				$res1 = $db->query($sql1);
				$member_id = $_SESSION['member_id'];
				if (DB::isError($res1)) {
					dbError("Error updating record ID in idcreate post-validate.", $res1, $sql1);
				}

			}

			/*
			INSERT/UPDATE SECRET QUESTIONS RECORD
			*/

			if (($reason == 'new_member' OR $reason == 'noidinfo') && ($_SESSION['type'] != 'paper')) {
			
				// create secret question record
				$sql2 = "INSERT INTO secret_questions
						SET member_id = '$member_id',
							question1 = '" . addslashes(get('question1')) . "',
							question2 = '" . addslashes(get('question2')) . "',
							question3 = '" . addslashes(get('question3')) . "',
							question1_answer = '" . addslashes(get('question1_answer')) . "',
							question2_answer = '" . addslashes(get('question2_answer')) . "',
							question3_answer = '" . addslashes(get('question3_answer')) . "'";
				$res2 = $db->query($sql2);				
				if (DB::isError($res2)) {
					dbError("Error adding new secret question record ID in idcreate post-validate.", $res2, $sql2);
				}
			
			} elseif ($_SESSION['type'] != 'paper') { 
			
				// update secret questions
				$sql2 = "UPDATE secret_questions
						SET	question1 = '" . addslashes(get('question1')) . "',
							question2 = '" . addslashes(get('question2')) . "',
							question3 = '" . addslashes(get('question3')) . "',
							question1_answer = '" . addslashes(get('question1_answer')) . "',
							question2_answer = '" . addslashes(get('question2_answer')) . "',
							question3_answer = '" . addslashes(get('question3_answer')) . "'
						WHERE member_id = {$_SESSION['member_id']}
						LIMIT 1";
				$res2 = $db->query($sql2);				
				if (DB::isError($res2)) {
					dbError("Error updating secret question record in idcreate post-validate.", $res2, $sql2);
				}

			}

			/*
			GET READY FOR NEXT PAGE
			*/
			if ($reason == 'new_member') {

				// update key and re-enter the reg process
				gotoNextPage("idcreate", "id=$member_id&key=" . makeKey(10, $member_id, $sm_db));
			
			} else {
			
				// go to next page
				gotoNextPage('idcreate');
			
			}

		}

?>

<h2 style="display: inline; float: left;">Create Your "Identity"</h2>

<form action="<? print $_GLOBALS['form_action']; ?>" method=post style="display: inline; float: right;">
	<input type=hidden name="session" value="destroy">
	<input type=hidden name="event_id" value="<?=$EVENT['id']?>">
	<input type=submit value="Start a New <?=$EVENT['casual_event_name']?> Registration" style="float: right; width: auto; margin-top: 10px;">
</form><br />


	<? 
	// If they're a new member:
		if ($reason == 'new_member'): ?>
		<p><!--We couldn't find your e-mail address in our database, so we're assuming that this is the first time that you'll be attending Conclave.  (If you <i>have</i> attended Conclave before, you can try to <a href="register">login again</a>).-->  In order to create your member record, we need to gather some information so that we can verify your identity in the future if needed.  </p>
		
	<? 
	// if they just dont have idverify data yet
		else: ?>
		<p>We need to gather some information from you so that we can identify you in the future if needed.</p>
	
	<? endif; ?>

	<? print $ERROR['error_summary']; ?>
	
	<center>
	<form action="<?=$_GLOBALS['form_action']?>" method="<?=$_GLOBALS['form_method']?>">

	<fieldset><legend>Personal Identification</legend>

		<!-- Name -->
		<label for="firstname">First Name:</label>
			<input name="firstname" id="firstname" value="<? req('firstname') ?>">
			<? print $ERROR['firstname']; ?><br />
		<label for="lastname">Last Name:</label>
			<input name="lastname" id="lastname" value="<? req('lastname') ?>">
			<? print $ERROR['lastname']; ?><br />

		<!-- E-mail (readonly) -->
		<label for="email">E-mail Address:</label>
			<input name="email" id="email" value="<? req('email') ?>"><br />
			<? print $ERROR['email']; ?><br />
			
		<!-- Birthday -->
		<script language="JavaScript">
			function checkAge(form) {
				var bday = new Date(form.bday_year.value, form.bday_month.value-1, form.bday_day.value)
				var today = new Date()
				var age_calc = today.getTime() - bday.getTime()
				var age = parseInt(age_calc/60/60/24/365/1000)
				var elem = document.getElementById("ageCheck")
				if (age < 13) {
					alert("Based on your selection, you are under the age of 13.  In order to use this system, you must be at least 13 years of age.  In order to continue, these forms must be completed and submitted by someone over the age of 13, like a parent.\n\nBy clicking OK, the person filling out this form confirms that they are over the age of 13 and may legally submit these forms on the registrant's behalf.")
					elem.innerHTML = "<div class='error_summary'><h3>You must be over the age of 13</h3>Note: Someone over the age of 13 <b><i>must</i></b> complete the rest of this form on your behalf.  If you are under the age of 13, you must exit the system now.</div>"
				} else {
					elem.innerHTML = ""
				}
			}
		</script>
				
		<label for="bday_month">Birthday:</label>
			<select name="bday_month" id="bday_month">
				<option value="">--</option>
				<?
				for($i=1; $i<=12; $i++) {
					print "\n\t\t<option value=\"$i\"";
					if (get('bday_month') == $i OR ((get('birthdate') != '0000-00-00 00:00:00' && get('birthdate') != '') && date("n", strtotime(get('birthdate'))) == $i)) {
						print " selected";
					}
					print ">" . date("F", mktime(0,0,0,$i,1,2000)) . "</option>";
				} ?>
			</select>
			<select name="bday_day" id="bday_day">
				<option value="">--</option>
				<?
				for($i=1; $i<=31; $i++) {
					print "\n\t\t<option value=\"$i\"";
					if (get('bday_day') == $i OR ((get('birthdate') != '0000-00-00 00:00:00' && get('birthdate') != '') && date("j", strtotime(get('birthdate'))) == $i)) {
						print " selected";
					}
					print ">$i</option>";
				} ?>
			</select>
			<select name="bday_year" id="bday_year" onChange="checkAge(this.form);">
				<option value="">--</option>
				<?
				for($i=(date("Y")-10); $i>=1915; $i--) {
					print "\n\t\t<option value=\"$i\"";
					if (get('bday_year') == $i OR (get('birthdate') != '0000-00-00 00:00:00' && date("Y", strtotime(get('birthdate'))) == $i)) {
						print " selected";
					}
					print ">$i</option>";
				} ?>
			</select>
			<?=help_link("birthdate")?>
			<?=$ERROR['birthdate']?><br />
			<div id="ageCheck"></div>
		
		</fieldset>
		
		<? if ($_SESSION['type'] != 'paper'): ?>
		
		<fieldset><legend>Secret Questions</legend>

		Choose three "secret questions" below and answer them in the fields provided.  <b>Make sure that you can remember the answers to these questions</b>, since they will be used in future years if you no longer have access to your e-mail account, or if we need to verify your identity for another purpose. <i>This information will not be used for any other purpose or given to any other party.</i>
		
		<p><center><? print $ERROR['diffQuestions'] . " " . $ERROR['blankQuestions']; ?></center></p><br>

		<!-- Question 1 -->
		<label for="question1">Secret Question 1:</label>
			<select name="question1" id="question1">
				<option value="">-- Select a Question --</option>
				<?
				foreach($secretQs as $q) {
					print "\n\t\t<option value=\"$q\"";
					if (get('question1') == $q) {
						print " selected";
					}
					print ">$q</option>";
				} ?>
			</select>
			<? print $ERROR['question1']; ?><br />

		<label for="question1_answer">Answer:</label>
			<input name="question1_answer" id="question1_answer" value="<? req('question1_answer') ?>">
			<? print $ERROR['question1_answer']; ?><br />


		<!-- Question 2 -->
		<label for="question2">Secret Question 2:</label>
			<select name="question2" id="question2">
				<option value="">-- Select a Question --</option>
				<?
				foreach($secretQs as $q) {
					print "\n\t\t<option value=\"$q\"";
					if (req('question2', true) == $q) {
						print " selected";
					}
					print ">$q</option>";
				} ?>
			</select>
			<? print $ERROR['question2']; ?><br />

		<label for="question2_answer">Answer:</label>
			<input name="question2_answer" id="question2_answer" value="<? req('question2_answer') ?>">
			<? print $ERROR['question2_answer']; ?><br />


		<!-- Question 3 -->
		<label for="question3">Secret Question 3:</label>
			<select name="question3" id="question3">
				<option value="">-- Select a Question --</option>
				<?
				foreach($secretQs as $q) {
					print "\n\t\t<option value=\"$q\"";
					if (req('question3', true) == $q) {
						print " selected";
					}
					print ">$q</option>";
				} ?>
			</select>
			<? print $ERROR['question3']; ?><br />

		<label for="question3_answer">Answer:</label>
			<input name="question3_answer" id="question3_answer" value="<? req('question3_answer') ?>">
			<? print $ERROR['question3_answer']; ?><br />

		<? endif; ?>

		<!-- Hiddens -->
		<input type="hidden" name="event_id" value="<?=$EVENT['id']?>">
		<input type="hidden" name="reason" value="<?=$reason?>">
		<input type="hidden" name="page" value="idcreate">
		<input type="hidden" name="command" value="save_questions">

		
		<input id="submit_button" name="submitted" type="submit" value="Continue to Next Step >">

	</form>
	</center>