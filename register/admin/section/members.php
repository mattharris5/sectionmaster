<?
$title = "Section > Members";
require "../pre.php";
$user->checkPermissions("members");

?>

<?

/**
 * Get Event Data
 */
	$EVENT 	 = event_info($sm_db, $user->info->current_event);
	
	
/**
 * Save Member Data
 */
// handle submitted form
if ($_REQUEST['submitted']) {
	$user->checkPermissions("members", 3);
	
	/*
	// Geocode the address if possible -- for use with Google Maps API
	define("MAPS_HOST", "maps.google.com");
	define("KEY", "ABQIAAAAWStFq6OKVbKkfaOkxjfA9BRYzDT7_cPe6K7A_GErk3FZjdJ54RTGCn3YAYLNURMLy9TU5NOhD83lTg");
	
	// Initialize delay in geocode speed
	$delay = 0;
	$base_url = "http://" . MAPS_HOST . "/maps/geo?output=csv&key=" . KEY;
	
	// Grab the geocode
	$address = $_REQUEST['address'] . " " . $_REQUEST['address2'] . ", " . $_REQUEST['city'] . ", " . $_REQUEST['state'];
	$id = $_REQUEST['member_id'];
	$request_url = $base_url . "&q=" . urlencode($address);
	$csv = file_get_contents($request_url) or die("url not loading");

	$csvSplit = split(",", $csv);
	$status = $csvSplit[0];
	$lat = $csvSplit[2];
	$lng = $csvSplit[3];
	if (strcmp($status, "200") == 0) {
	  // successful geocode
	  $lat = $csvSplit[2];
	  $lng = $csvSplit[3];
	}
	usleep($delay);
	*/

	// contact and health info
	$sql = "UPDATE members SET
				firstname = ". $db->quote($_REQUEST['firstname']) . ",
				lastname = ". $db->quote($_REQUEST['lastname']) . ",
				address = ". $db->quote($_REQUEST['address']) . ",
				address2 = ". $db->quote($_REQUEST['address2']) . ",
				city = ". $db->quote($_REQUEST['city']) . ",
				state = ". $db->quote($_REQUEST['state']) . ",
				zip = ". $db->quote($_REQUEST['zip']) . ",
				latitude = ". $db->quote($lat) . ",
				longitude = ". $db->quote($lng) . ",
				gender = " . $db->quote($_REQUEST['gender']) . ",
				primary_phone = ". $db->quote($_REQUEST['phone1'] . "-". $_REQUEST['phone2'] . "-". $_REQUEST['phone3']) . ",
				email = ". $db->quote($_REQUEST['email']) . ",
				oahonor = ". $db->quote($_REQUEST['oahonor']) . ",
				lodge = ". $db->quote($_REQUEST['lodge']) . ",
				chapter = ". $db->quote($_REQUEST['chapter']) . ",
				ordeal_date = ". $db->quote($_REQUEST['ordeal_year'] . "-". $_REQUEST['ordeal_month'] . "-01") . ",
				registered_unit_type = ". $db->quote($_REQUEST['registered_unit_type']) . ",
				registered_unit_number = ". $db->quote($_REQUEST['registered_unit_number']) . ",
				position = ". $db->quote($_REQUEST['position']) . ",
				custom1 = ". $db->quote($_REQUEST['custom1']) . ",
				custom2 = ". $db->quote($_REQUEST['custom2']) . ",
				custom3 = ". $db->quote($_REQUEST['custom3']) . ",
				birthdate = ". $db->quote($_REQUEST['bday_year'] . "-". $_REQUEST['bday_month'] . "-". $_REQUEST['bday_day']) . ",
				emer_contact_name = " . $db->quote($_REQUEST['emer_contact_name']) . ",
				emer_relationship = " . $db->quote($_REQUEST['emer_relationship']) . ",
				emer_phone_1 = " . $db->quote($_REQUEST['emer_phone_1_1'] . "-". $_REQUEST['emer_phone_1_2'] . "-". $_REQUEST['emer_phone_1_3']) . ",
				#insurance_company = " . $db->quote($_REQUEST['insurance_company']) . ",
				#policy_number = " . $db->quote($_REQUEST['policy_number']) . ",
				conditions_explanation = " . $db->quote($_REQUEST['conditions_explanation']) . ",
				special_diet_explain = " . $db->quote($_REQUEST['special_diet_explain']) . "
				#current_medications = " . $db->quote($_REQUEST['current_medications']) . ",
				#physical_limitations = " . $db->quote($_REQUEST['physical_limitations']) . ",
				#tetanus_month = " . $db->quote($_REQUEST['tetanus_month']) . ",
				#tetanus_year = " . $db->quote($_REQUEST['tetanus_year']);
				if (get('special_diet_explain') != '') { $sql .= "\n, special_diet = '1'"; }
				if (get('emer_phone_2_3') != '') { $sql .= "\n, emer_phone_2 = " . $db->quote($_REQUEST['emer_phone_2_1'] . "-". $_REQUEST['emer_phone_2_2'] . "-". $_REQUEST['emer_phone_2_3']) ; }
				foreach($_REQUEST['conditions'] as $cond => $val) { $sql .= "\n, $cond = " . $db->quote($val); }
			$sql .= " WHERE member_id = '{$_REQUEST['member_id']}' LIMIT 1";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not update member info in $title", $res, $sql);
		
	// training info
	$sql = "UPDATE conclave_attendance SET
				college_major = " . $db->quote($_REQUEST['college_major']) . ",
				college_degree = " . $db->quote($_REQUEST['college_degree']) . ",
				session_1 = " . $db->quote($_REQUEST['session_1']) . ",
				session_2 = " . $db->quote($_REQUEST['session_2']) . ",
				session_3 = " . $db->quote($_REQUEST['session_3']) . ",
				session_4 = " . $db->quote($_REQUEST['session_4']) . ",
				staff_class = " . $db->quote($_REQUEST['staff_class']) . ",
				check_in_date = " . $db->quote($_REQUEST['check_in_date']) . ",
				check_out_date = " . $db->quote($_REQUEST['check_out_date']) . ",
				housing_id = " . $db->quote($_REQUEST['housing_id']) . "
			WHERE member_id = {$_REQUEST['member_id']} AND conclave_year = {$EVENT['year']}
			LIMIT 1";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not update conclave_attendance info in $title", $res, $sql);

	// redirect back to event main page
	sessionMessage("Successfully saved member record for {$_REQUEST['firstname']} {$_REQUEST['lastname']}.", "success");
	header("Location:" . $_REQUEST['referrer']);
		
}



