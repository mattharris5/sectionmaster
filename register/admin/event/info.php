<?
$title = "Event > Info";
require "../pre.php";
$user->checkPermissions("event_prefs");

// check for event
if ($user->info->current_event == 0)
	redirect("event/select");

// handle submitted form
if ($_REQUEST['submitted']) {
	$user->checkPermissions("event_prefs", 3);

	$sql = "UPDATE events SET
				year = " . $db->quote($_REQUEST['year']) . ",
				casual_event_name = " . $db->quote($_REQUEST['casual_event_name']) . ",
				formal_event_name = " . $db->quote($_REQUEST['formal_event_name']) . ",
				conclave_date = " . $db->quote($_REQUEST['conclave_date']) . ",
				start_date = " . $db->quote($_REQUEST['start_date']) . ",
				end_date = " . $db->quote($_REQUEST['end_date']) . ",
				conclave_location = " . $db->quote($_REQUEST['conclave_location']) . ",
				city_and_state = " . $db->quote($_REQUEST['city_and_state']) . ",
				slogan = " . $db->quote($_REQUEST['slogan']) . ",
				online_reg_open_date = " . $db->quote($_REQUEST['online_reg_open_date']) . ",
				online_reg_close_date = " . $db->quote($_REQUEST['online_reg_close_date']) . ",
				arrival_time_details = " . $db->quoteSmart($_REQUEST['arrival_time_details']) . ",
				event_cost = '" . $_REQUEST['event_cost'] . "',
				detail_url = " . $db->quote($_REQUEST['detail_url']) . ",
				reg_contact_name = " . $db->quote($_REQUEST['reg_contact_name']) . ",
				reg_contact_phone = " . $db->quote($_REQUEST['reg_contact_phone']) . ",
				reg_contact_email = " . $db->quote($_REQUEST['reg_contact_email']) . ",
				support_contact_name = " . $db->quote($_REQUEST['support_contact_name']) . ",
				support_contact_phone = " . $db->quote($_REQUEST['support_contact_phone']) . ",
				support_contact_email = " . $db->quote($_REQUEST['support_contact_email']) . ",
				tp_contact_email = " . $db->quote($_REQUEST['tp_contact_email']) . ",
				section_chief = " . $db->quote($_REQUEST['section_chief']) . ",
				section_adviser = " . $db->quote($_REQUEST['section_adviser']) . ",
				do_training = " . $db->quote($_REQUEST['do_training']) . ",
				training_university = " . $db->quote($_REQUEST['training_university']) . ",
				training_cvc = " . $db->quote($_REQUEST['training_cvc']) . ",
				custom_degree_text1 = " . $db->quote($_REQUEST['custom_degree_text1']) . ",
				custom_degree_text2 = " . $db->quote($_REQUEST['custom_degree_text2']) . ",
				training_staff_college = " . $db->quote($_REQUEST['training_staff_college']) . ",
				training_unrestricted_college = " . $db->quote($_REQUEST['training_unrestricted_college']) . ",
				training_enforce_upper_level_courses = " . $db->quote($_REQUEST['training_enforce_upper_level_courses']) . ",
				training_required_classes_for_degree = " . $db->quote($_REQUEST['training_required_classes_for_degree']) . ",
				training_allow_manual_degree_level_changes = " . $db->quote($_REQUEST['training_allow_manual_degree_level_changes']) . ",
				training_ignore_colleges = " . $db->quote($_REQUEST['training_ignore_colleges']) . ",
				training_ignore_degrees = " . $db->quote($_REQUEST['training_ignore_degrees']) . ",
				do_tradingpost = " . $db->quote($_REQUEST['do_tradingpost']) . ",
				ship_calculation_method = " . $db->quote($_REQUEST['ship_calculation_method']) . ",
				ship_calculation = " . $db->quote($_REQUEST['ship_calculation']) . ",
				min_ship_cost = " . $db->quote($_REQUEST['min_ship_cost']) . ",
				ship_delivery_note = " . $db->quote($_REQUEST['ship_delivery_note']) . ",
				allow_pre_event_shipping = " . $db->quote($_REQUEST['allow_pre_event_shipping']) . ",
				do_online_payment = " . $db->quote($_REQUEST['do_online_payment']) . ",
				do_payatdoor = " . $db->quote($_REQUEST['do_payatdoor']) . ",
				custom_payment_method1 = " . $db->quote($_REQUEST['custom_payment_method1']) . ",
				do_eval = " . $db->quote($_REQUEST['do_eval']) . ",
				custom_agreement_text = " . $db->quote($_REQUEST['custom_agreement_text']) . ",
				separate_talent_release_signature = " . $db->quote($_REQUEST['separate_talent_release_signature']) . ",
				custom1_label = " . $db->quote($_REQUEST['custom1_label']) . ",
				custom2_label = " . $db->quote($_REQUEST['custom2_label']) . ",
				custom3_label = " . $db->quote($_REQUEST['custom3_label']) . ",
				recurring_event_id = " . $db->quote($_REQUEST['recurring_event_id']) . ",
				extra_css = '" . $_REQUEST['extra_css'] . "'
			WHERE id = '{$_REQUEST['event_id']}'";
	$res = $user->db->query($sql);
		if (DB::isError($res)) dbError("Could not update event info in $title", $res, $sql);

	// redirect back to event main page
	redirect("event/index");
}


