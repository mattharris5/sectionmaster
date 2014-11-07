<?
#########################################
# SM-Online Report E-mailer
#########################################

# Includes
$title = "Section > Event > Report E-mailer";
require "../pre.php";
require "../includes/SendReportMail.php";
require "../section/stataddresses.php";
$user->checkPermissions("report_summary");
$EVENT 	 = event_info($sm_db, $user->info->current_event);
print "<h1>Conclave Stats Report Mailer</h1>";

# Get the send & test variables from URL
$send = $_REQUEST['send'];
$test_mode = $_REQUEST['test_mode'];
$test_addr = $_REQUEST['test_addr'];

#######################################
# Send the emails!
#######################################
if ($_REQUEST['command'] == 'proceed') {

	$print_date = "as of " . date("F jS");
		
	# Summary report
	if ($send['summary'] == TRUE) {
		$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['summary'];
		$report_type = "summary";
		require "../includes/report_mailer_functions.php";
		if (SendReportMail($curr_addr, "Summary Report $print_date", $report_content_html, $report_content_text, $user->info->firstname . " " . $user->info->lastname, $user->info->email)) 
		{ $result[] = "Summary Report sent to ".htmlspecialchars($curr_addr); }
	}

	# Training report
	if ($send['training'] == TRUE) {
		$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['training'];
		$report_type = "training";
		require "../includes/report_mailer_functions.php";
		if (SendReportMail($curr_addr, "Training Report $print_date", $report_content_html, $report_content_text, $user->info->firstname . " " . $user->info->lastname, $user->info->email)) 
		{ $result[] = "Training Report sent to ".htmlspecialchars($curr_addr); }
	}
	
	# Trading Post report
	if ($send['tradingpost'] == TRUE) {
		$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['tradingpost'];
		$report_type = "tradingpost";
		require "../includes/report_mailer_functions.php";
		if (SendReportMail($curr_addr, "Trading Post Report $print_date", $report_content_html, $report_content_text, $user->info->firstname . " " . $user->info->lastname, $user->info->email)) 
		{ $result[] = "Trading Post Report sent to ".htmlspecialchars($curr_addr); }
	}

	# Dietary Needs report
	if ($send['diet'] == TRUE) {
		$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['diet'];
		$report_type = "diet";
		require "../includes/report_mailer_functions.php";
		if (SendReportMail($curr_addr, "Dietary Needs Report $print_date", $report_content_html, $report_content_text, $user->info->firstname . " " . $user->info->lastname, $user->info->email)) 
		{ $result[] = "Dietary Needs Report sent to ".htmlspecialchars($curr_addr); }
	}
	
	# Medical Issues report
	if ($send['medical'] == TRUE) {
		$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['medical'];
		$report_type = "medical";
		require "../includes/report_mailer_functions.php";
		if (SendReportMail($curr_addr, "Medical Issues Report $print_date", $report_content_html, $report_content_text, $user->info->firstname . " " . $user->info->lastname, $user->info->email)) 
		{ $result[] = "Medical Issues Report sent to ".htmlspecialchars($curr_addr); }
	}

	# Medical Issues & ALL Emergency Contacts report
	if ($send['medical-all'] == TRUE) {
		$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['medical-all'];
		$report_type = "medical-all";
		require "../includes/report_mailer_functions.php";
		if (SendReportMail($curr_addr, "Medical Issues & ALL Emergency Contacts Report $print_date", $report_content_html, $report_content_text, $user->info->firstname . " " . $user->info->lastname, $user->info->email)) 
		{ $result[] = "Medical Issues & ALL Emergency Contacts Report sent to ".htmlspecialchars($curr_addr); }
	}
	
	# Out-of-section report
	if ($send['out_of_section'] == TRUE) {
		$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['out_of_section'];
		$report_type = "out_of_section";
		require "../includes/report_mailer_functions.php";
		if (SendReportMail($curr_addr, "Out-of-Section Report $print_date", $report_content_html, $report_content_text, $user->info->firstname . " " . $user->info->lastname, $user->info->email)) 
		{ $result[] = "Out-of-Section Report sent to ".htmlspecialchars($curr_addr); }
	}

	# Lodge Reports
	if ($send['lodge_reports'] == TRUE) {
		$sql = "SELECT lodges.lodge_id, lodges.lodge_name, lodges.section
					FROM `sm-online`.lodges AS lodges WHERE section = '{$EVENT['section_name']}'
					ORDER BY lodge_name ASC";
		$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not retreive lodge list (ADMIN AREA)", $res, "");

		while ($rlodge = $res->fetchRow()) {
			$lodge_id = $rlodge->lodge_id != '' ? $rlodge->lodge_id : "XXX";

			// Are we supposed to send to this lodge?
			if ($send['which_lodge_reports'] == 'all' || $send['which_lodge_reports'] == $lodge_id) {
				
				// Get "to" address - test address or from the address list
				$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['lodge'][$lodge_id];
				
				// Is there an address?
				if ($curr_addr) {
					
					$report_type = "lodge";
					require "../includes/report_mailer_functions.php";
					if (SendReportMail($curr_addr, "$rlodge->lodge_name Lodge Report $print_date", $report_content_html, $report_content_text, $user->info->firstname . " " . $user->info->lastname, $user->info->email)) 
					{ $result[] = "$rlodge->lodge_name Lodge Report sent to ".htmlspecialchars($curr_addr); }
				}
			}
		}
	}

	# Chapter Reports
	if ($send['chapter_reports'] == TRUE) {

		$chapters_select = $db->query("SELECT * FROM chapters ORDER BY chapter_name");
			if (DB::isError($chapters_select)) dbError("Could not retrieve chapter list (ADMIN AREA)", $chapters_select, "");
		while ($rchapter = $chapters_select->fetchRow()) {
			
			$chapter_id = $rchapter->chapter_id;

			// Are we supposed to send to this chapter?
			if ($send['which_chapter_reports'] == 'all' || $send['which_chapter_reports'] == $chapter_id) {
				
				// Get "to" address - test address or from the address list
				$curr_addr = ($test_mode == 'TRUE') ? $test_addr : $addr['chapter'][$chapter_id];
				
				// Is there an address?
				if ($curr_addr) {
					
					// Send the mail
					$report_type = "chapter";
					require "../includes/report_mailer_functions.php";
//					die("<pre>$report_content_text");
					if (SendReportMail($curr_addr, "$rchapter->chapter_name Chapter Report $print_date", $report_content_html, $report_content_text, $user->info->firstname . " " . $user->info->lastname, $user->info->email)) 
					{ $result[] = "$rchapter->chapter_name Chapter Report sent to ".htmlspecialchars($curr_addr); }
				}
			}
		}
	}
	
	# Print the results
	print "<div class=\"info_box\"><h2>Results:</h2><ul>";
	if (!$result) {
		print "<li>No messages sent.</li>";
	} else {
		foreach ($result as $id => $msg) {
			print "<li>$msg</li>";
		}
	}
	print "</ul></div>";
}