/**
 * Get Member Data
 */
	if (isset($_REQUEST['member_id'])) {
		$sql = "SELECT *, members.member_id as memid FROM `".$user->info->section."`.members
				LEFT JOIN `".$user->info->section."`.conclave_attendance USING (member_id)
				LEFT JOIN `".$user->info->section."`.chapters ON `".$user->info->section."`.members.chapter = `".$user->info->section."`.chapters.chapter_id
				LEFT JOIN `sm-online`.lodges ON `".$user->info->section."`.members.lodge = `sm-online`.lodges.lodge_id
				WHERE members.member_id = '{$_REQUEST['member_id']}'
				AND conclave_year = '{$EVENT['year']}'";
		// query to use if no conclave attendance record
		$sql2 = "SELECT *, members.member_id as memid FROM `".$user->info->section."`.members
				LEFT JOIN `".$user->info->section."`.chapters ON `".$user->info->section."`.members.chapter = `".$user->info->section."`.chapters.chapter_id
				LEFT JOIN `sm-online`.lodges ON `".$user->info->section."`.members.lodge = `sm-online`.lodges.lodge_id
				WHERE members.member_id = '{$_REQUEST['member_id']}'";
		$member = $user->db->getRow($sql);

		/**
		 * rob, comment your code. :-)
		 *  what is this?
		 */
		if (get('memid')!='')
		{
			$member = $user->db->getRow($sql);
			$attendance_record = true;
		}
		else
		{
			$member = $user->db->getRow($sql2);
			$attendance_record = false;
		}
			if (DB::isError($member)) dbError("Could not get member info in $title", $member, $sql);
			
		// Earned training degrees
		$sql = "SELECT * FROM conclave_attendance
				LEFT JOIN degrees ON degrees.degree_id=conclave_attendance.college_degree
				LEFT JOIN colleges ON colleges.college_id=conclave_attendance.college_major
				WHERE member_id='{$_REQUEST['member_id']}'
				AND registration_complete=1
				AND degree_reqts_complete=1
				ORDER BY conclave_year";
		$earned_res = $db->query($sql);
			if (DB::isError($earned_res)) { dbError("Could not retrieve list of earned degrees in $title", $earned_res, $sql); }
		
		// Order info
		$sql = "SELECT * FROM orders
				LEFT JOIN `sm-online`.events ON `sm-online`.events.id=orders.event_id
				WHERE member_id='{$_REQUEST['member_id']}'
				AND order_year={$EVENT['year']}
				AND complete=1
				ORDER BY order_timestamp";
		$order_res = $db->query($sql);
			if (DB::isError($order_res)) { dbError("Could not get order data in $title", $order_res, '$sql'); }
		$num_orders = $order_res->numrows();

	}

/**
 * Get dropdown values for lodge, chapter, and training classes, colleges, and degrees
 */
	// Lodge Data:
	$sql = "SELECT lodge_id, lodge_name, section
			FROM lodges
			WHERE section='{$EVENT['section_name']}'";
	$lodge_res = $sm_db->query($sql);
		if (DB::isError($lodge_res)) { dbError("Could not retreive lodge names in $title", $lodge_res, $sql); }
		
	// Lodge Data (Full Nation):
	$sql = "SELECT lodge_id, lodge_name, section
			FROM lodges
			ORDER BY section='{$EVENT['section_name']}' DESC,
					 LEFT(section, 1)='".substr($EVENT['section_name'], 0,1)."' DESC, 
					 section='' ASC, section ASC, lodge_name";
	$lodge_res_full = $sm_db->query($sql);
		if (DB::isError($lodge_res_full)) { dbError("Could not retreive lodge names in $title", $lodge_res_full, $sql); }


	// Chapter Data
	$sql = "SELECT chapter_id, chapter_name, lodge_id, lodge_name
				FROM chapters
				LEFT JOIN `sm-online`.lodges ON chapters.lodge=`sm-online`.lodges.lodge_id
				ORDER BY lodge_name, chapter_name ASC";
	$chapter_res = $db->query($sql);
		if (DB::isError($chapter_res)) { dbError("Could not retreive chapter names in $title", $chapter_res, $sql); }
		
	// Staff Classification Data
	$sql = "SELECT staff_class_id, staff_classification
			FROM staff_classifications
			WHERE deleted <> 1
			ORDER BY staff_classification";
	$staff_class_res = $db->query($sql);
		if (DB::isError($staff_class_res)) { dbError("Could not retreive staff classifications in $title", $staff_class_res, $sql); }
		
	// Training data
	$all_registered = 
		"SELECT *
		FROM members, orders, conclave_attendance
		LEFT JOIN `sm-online`.lodges ON `sm-online`.lodges.lodge_id = members.lodge
		LEFT JOIN chapters ON chapters.chapter_id = members.chapter
		WHERE members.member_id = conclave_attendance.member_id
			AND conclave_attendance.conclave_order_id = orders.order_id
			AND conclave_year = '".$EVENT['year']."'
			AND registration_complete = 1 ";
			
	//if ($EVENT['training_staff_college']!=0) $staff = "AND college<>".$EVENT['training_staff_college'];
			
	$all_registered = "
		SELECT 
		members.*,
		conclave_attendance.conclave_year,
		conclave_attendance.reg_date,
		conclave_attendance.check_in_date,
		conclave_attendance.check_out_date,
		conclave_attendance.staff_class,
		conclave_attendance.college_major,
		conclave_attendance.college_degree, 
		conclave_attendance.degree_reqts_complete,
		conclave_attendance.session_1,
		conclave_attendance.session_2,
		conclave_attendance.session_3,
		conclave_attendance.session_4,
		conclave_attendance.current_reg_step,
		conclave_attendance.conclave_order_id,
		conclave_attendance.registration_complete,
		conclave_attendance.housing_id,
		orders.order_id,
		orders.order_timestamp,
		orders.order_year,
		orders.event_id,
		orders.total_collected,
		orders.shipping,
		orders.shipping_method,
		orders.cc,
		orders.exp_month,
		orders.exp_year,
		orders.paid,
		orders.cc_processed,
		orders.order_method,
		orders.payment_method,
		orders.payment_notes,
		orders.complete,
		orders.deleted,
		`sm-online`.lodges.lodge_id,
		`sm-online`.lodges.lodge_name,
		`sm-online`.lodges.section,
		`sm-online`.lodges.council,
		chapters.chapter_id,
		chapters.chapter_name,
		chapters.active_membership,
		chapters.chapter_chief
		FROM 
		 members
		 INNER JOIN conclave_attendance
		            ON members.member_id = conclave_attendance.member_id
		        LEFT JOIN orders
		            ON conclave_attendance.conclave_order_id = orders.order_id 
		        LEFT JOIN `sm-online`.lodges
		            ON members.lodge = `sm-online`.lodges.lodge_id
		        LEFT JOIN chapters
		            ON members.chapter = chapters.chapter_id
		    WHERE 
		        conclave_attendance.conclave_year = '".$EVENT['year']."'
		        AND conclave_attendance.registration_complete = 1
	
	";
	
		for ($time=1; $time<=4; $time++)
		{
			$sql = "SELECT training_sessions.session_id, college, college_name, college_prefix, time_slot, training_times.time AS train_time, course_number, session_name, trainer_name, max_size, description,
						IFNULL(session_count, 0) AS session_count,
						IFNULL(IF(session_count >= max_size, 'FULL', max_size - session_count), max_size) AS remaining,
						max_size - session_count AS remaining_count
				FROM training_sessions
				LEFT JOIN (
						SELECT session_id, COUNT(session_id) AS session_count
						FROM training_sessions, ( $all_registered ) AS temp1
						WHERE (time = 1 AND session_1 = session_id)
							OR (time = 2 AND session_2 = session_id)
							OR (time = 3 AND session_3 = session_id)
							OR (time = 4 AND session_4 = session_id)
						GROUP BY session_id
				) AS temp2 USING (session_id)
				LEFT JOIN colleges ON colleges.college_id = training_sessions.college
				LEFT JOIN training_times ON training_times.time_id = training_sessions.time
				WHERE training_sessions.deleted<>1 $staff AND training_sessions.time=$time
				ORDER BY college, course_number, time_slot";
		
			if ($time==1) $sess[1] = $db->query($sql);
			//print "<pre>$sql</pre>";
			if ($time==2) $sess[2] = $db->query($sql);
			if ($time==3) $sess[3] = $db->query($sql);
			if ($time==4) $sess[4] = $db->query($sql);
		}
	
	// Training colleges
	$sql = "SELECT * FROM colleges WHERE deleted<>1 ORDER BY college_prefix";
	$colleges_res = $db->query($sql);
		if (DB::isError($colleges_res)) { dbError("Could not retrieve college names in $title", $colleges_res, $sql); }

	// Training degrees
	$sql = "SELECT * FROM degrees ORDER BY sequence";
	$degrees_res = $db->query($sql);
		if (DB::isError($degrees_res)) { dbError("Could not retreive degree names in $title", $degrees_res, $sql); }
		

