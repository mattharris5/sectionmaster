<?
/*	SM-Online Registration System                      www.sectionmaster.org
	------------------------------------------------------------------------

	TRAINING PAGE (html/training.php)

	------------------------------------------------------------------------										*/

import_request_variables('GP');

// Does this section do online training?
$res = $sm_db->getRow("SELECT do_training FROM events WHERE id={$EVENT['id']}");
if ($res->do_training == 0) {
	gotoNextPage("training");
}

// Page heading
print "\n<h2>Training</h2>";
//if ($EVENT['id']=='2') {print "<pre>"; print_r($_REQUEST); print "</pre>";}

// TRAINING STEP 1: Select Major and Degree level
if (!$firststepcomplete):

	// Validate Data
	validate("college_major","X","Please select a college major.");
	if (get('college_major') != $EVENT['training_staff_college'])
		validate("college_degree","","Please choose the degree you'll be working toward.");
//print "training staff college: ".$EVENT['training_staff_college'];
//print get('college_major');
        
	// Post-Validate
	if (postValidate()) {
		$query="UPDATE conclave_attendance SET
								college_major = '". get('college_major') . "', college_degree = '". get('college_degree') . "'
							   WHERE member_id = {$_SESSION['member_id']} AND conclave_year = {$EVENT['year']}";
		$update = $db->query($query);
		$firststepcomplete=true;
							  
		if (DB::isError($update)) { dbError("Could not save training information", $update, ""); }
		
		// If the user chose the major allowing them to bypass the training registration process, make it so!
		if ($college_major==$EVENT['training_staff_college']) {
			$staffquery="UPDATE conclave_attendance SET session_1='1', session_2='2', session_3='3', session_4='4'
							WHERE member_id = {$_SESSION['member_id']} AND conclave_year = {$EVENT['year']}";
			$staffupdate=$db->query($staffquery);
			
			if (DB::isError($staffupdate)) { dbError("Could not save training information", $update, ""); }
			
			gotoNextPage("training");
		}
	}
	
	if (!$firststepcomplete):
	?>
	
	<form name="form1" action="<? print $_GLOBALS['form_action']; ?>" method="<? print $_GLOBALS['form_method']; ?>">
    
    <?
		// Check to see if the section ignores the degrees
		if ($EVENT['training_ignore_degrees'] == 1) {
			$ignoredegrees = true;
		}
		else $ignoredegrees = false;
	?>

	<p><?=$EVENT['training_university']?> is <i>just one</i> of the fun parts of <?=$EVENT['casual_event_name']?>!  A few hours are devoted to training,
	and you get to choose from a variety of fun and interesting classes.  The sessions are taught by the best leaders in the section
	and, in many cases, the nation.  They provide incredible opportunity for leadership growth as you continue in service to Scouting
	as an Arrowman.
    <? if (!$ignoredegrees) print "Each year, you can even earn a new degree, which usually comes	with a collectible item (patch, medal, etc.) for each degree!</p>"; ?>
	<? if ($EVENT['training_required_classes_for_degree']>0) print "<p><a href=http://www.sectionmaster.org/register/training/?event=$event_id target=_blank>Click here to view the full {$EVENT['training_university']} schedule.</a></p>";?>
	<? print $ERROR['error_summary']; ?>	

	<? $res = $db->query("SELECT * FROM colleges WHERE show_online=1 ORDER BY college_name"); ?>

		<script type="text/javascript">
			var descriptions=new Array()
			<?
			for ($x=0; $x<$res->numRows(); $x++) {
				$college=$res->fetchRow(DB_FETCHMODE_OBJECT,$x);
				print "\ndescriptions[$college->college_id]=\"<b>$college->college_name:</b> $college->college_desc\"";
			} 
			?>
			
			function updateDescription(choice) {
				var elem=document.getElementById("major_description_holder")
				if (choice=="X")
					elem.innerHTML="<br><div id='major_description'></div>"
				else
					elem.innerHTML="<br><div id='major_description'>" + descriptions[choice] + "</div>"
			}
		</script>
		
		<?
		// Check to see if the section allow users to manually change their degree levels (i.e., from Bachelors to Masters)
		if ($EVENT['training_allow_manual_degree_level_changes'] == 1) {
			$changedegree = true;
		}
		else $changedegree = false;
		
		// Check to see if the section ignores the training college
		if ($EVENT['training_ignore_colleges'] == 1) {
			$ignorecolleges = true;
		}
		else $ignorecolleges = false;
		
		// Check to see what the last EARNED degree was -AND-
		// Set this year's degree to the next eligible one OR keep it the same if above Master's level or is something non-standard
		if (!$changedegree) {
			$reslastdeg = $db->query("SELECT college_degree AS last_degree
								FROM conclave_attendance
								WHERE member_id = {$_SESSION['member_id']}
									AND degree_reqts_complete = 1
								ORDER BY conclave_year DESC
								LIMIT 1");
			$degree=$reslastdeg->fetchRow();
			// If a degree was previously earned
			if (($degree->last_degree<4) and ($reslastdeg->numRows()!=0)) {
				$college_degree=$degree->last_degree+1;
			}
			// If a degree possibly was previously worked on (without prior records to it) but didn't finish the last one
			else {
				$reslastworkingdeg = $db->query("SELECT college_degree AS last_working_degree
													FROM conclave_attendance
													WHERE member_id = {$_SESSION['member_id']}
														AND conclave_year <> {$EVENT['year']}
														AND degree_reqts_complete = 0
														AND college_degree <= 4
													ORDER BY conclave_year DESC
													LIMIT 1");
				$lastworkingdeg=$reslastworkingdeg->fetchRow();
				// If a degree was previously started but unearned
				if ($reslastworkingdeg->numRows()>0) {
					$college_degree=$lastworkingdeg->last_working_degree;
				}
				// If there was no degree previously earned or they were all earned
				else {
					$college_degree=1;
				}
			}
			$degrees = $db->query("SELECT * FROM degrees WHERE degree_id=$college_degree");
			$degree=$degrees->fetchRow();
				if (!$ignoredegrees) print "This year at {$EVENT['casual_event_name']} you will be working toward your <b>$degree->degree_name Degree</b> according to our records.
				If this is incorrect, contact the {$EVENT['training_university']} President or resolve it when you get to {$EVENT['casual_event_name']}.";
				print "<input type=\"hidden\" class=\"hidden\" name=\"college_degree\" value=\"$college_degree\"><br>";
		}
		
		print "<script language=javascript>
			function toggleDegreeSelection(myVal, form) {
				myDiv = document.getElementById('DegreeDiv');
				if (myVal == '{$EVENT['training_staff_college']}') {
					myDiv.style.display = 'none';
					myDiv.style.visibility = 'hidden';
					form.college_degree.selectedIndex = 0;
					form.submitted.value = 'Bypass Training Registration & Continue >';
				} else {
					myDiv.style.display = '';
					myDiv.style.visibility = 'visible';
					form.submitted.value = 'Choose My Major & Continue >';
				}
			}
			</script>";

		print "<p>To begin, choose curriculum you'd like to work on this year with the option(s) below.</p>";
		
		// Allow the user to choose a college major assuming it's okay with the section
		if (!$ignorecolleges) {
			print "
			<label for=\"college_major\">College Major or Category:</label>
				<select name=\"college_major\" id=\"college_major\" onChange=\"updateDescription(this.value); toggleDegreeSelection(this.value,this.form);\">
					<option value=\"X\">-- Select College Major--</option>";
	
					$opt[get('college_major')] = " selected";
					for ($x=0; $x<$res->numRows(); $x++) {
						$college=$res->fetchRow(DB_FETCHMODE_OBJECT,$x);
						print "\n<option value=\"$college->college_id\"{$opt[$college->college_id]}>$college->college_name</option>";
					}
					if ($EVENT['training_unrestricted_college'] != 0) {
						print "<option value=\"{$EVENT['training_unrestricted_college']}\">No Major</option>";
					}
					if ($_SESSION['type'] == 'paper') {
						print "<option value=\"{$EVENT['training_staff_college']}\">None -- Bypass Training</option>";
					}
					
				print "</select>";
			print help_link('college_major') . $ERROR['college_major'];
			print "<div id=\"major_description_holder\"></div>";
		}
		else print "<input type=hidden name=\"college_major\" id=\"college_major\" value=\"{$EVENT['training_unrestricted_college']}\">";
		
		// Allow the user to change his degree assuming it's okay with the section
		if ($changedegree) {
			print "<div id=\"DegreeDiv\">";
			$res = $db->query("SELECT * FROM degrees ORDER BY sequence");
			print "<br><br><label for=\"college_degree\">Degree:</label>
						<select name=\"college_degree\" id=\"college_degree\">
							<option value=\"\">-- Select Degree --</option>";
			$optdeg[get('college_degree')] = " selected";
			while ($degree = $res->fetchRow()) {
				print "\n\t\t\t<option value=\"$degree->degree_id\"{$optdeg[$degree->degree_id]}>$degree->degree_name</option>";
			}
			print "\n\t\t</select>";
			print $ERROR['college_degree'];
			print "</div>";
		}
		
		//Disable training session selection if zero sessions are held
		if ($EVENT['training_required_classes_for_degree'] == '0')
		{
			print '<input type="hidden" name="savetraining" value="true">';
			print '<input type="hidden" name="session_1" value="0">';
			print '<input type="hidden" name="session_2" value="0">';
			print '<input type="hidden" name="session_3" value="0">';
			print '<input type="hidden" name="session_4" value="0">';
			print '<input type="hidden" name="secondstepcomplete" value="true">';
		}
		?>
		<br><input id="submit_button" name="submitted" type="submit" value="Choose My Major & Continue >">
		</form>
		<?
		endif;
endif;
		
// TRAINING STEP 2: Select Training Sessions
if ($firststepcomplete):
		
	$instructiontext="<p>Now it's time to select your training sessions.  There is a wide variety of sessions available for your major.
	If you decide you want to change your major or completely start over, click the <b>Restart Training Signup</b> button below to go back to the previous page.</p>
	
	<div class=\"info_box\">To earn your training degree at " . $EVENT['training_university'] . ", you must attend all "
	. $EVENT['training_required_classes_for_degree'] . " sessions, "
	. ($EVENT['training_required_classes_for_degree'] - 1) . " of which are from your major and 1 is an elective of your choice.
	If you're working on your Master's or Doctorate degrees, you should plan to sign up for a Doctoral Preparation or Train the Trainer (TTT) session if one is available.";
	
	//Store the name of the college that allows unrestricted session registrations
	$unrestrictedsql=$db->query("SELECT college_id, college_name FROM colleges WHERE deleted<>1 AND college_id={$EVENT['training_unrestricted_college']}");
	$unrestricted=$unrestrictedsql->fetchRow();

	if ($EVENT['training_unrestricted_college']!=0) {
		$instructiontext=$instructiontext . " A degree in the " . $unrestricted->college_name . " allows you to select any available sessions at any
		time if you can't decide on one particular subject area in which to focus.";
	}
	$instructiontext=$instructiontext . "</div>";
	
	//Determine person's degree level
	$res=$db->query("SELECT college_degree, college_major FROM conclave_attendance
						WHERE member_id = {$_SESSION['member_id']} AND conclave_year = {$EVENT['year']}");
	$degree=$res->fetchRow();
	
	//Determine whether to only show classes at or above the person's degree level
	if ($EVENT['training_enforce_upper_level_courses']==1) {
		$upperclassessql=" AND course_number>=" . $degree->college_degree*100 . " ";
		print "<p>The degree you are working toward requires you take <b>" . $degree->college_degree*100 . "-level</b> classes or above.</p>";
	}
	
	// SESSION 1*****************************************************************************************************
	$sessions1=$db->query("SELECT session_id, college_prefix, course_number, session_name, description, max_size, session_length AS length,
								CONCAT(college_prefix, course_number) AS course_code
							FROM training_sessions, colleges
							WHERE training_sessions.deleted<>1 AND college = college_id AND offered = 1 AND time = 1 AND college_id <> {$EVENT['training_staff_college']} $upperclassessql 
							ORDER BY college_prefix, course_number, session_name");
	if ((!$sessions1->numRows()==0) and ($session_1=="")) {
		print $instructiontext;
		$instructionsoff=true;
		$sessioncount++;
		print "<table class=\"training\" width=100%><th colspan=3>Select Your Choice for Session $sessioncount<div class=\"button\" id=\"right\"><a href=\"register/\">Restart Training Signup</a></div></th>";
		$i=0;
		while ($session1=$sessions1->fetchRow()) {
			// Check if class is already full, then display list of classes
			$sessionsizesql=$db->query("SELECT count(*) AS size FROM conclave_attendance WHERE session_1=$session1->session_id AND conclave_year={$EVENT['year']}");
			$sessionsize=$sessionsizesql->fetchRow();
			if (($sessionsize->size<$session1->max_size) or ($session1->max_size=="")) {
				if ($session1->length==2) $sess_2=2;
				elseif ($session1->length==3) { $sess_2=2; $sess_3=3; }
				elseif ($session1->length==4) { $sess_2=2; $sess_3=3; $sess_4=4; $secondstep=true; }
				$i++;
				print "\n\t\t\t<tr id=\""; print $i%2==1 ? "odd" : "even"; print "\"><td>$session1->college_prefix$session1->course_number</td><td width=75%>".stripslashes($session1->session_name);
				print $session1->length>1 ? " <i>($session1->length hours)</i>" : ""; print "</td>
				<td width=\"50\"><form name=\"form1\" action=\"{$_GLOBALS['form_action']}\" method=\"{$_GLOBALS['form_method']}\">
								<input type=\"submit\" name=\"submitted\" id=\"submitted\" value=\"Add to My Schedule\">
								<input type=\"hidden\" class=\"hidden\" name=\"session_1\" id=\"session_1\" value=\"$session1->session_id\"><input type=\"hidden\" class=\"hidden\" name=\"s1code\" id=\"s1code\" value=\"$session1->course_code\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_2\" id=\"session_2\" value=\"$sess_2\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_3\" id=\"session_3\" value=\"$sess_3\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_4\" id=\"session_4\" value=\"$sess_4\">
				<input type=\"hidden\" class=\"hidden\" name=\"firststepcomplete\" id=\"firststepcomplete\" value=\"true\">
				<input type=\"hidden\" class=\"hidden\" name=\"instructionsoff\" id=\"instructionsoff\" value=\"$instructionsoff\">
				<input type=\"hidden\" class=\"hidden\" name=\"secondstepcomplete\" id=\"secondstepcomplete\" value=\"$secondstep\">
				<input type=\"hidden\" class=\"hidden\" name=\"sessioncount\" id=\"sessioncount\" value=\"$sessioncount\">
</form></td></tr>
				<tr id=\""; print $i%2==1 ? "odd" : "even"; print "\"><td></td><td id=\"description\" colspan=2>".nl2br(stripslashes($session1->description))."</td></tr>";
				$sess_2=""; $sess_3=""; $sess_4=""; $secondstep="";
			}
		}
		if ($i==0) {
			print "<tr><td colspan=\"3\"><div class=\"error_summary\">All classes in your major are full for this session. You might try using this session for your
			elective or choosing a different major.  If you want to be able to take any available class during any session, you can start over and choose the
			$unrestricted->college_name as your major.</div></td></tr>";
		}
	}
	elseif ((!$sessions1->numRows()==0) and ($session_1=="")) $session_1=0;
	elseif ($sessions1->numRows()==0) $session_1=1;
	else $session_1=$session_1;

	
	$excludesql=" HAVING course_code<>\"$s1code\" AND course_code<>\"$s2code\" AND course_code<>\"$s3code\"";
	// SESSION 2*****************************************************************************************************
	// Determine whether to only show classes in the major (i.e., was an elective already chosen?)
	// Also ignores the elective restriction if the unrestricted (general studies) major was chosen
	if ($session_1!=0) {
		if (!$instructionsoff) { print $instructiontext; $instructionsoff=true; }
		$checksession1=$db->query("SELECT college FROM training_sessions WHERE session_id=$session_1");
		$check1=$checksession1->fetchRow();
		if ($check1->college==$EVENT['training_staff_college']) $electivedone=false;
		else if ($check1->college==$degree->college_major) $electivedone=false;
		else if ($degree->college_major==$EVENT['training_unrestricted_college']) $electivedone=false;
		else $electivedone=true;
		if ($electivedone) $electivesql=" AND college_id=" . $degree->college_major;
	}
	
	$sessions2=$db->query("SELECT session_id, college_prefix, course_number, session_name, description, max_size, session_length AS length,
								CONCAT(college_prefix, course_number) AS course_code
							FROM training_sessions, colleges
							WHERE training_sessions.deleted<>1 AND college = college_id AND offered = 1 AND time = 2 AND college_id <> {$EVENT['training_staff_college']} $upperclassessql $electivesql $excludesql
							ORDER BY college_prefix, course_number, session_name");
	if (((!$sessions2->numRows()==0) and (($session_2=="") or ($session_2==0))) and ($session_1!=0)) {
		// Print table of already-selected sessions
		if ($session_1!=1) print "\n<p>Here's your training schedule so far. Choose your next class below or start over.<p>\n<table class=\"classschedule\"><th colspan=3>$member->firstname's Training Schedule</th>";
		if ($session_1!=1) {
			$s1q=$db->query("SELECT college_prefix, college, course_number, session_name, session_length FROM training_sessions, colleges WHERE college=college_id AND session_id=$session_1"); $s1=$s1q->fetchRow();
			$tablecount++; print "\n<tr valign=top><td width=70>Session $tablecount</td><td width=60>$s1->college_prefix$s1->course_number</td><td>".stripslashes($s1->session_name);
			if (($s1->college!=$degree->college_major) and ($degree->college_major!=$unrestricted->college_id)) print " <i>(Elective)</i>";
			print $s1->session_length>1 ? "<br><i>This session will last $s1->session_length hours on your actual schedule.</i>" : "";	print "</td></tr>"; }
		if ($session_1!=1) print "\n</table>";

		$sessioncount++;
		$i=0;
		print "<table class=\"training\" width=100%><th colspan=3>Select Your Choice for Session $sessioncount<div class=\"button\" id=\"right\"><a href=\"register/\">Restart Training Signup</a></div></th>";
		while ($session2=$sessions2->fetchRow()) {
			// Check if class is already full, then display list of classes
			$sessionsizesql=$db->query("SELECT count(*) AS size FROM conclave_attendance WHERE session_2=$session2->session_id AND conclave_year={$EVENT['year']}");
			$sessionsize=$sessionsizesql->fetchRow();
			if (($sessionsize->size<$session2->max_size) or ($session2->max_size=="")) {
				if ($session2->length==2) $sess_3=3;
				elseif ($session2->length==3) { $sess_3=3; $sess_4=4; $secondstep=true; }
				//Check if there are only three training sessions required and this one takes up 2 hours
				$sessions4_check=$db->query("SELECT session_id, course_number, session_name, description, max_size
										FROM training_sessions
										WHERE deleted<>1 AND offered = 1 AND time = 4 AND college <> {$EVENT['training_staff_college']}");
				if (($EVENT['training_required_classes_for_degree']==3) and ($sessions4_check->numRows()==0) and ($session2->length==2)) { $secondstep=true; $sess_3=3; $sess_4=4; }
				$i++;
				print "\n\t\t\t<tr id=\""; print $i%2==1 ? "odd" : "even"; print "\"><td>$session2->college_prefix$session2->course_number</td><td width=75%>".stripslashes($session2->session_name);
				print $session2->length>1 ? " <i>($session2->length hours)</i>" : ""; print "</td>
				<td width=\"50\"><form name=\"form1\" action=\"{$_GLOBALS['form_action']}\" method=\"{$_GLOBALS['form_method']}\">
				<input type=\"submit\" name=\"submitted\" id=\"submitted\" value=\"Add to My Schedule\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_1\" id=\"session_1\" value=\"$session_1\"><input type=\"hidden\" class=\"hidden\" name=\"s1code\" id=\"s1code\" value=\"$s1code\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_2\" id=\"session_2\" value=\"$session2->session_id\"><input type=\"hidden\" class=\"hidden\" name=\"s2code\" id=\"s2code\" value=\"$session2->course_code\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_3\" id=\"session_3\" value=\"$sess_3\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_4\" id=\"session_4\" value=\"$sess_4\">
				<input type=\"hidden\" class=\"hidden\" name=\"firststepcomplete\" id=\"firststepcomplete\" value=\"true\">
				<input type=\"hidden\" class=\"hidden\" name=\"instructionsoff\" id=\"instructionsoff\" value=\"$instructionsoff\">
				<input type=\"hidden\" class=\"hidden\" name=\"secondstepcomplete\" id=\"secondstepcomplete\" value=\"$secondstep\">
				<input type=\"hidden\" class=\"hidden\" name=\"sessioncount\" id=\"sessioncount\" value=\"$sessioncount\">
				</form></td></tr>
				<tr id=\""; print $i%2==1 ? "odd" : "even"; print "\"><td></td><td id=\"description\" colspan=2>".nl2br(stripslashes($session2->description))."</td></tr>";
				$sess_3=""; $sess_4=""; $secondstep="";
			}
		}
		if ($i==0) {
			print "<tr><td colspan=\"3\"><div class=\"error_summary\">All classes in your major are full for this session. You might try using this session for your
			elective or choosing a different major.  If you want to be able to take any available class during any session, you can start over and choose the
			$unrestricted->college_name as your major.</div></td></tr>";
		}
	}
	elseif ((!$sessions2->numRows()==0) and ($session_2=="")) $session_2=0;
	elseif ((($sessions2->numRows()==0) and (($session_2=="") or ($session_2==0))) and ($session_1!=0)) {
		print "<tr><td colspan=\"3\"><div class=\"error_summary\">All classes in your major are full for this session. You might try using this session for your
		elective or choosing a different major.  If you want to be able to take any available class during any session, you can start over and choose the
		$unrestricted->college_name as your major.<br><ul><li><a href=register/?goto=training>Start over with my training signup.</a></li></div></td></tr>";
	}
	else $session_2=$session_2;
	
	// SESSION 3*****************************************************************************************************

	// Determine whether to only show classes in the major (i.e., was an elective already chosen?)
	// Also ignores the elective restriction if the unrestricted (general studies) major was chosen
	if ($session_2!=0) {
		if (!$instructionsoff) { print $instructiontext; $instructionsoff=true; }
		$checksession2=$db->query("SELECT college FROM training_sessions WHERE session_id=$session_2");
		$check2=$checksession2->fetchRow();
		if ($electivedone) $electivedone=true;
		else if ($check2->college==$EVENT['training_staff_college']) $electivedone=false;
		else if ($check2->college==$degree->college_major) $electivedone=false;
		else if ($degree->college_major==$EVENT['training_unrestricted_college']) $electivedone=false;
		else $electivedone=true;
		if ($electivedone) $electivesql=" AND college_id=" . $degree->college_major;
	}
	
	//Check if there are only three training sessions required
	$sessions4_check=$db->query("SELECT session_id, course_number, session_name, description, max_size
							FROM training_sessions
							WHERE deleted<>1 AND offered = 1 AND time = 4 AND college <> {$EVENT['training_staff_college']}");
	if (($EVENT['training_required_classes_for_degree']==3) and ($sessions4_check->numRows()==0)) { $secondstep=true; $sess_4=4; }


	$sessions3=$db->query("SELECT session_id, college_prefix, course_number, session_name, description, max_size, session_length AS length,
								CONCAT(college_prefix, course_number) AS course_code
							FROM training_sessions, colleges
							WHERE training_sessions.deleted<>1 AND college = college_id AND offered = 1 AND time = 3 AND college_id <> {$EVENT['training_staff_college']} $upperclassessql $electivesql $excludesql
							ORDER BY college_prefix, course_number, session_name");
	if (((!$sessions3->numRows()==0) and (($session_3=="") or ($session_3==0))) and ($session_2!=0)) {
		// Print table of already-selected sessions
		if (($session_1!=1) or ($session_2!=2)) print "\n<p>Here's your training schedule so far. Choose your next class below or start over.<p>\n<table class=\"classschedule\"><th colspan=3>$member->firstname's Training Schedule</th>";
		if ($session_1!=1) {
			$s1q=$db->query("SELECT college_prefix, college, course_number, session_name, session_length FROM training_sessions, colleges WHERE college=college_id AND session_id=$session_1"); $s1=$s1q->fetchRow();
			$tablecount++; print "\n<tr valign=top><td width=70>Session $tablecount</td><td width=60>$s1->college_prefix$s1->course_number</td><td>".stripslashes($s1->session_name);
			if (($s1->college!=$degree->college_major) and ($degree->college_major!=$unrestricted->college_id)) print " <i>(Elective)</i>";
			print $s1->session_length>1 ? "<br><i>This session will last $s1->session_length hours on your actual schedule.</i>" : "";	print "</td></tr>"; }
		if ($session_2!=2) {
			$s2q=$db->query("SELECT college_prefix, college, course_number, session_name, session_length FROM training_sessions, colleges WHERE college=college_id AND session_id=$session_2"); $s2=$s2q->fetchRow();
			$tablecount++; print "\n<tr valign=top><td width=70>Session $tablecount</td><td width=60>$s2->college_prefix$s2->course_number</td><td>".stripslashes($s2->session_name);
			print $s2->college!=$degree->college_major && $degree->college_major!=$unrestricted->college_id ? " <i>(Elective)</i>" : ""; 
			print $s2->session_length>1 ? "<br><i>This session will last $s2->session_length hours on your actual schedule.</i></td></tr>" : ""; print "</td></tr>"; }
		if (($session_1!=1) or ($session_2!=2)) print "\n</table>";
		
		$sessioncount++;
		print "<table class=\"training\" width=100%><th colspan=3>Select Your Choice for Session $sessioncount<div class=\"button\" id=\"right\"><a href=\"register/\">Restart Training Signup</a></div></th>";
		$i=0;
		while ($session3=$sessions3->fetchRow()) {
			// Check if class is already full, then display list of classes
			$sessionsizesql=$db->query("SELECT count(*) AS size FROM conclave_attendance WHERE session_3=$session3->session_id AND conclave_year={$EVENT['year']}");
			$sessionsize=$sessionsizesql->fetchRow();
			if (($sessionsize->size<$session3->max_size) or ($session3->max_size=="")) {
				if ($session3->length==2) { $sess_4=4; $secondstep=true; }
				$i++;
				print "\n\t\t\t<tr id=\""; print $i%2==1 ? "odd" : "even"; print "\"><td>$session3->college_prefix$session3->course_number</td><td width=75%>".stripslashes($session3->session_name);
				print $session3->length>1 ? "<i> ($session3->length hours)</i>" : ""; print "</td>
				<td width=\"50\"><form name=\"form1\" action=\"{$_GLOBALS['form_action']}\" method=\"{$_GLOBALS['form_method']}\">
				<input type=\"submit\" name=\"submitted\" id=\"submitted\" value=\"Add to My Schedule\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_1\" id=\"session_1\" value=\"$session_1\"><input type=\"hidden\" class=\"hidden\" name=\"s1code\" id=\"s1code\" value=\"$s1code\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_2\" id=\"session_2\" value=\"$session_2\"><input type=\"hidden\" class=\"hidden\" name=\"s2code\" id=\"s2code\" value=\"$s2code\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_3\" id=\"session_3\" value=\"$session3->session_id\"><input type=\"hidden\" class=\"hidden\" name=\"s3code\" id=\"s3code\" value=\"$session3->course_code\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_4\" id=\"session_4\" value=\"$sess_4\">
				<input type=\"hidden\" class=\"hidden\" name=\"firststepcomplete\" id=\"firststepcomplete\" value=\"true\">
				<input type=\"hidden\" class=\"hidden\" name=\"instructionsoff\" id=\"instructionsoff\" value=\"$instructionsoff\">
				<input type=\"hidden\" class=\"hidden\" name=\"secondstepcomplete\" id=\"secondstepcomplete\" value=\"$secondstep\">
				<input type=\"hidden\" class=\"hidden\" name=\"sessioncount\" id=\"sessioncount\" value=\"$sessioncount\">
				</form></td></tr>
				<tr id=\""; print $i%2==1 ? "odd" : "even"; print "\"><td></td><td id=\"description\" colspan=2>".nl2br(stripslashes($session3->description))."</td></tr>";
				if (!$secondstep) { $secondstep=""; $sess_4=""; }
				else $sess_4=4;
			}
		}
		if ($i==0) {
			print "<tr><td colspan=\"3\"><div class=\"error_summary\">All classes in your major are full for this session. You might try using this session for your
			elective or choosing a different major.  If you want to be able to take any available class during any session, you can start over and choose the
			$unrestricted->college_name as your major.</div></td></tr>";
		}
	}
	elseif ((!$sessions3->numRows()==0) and ($session_3=="")) $session_3=0;
	elseif (($session_2!=0) and ($EVENT['training_required_classes_for_degree']<3))
	{
		$session_3=3;
		$session_4=4;
		$secondstepcomplete=true;
	}
	elseif ((($sessions3->numRows()==0) and (($session_3=="") or ($session_3==0))) and ($session_2!=0)) {
		print "<tr><td colspan=\"3\"><div class=\"error_summary\">All classes in your major are full for this session. You might try using this session for your
		elective or choosing a different major.  If you want to be able to take any available class during any session, you can start over and choose the
		$unrestricted->college_name as your major.<br><ul><li><a href=register/?goto=training>Start over with my training signup.</a></li></div></td></tr>";
	}
	else $session_3=$session_3;
	
	// SESSION 4*****************************************************************************************************

	// Determine whether to only show classes in the major (i.e., was an elective already chosen?)
	// Also ignores the elective restriction if the unrestricted (general studies) major was chosen
	if ($session_3!=0) {
		if (!$instructionsoff) { print $instructiontext; $instructionsoff=true; }
		$checksession3=$db->query("SELECT college FROM training_sessions WHERE session_id=$session_3");
		$check3=$checksession3->fetchRow();
		if ($electivedone) $electivedone=true;
		else if ($check3->college==$EVENT['training_staff_college']) $electivedone=false;
		else if ($check3->college==$degree->college_major) $electivedone=false;
		else if ($degree->college_major==$EVENT['training_unrestricted_college']) $electivedone=false;
		else $electivedone=true;
		if ($electivedone) $electivesql=" AND college_id=" . $degree->college_major;
	}
	
	$sessions4=$db->query("SELECT session_id, college_prefix, course_number, session_name, description, max_size, session_length AS length,
								CONCAT(college_prefix, course_number) AS course_code
							FROM training_sessions, colleges
							WHERE training_sessions.deleted<>1 AND college = college_id AND offered = 1 AND time = 4 AND college_id <> {$EVENT['training_staff_college']} $upperclassessql $electivesql $excludesql
							ORDER BY college_prefix, course_number, session_name");

	if (((!$sessions4->numRows()==0) and (($session_4=="") or ($session_4==0))) and ($session_3!=0)) {
		// Print table of already-selected sessions
		if (($session_1!=1) or ($session_2!=2) or ($session_3!=3)) print "\n<p>Here's your training schedule so far. Choose your next class below or start over.<p>\n<table class=\"classschedule\"><th colspan=3>$member->firstname's Training Schedule</th>";
		if ($session_1!=1) {
			$s1q=$db->query("SELECT college_prefix, college, course_number, session_name, session_length FROM training_sessions, colleges WHERE college=college_id AND session_id=$session_1"); $s1=$s1q->fetchRow();
			$tablecount++; print "\n<tr valign=top><td width=70>Session $tablecount</td><td width=60>$s1->college_prefix$s1->course_number</td><td>".stripslashes($s1->session_name);
			if (($s1->college!=$degree->college_major) and ($degree->college_major!=$unrestricted->college_id)) print " <i>(Elective)</i>";
			print $s1->session_length>1 ? "<br><i>This session will last $s1->session_length hours on your actual schedule.</i>" : "";	print "</td></tr>"; }
		if ($session_2!=2) {
			$s2q=$db->query("SELECT college_prefix, college, course_number, session_name, session_length FROM training_sessions, colleges WHERE college=college_id AND session_id=$session_2"); $s2=$s2q->fetchRow();
			$tablecount++; print "\n<tr valign=top><td width=70>Session $tablecount</td><td width=60>$s2->college_prefix$s2->course_number</td><td>".stripslashes($s2->session_name);
			print $s2->college!=$degree->college_major && $degree->college_major!=$unrestricted->college_id ? " <i>(Elective)</i>" : ""; 
			print $s2->session_length>1 ? "<br><i>This session will last $s2->session_length hours on your actual schedule.</i></td></tr>" : ""; print "</td></tr>"; }
		if ($session_3!=3) {
			$s3q=$db->query("SELECT college_prefix, college, course_number, session_name, session_length FROM training_sessions, colleges WHERE college=college_id AND session_id=$session_3"); $s3=$s3q->fetchRow();
			$tablecount++; print "\n<tr valign=top><td width=70>Session $tablecount</td><td width=60>$s3->college_prefix$s3->course_number</td><td>".stripslashes($s3->session_name);
			print $s3->college!=$degree->college_major && $degree->college_major!=$unrestricted->college_id ? " <i>(Elective)</i>" : "";
			print $s3->session_length>1 ? "<br><i>This session will last $s3->session_length hours on your actual schedule.</i>" : "";	print "</td></tr>"; }
		if (($session_1!=1) or ($session_2!=2) or ($session_3!=3)) print "\n</table>";
		
		$sessioncount++;
		print "<table class=\"training\" width=100%><th colspan=3>Select Your Choice for Session $sessioncount<div class=\"button\" id=\"right\"><a href=\"register/\">Restart Training Signup</a></div></th>";
		$i=0;
		while ($session4=$sessions4->fetchRow()) {
			// Check if class is already full, then display list of classes
			$sessionsizesql=$db->query("SELECT count(*) AS size FROM conclave_attendance WHERE session_4=$session4->session_id AND conclave_year={$EVENT['year']}");
			$sessionsize=$sessionsizesql->fetchRow();
			if (($sessionsize->size<$session4->max_size) or ($session4->max_size=="")) {
				$i++;
				print "\n\t\t\t<tr id=\""; print $i%2==1 ? "odd" : "even"; print "\"><td>$session4->college_prefix$session4->course_number</td><td width=75%>".stripslashes($session4->session_name)."</td>
				<td width=\"50\"><form name=\"form1\" action=\"{$_GLOBALS['form_action']}\" method=\"{$_GLOBALS['form_method']}\">
				<input type=\"submit\" name=\"submitted\" id=\"submitted\" value=\"Add to My Schedule\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_1\" id=\"session_1\" value=\"$session_1\"><input type=\"hidden\" class=\"hidden\" name=\"s1code\" id=\"s1code\" value=\"$s1code\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_2\" id=\"session_2\" value=\"$session_2\"><input type=\"hidden\" class=\"hidden\" name=\"s2code\" id=\"s2code\" value=\"$s2code\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_3\" id=\"session_3\" value=\"$session_3\"><input type=\"hidden\" class=\"hidden\" name=\"s3code\" id=\"s3code\" value=\"$s3code\">
				<input type=\"hidden\" class=\"hidden\" name=\"session_4\" id=\"session_4\" value=\"$session4->session_id\"><input type=\"hidden\" class=\"hidden\" name=\"s4code\" id=\"s4code\" value=\"$session4->course_code\">
				<input type=\"hidden\" class=\"hidden\" name=\"firststepcomplete\" id=\"firststepcomplete\" value=\"true\">
				<input type=\"hidden\" class=\"hidden\" name=\"instructionsoff\" id=\"instructionsoff\" value=\"$instructionsoff\">
				<input type=\"hidden\" class=\"hidden\" name=\"sessioncount\" id=\"sessioncount\" value=\"$sessioncount\">
				<input type=\"hidden\" class=\"hidden\" name=\"secondstepcomplete\" id=\"secondstepcomplete\" value=\"true\">
				</form></td></tr>
				<tr id=\""; print $i%2==1 ? "odd" : "even"; print "\"><td></td><td id=\"description\" colspan=2>".nl2br(stripslashes($session4->description))."</td></tr>";
			}
		}
	}
	elseif ($secondstepcomplete) {}
	elseif ((!$sessions4->numRows()==0) and ($session_4=="")) $session_4=0;
	elseif ($EVENT['training_required_classes_for_degree']<4) $session_4=0;
	elseif ((($sessions4->numRows()==0) and (($session_4=="") or ($session_4==0))) and ($session_3!=0)) {
		print "<tr><td colspan=\"3\"><div class=\"error_summary\">All classes in your major are full for this session. You might try using this session for your
		elective or choosing a different major.  If you want to be able to take any available class during any session, you can start over and choose the
		$unrestricted->college_name as your major.<br><ul><li><a href=register/?goto=training>Start over with my training signup.</a></li></div></td></tr>";
	}
	elseif ($session_4=="") $session_4=4;

	print "</table>";

endif; 

// TRAINING STEP 3: Show the schedule and ask if it's okay.
if (($secondstepcomplete) and (!$savetraining)):

	print "<p>Your training schedule is complete!</p>
	
	<p>If you like your schedule, click the <b>Continue</b> button below to confirm and save your selections.</p>";

	// Print table of already-selected sessions
	if (($session_1!=1) or ($session_2!=2) or ($session_3!=3) or ($session_4!=4)) print "<table class=\"classschedule\"><th colspan=3>$member->firstname's Training Schedule<div class=\"button\" id=\"right\"><a href=\"register/\">Restart Training Signup</a></div></th>";
	if ($session_1!=1) {
		$s1q=$db->query("SELECT college_prefix, college, course_number, session_name, session_length FROM training_sessions, colleges WHERE college=college_id AND session_id=$session_1"); $s1=$s1q->fetchRow();
		$tablecount++; print "\n<tr valign=top><td width=70>Session $tablecount</td><td width=60>$s1->college_prefix$s1->course_number</td><td>".stripslashes($s1->session_name);
		if (($s1->college!=$degree->college_major) and ($degree->college_major!=$unrestricted->college_id)) print " <i>(Elective)</i>";
		print $s1->session_length>1 ? "<br><i>This session will last $s1->session_length hours on your actual schedule.</i>" : "";	print "</td></tr>"; }
	if ($session_2!=2) {
		$s2q=$db->query("SELECT college_prefix, college, course_number, session_name, session_length FROM training_sessions, colleges WHERE college=college_id AND session_id=$session_2"); $s2=$s2q->fetchRow();
		$tablecount++; print "\n<tr valign=top><td width=70>Session $tablecount</td><td width=60>$s2->college_prefix$s2->course_number</td><td>".stripslashes($s2->session_name);
		print $s2->college!=$degree->college_major && $degree->college_major!=$unrestricted->college_id ? " <i>(Elective)</i>" : ""; 
		print $s2->session_length>1 ? "<br><i>This session will last $s2->session_length hours on your actual schedule.</i></td></tr>" : ""; print "</td></tr>"; }
	if ($session_3!=3) {
		$s3q=$db->query("SELECT college_prefix, college, course_number, session_name, session_length FROM training_sessions, colleges WHERE college=college_id AND session_id=$session_3"); $s3=$s3q->fetchRow();
		$tablecount++; print "\n<tr valign=top><td width=70>Session $tablecount</td><td width=60>$s3->college_prefix$s3->course_number</td><td>".stripslashes($s3->session_name);
		print $s3->college!=$degree->college_major && $degree->college_major!=$unrestricted->college_id ? " <i>(Elective)</i>" : "";
		print $s3->session_length>1 ? "<br><i>This session will last $s3->session_length hours on your actual schedule.</i>" : "";	print "</td></tr>"; }
	if ($session_4!=4) {
		$s4q=$db->query("SELECT college_prefix, college, course_number, session_name, session_length FROM training_sessions, colleges WHERE college=college_id AND session_id=$session_4"); $s4=$s4q->fetchRow();
		$tablecount++; print "\n<tr valign=top><td width=70>Session $tablecount</td><td width=60>$s4->college_prefix$s4->course_number</td><td>".stripslashes($s4->session_name);
		print $s4->college!=$degree->college_major && $degree->college_major!=$unrestricted->college_id ? " <i>(Elective)</i>" : ""; print "</td></tr>"; }
	if (($session_1!=1) or ($session_2!=2) or ($session_3!=3) or ($session_4!=4)) print "\n</table>";
	
	print "\n\n<form name=\"form1\" action=\"{$_GLOBALS['form_action']}\" method=\"{$_GLOBALS['form_method']}\">
		<input type=\"hidden\" class=\"hidden\" name=\"session_1\" id=\"session_1\" value=\"$session_1\">
		<input type=\"hidden\" class=\"hidden\" name=\"session_2\" id=\"session_2\" value=\"$session_2\">
		<input type=\"hidden\" class=\"hidden\" name=\"session_3\" id=\"session_3\" value=\"$session_3\">
		<input type=\"hidden\" class=\"hidden\" name=\"session_4\" id=\"session_4\" value=\"$session_4\">
		<input type=\"hidden\" class=\"hidden\" name=\"firststepcomplete\" id=\"firststepcomplete\" value=\"true\">
		<input type=\"hidden\" class=\"hidden\" name=\"secondstepcomplete\" id=\"secondstepcomplete\" value=\"true\">
		<input type=\"hidden\" class=\"hidden\" name=\"savetraining\" id=\"savetraining\" value=\"true\">
		<input type=\"submit\" name=\"submit_button\" id=\"submit_button\" value=\"Save My Schedule & Continue >\">
	</form>";

endif;

// TRAINING STEP 4: Save training sessions to database and go to next registration step
if ($savetraining):

	// Save to DB
		$query="UPDATE conclave_attendance SET
				session_1=$session_1, session_2=$session_2, session_3=$session_3, session_4=$session_4
			WHERE member_id = {$_SESSION['member_id']} AND conclave_year = {$EVENT['year']}";
		$update = $db->query($query);
	if (DB::isError($update)) dbError("Could not save training information", $update, "");

	// Forward to next step in registration process
	gotoNextPage("training");

endif;

?>