#######################################
# Print HTML Form
#######################################
print "
	<FORM NAME=\"statsform\" ACTION=\"{$_GLOBALS['form_action']}admin/event/report_mailer.php\" METHOD=GET CLASS=\"default\">
		
		<p>Use this page to send e-mail reports to your users.  You can choose to send the reports to the stored addresses for each type of report, or to a specific individual that requests it.  The reports that are sent match the reports accessible on the SM-Online Admin Menu.</p>
				
		<INPUT TYPE=radio NAME=\"test_mode\" VALUE=\"FALSE\"><b>Send to stored recipients (as listed below)
		<br>
		<INPUT TYPE=radio NAME=\"test_mode\" VALUE=\"TRUE\" checked>Send only to this address:
		<INPUT TYPE=text NAME=\"test_addr\" VALUE=\"enter email address\" STYLE=\"font-size: 10px; width:150px;\" onClick=\"this.value='';\"></B>
		
			<table>
			<tr><th colspan=2>Select Reports to Send</th></tr>
			<tr><td><INPUT TYPE=checkbox NAME=\"send[summary]\" VALUE=\"TRUE\"></td>
				<td><font id=item>Summary Report <br></font><font id=description>Stored recipients: ".$addr_names['summary']."
				(<a href=\"section/edit_stataddresses.php\">Edit</a>)</font></td>
			</tr>
			<tr><td><INPUT TYPE=checkbox NAME=\"send[lodge_reports]\" VALUE=\"TRUE\">	</td>
				<td><label style=\"float:left;\"><font id=item>Lodge Reports: </label>
					<br><SELECT NAME=\"send[which_lodge_reports]\">
					<OPTION VALUE=\"all\">All lodges</OPTION>";

					$sql = "SELECT lodges.lodge_id, lodges.lodge_name, lodges.section
								FROM `sm-online`.lodges AS lodges WHERE section = '{$EVENT['section_name']}'
								ORDER BY lodge_name ASC";
					$lodges = $db->query($sql);
					while ($lodge = $lodges->fetchRow()) {
						print "<option value=\"$lodge->lodge_id\"";
						print ">$lodge->lodge_name - ". $addr_names['lodge'][$lodge->lodge_id] . "</option>";
					}

			print "</SELECT>
				<br></font>(<a href=\"section/edit_stataddresses.php\">Edit stored recipients</a>)</td>
				</tr>
				<tr><td><INPUT TYPE=checkbox NAME=\"send[chapter_reports]\" VALUE=\"TRUE\"></td>
					<td><label style=\"float:left;\"><font id=item>Chapter Reports:</font></label>
					<br><SELECT NAME=\"send[which_chapter_reports]\">
					<OPTION VALUE=\"all\">All chapters</OPTION>";

					$sql = "SELECT * FROM chapters 
							LEFT JOIN `sm-online`.lodges ON `sm-online`.lodges.lodge_id = chapters.lodge
							ORDER BY lodge_name, chapter_name";
					$chapters = $db->query($sql);
			
					while ($chapter = $chapters->fetchRow()) {
						if ($chapter->lodge_id != $current_lodgedd_id){
							print "</optgroup><optgroup label=\"$chapter->lodge_name\">";
							$current_lodgedd_id = $chapter->lodge_id;
						} 
						print "<option value=\"$chapter->chapter_id\"";
						print ">$chapter->chapter_name - "
									. ($addr_names['chapter'][$chapter->chapter_id]=='' ? 
											"(No addresses stored)" :
											$addr_names['chapter'][$chapter->chapter_id])
									. "</option>";
					}
					print "</optgroup>";

					print "</SELECT><br>(<a href=\"section/edit_stataddresses.php\">Edit stored recipients</a>)</td>
					</tr>
					<tr>
						<td><INPUT TYPE=checkbox NAME=\"send[out_of_section]\" VALUE=\"TRUE\">	</td>
						<td><label style=\"float:left;\"><font id=item>Out-of-Section Report</label>
						<br></font><font id=description>Stored recipients: ".$addr_names['out_of_section']."
						(<a href=\"section/edit_stataddresses.php\">Edit</a>)</font></td>
						</td>
					</tr>

				<tr><td><INPUT TYPE=checkbox NAME=\"send[tradingpost]\" VALUE=\"TRUE\">		</td>
					<td><font id=item>Trading Post Report <br></font><font id=description>Stored recipients: ".$addr_names['tradingpost']."
					(<a href=\"section/edit_stataddresses.php\">Edit</a>)</font></td>
				</tr>

				<tr><td><INPUT TYPE=checkbox NAME=\"send[training]\" VALUE=\"TRUE\">	</td>
					<td><font id=item>Training Report <br></font><font id=description>Stored recipients: ".$addr_names['training']."
					(<a href=\"section/edit_stataddresses.php\">Edit</a>)</font></td>
				</tr>
			
				<tr><td><INPUT TYPE=checkbox NAME=\"send[diet]\" VALUE=\"TRUE\">	</td>
					<td><font id=item>Dietary Needs Report</font></td>
				</tr>
			
				<tr><td><INPUT TYPE=checkbox NAME=\"send[medical]\" VALUE=\"TRUE\">	</td>
					<td><font id=item>Medical Issues Report</font></td>
				</tr>
				
				<tr><td><INPUT TYPE=checkbox NAME=\"send[medical-all]\" VALUE=\"TRUE\">	</td>
					<td><font id=item>Medical Issues &amp; ALL Emergency Contacts Report</font></td>
				</tr>
				
			</table>
		
		<INPUT TYPE=hidden NAME=\"command\" VALUE=\"proceed\">
		<INPUT TYPE=hidden NAME=\"event\" VALUE=\"{$EVENT['id']}\">
		<P><INPUT TYPE=submit VALUE=\"Send Conclave Stat Reports\">

	</FORM>";

require "../post.php";
