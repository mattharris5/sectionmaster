<?
#########################################
# Send OAReg Stat Emails Program
#########################################

# Includes
$title = "Section > Event > Report E-mailer";
require "../pre.php";
$user->checkPermissions("report_summary");

# Check for event
$EVENT 	 = event_info($sm_db, $user->info->current_event);
$SECTION = section_id_convert($EVENT['section_name']);

require "../section/stataddresses.php";


# Current Year variable
$curr_year = $EVENT['year'];
$year_ago = strtotime($EVENT['start_date']) - (365*24*60*60);
$new_member_count = 0;

# Global addresses
$addr['from'] = "{$EVENT['section_name']} Stats Reporting <stats@sectionmaster.org>";
$addr['subject_prefix'] = "{$EVENT['formal_event_name']} Registration Statistics";

# Get the send & test variables from URL
$send = $_REQUEST['send'];
$test_mode = $_REQUEST['test_mode'];
$test_addr = $_REQUEST['test_addr'];


# Get lodge info
$lodges = array();
$res = $db->query("SELECT DISTINCT lodge_id, lodge_name
					FROM members, orders, conclave_attendance
					LEFT JOIN `sm-online`.lodges ON members.lodge = `sm-online`.lodges.lodge_id
					WHERE members.member_id=conclave_attendance.member_id
						AND conclave_attendance.conclave_order_id=orders.order_id
						AND conclave_year=$curr_year
						AND registration_complete=1
					ORDER BY section='{$EVENT['section_name']}' DESC, lodge_name ASC");
	if (DB::isError($res)) dbError("Could not retrieve lodge list (ADMIN AREA)", $res, "");
	
while ($lodge = $res->fetchRow()) {
	$lodge_id = $lodge->lodge_id != '' ? $lodge->lodge_id : "XXX";
	$lodges[$lodge_id] = $lodge->lodge_name=='' ? "Non-Member": $lodge->lodge_name . " Lodge";
}
	$lodges['0'] = "--- Out of Section Total"; // out of section lodge

# Get the counts

	$lodge_count = array();
	$chapter_count = array();
	
	######################################
	# Create the count arrays
	######################################
		// Create the lodge/chapter array
		$chapters = array();
		$chapter_names = array();
		$chapter_memberships = array();
		
		// Populate the chapter counts to 0
		$chapters_select = $db->query("SELECT * FROM chapters ORDER BY chapter_name");
			if (DB::isError($chapters_select)) dbError("Could not retrieve chapter list (ADMIN AREA)", $chapters_select, "");
		while ($chapter = $chapters_select->fetchRow()) {
			$chapter_count[$curr_chapter] = 0;
			$chapters[$chapter->chapter_id] = $chapter->lodge;
			$chapter_names[$chapter->chapter_id] = $chapter->chapter_name . " Chapter";
			$chapter_memberships[$chapter->chapter_id] = $chapter->active_membership;
		}
		
		// Populate the total counts to 0
		$total_count = 0;
		
	#######################################
	# Fill the counts
	#######################################
		$sql = "SELECT members.*, reg_date, payment_method
				FROM members, orders, conclave_attendance
				LEFT JOIN `sm-online`.lodges ON members.lodge = lodges.lodge_id
				WHERE members.member_id = conclave_attendance.member_id
					AND conclave_attendance.conclave_order_id = orders.order_id
					AND conclave_year = $curr_year
					AND registration_complete = 1
				ORDER BY section='{$EVENT['section_name']}' DESC, lodge_name ASC";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Could not fill counts (ADMIN AREA)", $res, "");

		while ($member = $res->fetchRow()) {
			
			$curr_chapter = $member->chapter;
			$curr_lodge = $member->lodge;
			
			// Increase the count for this chapter
			if ($chapter_count[$curr_chapter] == '') {
				$chapter_count[$curr_chapter] = 1;
				$total_count++;
			} else {	
				$chapter_count[$curr_chapter]++;
				$total_count++;
			}
			
			// Increase the count for this lodge
			if ($lodge_count[$curr_lodge] == '') {
				$lodge_count[$curr_lodge] = 1;
			} else {	
				$lodge_count[$curr_lodge]++;
			}
			
//				print "<br>" . strtotime($member->ordeal_date);
			// check for new ordeal member status
			if (strtotime($member->ordeal_date) > $year_ago) {
				$new_member_count = $new_member_count + 1;
			}
			
		}
		
		// out of section
		$lodge_count['0'] = $chapter_count['0'];
		$outofsection_count = $lodge_count['0'];
		

	#######################################
	# Make percent breakdowns ("REGISTRATION SUMMARY" at top)
	#######################################
	foreach($lodges as $lodge_id => $lodge_name) {
		$percent[$lodge_id] = $lodge_count[$lodge_id] / $total_count;
	}
