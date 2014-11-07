<? 
/*	SM-Online Registration System                      www.sectionmaster.org
	------------------------------------------------------------------------

	TP-INFO PAGE (html/tp-info.php)

	------------------------------------------------------------------------										*/

	# Validate Data
		validate("firstname","","Please enter your first name.");
		validate("lastname","","Please enter your last name.");
		validate("address","","Please enter your address.");
		validate("city","","Please enter your city.");
		validate("state", "", "Please enter your state.");
		validate("zip","","Please enter your Zip Code.");
		validate("phone3","","Please enter your phone number.");
        
	# Post-Validate
		if (postValidate()) {
			$update = $db->query("UPDATE members SET
									firstname = '". addslashes(get('firstname')) . "',
									lastname = '". addslashes(get('lastname')) . "',
									address = '". addslashes(get('address')) . "',
									address2 = '". addslashes(get('address2')) . "',
									city = '". addslashes(get('city')) . "',
									state = '". addslashes(get('state')) . "',
									zip = '". addslashes(get('zip')) . "',
									primary_phone = '". addslashes(get('phone1') . "-". get('phone2') . "-". get('phone3')) . "',
									email = '". addslashes(get('email')) . "'
								  WHERE member_id = {$_SESSION['member_id']}");
								  
			if (DB::isError($update)) { dbError("Could not save member information", $update, ""); }

			gotoNextPage("tp-info");
		}

?>

<h2>Shipping Information</h2>

	<p>Please enter your shipping information below.  All fields marked with a <? print $ERROR['sample'] ?> are required.  When you're finished, click the "Continue" button.</p>

	<? print $ERROR['error_summary']; ?>
	
	<center>
	<form name="form1" action="<? print $_GLOBALS['form_action']; ?>" method="<? print $_GLOBALS['form_method']; ?>">


	<fieldset><legend>Shipping Information</legend>

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
				
	</fieldset>
	

	<!-- Hiddens -->
	<input type="hidden" name="section" value="<? print $SECTION ?>">
	<input type="hidden" name="page" value="tp-info">
	
	<input id="submit_button" name="submitted" type="submit" value="Continue to Next Step >">

	</form>