/**
 * Setup JavaScript for the lodge-chapter dropdowns
 */
	print "
	<script language=\"JavaScript\">
		
		function updateChapters(form) {
		/*
			var chapterdd = form.chapter;
			var lodgedd   = form.lodge_local;
			
			chapterdd.options.length = 0; // Clear the menu

			if (lodgedd.value == \"\") {
				chapterdd.options[0] = new Option(\"-- Choose your Lodge first --\",\"\");
			";
			
			$opt[get('chapter')] = ", true";
			$rowid = 1;
			while($chapter = $chapter_res->fetchRow()) {
				if ($chapter->lodge != $currLodge) {
					print "\n\t\t\t} else if (lodgedd.value == \"$chapter->lodge\") {";
					print "\n\t\t\t\t chapterdd.options[0] = new Option(\"-- Select Your Chapter --\", \"\");";
				}
				print "\n\t\t\t\t chapterdd.options[$rowid] = new Option(\"$chapter->chapter_name\", \"$chapter->chapter_id\"". $opt[$chapter->chapter_id] .");";
				$currLodge = $chapter->lodge;
				$rowid++;
				if ($chapter->lodge = $currLodge)
					print "\n\t\t\t\t chapterdd.options[$rowid] = new Option(\"I dont know my chapter or district\", \"0\");";
			}
			
							
	print " } else {
				if (form.lodge_local.value == 'OUT')
					chapterdd.options[0] = new Option(\"Out of Section\", \"OUT\");
				else
					chapterdd.options[0] = new Option(\"(None)\", \"NONE\");
			}
		}
		
		var already_updated = false;
		function initialUpdateChapters(form) {
			
			if (already_updated == false) {
				updateChapters(form)
				already_updated = true
			}
		
		}*/
			
			
		function updateFullLodges(form) {
			if (form.lodge_local.value == 'OUT')
				form.lodge.style.visibility = \"visible\";
			else
				form.lodge.style.visibility = \"hidden\";
		}
			
			
		function updateScoutingInfoDiv(myVal, form) {
			myDiv = document.getElementById('ScoutingInfoDiv');
			if (myVal == 'Non-Member') {
				myDiv.style.display = 'none';
				myDiv.style.visibility = 'hidden';
				form.chapter.selectedIndex = 0;
				form.lodge.selectedIndex = 0;
				form.lodge_local.selectedIndex = 0;
				form.ordeal_month.selectedIndex = 0;
				form.ordeal_year.selectedIndex = 0;
			} else {
				myDiv.style.display = '';
				myDiv.style.visibility = 'visible';
			}
		}			
			
	</script>";
	
/**
 * Print the actual HTML form
 */
?>

<h1>Members</h1>

<? if ($_REQUEST['member_id'] === '') die("<p>You did not specify a valid Member ID number. Please try again.</p>"); ?>

<?
// If no member_id specified, show the "SEARCH" form
if (!isset($_REQUEST['member_id'])): ?>

	<fieldset><legend>Find a Member</legend>
	<form name="find_member" action="section/members.php" method=get>
	
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
	
		<input type=submit id="submit_button" value="View Member Record"><br />
	
	</form>
	</fieldset>

<? 
// Otherwise, print out all the details for the member.
else : ?>

<h2>Member Details for <?=$member->firstname?> <?=$member->lastname?> (ID #<?=$member->member_id?>)<br>
	<? if ($member->registration_complete=='') print "<font color=orange>".$EVENT['year']." Registration Not Started</font>";
		elseif ($member->registration_complete==0) print "<font color=red>".$EVENT['year']." Registration Incomplete: Last attempt on ".date('m/d/Y h:m A T', strtotime($member->reg_date))."</font>";
		elseif ($member->registration_complete==1) print "<font color=green>Registered for ".$EVENT['year']."</font>";
	?>
</h2>

<form name="members" action="section/members.php" method=post>
<div class="tabber">

	<!-- ------------------------------------------------ General Tab ------------------------------------------------------ -->
	<div class="tabbertab">
	<h2>General</h2>
	
	<fieldset><legend>Contact Information</legend>
		<!-- First Name -->
		<label for="firstname">First Name:</label>
				<input name="firstname" id="firstname" value="<? req('firstname') ?>">
				<? print $ERROR['firstname']; ?><br />

		<!-- Last Name -->
		<label for="lastname">Last Name:</label>
				<input name="lastname" id="lastname" value="<? req('lastname') ?>">
				<? print $ERROR['lastname']; ?><br />

		<!-- Mailing Address -->
		<? $mapurl = "http://maps.google.com/maps?f=q&hl=en&geocode=&q=".urlencode(get('address')." ".get('address2').", ".get('city').", ".get('state')." (".get('firstname')." ".get('lastname').")")."&sll=".get('latitude').",".get('longitude')."&ie=UTF8&z=16&iwloc=addr"; ?>
        
		<label for="address">Mailing Address (<a href="<?=$mapurl?>" target="_blank">map</a>):</label>
				<input name="address" id="address" value="<? req('address') ?>">
				<? print $ERROR['address']; ?><br />
		<label for="address2"> </label>
				<input name="address2" id="address2" value="<? req('address2') ?>"><br />

		<!-- City -->
		<label for="city">City:</label>
				<input name="city" id="city" value="<? req('city') ?>">
				<? print $ERROR['city']; ?><br />

		<!-- State -->
		<label for="state">State:</label>
				<select name="state" id="state">
					<option value="">-- Select your State --</option>
					<?
					$res = $sm_db->query("SELECT * FROM states");
					$opt[get('state')] = "selected";
					while($state = $res->fetchRow()) {
						print "<option value=\"$state->state_id\" {$opt[$state->state_id]}>$state->state_name</option>";
					}
					?>
				</select>
				<? print $ERROR['state']; ?><br />

		<!-- Zip Code -->
		<label for="zip">Zip Code:</label>
				<input name="zip" id="zip" value="<? req('zip') ?>">
				<? print $ERROR['zip']; ?><br />
        
        <!-- Lat/Long -->
		<label for="zip">Lat/Long:</label>
				(<a href="<?=$mapurl?>" target="_blank">map</a>)
				<? 
				if ((get('latitude') == "0.000000") or (get('longitude') == "0.000000")) print "none calculated yet";
				else
				{
					req('latitude');
					print ", ";
					req('longitude');
				}
				?>
                -- Save record to update geocode<br />

		<!-- Phone -->
		<label for="primary_phone">Phone:</label>
				<?
				$phone1 = get('phone1') ?  get('phone1') : substr(get('primary_phone'), 0, 3);
				$phone2 = get('phone2') ?  get('phone2') : substr(get('primary_phone'), 4, 3);
				$phone3 = get('phone3') ?  get('phone3') : substr(get('primary_phone'), 8, 4); ?>
				<font style="float: left;">(</font>
				<input name="phone1" id="phone1" maxlength=3 value="<?=$phone1?>" style="width:3em;"
					onfocus="if (this.value.length==3) {this.select();magic=false;}else{magic=true;}" 
					onkeypress="magic=true;" 
					onkeyup="if (this.value.length==3 && magic) {document.form1.phone2.focus();}">
				<font style="float: left;">) </font>
				<input name="phone2" id="phone2" value="<?=$phone2?>" maxlength=3 style="width:3em;"
					onfocus="if (this.value.length==3) {this.select();magic=false;}else{magic=true;}" 
					onkeypress="magic=true;" 
					onkeyup="if (this.value.length==3 && magic) {document.form1.phone3.focus();}">
				 <font style="float: left;"> - </font>
				 <input type="text" name="phone3" id="phone3" value="<?=$phone3?>" maxlength=4 style="width:4em;">		
				<? print $ERROR['phone3']; ?><br />

		<!-- E-mail Address -->
		<label for="email">E-mail Address:</label>
				<input name="email" id="email" value="<? req('email') ?>">
				<? print $ERROR['email']; ?><br />

		<!-- Birthday -->
		<label for="bday_month">Birthday:</label>
			<select name="bday_month" id="bday_month">
				<option value="">--</option>
				<?
				for($i=1; $i<=12; $i++) {
					print "\n\t\t<option value=\"$i\"";
					if (get('bday_month') == $i OR (get('birthdate') != '0000-00-00 00:00:00' && date("n", strtotime(get('birthdate'))) == $i)) {
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
					if (get('bday_day') == $i OR (get('birthdate') != '0000-00-00 00:00:00' && date("j", strtotime(get('birthdate'))) == $i)) {
						print " selected";
					}
					print ">$i</option>";
				} ?>
			</select>
			<select name="bday_year" id="bday_year" onchange="checkAge(this.form);">
				<option value="">--</option>
				<?
				for($i=(date("Y")-10); $i>=1915; $i--) {
					print "\n\t\t<option value=\"$i\"";
					if (get('bday_year') == $i or (get('birthdate') != '0000-00-00 00:00:00' && date("Y", strtotime(get('birthdate'))) == $i)) {
						print " selected";
					}
					print ">$i</option>";
				} ?>
			</select>
			<?=$ERROR['birthdate']?><br />
			<div id="ageCheck"></div>

		<!-- Gender -->
		<label for="gender">Gender:</label>
			<select name="gender" id="gender">
				<? $opt[get('gender')] = "selected"; ?>
				<option value="" <?=$opt['']?>>-- Select Gender --</option>
				<option value="M" <?=$opt['M']?>>Male</option>
				<option value="F" <?=$opt['F']?>>Female</option>
			</select>
			<?=$ERROR['gender']?><br />
		
		</fieldset>
		
		<fieldset><legend>Scouting Information</legend>
		
		<!-- oahonor -->
		<label for="oahonor">OA Honor:</label>
				<select name="oahonor" id="oahonor" onChange="updateScoutingInfoDiv(this.value, this.form);">
					<? $opt[get('oahonor')] = "selected"; ?>
					<option value="" <?=$opt['']?>>-- Select Honor --</option>
					<option value="Ordeal" <?=$opt['Ordeal']?>>Ordeal</option>
					<option value="Brotherhood" <?=$opt['Brotherhood']?>>Brotherhood</option>
					<option value="Vigil" <?=$opt['Vigil']?>>Vigil</option>
					<option value="Non-Member" <?=$opt['Non-Member']?>>
						I am not an OA Member</option>
				</select>
				<? print $ERROR['oahonor']; ?><br />

		<!-- lodge -->
		<label for="lodge">Lodge:</label>
				<select name="lodge" id="lodge">
					<option value="">-- Select your Lodge --</option>
					<optgroup label="Our Section (<?=$EVENT['section_name']?>)">
						<?					
						$currSection = $EVENT['section_name'];
						$opt[get('lodge_id')] = $dont_select ? "" : "selected";
						while($lodge = $lodge_res_full->fetchRow()) {
							if ($lodge->section != $currSection) {
								print "</optgroup><optgroup label=\"";
								print $lodge->section != '' ? "Section $lodge->section" : "Other";
								print "\">";
							}
							print "<option value=\"$lodge->lodge_id\" {$opt[$lodge->lodge_id]}>$lodge->lodge_name</option>";
							$currSection = $lodge->section;
						}
						?>
						<option value="0">N/A or Unknown</option>
					</select>

				<? print $ERROR['lodge']; ?><br />

		<!-- chapter -->
		<label for="chapter">Chapter:</label>
				<select name="chapter" id="chapter">
				<option value="">-- Select your Chapter --</option>
					<?
					$opt[get('chapter')] = "selected";
					$chapter_res->fetchRow('', 0);
					while($chapter = $chapter_res->fetchRow()) {
						if ($chapter->lodge_id != $current_lodgedd_id){
							print "</optgroup><optgroup label=\"$chapter->lodge_name\">";
							$current_lodgedd_id = $chapter->lodge_id;
						}
						print "<option value=\"$chapter->chapter_id\" {$opt[$chapter->chapter_id]}>$chapter->chapter_name</option>";
						$currLodge = $chapter->lodge;
					}
					?>
				</optgroup><optgroup><option value="0">N/A or Unknown</option></optgroup>
				</select>
				<? print $ERROR['chapter']; ?><br />

		<!-- ordeal_date -->
		<label for="ordeal_month">Ordeal Date:</label>
			<select name="ordeal_month" id="ordeal_month">
				<option value="">--</option>
				<?
				for($i=1; $i<=12; $i++) {
					print "\n\t\t<option value=\"$i\"";
					if (get('ordeal_month') == $i OR (get('ordeal_date') != '0000-00-00 00:00:00' && date("n", strtotime(get('ordeal_date'))) == $i)) {
						print " selected";
					}
					print ">" . date("F", mktime(0,0,0,$i,1,2000)) . "</option>";
				} ?>
			</select>
			<select name="ordeal_year" id="ordeal_year">
				<option value="">--</option>
				<?
				for($i=(date("Y")); $i>=1915; $i--) {
					print "\n\t\t<option value=\"$i\"";
					if (get('ordeal_year') == $i or (get('ordeal_date') != '0000-00-00 00:00:00' && date("Y", strtotime(get('ordeal_date'))) == $i)) {
						print " selected";
					}
					print ">$i</option>";
				} ?>
			</select>

			<? print $ERROR['ordeal_year']; ?><br />
			
		<script language="JavaScript">

			var tempNumber;  // global var to store unit number temporarily

			function checkUnitNumber(form) {
				// save the registered_unit_type registered_unit_number if one's been entered
				if ((form.registered_unit_number.value != "") && (form.registered_unit_number.value != "N/A")) {
					tempregistered_unit_number = form.registered_unit_number.value;
				} else if (form.registered_unit_number.value == "") {
					tempregistered_unit_number = "";
				}

				// change registered_unit_number to "N/A" if they choose district or atlarge
				if (form.registered_unit_type.value == "District" || form.registered_unit_type.value == "At Large") {
					form.registered_unit_number.value = "N/A";
					form.registered_unit_number.readOnly = true;
				} else {
					form.registered_unit_number.value = tempregistered_unit_number;
					form.registered_unit_number.readOnly = false;
				}
			}

		</script>

		<!-- Unit -->
		<label for="registered_unit_type">Registered Unit:</label>
			<select name="registered_unit_type" onchange="checkUnitNumber(this.form);">
				<? $checked[get('registered_unit_type')] = "selected"; ?>
				<option value="">-- Unit Type --</option>
				<option value="Troop" <?=$checked['Troop']?>>Troop</option>
				<option value="Team" <?=$checked['Team']?>>Team</option>
				<option value="Crew" <?=$checked['Crew']?>>Crew</option>
				<option value="Ship" <?=$checked['Ship']?>>Ship</option>
				<option value="Pack" <?=$checked['Pack']?>>Pack</option>
				<option value="Post" <?=$checked['Post']?>>Post</option>
				<option value="District" <?=$checked['District']?>>District</option>
				<option value="At Large" <?=$checked['At Large']?>>At Large</option>
				<option value="Other" <?=$checked['Other']?>>Other</option>
			</select>
			<? print $ERROR['registered_unit_type']; ?>

			<input name="registered_unit_number" id="registered_unit_number" value="<? req('registered_unit_number') ?>" style="width: 5em;">
			<? print $ERROR['registered_unit_number']; ?><br />

		<!-- Current OA Position -->
		<label for="position">Current OA Position (if any):</label>
				<input name="position" id="position" value="<? req('position') ?>">
				<? print $ERROR['position']; ?><br />
				
		</fieldset>
				
				<!-- Custom Fields -->
		<? if ($EVENT['custom1_label'] != '' || $EVENT['custom2_label'] != '' || $EVENT['custom3_label'] != ''): ?>
			<fieldset><legend>Other Information</legend>
			<? for ($i=1; $i<=3; $i++) {
				if ($EVENT["custom{$i}_label"] != ''): ?>
				<label for="custom<?=$i?>"><?=$EVENT["custom{$i}_label"]?>:</label>
					<input name="custom<?=$i?>" id="custom<?=$i?>" value="<? req("custom{$i}") ?>">
					<? print $ERROR["custom{$i}"]; ?><br />
				<?
				endif;
			} ?>
			</fieldset>
		<? endif; ?>

		<? if ($member->registration_complete!=''): ?>
		<fieldset>
			<legend><?=$EVENT['year'] . " " . $EVENT['casual_event_name']?></legend>
			
			<label for="staff_class">Staff Classification:</label>
				<select name="staff_class" id="staff_class">
					<option value="">-- Select Staff Class --</option>
					<?
					$opt[get('staff_class')] = "selected";
					while($staff_class = $staff_class_res->fetchRow()) {
						print "<option value=\"$staff_class->staff_class_id\" {$opt[$staff_class->staff_class_id]}>$staff_class->staff_classification</option>";
					}
					?>
				</select>
				<? print $ERROR['staff_class']; ?><br />		
				<? if ($EVENT['start_date'] <= date('Y-m-d H:i:s')): ?>
				<label for="check_in_date">Check-in Date:</label><input name="check_in_date" id="check_in_date" value="<? req('check_in_date') ?>">
				<? print $ERROR['check_in_date']; ?><br>
				
				<label for="check_out_date">Check-out Date:</label><input name="check_out_date" id="check_out_date" value="<? req('check_out_date') ?>">
				<? print $ERROR['check_out_date']; ?>
				
				<? endif; ?>
		</fieldset>
		<? endif; ?>

	</div>
	
	<!-- ------------------------------------------------ Medical Tab ------------------------------------------------------ -->
	<div class="tabbertab">
	<h2>Medical</h2>
	
	<fieldset><legend>Emergency Information</legend>
		<!-- Emergency Contact Name -->
			<label for="emer_contact_name">In the event of emergency, notify:</label>
				<input name="emer_contact_name" id="emer_contact_name" value="<? req('emer_contact_name') ?>">

		<!-- Emergency Contact Relationship -->
				<select name="emer_relationship" id="emer_relationship">
					<? $opt[get('emer_relationship')] = "selected"; ?>
					<option value="" <?=$opt['']?>>-- Relationship --</option>
					<option value="parent" <?=$opt['parent']?>>Parent</option>
					<option value="guardian" <?=$opt['guardian']?>>Guardian</option>
					<option value="friend" <?=$opt['friend']?>>Friend</option>
					<option value="spouse" <?=$opt['spouse']?>>Spouse</option>
					<option value="other" <?=$opt['other']?>>Other</option>
				</select>
				<?=$ERROR['emer_contact_name']?><br />

		<!-- Phone 1 -->
			<label for="emer_phone_1_1">Phone Number:</label>
				<?
				$emer_phone_1_1 = get('emer_phone_1_1') ?  get('emer_phone_1_1') : substr(get('emer_phone_1'), 0, 3);
				$emer_phone_1_2 = get('emer_phone_1_2') ?  get('emer_phone_1_2') : substr(get('emer_phone_1'), 4, 3);
				$emer_phone_1_3 = get('emer_phone_1_3') ?  get('emer_phone_1_3') : substr(get('emer_phone_1'), 8, 4); ?>
				<font style="float: left;">(</font>
				<input name="emer_phone_1_1" id="emer_phone_1_1" maxlength=3 value="<?=$emer_phone_1_1?>" style="width:3em;"
					onfocus="if (this.value.length==3) {this.select();magic=false;}else{magic=true;}" 
					onkeypress="magic=true;" 
					onkeyup="if (this.value.length==3 && magic) {document.form1.emer_phone_1_2.focus();}">
				<font style="float: left;">) </font>
				<input name="emer_phone_1_2" id="emer_phone_1_2" value="<?=$emer_phone_1_2?>" maxlength=3 style="width:3em;"
					onfocus="if (this.value.length==3) {this.select();magic=false;}else{magic=true;}" 
					onkeypress="magic=true;" 
					onkeyup="if (this.value.length==3 && magic) {document.form1.emer_phone_1_3.focus();}">
				 <font style="float: left;"> - </font>
				 <input type="text" name="emer_phone_1_3" id="emer_phone_1_3" value="<?=$emer_phone_1_3?>" maxlength=4 style="width:4em;">		
				<? print $ERROR['emer_phone_1_3']; ?><br />

		<!-- Phone 2 -->
			<label for="emer_phone_1_1">Alternate Phone Number:</label>
				<?
				$emer_phone_2_1 = get('emer_phone_2_1') ?  get('emer_phone_2_1') : substr(get('emer_phone_2'), 0, 3);
				$emer_phone_2_2 = get('emer_phone_2_2') ?  get('emer_phone_2_2') : substr(get('emer_phone_2'), 4, 3);
				$emer_phone_2_3 = get('emer_phone_2_3') ?  get('emer_phone_2_3') : substr(get('emer_phone_2'), 8, 4); ?>
				<font style="float: left;">(</font>
				<input name="emer_phone_2_1" id="emer_phone_2_1" maxlength=3 value="<?=$emer_phone_2_1?>" style="width:3em;"
					onfocus="if (this.value.length==3) {this.select();magic=false;}else{magic=true;}" 
					onkeypress="magic=true;" 
					onkeyup="if (this.value.length==3 && magic) {document.form1.emer_phone_2_2.focus();}">
				<font style="float: left;">) </font>
				<input name="emer_phone_2_2" id="emer_phone_2_2" value="<?=$emer_phone_2_2?>" maxlength=3 style="width:3em;"
					onfocus="if (this.value.length==3) {this.select();magic=false;}else{magic=true;}" 
					onkeypress="magic=true;" 
					onkeyup="if (this.value.length==3 && magic) {document.form1.emer_phone_2_3.focus();}">
				 <font style="float: left;"> - </font>
				 <input type="text" name="emer_phone_2_3" id="emer_phone_2_3" value="<?=$emer_phone_2_3?>" maxlength=4 style="width:4em;">		
				<? print $ERROR['emer_phone_2_3']; ?><br />

		<!-- Insurance Company -->
			<!--label for="insurance_company">Health Insurance Company:</label>
					<input name="insurance_company" id="insurance_company" value="<? req('insurance_company') ?>"><br /-->
		
		<!-- Policy Number -->
			<!--label for="policy_number">Policy Number:</label>
					<input name="policy_number" id="policy_number" value="<? req('policy_number') ?>"><br /-->
								
	</fieldset>
	
	<fieldset><legend>Medical Conditions</legend>
						
		<!-- Medical Conditions -->
		<label class="spanned">Are you subject to, or have a history of any of the following conditions:</label><br>
			
			<?
			function checked($arg) {
				global $member;
				if (($member->$arg == 1) OR ($_REQUEST['conditions'][$arg] == 1)) return "checked";
			}
			?>

			<!-- include all the checkboxes as hidden so unchecked boxes register properly -->
			<!--input type="hidden" class="hidden" name="conditions[asthma]" value="0">
			<input type="hidden" class="hidden" name="conditions[diabetes]" value="0">
			<input type="hidden" class="hidden" name="conditions[heart_condition]" value="0">
			<input type="hidden" class="hidden" name="conditions[bleeding_disorder]" value="0">
			<input type="hidden" class="hidden" name="conditions[attention_deficit]" value="0">
			<input type="hidden" class="hidden" name="conditions[fainting_spells]" value="0">
			<input type="hidden" class="hidden" name="conditions[depression]" value="0">
			<input type="hidden" class="hidden" name="conditions[bed_wetting]" value="0"-->
			<input type="hidden" class="hidden" name="conditions[special_care]" value="0">
			<input type="hidden" class="hidden" name="conditions[sleeping_device]" value="0">
			<input type="hidden" class="hidden" name="conditions[food_allergy]" value="0">
			<!--input type="hidden" class="hidden" name="conditions[plant_allergy]" value="0">
			<input type="hidden" class="hidden" name="conditions[animal_allergy]" value="0">
			<input type="hidden" class="hidden" name="conditions[insect_allergy]" value="0">
			<input type="hidden" class="hidden" name="conditions[toxin_allergy]" value="0">
			<input type="hidden" class="hidden" name="conditions[medication_allergy]" value="0"-->


			<div class="checkboxes">

				<!--span><input type="checkbox" name="conditions[asthma]" value="1" <?=checked("asthma")?>>Asthma</span>
				<span><input type="checkbox" name="conditions[diabetes]" value="1" <?=checked("diabetes")?>>Diabetes</span>
				<span><input type="checkbox" name="conditions[heart_condition]" value="1" <?=checked("heart_condition")?>>Heart Condition</span>
				
			<br><span><input type="checkbox" name="conditions[bleeding_disorder]" value="1" <?=checked("bleeding_disorder")?>>Bleeding Disorder</span>
				<span><input type="checkbox" name="conditions[attention_deficit]" value="1" <?=checked("attention_deficit")?>>ADD/ADHD</span>
				<span><input type="checkbox" name="conditions[fainting_spells]" value="1" <?=checked("fainting_spells")?>>Fainting Spells</span>

			<br><span><input type="checkbox" name="conditions[depression]" value="1" <?=checked("depression")?>>Depression</span>
				<span><input type="checkbox" name="conditions[bed_wetting]" value="1" <?=checked("bed_wetting")?>>Bed Wetting</span>
				
			<br--><span><input type="checkbox" name="conditions[special_care]" value="1" <?=checked("special_care")?>>I require special care or special facilities</span>
			
			<br><span><input type="checkbox" name="conditions[sleeping_device]" value="1" <?=checked("sleeping_device")?>>I use a sleeping device that requires power (such as a CPAP)</span>
				
			</div><br>

		<!-- Allergies -->
		<label class="spanned">Are you allergic to any of the following:</label><br>
			
			<div class="checkboxes">
			
				<span><input type="checkbox" name="conditions[food_allergy]" value="1" <?=checked("food_allergy")?>>Food</span>
				<!--span><input type="checkbox" name="conditions[plant_allergy]" value="1" <?=checked("plant_allergy")?>>Plant</span>
				<span><input type="checkbox" name="conditions[animal_allergy]" value="1" <?=checked("animal_allergy")?>>Animal</span>
				
			<br><span><input type="checkbox" name="conditions[insect_allergy]" value="1" <?=checked("insect_allergy")?>>Insect</span>
				<span><input type="checkbox" name="conditions[toxin_allergy]" value="1" <?=checked("toxin_allergy")?>>Toxin</span>
				<span><input type="checkbox" name="conditions[medication_allergy]" value="1" <?=checked("medication_allergy")?>>Medication</span-->
				
			</div><br>

			
		<!-- Conditions Explanations -->	
		<label for="conditions_explanation">Explanation of above conditions or allergies:</label>
			<input name="conditions_explanation" id="conditions_explanation" value="<?=get('conditions_explanation')?>" style="width: 300px;"><br>
				
		<!-- Special Diet -->
		<!--<label for="special_diet_dd">Dietary Needs:</label>
			<select name="special_diet_dd" id="special_diet_dd" onChange="toggleSpecialDiet(this.value);">
				<option value="">None</option>
				<option value="Vegetarian">Vegetarian</option>
				<option value="Vegan">Vegan</option>
				<option value="Food Allergy">Food Allergy</option>
			</select>-->
			<!--<div style="display: inline; float:left;" id="special_diet_explain_div"></div><br>-->
			
		<label for="special_diet_explain">Special Diet Details: </label>
			<input name="special_diet_explain" id="special_diet_explain" value="<?=get('special_diet_explain')?>" style="width: 300px;"><br>
		
		<!--label for="current_medications">Current Medications: </label>
			<input name="current_medications" id="current_medications" value="<?=get('current_medications')?>" style="width: 300px;"><br>
		
		<label for="physical_limitations">Physical Limitations: </label>
			<input name="physical_limitations" id="physical_limitations" value="<?=get('physical_limitations')?>" style="width: 300px;"><br>
			
		<label for="tetanus_month">Date of Last Tetanus Toxoid Immunization:</label>
			<select name="tetanus_month" id="tetanus_month">
				<option value="">-- Month --</option>
				<? for ($m=1; $m<=12; $m++) {
					if (get('tetanus_month') == $m) $optm[$m] = "selected";
					print "\n\t\t\t <option value=\"$m\" {$optm[$m]}>" . date("F", mktime(0,0,0,$m,1,2000)) . "</option>";
				} ?>
			</select>
			
			<select name="tetanus_year" id="tetanus_year">
				<option value="">-- Year --</option>
				<? for ($y=date("Y"); $y>date("Y") - 20; $y--) {
					if (get('tetanus_year') == $y) $opty[$y] = "selected";
					print "\n\t\t\t <option value=\"$y\" {$opty[$y]}>$y</option>";
				} ?>
			</select><br-->		

	</fieldset>

	<!--fieldset><legend>Authorization for Participation and Medical Care</legend-->
		

		
		<!-- Digital Consent -->
		<!--br><label for="digital_consent_signature"><? if(getAge($member)<18) print "Parent/Guardian "; ?>Full Legal Name: </label>
			 	<? print get('digital_consent_signature');
				?>
			<?=$ERROR['digital_consent_signature']?>

		<br>

		<?
		if ($EVENT['separate_talent_release_signature'] == 1):
		if (getAge($member) < 18):  // YOUTH STATEMENT ?>		

			<?=$talent_release_text;?>
			<p><b>I understand that by typing my full legal name in the space provided below, I am affirming that I am the legal Parent or Guardian of <?=getFullName($member)?> and that I agree to the talent release statement written above.</b></p>

		<? else:  // ADULT STATEMENT ?>
			
			<?=$talent_release_text;?>
			<p><b>I understand that by typing my full legal name in the space provided below, I am affirming that I am at least 18 years of age and that I agree to the talent release statement written above.</b></p-->

		<? endif; ?>

		<!-- Talent Release -->
		<!--br><label for="talent_release_signature"><? if(getAge($member)<18) print "Parent/Guardian "; ?>Full Legal Name: </label>
			<?
			print get('talent_release_signature');
			?>
			<?=$ERROR['talent_release_signature']?>

		<? endif; ?>
		
		<!--Signature date -->
		<!--br><label for="digital_consent_timestamp">Date: </label><?=get('digital_consent_timestamp')?>

	!-->
	</div>
	
		<? if ($EVENT['do_training']!=0): ?>
		
		<!-- ------------------------------------------------ Training Tab ------------------------------------------------------ -->
		<div class="tabbertab">
		<h2>Training</h2>

			<? if ($attendance_record): ?>

			<fieldset><legend><?=$EVENT['year']?> Training Registration</legend>
			<!-- major -->
			<label for="college_major">College Major:</label>
					<select name="college_major" id="college_major">
					<option value="">-- Select College Major --</option>
						<?
						$optcollege[get('college_major')] = "selected";
						while($college = $colleges_res->fetchRow()) {
							print "<option value=\"$college->college_id\" {$optcollege[$college->college_id]}>$college->college_prefix: $college->college_name</option>";
						}
						?>
					</select>
					<? print $ERROR['college_major']; ?><br />
	
			<!-- degree -->
			<label for="college_degree">Degree in Progress:</label>
					<select name="college_degree" id="college_degree">
					<option value="">-- Select Degree in Progress --</option>
						<?
						$optdegree[get('college_degree')] = "selected";
						while($degree = $degrees_res->fetchRow()) {
							print "<option value=\"$degree->degree_id\" {$optdegree[$degree->degree_id]}>$degree->degree_name</option>";
						}
						?>
					</select>
					<? print $ERROR['college_degree']; ?><br />
					
			<!-- sessions -->
			
			<?
			for ($x=1; $x<=4; $x++) {
			
			/*print "<pre>";
			print "session $x: ".get('session_'.$x);
			print "</pre>";*/
			print "
				<label for=\"session_$x\">Session $x:</label>
					<select name=\"session_$x\" id=\"session_$x\" style=\"width: 300px;\">
					<option value=\"\">-- Select Session $x --</option>";
						$opttraining[get('session_'.$x)] = "selected";
						while($session = $sess[$x]->fetchRow()) {
							if ($session->college != $currCollege) {
								print "</optgroup><optgroup label=\"";
								print $session->college_name;
								print "\">";
							}
							print "\n<option value=\"$session->session_id\" {$opttraining[$session->session_id]}>$session->college_prefix$session->course_number$session->time_slot: $session->session_name";
								if ($session->remaining!='FULL') print " ($session->remaining slots open)";
								else print " <font color=red>(FULL)</font>";
							print "</option>";
							$currCollege = $session->college;
						}
					print "</select><br />";
			} ?>
		</fieldset>
		<? endif; //if attendance_record=true ?>
		
		<fieldset><legend>Earned Training Degree(s)</legend>
		<ul>
		<?
			while ($earned = $earned_res->fetchRow())
			{
				print "<li>$earned->conclave_year: $earned->degree_name Degree, $earned->college_name</li>";
			}
		?>
		</ul>
		</fieldset>
		</div>
		
	<? endif; //if do_training=true ?>
	
	<!-- ------------------------------------------------ Housing Tab ----------------------------------------------------- -->
	
	<? if ($num_orders!=0): ?>
	<div class="tabbertab">
		<h2>Housing</h2>
		<fieldset>
			<legend>Housing Assignment</legend>
			<label>Assigned Location</label>
			<select name="housing_id">
				<option>-- Select Location --</option>
<?
$sql = "SELECT housing.*, COUNT(c.housing_id) AS housing_assigned
FROM housing
LEFT JOIN (SELECT * FROM conclave_attendance WHERE registration_complete=1 AND conclave_year='{$EVENT['year']}') c USING (housing_id)
GROUP BY housing_name
ORDER BY housing_name";
$locations = $db->query($sql);
	if (DB::isError($locations)) dbError("Could not get locations listing in $title", $locations, $sql);
	while ($location = $locations->fetchRow())
	{
		print "<option value=\"$location->housing_id\"";
		if (get('housing_id') == $location->housing_id) print " selected";
		print ">";
		if ($location->housing_assigned == $location->housing_capacity) print "FULL - ";
		elseif ($location->housing_assigned >= $location->housing_capacity) print "**OVERFULL** - ";
		print "$location->housing_name - $location->housing_description ($location->housing_assigned/$location->housing_capacity assigned";
		if (get('housing_id') == $location->housing_id) print " including $member->firstname $member->lastname";
		print ")</option>";
	}
?>
			</select>
		</fieldset>
		
	</div>
	
	<!-- ------------------------------------------------ Orders Tab ------------------------------------------------------ -->
	
	<div class="tabbertab">
		<h2><?=$EVENT['year']?> Orders</h2>
		
		<? while ($order = $order_res->fetchRow()): ?>
		
		<fieldset><legend><?=$order->casual_event_name." Order ".$EVENT['section_name']?>/<?=str_pad($EVENT['id'], 3, '0', STR_PAD_LEFT)?>-<?=str_pad($order->order_id, 7, '0', STR_PAD_LEFT)?></legend>
	
				<label>Date/Time</label><?=$order->order_timestamp?><br>
				
				<label>Payment Info</label>
					<? if ($order->payment_method == 'cc'): ?>
						Credit Card <?=$order->cc?>
							-- Total Collected: $<?=number_format($order->total_collected, 2)?>
					<? elseif ($order->payment_method == 'cash'): ?>
						Paid - Cash
							-- Total Collected: $<?=number_format($order->total_collected, 2)?>
					<? elseif ($order->payment_method == 'check'): ?>
						Paid - Check
							-- Total Collected: $<?=number_format($order->total_collected, 2)?>
					<? elseif ($order->payment_method == 'other'): ?>
						Paid - Other
							-- Total Collected: $<?=number_format($order->total_collected, 2)?>
					<? elseif ($order->payment_method == 'free'): ?>
						Paid - Free Order
							-- Total Collected: $<?=number_format($order->total_collected, 2)?>
					<? elseif ($order->payment_method == 'custom1'): ?>
						<?=$EVENT['custom_payment_method1']?>
							-- <font color=red>Total Due: $<?=number_format($order->total_collected, 2)?></font>
					<? else: ?>
						Pay-at-Door
							-- <font color=red>Pay when you arrive at the event.</font>
					<? endif; ?>
				<br>
				
				<?=($order->payment_method == 'cc')?"<label>Transaction ID</label> $order->transaction_id<br>":""?>
				
				<table>
				
				<tr><th colspan=4>Ordered Items:</th></tr>

				<?
				$sql = "SELECT ordered_item_id, products.product_id, item, description,
							   product_options.product_option_id, quantity, ordered_items.price,
							   option_name, option_value, image_thumbnail, show_in_store, coupon
						FROM ordered_items
						LEFT JOIN products ON ordered_items.product_id = products.product_id
						LEFT JOIN product_options ON ordered_items.product_option_id = product_options.product_option_id
						WHERE order_id = '$order->order_id'
						ORDER BY coupon ASC, show_in_store ASC";
				$ordered_items = $db->query($sql);
					if (DB::isError($ordered_items)) { dbError("Couldn't get ordered item product info in $title", $ordered_items, $sql); }
					
				// show a row for each item
				while ($item = $ordered_items->fetchRow()) { $i++;
				
					// <tr>
					print "<tr>";
					
					// item & description
					print "<td id=\"description\"><font id=\"item\">$item->item</font>";
					if ($item->option_name != '')
						print "<li>$item->option_name: $item->option_value</li>";
					print "</font></td>";
							   
					// quantity
					print "<td id=\"quantity\">$item->quantity&nbsp;@</td>";
					print "<td align=right>" . number_format($item->price, 2) . "</td>";
					
					// item total
					$item_total = $item->quantity * $item->price;
					print "<td id=\"item_total\" align=right>$" . number_format($item_total, 2) . "</td>";
					
					// add to subtotal
					$subtotal = $subtotal + $item_total;
					
				}
				?>
	
				<tr id="subtotal">
					<td colspan=3 align=right>Subtotal</td>
					<td align=right>$<?=number_format($subtotal, 2)?></td></tr>
					
				<tr id="shipping">
					<td colspan=3 align=right>Shipping</td>
					<td align=right>$<?=number_format($order->shipping, 2)?></td></tr>
					
				<tr id="total" align=right><td colspan=3><b>Total&nbsp;<? print $order->paid!='1' ? "Due" : "Paid"; ?></b></td>
					<td align=right><b><?	
						$total_cost = $subtotal + $order->shipping;
						$total_cost = number_format((($total_cost < 0) ? 0 : $total_cost), 2);
						$subtotal = 0; ?>
						$<?=$total_cost?></b></td></tr>
						
				</table>
	
		</fieldset>

		
		<? 
		endwhile; ?>
	
	</div>
	
	<? endif; ?>



<!-- /tabber -->
</div>


<center>
	<input type=submit id="submit_button" name="submitted" value="Save Member Record" class="default">
</center>

<input type=hidden name="member_id" value="<?= $_REQUEST['member_id'] ?>">
<input type=hidden name="referrer" value="<?= $_SERVER["HTTP_REFERER"] ?>">

</form>

<script>
	initialUpdateChapters(document.forms.members);
</script>

<?
endif;

require "../post.php";
?>