//	$percent['0'] = $chapter_count['0'] / $total_count;
	unset($percent['0']);
	$outofsection_percent = $outofsection_count / $total_count;
	$new_member_percent = $new_member_count / $total_count;
	
	$body['count_breakdown'] = "\nREGISTRATION SUMMARY";
	foreach ($percent as $lodge_name => $percent) {
		$lodge_count_for_percent = $lodge_count[$lodge_name];
		$body['count_breakdown'] .= "\n" 	. str_pad("  " . $lodges[$lodge_name], 30) 
											. str_pad(ceil($percent*100), 4," ", STR_PAD_LEFT) . "%"
											. str_pad($lodge_count_for_percent, 6, " ", STR_PAD_LEFT);
	}
	$body['count_breakdown'] .= "\n" . str_repeat("-", 41);
	$body['count_breakdown'] .= "\nTOTAL REGISTERED:" . str_pad($total_count, 24, " ", STR_PAD_LEFT);
	$body['count_breakdown'] .= "\n" 	. str_pad("  New Ordeal Members", 30) 
										. str_pad(ceil($new_member_percent*100), 4," ", STR_PAD_LEFT) . "%"
										. str_pad($new_member_count, 6, " ", STR_PAD_LEFT);
	$body['count_breakdown'] .= "\n" 	. str_pad("  Out of Section Guests", 30) 
										. str_pad(ceil($outofsection_percent*100), 4," ", STR_PAD_LEFT) . "%"
										. str_pad($outofsection_count, 6, " ", STR_PAD_LEFT);
	$body['count_breakdown'] .= "\n";


	#######################################
	# Create DETAILED Report E-mails
	#######################################
	foreach ($lodge_count as $lodge_name => $count) {
		$body['lodge'][$lodge_name] = "\n{$lodges[$lodge_name]}";
		$body['lodge'][$lodge_name] .= "\nCurrently Registered: $count";
		
		foreach ($chapters as $chapter_name => $lodge) {
			if ($lodge == $lodge_name) {
				$body['lodge'][$lodge_name] .= "\n\n\t{$chapter_names[$chapter_name]}: ".$chapter_count[$chapter_name];
				if ($chapter_memberships[$chapter_name]!=0) $body['lodge'][$lodge_name] .= " (".ceil($chapter_count[$chapter_name]/$chapter_memberships[$chapter_name]*100) . "% of ".$chapter_memberships[$chapter_name]." Active Members)";
	
				$sql = "SELECT firstname, lastname, reg_date
						FROM members, orders, conclave_attendance
						WHERE members.member_id=conclave_attendance.member_id
							AND conclave_attendance.conclave_order_id=orders.order_id
							AND conclave_year=$curr_year
							AND registration_complete=1
							AND chapter = $chapter_name
						ORDER BY lastname ASC, firstname ASC";
				$res = $db->query($sql);
					if (DB::isError($res)) dbError("Creating detailed report (ADMIN AREA)", $res, $sql);
				
				while ($names = $res->fetchRow()) {
					$body['lodge'][$lodge_name] .= "\n\t\t$names->firstname $names->lastname";
				}
			}
		}
		$body['lodge'][$lodge_name] .= "\n\n";
		
	}
	
	#######################################
	# Create SUMMARY Report E-mails
	#######################################
	foreach ($lodge_count as $lodge_name => $count) {
		$body['summary'] .= "\n\n\t$lodges[$lodge_name]: $count";
	
		foreach ($chapters as $chapter_name => $lodge) {
			if ($lodge == $lodge_name) {
				$body['summary'] .= "\n\t\t$chapter_names[$chapter_name]: ".$chapter_count[$chapter_name];
				if ($chapter_memberships[$chapter_name]!=0) $body['summary'] .= " (".ceil($chapter_count[$chapter_name]/$chapter_memberships[$chapter_name]*100) . "% of ".$chapter_memberships[$chapter_name]." Active Members)";
			}
		}
	}
	
	#######################################
	# Create CHAPTER Report E-Mails
	#######################################
	foreach ($chapters as $chapter_name => $lodge) {
		$body['chapter'][$chapter_name] .= "\n$chapter_names[$chapter_name] Report \nCurrently Registered: " . $chapter_count[$chapter_name] . "\n";
		if ($chapter_memberships[$chapter_name]!=0) $body['chapter'][$chapter_name] .= ceil($chapter_count[$chapter_name]/$chapter_memberships[$chapter_name]*100) . "% of ".$chapter_memberships[$chapter_name]." Active Members\n";

		$sql = "SELECT firstname, lastname, oahonor, primary_phone, email 
				FROM members, orders, conclave_attendance
				WHERE members.member_id = conclave_attendance.member_id
					AND conclave_attendance.conclave_order_id = orders.order_id
					AND conclave_year = $curr_year
					AND registration_complete = 1
					AND chapter = '$chapter_name'
				ORDER BY lastname ASC, firstname ASC";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Creating CHAPTER report (ADMIN AREA)", $res, $sql);
		
		while ($names = $res->fetchRow()) {
			$body['chapter'][$chapter_name] .= "\n". str_pad("$names->firstname $names->lastname", 20);
			$body['chapter'][$chapter_name] .= "  " . str_pad("$names->oahonor", 11);
			$body['chapter'][$chapter_name] .= "  " . str_pad("$names->primary_phone", 12);
			$body['chapter'][$chapter_name] .= "  " . $names->email;
		}
	}
	
	#######################################
	# Create Training Counts Reports
	#######################################
	$body['training'] .= "\nTRAINING REPORT";
	$session_letters = array("1" => "A", "2" => "B", "3" => "C", "4" => "D");
	for ($curr_sess = 1; $curr_sess <= 4; $curr_sess++) {
		$sql = "SELECT session_id, college_prefix, course_number, session_name, time, 
					COUNT(session_id) AS session_count, 
					max_size,
					IF(COUNT(session_id) >= max_size, 'FULL', max_size - COUNT(session_id)) AS remaining
				FROM training_sessions
				
				LEFT JOIN conclave_attendance ON (training_sessions.session_id = conclave_attendance.session_$curr_sess
											AND conclave_year = $curr_year
											AND registration_complete = 1)
				LEFT JOIN members USING (member_id)
				LEFT JOIN orders ON conclave_attendance.conclave_order_id = orders.order_id
				LEFT JOIN colleges ON college_id = college
				
				WHERE offered != 0 AND time = $curr_sess
					AND session_id != 1 AND session_id != 2 AND session_id != 3 AND session_id != 4
					AND training_sessions.deleted != 1
				
				GROUP BY session_id
				ORDER BY college, course_number";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Creating TRAINING report (ADMIN AREA)", $res, $sql);
			
		$body['training'] .= "\n\nSession $curr_sess";
		$body['training'] .= "                                            Count  Max  Remaining";
		while ($sess = $res->fetchRow()) {
			$body['training'] .= "\n   $sess->college_prefix$sess->course_number{$session_letters[$curr_sess]}  ";
			$max_width = 40;
			$body['training'] .= strlen($sess->session_name) > $max_width ?
								str_pad(substr($sess->session_name, 0, $max_width-3)
									. "...", $max_width+3) :
								str_pad($sess->session_name, $max_width+3);
			$body['training'] .= str_pad("$sess->session_count", 6, " ", STR_PAD_BOTH);
			$body['training'] .= str_pad("$sess->max_size", 6, " ", STR_PAD_BOTH);
			$body['training'] .= str_pad("$sess->remaining", 9, " ", STR_PAD_BOTH);
		}
	}

	$body['training'] .= "\n\n";
	

	#######################################
	# Create Trading Post Report
	#######################################
	$body['tradingpost'] = "\nTRADING POST REPORT\n\n" .
							"Item                                            Sold   Remaining \n" .
							"----------------------------------------------------------------";
	$sql = "SELECT products.product_id AS product_id, option_name, item,
					SUM(ordered_items.quantity) AS sold, inventory AS remaining
			FROM orders, ordered_items, products
			WHERE complete = 1
			AND	ordered_items.order_id = orders.order_id
			AND ordered_items.product_id = products.product_id
			AND show_in_store=1
			AND products.deleted != 1
			AND ordered_items.deleted != 1
			AND orders.deleted != 1
			AND order_year = '{$EVENT['year']}'
			GROUP BY ordered_items.product_id
			ORDER BY sold DESC";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Creating tradingpost report (ADMIN AREA)", $res, $sql);

	while($item = $res->fetchRow()) {

		$remaining = $item->option_name != '' ? 'n/a' : $item->remaining;
		$body['tradingpost'] .= "\n" . str_pad($item->item, 45)
									 . str_pad($item->sold, 10, ' ', STR_PAD_BOTH)
									 . str_pad($remaining, 10, ' ', STR_PAD_BOTH);
									 
		if ($item->option_name != '') {
			$sql = "SELECT SUM(ordered_items.quantity) AS sold, option_value,
						product_options.inventory AS remaining
					FROM orders, ordered_items, products, product_options
					WHERE complete = 1
					AND ordered_items.order_id = orders.order_id
					AND ordered_items.product_id = products.product_id
					AND ordered_items.product_option_id = product_options.product_option_id
					AND show_in_store=1
					AND products.deleted != 1
					AND ordered_items.deleted != 1
					AND orders.deleted != 1
					AND product_options.product_id = $item->product_id
					AND order_year = '{$EVENT['year']}'
					GROUP BY ordered_items.product_option_id
					ORDER BY item ASC, option_value ASC";
			$res2 = $db->query($sql);
				if (DB::isError($res2)) dbError("Creating tradingpost report (ADMIN AREA) - prod options", $res2, $sql);
				
			while($item_option = $res2->fetchRow()) {
				$body['tradingpost'] .= "\n" . "     " . str_pad($item_option->option_value, 40)
											 . str_pad($item_option->sold, 10, ' ', STR_PAD_BOTH)
											 . str_pad($item_option->remaining, 10, ' ', STR_PAD_BOTH);
			}
		}
	}



	#######################################
	# Create header
	#######################################
	$body['header'] = "{$EVENT['formal_event_name']} Registration Statistics";
	$body['header'] .= "\nAs of ". date("D M j G:i:s T Y");
	$body['header'] .= "\nTOTAL REGISTERED: $total_count";
	$body['header'] .= "\n\nReminder: " . time_until($EVENT['casual_event_name'], 
											date('j', strtotime($EVENT['start_date'])),
											date('m', strtotime($EVENT['start_date'])),
											date('Y', strtotime($EVENT['start_date']))) . "\n";
	
	#######################################
	# Print HTML Form
	#######################################
	print "
		<h1>Conclave Stats Report Mailer</h1>
		<FORM NAME=\"statsform\" ACTION=\"{$_GLOBALS['form_action']}admin/event/stats.php\" METHOD=GET CLASS=\"default\">
			
			<fieldset>
			<legend>Send Reports</legend>
			
			<INPUT TYPE=radio NAME=\"test_mode\" VALUE=\"FALSE\"><b>Send to stored recipients (as listed below)
			<br>
			<INPUT TYPE=radio NAME=\"test_mode\" VALUE=\"TRUE\" checked>Send only to this address:
			<INPUT TYPE=text NAME=\"test_addr\" VALUE=\"enter email address\" STYLE=\"font-size: 10px; width:150px;\" onClick=\"this.value='';\"></B>
			
				<table>
				<tr><th colspan=2>Select Reports to Send</th></tr>
				<tr><td><INPUT TYPE=checkbox NAME=\"send[full]\" VALUE=\"TRUE\"></td>
					<td><font id=item>Full Report<br></font><font id=description>Stored recipients: ".$addr_names['full</font>']."
						(<a href=\"section/edit_stataddresses.php\">Edit</a>)</font></td>
				</tr>
				<tr><td><INPUT TYPE=checkbox NAME=\"send[summary]\" VALUE=\"TRUE\"></td>
					<td><font id=item>Summary Report <br></font><font id=description>Stored recipients: ".$addr_names['summary']."
					(<a href=\"section/edit_stataddresses.php\">Edit</a>)</font></td>
				</tr>
				<tr><td><INPUT TYPE=checkbox NAME=\"send[training]\" VALUE=\"TRUE\">	</td>
					<td><font id=item>Training Report <br></font><font id=description>Stored recipients: ".$addr_names['training']."
					(<a href=\"section/edit_stataddresses.php\">Edit</a>)</font></td>
				</tr>
				<tr><td><INPUT TYPE=checkbox NAME=\"send[tradingpost]\" VALUE=\"TRUE\">		</td>
					<td><font id=item>Trading Post Report <br></font><font id=description>Stored recipients: ".$addr_names['tradingpost']."
					(<a href=\"section/edit_stataddresses.php\">Edit</a>)</font></td>
				</tr>
				<tr><td><INPUT TYPE=checkbox NAME=\"send[lodge_reports]\" VALUE=\"TRUE\">	</td>
					<td><label style=\"float:left;\"><font id=item>Lodge Reports: </label>
						<br><SELECT NAME=\"send[which_lodge_reports]\">
						<OPTION VALUE=\"all\">All lodges</OPTION>";
						foreach($lodges as $lodge_no => $lodge_name) {
							print "<OPTION VALUE=\"$lodge_no\">$lodge_name - ".$addr_names['lodge'][$lodge_no]."</OPTION>\n\t";
						}
				print "</SELECT>
					<br>(<a href=\"section/edit_stataddresses.php\">Edit stored recipients</a>)</td>
					</tr>
					<tr><td><INPUT TYPE=checkbox NAME=\"send[chapter_reports]\" VALUE=\"TRUE\"></td>
						<td><label style=\"float:left;\"><font id=item>Chapter Reports:</font></label>
						<br><SELECT NAME=\"send[which_chapter_reports]\">
						<OPTION VALUE=\"all\">All chapters</OPTION>";
						foreach($chapter_names as $chapter_id => $chapter_name) {
							print "<OPTION VALUE=\"$chapter_id\">$chapter_name - ".$addr_names['chapter'][$chapter_id]."</OPTION>\n\t";
						}
				print "</SELECT><br>(<a href=\"section/edit_stataddresses.php\">Edit stored recipients</a>)</td>
					</tr>
				</table>
			
			<INPUT TYPE=hidden NAME=\"command\" VALUE=\"proceed\">
			<INPUT TYPE=hidden NAME=\"event\" VALUE=\"{$EVENT['id']}\">
			<P><INPUT TYPE=submit VALUE=\"Send Conclave Stat Reports\">

		</FORM>
		</fieldset>
		
		<BR><HR><BR>
		
		<pre>";
		
	#######################################
	# Send the emails!
	#######################################
if ($_REQUEST['command'] == 'proceed') {

	$print_date = " as of " . date("F jS");
	
	print $body['header'] . "\n";
	
	# Full report
	if ($send['full'] == TRUE) {
		$body['full'] = $body['header'] . $body['count_breakdown'];
		foreach($body['lodge'] as $lodge_name => $lodge_content) {
			$body['full'] .= $body['lodge'][$lodge_name];
		}
		$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['full'];
		if (mail($curr_addr,
			"{$addr['subject_prefix']}: Full Report" . $print_date,
			$body['full'],
			"From: " . $addr['from'])) 
		{ print "\nFull Report sent to ".htmlspecialchars($curr_addr); }
	}

	# Summary report
	if ($send['summary'] == TRUE) {
		$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['summary'];
		if (mail($curr_addr,
			"{$addr['subject_prefix']}: Summary Report" . $print_date,
			$body['header'] . $body['count_breakdown'] . $body['summary'],
			"From: " . $addr['from']))
		{ print "\nSummary Report sent to ".htmlspecialchars($curr_addr); }
	}

	# Training report
	if ($send['training'] == TRUE) {
		$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['training'];
		if (mail($curr_addr,
			"{$addr['subject_prefix']}: Training Report" . $print_date,
			$body['header'] . $body['training'],
			"From: " . $addr['from']))
		{ print "\nTraining Report sent to ".htmlspecialchars($curr_addr); }
	}
	
	# Trading Post report
	if ($send['tradingpost'] == TRUE) {
		$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['tradingpost'];
		if (mail($curr_addr,
			"{$addr['subject_prefix']}: Trading Post Report" . $print_date,
			$body['header'] . $body['tradingpost'],
			"From: " . $addr['from']))
		{ print "\nTrading Post Report sent to ".htmlspecialchars($curr_addr); }
	}

	# Lodge Reports
	if ($send['lodge_reports'] == TRUE) {
		foreach($lodges as $lodge_id => $lodge) {

			// Are we supposed to send to this lodge?
			if ($send['which_lodge_reports'] == 'all' || $send['which_lodge_reports'] == $lodge_id) {
				
				// Get "to" address - test address or from the address list
				$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['lodge'][$lodge_id];
				
				// Is there an address?
				if ($curr_addr) {
					
					// Send the mail
					if (mail($curr_addr,
						"{$addr['subject_prefix']}: $lodge" . $print_date,
						$body['header'] . $body['count_breakdown'] . $body['lodge'][$lodge_id],
						"From: " . $addr['from']))
					{ print "\n$lodge Report sent to ".htmlspecialchars($curr_addr); }
				}
			}
		}
	}

	# Chapter Reports
	if ($send['chapter_reports'] == TRUE) {
		foreach($chapter_names as $chapter_id => $chapter_name) {

			// Are we supposed to send to this chapter?
			if ($send['which_chapter_reports'] == 'all' || $send['which_chapter_reports'] == $chapter_id) {
				
				// Get "to" address - test address or from the address list
				$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['chapter'][$chapter_id];
				
				// Is there an address?
				if ($curr_addr) {
					
					// Send the mail
					if (mail($curr_addr,
						"{$addr['subject_prefix']}: $chapter_name" . $print_date,
						$body['header'] . $body['chapter'][$chapter_id],
						"From: " . $addr['from']))
					{ print "\n$chapter_name Report sent to ".htmlspecialchars($curr_addr); }
				}
			}
		}
	}
}

######################################
# Print stuff out
######################################
print "<pre>";
print "\n\n";
//print $body['header'];
//print_r($body);
?>