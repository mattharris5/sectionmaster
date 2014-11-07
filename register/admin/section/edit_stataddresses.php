<?
$title = "Section > Edit Report Recipients";
require "../pre.php";
$user->checkPermissions("report_summary");

$contact_types = array (
						"summary"		=> "Summary",
						"lodge"			=> "Lodge",
						"chapter"		=> "Chapter",
						"out_of_section"=> "Out-of-Section",
						"tradingpost"	=> "Trading Post",
						"training"		=> "Training",
						);

// delete user if requested
if ($_REQUEST['command'] == 'delete' && $_REQUEST['contact_id'] != '') {
	$del = $db->query("DELETE FROM `contacts` WHERE id = '{$_REQUEST['contact_id']}' LIMIT 1");
		if (DB::isError($del)) dbError("Could not delete contact in $title", $del, "");
}

// add new contact person
if ($_REQUEST['command'] == 'add') {

	// good input
	if ($_REQUEST['name'] != '' && $_REQUEST['email'] != '') {
		$sql = "INSERT INTO `contacts` SET
					name = '{$_REQUEST['name']}',
					email = '{$_REQUEST['email']}',
					contact_type = '{$_REQUEST['contact_type']}'";
					if ($_REQUEST['org_id'] != '') $sql .= ", org_id = '{$_REQUEST['org_id']}'";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Could not add new contact person in $title", $res, $sql);
		$new_contact_id = mysql_insert_id();

		$ERROR['add'] = "<font color=green>{$_REQUEST['name']} added to {$contact_types[$_REQUEST['contact_type']]} report list.</font>";
	}
	
	// bad input -- ERROR
	else {
		$ERROR['add'] = "<font color=red>Please enter values in all fields and try again.</font>";
	}
}

?>

<h1>Edit Report Recipients</h1>
For each type of report listed below, add recipients to receive report e-mails.  Reports must be manually triggered to be sent using the <a href="event/stats.php">Reports E-mailer</a> page.  <b>Please Note:</b> The users listed here for the "Trading Post" report will NOT receive order e-mails for each order.  Use the <a href="event/info.php">Event Prefs</a> page to specify that user.

<p><?=$ERROR['add']?></p>

<table class="default">
<?
	// iterate through each report type
	foreach($contact_types as $type => $caption) {
		print "<tr>";
		print "<td><font id=item>$caption Report(s):</font></td>";
		print "<td><table>
					<tr><th>Name</th><th>E-mail Address</th><th>Detail</th><th>Edit</th></tr>";
		
		// get the current entries in the database
		$contacts = $db->query("SELECT * FROM contacts WHERE contact_type = '$type' ORDER BY org_id, name");
		while ($contact = $contacts->fetchRow()){
			
			// NAME AND EMAIL
			if ($new_contact_id == $contact->id) $myStyle[$contact->id] = "background-color: #fee933;";
			print "<tr style=\"{$myStyle[$contact->id]}\"><td>$contact->name</td>
					   <td>$contact->email</td>"; // display line

			// ORG ID
			if ($type == 'lodge')  // print out lodge name
				print "<td>" . $db->getOne("SELECT lodge_name FROM `sm-online`.lodges WHERE lodge_id = '$contact->org_id'") . "</td>";
			else if ($type == 'chapter')
				print "<td>" . $db->getOne("SELECT chapter_name FROM chapters WHERE chapter_id = '$contact->org_id'") . "</td>";
			else
				print "<td></td>";

			// DELETE button
			print "<td><input class=\"default\" type=button
				  		 onClick=\"if (confirm('Are you sure you want to remove ".addslashes(htmlspecialchars($contact->name))." from the $caption Report list?'))
										window.location='/sectionmaster.org/register/admin/section/edit_stataddresses.php?command=delete&contact_id=$contact->id';\" value=\"Delete\"></tr>";
										
			print "</tr>";			
		}
		
		// ADD A NEW ENTRY
		print "<form action=\"section/edit_stataddresses.php\" class=default>
				<input type=hidden name=\"command\" value=\"add\">
				<input type=hidden name=\"contact_type\" value=\"$type\">
				<tr>
					<td><input name=\"name\"></td>
					<td><input name=\"email\"></td>
					<td>";
					
				// lodge and chapter drop downs
				if ($type == 'lodge') {
					$EVENT 	 = event_info($sm_db, $user->info->current_event);
					$sql = "SELECT lodges.lodge_id, lodges.lodge_name, lodges.section
								FROM `sm-online`.lodges AS lodges WHERE section = '{$EVENT['section_name']}'
								ORDER BY lodge_name ASC";
					$lodges = $db->query($sql);

					print "<select name=\"org_id\">";
						while ($lodge = $lodges->fetchRow()) {
							print "<option value=\"$lodge->lodge_id\"";
							print ">$lodge->lodge_name</option>";
						}
					print "</select>";
				}
				
				else if ($type == 'chapter') {
					$sql = "SELECT * FROM chapters 
							LEFT JOIN `sm-online`.lodges ON `sm-online`.lodges.lodge_id = chapters.lodge
							ORDER BY lodge_name, chapter_name";
					$chapters = $db->query($sql);
					
					print "<select name=\"org_id\">";
						while ($chapter = $chapters->fetchRow()) {
							if ($chapter->lodge_id != $current_lodgedd_id){
								print "</optgroup><optgroup label=\"$chapter->lodge_name\">";
								$current_lodgedd_id = $chapter->lodge_id;
							} 
							print "<option value=\"$chapter->chapter_id\"";
							print ">$chapter->chapter_name</option>";
						}
					print "</optgroup>
					</select>";
				}
				
				else {
					print "</td>";
				}
					
				print "</td>
						<td><input type=submit value='Add'></td>
					</tr>";
		
		print "</table>
			</td></tr></form>";
	
}





?>