// get event info
$sql = "SELECT * FROM events WHERE id={$user->info->current_event}";
$info = $sm_db->getRow($sql);
	if (DB::isError($info)) dbError("Could not get event info in $title", $info, $sql);

?>

<h1>Event Details: <?=$info->formal_event_name?></h1>

<script language="JavaScript">
	var init = true;
	function initDivs() {
		if (init == true) {
			<? if ($info->do_tradingpost != 1) print "hideDiv('tradingpost_info');" ?> 
			<? if ($info->allow_pre_event_shipping != 1) print "hideDiv('shipping_info');" ?> 
			<? if ($info->do_training != 1) print "hideDiv('training_info');" ?> 
		}
		init = false;
	}
</script>

<form action="event/info.php" method=post onMouseOver="initDivs();">
	
<p>Use this page to set your preferences for <?=$info->formal_event_name?>.  When you are done making your changes, press the "Save Changes" button at the bottom of the page to record your settings.</p>
	
<div class="tabber">

	<input type="hidden" class="hidden" name="event_id" value="<?= $user->info->current_event ?>">

	<div class="tabbertab">
	<h2>Date and Location</h2>

		<label for="year">Event Year:</label>
		<input name="year" id="year" value="<?= stripslashes($info->year) ?>"><br />

		<label for="casual_event_name">Casual Event Name:</label>
		<input name="casual_event_name" id="casual_event_name" value="<?= stripslashes($info->casual_event_name) ?>"><br />
		<span id="comment">Event name used when referring to it in a typical conversation (i.e., Conclave).</span><br />

		<label for="formal_event_name">Formal Event Name:</label>
		<input name="formal_event_name" id="formal_event_name" value="<?= stripslashes($info->formal_event_name) ?>"><br />
		<span id="comment">Event name used when referring to it on publications (i.e., Conclave 2006).</span><br />
		
		<label for="recurring_event_id">Belongs to Recurring Event:</label>
		<select name="recurring_event_id" id="recurring_event_id">
			<option name="">None</option>
			<? $recurring_events = $sm_db->query("SELECT * FROM recurring_events 
												  WHERE section_name = '" . section_id_convert($info->section_name) . "'");
			while ($recurring_events && $recurring_event = $recurring_events->fetchRow()) {
				print "<option value=\"$recurring_event->id\"";
				if ($info->recurring_event_id == $recurring_event->id) print " selected";
				print ">$recurring_event->title</option>";
			} ?>
		</select> &nbsp;
		<a href="javascript:window.open('event/recurring_events.php','recurring_events_window','toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=400,height=500,top=20'); return false;">Add or Edit Recurring Events</a><br />
		<span id="comment">If this event is one that happens every year or multiple times, you can group these events together as recurring events.  This allows you to see statistics and reports that compare your event to events in the past.</span><br />	

		<label for="org_name">Official Organization Name:</label>
		<input name="org_name" id="org_name" value="<?= stripslashes($info->org_name) ?>"><br />
		<span id="comment">The "official" organization name.  Usually, this should be something like "Section X-XX".</span><br />


		<label for="conclave_date">Event Date:</label>
		<input name="conclave_date" id="conclave_date" value="<?= stripslashes($info->conclave_date) ?>"><br />
		<span id="comment">The event date as if it were to appear on printed publications (ex., September 8-10, 2006).</span><br />

		<label for="start_date">Start Date/Time:</label>
		<input name="start_date" id="start_date" value="<?= stripslashes($info->start_date) ?>"><br />
		<span id="comment">The date and time stamp indicating when people would typically arrive at the event
			using the format YYYY-MM-DD HH:MM:SS.</span><br />

		<label for="end_date">End Date/Time:</label>
		<input name="end_date" id="end_date" value="<?= stripslashes($info->end_date) ?>"><br />
		<span id="comment">The date and time stamp indicating when people would typically check out from the
			event using the format YYY-MM-DD HH:MM:SS.</span><br />

		<label for="conclave_location">Event Location:</label>
		<input name="conclave_location" id="conclave_location" value="<?= stripslashes($info->conclave_location) ?>"><br />
		<span id="comment">Name of the camp, school, or other location at which the event is being held.</span><br />

		<label for="city_and_state">City &amp; State:</label>
		<input name="city_and_state" id="city_and_state" value="<?= stripslashes($info->city_and_state) ?>"><br />
		<span id="comment">The approximate location of the event's venue.</span><br />

		<label for="slogan">Event Theme/Slogan:</label>
		<input name="slogan" id="slogan" value="<?= stripslashes($info->slogan) ?>"><br />
	
	</div>
	
	<div class="tabbertab">
	<h2>Online Details</h2>
	
		<label for="online_reg_open_date">Online Registration Open Date/Time:</label>
		<input name="online_reg_open_date" id="online_reg_open_date" value="<?= stripslashes($info->online_reg_open_date) ?>"><br />
		<span id="comment">The exact date and time at which online registration will be set to begin using the format
			YYYY-MM-DD HH:MM:SS.  This occurs according to the location of the server, which is Mountain Time.</span><br />

		<label for="online_reg_close_date">Online Registration Close Date/Time:</label>
		<input name="online_reg_close_date" id="online_reg_close_date" <?=($user->peekPermissions("superuser"))?"":"readonly"?> value="<?= stripslashes($info->online_reg_close_date) ?>"><br />
		<span id="comment"><b>NOTE: Only a super user can modify this date/time as it cannot be extended beyond the contracted date.</b>  The exact date and time at which online registration will be set to end using the format
			YYYY-MM-DD HH:MM:DD.  This occurs according to the location of the server, which is Mountain Time.</span><br />

		<label for="arrival_time_details">Confirmation Page Details:</label>
		<textarea name="arrival_time_details" id="arrival_time_details" style="height: 150px;"><?= stripslashes($info->arrival_time_details) ?></textarea><br />
		<span id="comment">Any additional information to be displayed on the final order completion and receipt page.
			HTML input is okay.  Include things such as when check-in begins, items to bring, special transportation
			considerations, etc.</span><br />

		<label for="event_cost">Event Cost:</label>
		<textarea name="event_cost" id="event_cost" style="height: 150px;"><?= stripslashes($info->event_cost) ?></textarea><br />
		<span id="comment">This information will be displayed on the home page.  HTML input is okay.  If you have
			a multi-tiered registration fee structure or are offering any special deals, be sure to include that
			information here.</span><br />

		<label for="detail_url">Detail URL:</label>
		<input name="detail_url" id="detail_url" value="<?= stripslashes($info->detail_url) ?>"><br />
		<span id="comment">A URL that is placed on the home page directing the user to additional information.</span><br />
		

	</div>

	<div class="tabbertab">
	<h2>Authorized Contacts</h2>
	
		<label for="reg_contact_name">Registrar:</label>
		<input name="reg_contact_name" id="reg_contact_name" value="<?= stripslashes($info->reg_contact_name) ?>"><br />
		<span id="comment">The Registrar is the person who will field any questions about registration itself, and
			will also receive any refund inquiries.  According to the SectionMaster contact, this is either the Staff Adviser
			or the ONLY approved volunteer who	is authorized to approve or decline refund requests.  This person's contact
			information will appear on the Refund Policy & Customer Service page.</span><br />

		<label for="reg_contact_phone">Registrar Phone:</label>
		<input name="reg_contact_phone" id="reg_contact_phone" value="<?= stripslashes($info->reg_contact_phone) ?>"><br />

		<label for="reg_contact_email">Registrar E-mail:</label>
		<input name="reg_contact_email" id="reg_contact_email" value="<?= stripslashes($info->reg_contact_email) ?>"><br />

		<label for="support_contact_name">Technical Support Contact:</label>
		<input name="support_contact_name" id="support_contact_name" value="<?= stripslashes($info->support_contact_name) ?>"><br />
		<span id="comment">The Technical Support Contact fields questions concerning people having difficulty registering online.
			This person attempts to resolve any trouble users might be having before escalating the issue to SectionMaster
			personnel.  The Technical Support Contact is may also be the same person as the Registrar.</span><br />

		<label for="support_contact_phone">Technical Support Contact Phone:</label>
		<input name="support_contact_phone" id="support_contact_phone" value="<?= stripslashes($info->support_contact_phone) ?>"><br />

		<label for="support_contact_email">Technical Support Contact E-mail:</label>
		<input name="support_contact_email" id="support_contact_email" value="<?= stripslashes($info->support_contact_email) ?>"><br />

		<label for="tp_contact_email">Trading Post Manager E-mail:</label>
		<input name="tp_contact_email" id="tp_contact_email" value="<?= stripslashes($info->tp_contact_email) ?>"><br />
		<span id="comment">The Trading Post Manager will receive a copy of every order confirmation for orders
			containing trading post merchandise purchases and pre-orders.</span><br />

		<label for="section_chief">Section Chief:</label>
		<input name="section_chief" id="section_chief" value="<?= stripslashes($info->section_chief) ?>"><br />

		<label for="section_adviser">Section Adviser:</label>
		<input name="section_adviser" id="section_adviser" value="<?= stripslashes($info->section_adviser) ?>"><br />

	</div>


	<div class="tabbertab">
	<h2>Training Information</h2>

		<label for="do_training">Do Online Training Registration:</label>
		<input type=hidden name="do_training" value="0">
		<input type=checkbox name="do_training" id="do_training" value="1" 
			<?= $info->do_training=='1'?"checked":"" ?>
			onClick="if(this.checked==true) { showDiv('training_info'); } else { hideDiv('training_info'); }"><br>
			<span id="comment">Select this to utilize SectionMaster's University-style training registration process.</span><br />

		<div id="training_info">
			<label for="training_university">Training University Name:</label>
			<input name="training_university" id="training_university" value="<?= stripslashes($info->training_university) ?>"><br />
			<span id="comment">Ex.: Uncas University</span><br />

			<label for="training_cvc">Training CVC:</label>
			<input name="training_cvc" id="training_cvc" value="<?= stripslashes($info->training_cvc) ?>"><br />
			<span id="comment">Name of the Training CVC or University President for use on printed degree certificates.</span><br />

			<label for="custom_degree_text1">Custom Degree Text1:</label>
			<input name="custom_degree_text1" id="custom_degree_text1" value="<?= stripslashes($info->custom_degree_text1) ?>"><br />
			<span id="comment">Extra text for use on degree certificates printed with the SectionMaster Desktop software.</span><br />

			<label for="custom_degree_text2">Custom Degree Text2:</label>
			<input name="custom_degree_text2" id="custom_degree_text2" value="<?= stripslashes($info->custom_degree_text2) ?>"><br />

			<label for="training_staff_college">Staff College (No classes required):</label>
			<select name="training_staff_college" id="training_staff_college">
				<option value=""></option>
				<? $colleges = $db->query("SELECT * FROM colleges");
				while ($college = $colleges->fetchRow()) {
					print "<option value=\"$college->college_id\"";
					if ($info->training_staff_college == $college->college_id) print " selected";
					print ">$college->college_name ($college->college_prefix)</option>";
				} ?>
			</select><br />
			<span id="comment">The college or major that allows self-proclaimed staff members to bypass the training
				registration process altogether.  Leave blank to disable this feature.  A good choice would be one
				titled "STAFF -- No Classes".  If nothing appears in the list, you will need to define your colleges
				first.</span><br />

			<label for="training_unrestricted_college">Unrestricted College (Choose any classes):</label>
			<select name="training_unrestricted_college" id="training_unrestricted_college">
				<option value=""></option>
				<? $colleges = $db->query("SELECT * FROM colleges");
				while ($college = $colleges->fetchRow()) {
					print "<option value=\"$college->college_id\"";
					if ($info->training_unrestricted_college == $college->college_id) print " selected";
					print ">$college->college_name ($college->college_prefix)</option>";
				} ?>
			</select><br />
			<span id="comment">The college or major that allows a user to register for any class in any major.
				This is particularly helpful for first-time attendees who want a sampling of everything or for those
				people who simply can't make up their mind.  Ex.: College of General Studies.  If nothing appears in
				the list, you will need to define your colleges first.</span><br />
			
			<label for="training_enforce_upper_level_courses">Enforce Upper Level Courses:</label>
			<input type=hidden name="training_enforce_upper_level_courses" value="0">
			<input type=checkbox name="training_enforce_upper_level_courses" id="training_enforce_upper_level_courses" value="1" 
				<?= $info->training_enforce_upper_level_courses=='1'?"checked":"" ?>><br>
			<span id="comment">This feature allows you to restrict the level of courses people take based on the degree
				they are working on.  For instance, if someone is working toward their third degree, they would only
				be permitted to select classes with numbers beginning in the 300s (and higher).  This helps ensure that as
				participants progress through their OA education, they are continuously being challenged.
				Only choose this option if you have enough sessions scheduled during each time slot in each college
				to accommodate everyone's needs.</span><br />

			<label for="training_required_classes_for_degree">Number of required classes for degree:</label>
			<input name="training_required_classes_for_degree" id="training_required_classes_for_degree" value="<?= stripslashes($info->training_required_classes_for_degree) ?>"><br />
			<span id="comment">The number of sessions for which people will sign up for classes.  SectionMaster
				supports up to four (4) sessions.</span><br />

			<label for="training_allow_manual_degree_level_changes">Allow manual selection of degree:</label>
			<input type=hidden name="training_allow_manual_degree_level_changes" value="0">
			<input type=checkbox name="training_allow_manual_degree_level_changes"
					id="training_allow_manual_degree_level_changes" value="1" 
				<?= $info->training_allow_manual_degree_level_changes=='1'?"checked":"" ?>><br>
			<span id="comment">SectionMaster will attempt to automatically detect which degree the person should
				be working on at this event based on any historical information that may be in the database from
				previous years.  For first-time attendees or people not previously in the database, the default is the
				first degree level (typically Associate or Bachelor).  If your database does not have a good record of
				previous attendance and training degree completion information, you should choose this option so that
				people will be allowed to select which degree they should be working on.</span><br />

			<label for="training_ignore_colleges">Ignore colleges:</label>
			<input type=hidden name="training_ignore_colleges" value="0">
			<input type=checkbox name="training_ignore_colleges" id="training_ignore_colleges" value="1" 
				<?= $info->training_ignore_colleges=='1'?"checked":"" ?>><br>
			<span id="comment">If your section does not care about training colleges and just want people to be able to
				sign up for classes and to choose a degree, choose this option.</span><br />

			<label for="training_ignore_degrees">Ignore degrees:</label>
			<input type=hidden name="training_ignore_degrees" value="0">
			<input type=checkbox name="training_ignore_degrees" id="training_ignore_degrees" value="1" 
				<?= $info->training_ignore_degrees=='1'?"checked":"" ?>><br>
			<span id="comment">If your section prefers to hide all references to degrees during registration, and not store a value for the current_degree field in the member's event history, choose this option.</span><br />


		</div>

	</div>
	

	<div class="tabbertab">
	<h2>Trading Post Options</h2>

		<label for="do_tradingpost">Use Online Trading Post:</label>
		<input type=hidden name="do_tradingpost" value="0">
		<input type=checkbox name="do_tradingpost" id="do_tradingpost" value="1" 
			<?= $info->do_tradingpost=='1'?"checked":"" ?>
			onClick="if(this.checked==true) { showDiv('tradingpost_info'); } else { hideDiv('tradingpost_info'); }"><br>

		<div id="tradingpost_info">

			<label for="allow_pre_event_shipping">Allow items to be shipped before the event:</label>
			<input type=hidden name="allow_pre_event_shipping" value="0">
			<input type=checkbox name="allow_pre_event_shipping" id="allow_pre_event_shipping" value="1" 
				<?= $info->allow_pre_event_shipping=='1'?"checked":"" ?>
				onClick="if(this.checked==true) { showDiv('shipping_info'); } else { hideDiv('shipping_info'); }"><br>
				<span id="comment">This adds an option for the customer to get items shipped directly to them rather
					than picking them up at the event.  SectionMaster will then calculate a shipping charge for
					every order.</span><br />
		
			<div id="shipping_info">

				<label for="event_cost">Shipping Delivery Note:</label>
				<textarea name="ship_delivery_note" id="ship_delivery_note" style="height: 150px;"><?= stripslashes($info->ship_delivery_note) ?></textarea><br />
				<span id="comment">Customers are told that in most cases their merchandise will ship within a week of their order.
					However, if you have products that will be delayed, or products cannot be shipped immediately following
					the order, please indicate so here, using complete sentences.</span><br />

				<label for="ship_calculation_method">Shipping Calculation Method:</label>
				<select name="ship_calculation_method" id="ship_calculation_method">
					<option value=""></option>
					<option value="by_weight" <? if ($info->ship_calculation_method == 'by_weight') print "selected"; ?>>
						By Weight - sum of item weights (in ounces), multiplied by dollar amount in shipping calculation</option>
					<option value="flat_amt" <? if ($info->ship_calculation_method == 'flat_amt') print "selected"; ?>>
						Flat Amount - simply use shipping calculation as shipping cost for every order</option>
					<option value="flat_rate" <? if ($info->ship_calculation_method == 'flat_rate') print "selected"; ?>>
						Flat Rate - number of items, multiplied by a dollar amount in the shipping calculation</option>
					<option value="per_item" <? if ($info->ship_calculation_method == 'per_item') print "selected"; ?>>
						Per Item - sum of each item's shipping cost (as stored in the product details)</option>
				</select><br />

				<label for="ship_calculation">Shipping Calculation:</label>
				<input name="ship_calculation" id="ship_calculation" value="<?= stripslashes($info->ship_calculation) ?>"><br />
				<span id="comment">A number used in conjunction with the Shipping Calculation Method, that determines
					the amount to be charged for shipping.</span><br />

				<label for="min_ship_cost">Minimum Shipping Cost:</label>
				<input name="min_ship_cost" id="min_ship_cost" value="<?= stripslashes($info->min_ship_cost) ?>"><br />
				<span id="comment">The minimum charge in dollars for shipping on any order, regardless of the
					method by which shipping is calculated.</span><br />

			</div>
		</div>

	</div>
	

	<div class="tabbertab">
	<h2>Event Options</h2>

		<label for="do_online_payment">Allow Online Payment:</label>
		<input type=hidden name="do_online_payment" value="0">
		<input type=checkbox name="do_online_payment" id="do_online_payment" value="1" 
			<?= $info->do_online_payment=='1'?"checked":"" ?>><br>
		<span id="comment">Use SectionMaster's live, secure credit card processing capabilities (Visa and Mastercard).</span><br />

		<label for="do_payatdoor">Allow Pay-at-Door Payment option:</label>
		<input type=hidden name="do_payatdoor" value="0">
		<input type=checkbox name="do_payatdoor" id="do_payatdoor" value="1" 
			<?= $info->do_payatdoor=='1'?"checked":"" ?>><br>
		<span id="comment">Give users the option to pay when they arrive at the event instead of paying online.</span><br />

		<label for="custom_payment_method1">Allow a Custom Payment Method:</label>
		<input name="custom_payment_method1" id="custom_payment_method1" value="<?= stripslashes($info->custom_payment_method1) ?>"><br />
		<span id="comment">Use this option if your section uses a non-standard payment collection method and would like to
			allow users the option (e.g., "Pay Your Lodge").</span><br />

		<label for="do_eval">Allow online evaluations after event:</label>
		<input type=hidden name="do_eval" value="0">
		<input type=checkbox name="do_eval" id="do_eval" value="1" 
			<?= $info->do_eval=='1'?"checked":"" ?>><br>
		<span id="comment">Toggle this checkbox on or off to activate the ability to collect post-event evaluation information.</span><br />

		<label for="extra_css">Custom Agreement Text:</label>
		<textarea name="custom_agreement_text" id="custom_agreement_text" style="height: 150px; width: 400px;"><?= stripslashes($info->custom_agreement_text) ?></textarea><br />
		<span id="comment">Define additional agreements to appear below the standard Health History &amp; Authorization and Talent
			Release using HTML.  Be sure to use generic language that is not specific to the reader (i.e., do not use "my son" or "myself").</span><br />

		<label for="separate_talent_release_signature">Make Talent Release Consent Signature Optional:</label>
		<input type=hidden name="separate_talent_release_signature" value="0">
		<input type=checkbox name="separate_talent_release_signature" id="separate_talent_release_signature" value="1"
			<?= $info->separate_talent_release_signature=='1'?"checked":"" ?>><br>
		<span id="comment">Selecting this option separates the talent release consent agreement from the rest of the digital consent agreement(s), including a separate digital signature input box.  This makes the talent release agreement optional for the user.  Deselecting this option results in normal behavior, whereby the talent release is included within the digital consent agreement and all agreements are authorized using a single digital signature.</span><br />

		<label for="custom1_label">Custom Field 1:</label>
		<input name="custom1_label" id="custom1_label" value="<?=stripslashes($info->custom1_label) ?>"><br />
		<span id="comment">These custom fields can be used to collect extra information specific to your event. If left blank, no custom fields will be displayed to the user; otherwise, a text input box will be presented to the user with the label specified here.</span><br />

		<label for="custom2_label">Custom Field 2:</label>
		<input name="custom2_label" id="custom2_label" value="<?=stripslashes($info->custom2_label) ?>"><br />

		<label for="custom3_label">Custom Field 3:</label>
		<input name="custom3_label" id="custom3_label" value="<?=stripslashes($info->custom3_label) ?>"><br />

		<label for="extra_css">Extra CSS Definitions:</label>
		<textarea name="extra_css" id="extra_css" style="height: 150px; width: 400px;"><?= stripslashes($info->extra_css) ?></textarea><br />
		<span id="comment">Define additional Cascading Style Sheets properties to further customize the look and feel
			of the online system for this particular event.</span><br />

	</div>

</div>
	
	<input type=submit id="submit_button" name="submitted" value="Save Changes"><br />


<?
require "../post.php";
?>