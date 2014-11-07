<?
/*	SM-Online Registration System                      www.sectionmaster.org
	------------------------------------------------------------------------

	HEALTH INFO PAGE (html/health-info.php)

	------------------------------------------------------------------------										*/

	# Validate Data
		validate("emer_contact_name","","Please provide an emergency contact name.");
		validate("emer_phone_1_3", "", "Please provide at lease one phone number for this contact.");
        
	# Post-Validate
		if (postValidate()) {
			$sql = "UPDATE members SET
						emer_contact_name = " . $db->quote($_REQUEST['emer_contact_name']) . ",
						emer_relationship = " . $db->quote($_REQUEST['emer_relationship']) . ",
						emer_phone_1 = " . $db->quote($_REQUEST['emer_phone_1_1'] . "-". $_REQUEST['emer_phone_1_2'] . "-". $_REQUEST['emer_phone_1_3']) . ",
						conditions_explanation = " . $db->quote($_REQUEST['conditions_explanation']) . ",
						special_diet_explain = " . $db->quote($_REQUEST['special_diet_explain']);
				if (get('special_diet_explain') != '') { $sql .= "\n, special_diet = '1'"; }
				if (get('emer_phone_2_3') != '') { $sql .= "\n, emer_phone_2 = " . $db->quote($_REQUEST['emer_phone_2_1'] . "-". $_REQUEST['emer_phone_2_2'] . "-". $_REQUEST['emer_phone_2_3']) ; }
				foreach($_REQUEST['conditions'] as $cond => $val) { $sql .= "\n, $cond = " . $db->quote($val); }
				$sql .=	"WHERE member_id = {$_SESSION['member_id']}";

			$update = $db->query($sql);
				if (DB::isError($update)) { dbError("Could not save member information", $update, $sql); }
					
			// go to next page
			gotoNextPage("health-info");
		}

?>

<h2>Health and Emergency Information</h2>

	<p>Please provide information that will be necessary in the event of an emergency, as well as for planning special accommodations.  When you're finished, click the "Continue" button.</p>

	<? print $ERROR['error_summary']; ?>
	
	<center>
	<form name="form1" action="<? print $_GLOBALS['form_action']; ?>" method="<? print $_GLOBALS['form_method']; ?>">

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

	</fieldset>
	
	<fieldset><legend>Medical Conditions</legend>
						
		<!-- Medical Conditions -->
		<label class="spanned">Are you subject to, or have a history of any of the following conditions:</label>
			
			<?
			function checked($arg) {
				global $member;
				if (($member->$arg == 1) OR ($_REQUEST['conditions'][$arg] == 1)) return "checked";
			}
			?>

			<!-- include all the checkboxes as hidden so unchecked boxes register properly -->
			<input type="hidden" class="hidden" name="conditions[special_care]" value="0">
			<input type="hidden" class="hidden" name="conditions[sleeping_device]" value="0">
			<input type="hidden" class="hidden" name="conditions[food_allergy]" value="0">


			<div class="checkboxes">

			<span><input type="checkbox" name="conditions[special_care]" value="1" <?=checked("special_care")?>>I require special care or special facilities</span>
			
			<br><span><input type="checkbox" name="conditions[sleeping_device]" value="1" <?=checked("sleeping_device")?>>I use a sleeping device that requires power (such as a CPAP)</span>
				
				<span><input type="checkbox" name="conditions[food_allergy]" value="1" <?=checked("food_allergy")?>>Food Allergy</span>
			</div><br><br>

			
		<!-- Conditions Explanations -->	
		<label for="conditions_explanation">Explain Allergies/Conditions:</label>
			<input name="conditions_explanation" id="conditions_explanation" value="<?=get('conditions_explanation')?>" style="width: 300px;"><br>
				
		<label for="special_diet_explain">Food Allergies or Needs: </label>
			<input name="special_diet_explain" id="special_diet_explain" value="<?=get('special_diet_explain')?>" style="width: 300px;"><br>
		
	</fieldset>
	
	<div class="payment_note">
		<b>IMPORTANT:</b> You must bring a completed copy of your Annual Health and Medical Record form with you to this event.  Please consult your leadership with any questions regarding this requirement.  <a href="http://www.scouting.org/scoutsource/HealthandSafety/ahmr.aspx" target="_blank">Click here to download a PDF of this form.</a>
	</div>

		<!-- Hiddens -->
		<input type="hidden" name="section" value="<? print $SECTION ?>">
		<input type="hidden" name="page" value="health-info">
		
		<input id="submit_button" name="submitted" type="submit" value="Continue to Next Step >">

	</form>
	</center>