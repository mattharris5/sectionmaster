<? 
/*	SM-Online Registration System                      www.sectionmaster.org
	------------------------------------------------------------------------

	CONTACT-INFO PAGE (html/contact-info.php)

	------------------------------------------------------------------------										*/

	# Validate Data
		validate("firstname","","Please enter your first name.");
		validate("lastname","","Please enter your last name.");
		validate("address","","Please enter your address.");
		validate("city","","Please enter your city.");
		validate("state", "", "Please enter your state.");
		validate("zip","","Please enter your Zip Code.");
		validate("phone3","","Please enter your phone number.");
		validate("oahonor","","Please enter your OA Honor.");
		validate("birthdate","validBirthdate()","Please enter your birthdate.");
		validate("gender", "", "Please identify your gender.");
		if (get('oahonor') != 'Non-Member') {
			validate("chapter","","Please enter your Chapter.");
			if ($_SESSION['type'] != 'paper') {
				validate("registered_unit_type", "", "Please choose your unit type.");
				validate("registered_unit_number", "", "Please enter your unit number.");
				validate("ordeal_year","","Please enter your approximate Ordeal date.");
			}
       }
	# Post-Validate
		if (postValidate()) {
				/*
				// Geocode the address if possible -- for use with Google Maps API
				define("MAPS_HOST", "maps.google.com");
				define("KEY", "ABQIAAAAWStFq6OKVbKkfaOkxjfA9BRYzDT7_cPe6K7A_GErk3FZjdJ54RTGCn3YAYLNURMLy9TU5NOhD83lTg");
				
				// Initialize delay in geocode speed
				$delay = 0;
				$base_url = "http://" . MAPS_HOST . "/maps/geo?output=csv&key=" . KEY;
				
				// Grab the geocode
				$address = get('address') . " " . get('address2') . ", " . get('city') . ", " . get('state');
				$id = $_SESSION['member_id'];
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
			
			$update = $db->query("UPDATE members SET
									firstname = '". addslashes(get('firstname')) . "',
									lastname = '". addslashes(get('lastname')) . "',
									address = '". addslashes(get('address')) . "',
									address2 = '". addslashes(get('address2')) . "',
									city = '". addslashes(get('city')) . "',
									state = '". addslashes(get('state')) . "',
									zip = '". addslashes(get('zip')) . "',
									latitude = '". addslashes($lat) . "',
									longitude = '". addslashes($lng) . "',
									gender = '" . get('gender') . "',
									primary_phone = '". addslashes(get('phone1') . "-". get('phone2') . "-". get('phone3')) . "',
									email = '". addslashes(get('email')) . "',
									oahonor = '". addslashes(get('oahonor')) . "',
									lodge = '". addslashes(get('lodge')) . "',
									chapter = '". addslashes(get('chapter')) . "',
									ordeal_date = '". addslashes(get('ordeal_year') . "-". get('ordeal_month') . "-01") . "',
									registered_unit_type = '". addslashes(get('registered_unit_type')) . "',
									registered_unit_number = '". addslashes(get('registered_unit_number')) . "',
									position = '". addslashes(get('position')) . "',
									custom1 = '". addslashes(get('custom1')) . "',
									custom2 = '". addslashes(get('custom2')) . "',
									custom3 = '". addslashes(get('custom3')) . "',
									birthdate = '". addslashes(get('bday_year') . "-". get('bday_month') . "-". get('bday_day')) . "'
								  WHERE member_id = {$_SESSION['member_id']}");
								  
			if (DB::isError($update)) { dbError("Could not save member information", $update, ""); }

			gotoNextPage("contact-info");
		}

	# Get dropdown values
		// Lodge Data:
		$sql = "SELECT lodge_id, lodge_name, section
				FROM lodges
				WHERE section='{$EVENT['section_name']}'
				ORDER BY lodge_name";
		$lodge_res = $sm_db->query($sql);
			if (DB::isError($lodge_res)) { dbError("Could not retreive lodge names", $lodge_res, $sql); }
			
		// Lodge Data (Full Nation):
		$sql = "SELECT lodge_id, lodge_name, section
				FROM lodges
				ORDER BY section='{$EVENT['section_name']}' DESC,
						 LEFT(section, 1)='".substr($EVENT['section_name'], 0,1)."' DESC, 
						 section='' ASC, section ASC, lodge_name";
		$lodge_res_full = $sm_db->query($sql);
			if (DB::isError($lodge_res_full)) { dbError("Could not retreive lodge names", $lodge_res_full, $sql); }


		// Chapter Data
		$sql = "SELECT chapter_id, chapter_name, lodge
				FROM chapters
				WHERE deleted<>1
				ORDER BY lodge, chapter_name asc";
		$chapter_res = $db->query($sql);
			if (DB::isError($chapter_res)) { dbError("Could not retreive chapter names", $chapter_res, $sql); }
		
	# Javascript functionality
		print "
		<script language=\"JavaScript\">
			
			function updateChapters(form) {
			
				var chapterdd = form.chapter;
				var lodgedd   = form.lodge_local;
				
				chapterdd.options.length = 0; // Clear the menu

				if (lodgedd.value == \"\") {
					chapterdd.options[0] = new Option(\"-- Choose your Lodge first --\",\"\");
				";
				
				$opt[get('chapter')] = ", true";
				while($chapter = $chapter_res->fetchRow()) {
					if ($chapter->lodge != $currLodge) {
						print "\n\t\t\t} else if (lodgedd.value == \"$chapter->lodge\") {";
						print "\n\t\t\t\t chapterdd.options[0] = new Option(\"-- Select Your Chapter --\", \"\");";
						$rowid = 1;
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
			
			}
				
				
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
?>

<h2>Contact and Scouting Information</h2>

	<p>Please enter your contact information below.  This information is used to identify you in our database, and the information may be used to occasionally send you information about <? print $EVENT['casual_event_name']; ?>.  All fields marked with a <? print $ERROR['sample'] ?> are required.  When you're finished, click the "Continue" button.</p>

	<? print $ERROR['error_summary']; ?>
	
	<center>
	<form name="form1" action="<? print $_GLOBALS['form_action']; ?>" method="<? print $_GLOBALS['form_method']; ?>" onmouseover="initialUpdateChapters(this);">


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
		<label for="address">Mailing Address:</label>
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
			<?=help_link('birthdate')?>
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
				
				
		<div id="ScoutingInfoDiv">

		<!-- lodge -->
		<label for="lodge_local">Lodge:</label>
				<select name="lodge_local" id="lodge_local" onchange="updateFullLodges(this.form); updateChapters(this.form); this.form.lodge.value=this.value;">
				<option value="">-- Select your Lodge --</option>
					<?					
					$opt[get('lodge')] = "selected";
					while($lodge = $lodge_res->fetchRow()) {
						print "<option value=\"$lodge->lodge_id\" {$opt[$lodge->lodge_id]}>$lodge->lodge_name</option>";
					}
					$myLodge = $sm_db->getRow("SELECT lodge_name, section FROM lodges WHERE lodge_id='".get('lodge')."'");
					print "<option value=\"OUT\"";
					if (get('lodge') != '' && get('lodge') != '0' && get('lodge') != null)
						if ($EVENT['section_name'] != $myLodge->section)
							print " selected";
					print ">Out of Section</option>";
					?>
				</select>

				<!-- Out of Section Lodge -->
				<select name="lodge" id="lodge" onchange="updateChapters(this.form);" <? if($EVENT['section_name'] == $myLodge->section OR get('lodge') == ''): ?>style="visibility:hidden;"<? endif; ?>>
				<option value="">-- Select your Lodge --</option>
				<? if (get('lodge') != '') {
					print "<option value=\"".get('lodge')."\" selected>$myLodge->lodge_name</option>";
					$dont_select = true;
				} ?>
				<optgroup label="Our Section (<?=$EVENT['section_name']?>)">
					<?					
					$currSection = $EVENT['section_name'];
					$opt[get('lodge')] = $dont_select ? "" : "selected";
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
						//if ($chapter->lodge != $currLodge) {
						//	print "</optgroup><optgroup label=\"$chapter->lodge_name\">";
						//}
						print "<option value=\"$chapter->chapter_id\" {$opt[$chapter->chapter_id]}>$chapter->chapter_name</option>";
						$currLodge = $chapter->lodge;
					}
					?>
				<option value="0">I Don't Know My Chapter or District</option>
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

		<!-- /ScoutingInfoDiv -->
		</div>



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
		
		<!-- Hiddens -->
		<input type="hidden" name="section" value="<? print $SECTION ?>">
		<input type="hidden" name="page" value="contact-info">
		
		<input id="submit_button" name="submitted" type="submit" value="Continue to Next Step >">

	</form>
	</center>