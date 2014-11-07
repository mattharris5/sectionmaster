<?
$no_header = true;
require "../pre.php";
$user->checkPermissions("members", 1);
$EVENT 	 = event_info($sm_db, $user->info->current_event);

/** Handle an AJAX request instead of a direct request */
if (isset($_REQUEST['autocomplete_entry'])) {
	$sql = "SELECT * FROM members
			LEFT JOIN conclave_attendance USING (member_id)
			LEFT JOIN chapters ON members.chapter = chapters.chapter_id
			LEFT JOIN `sm-online`.lodges ON members.lodge = `sm-online`.lodges.lodge_id ";
			
		// if ajax returns a number, assume we're looking up the member_id
		if (is_numeric($_REQUEST['autocomplete_entry'])) {
			$sql .= " WHERE member_id LIKE '{$_REQUEST['autocomplete_entry']}%'";
		}
		
		// otherwise, search for the name
		else {
			$sql .= " WHERE (firstname LIKE '%{$_REQUEST['autocomplete_entry']}%'
					 		OR lastname LIKE '%{$_REQUEST['autocomplete_entry']}%')
						OR  (CONCAT_WS(' ', firstname, lastname) LIKE '%{$_REQUEST['autocomplete_entry']}%')
						OR  (CONCAT_WS(' ',lastname, firstname) LIKE '%{$_REQUEST['autocomplete_entry']}%')";
		}
		
		$sql .= " ORDER BY members.member_id ASC, conclave_year DESC";
		
	// do the lookup	
	$members = $db->query($sql);
		if (DB::isError($members)) dbError("Problem searching for members in find_member.php during autocomplete", $members, $sql);
	
	// return the list for the autocompleter
	print "<ul>";
	while ($member = $members->fetchRow()) { 
		if ($x > 10) { print "<p id='more'>More records are available...</p>"; break; }
		elseif ($member->member_id != $current_id) {  // only print the row if this is a new member in the list
			$current_id = $member->member_id;
			$x++;
			print "<li id=\"$member->member_id\">
						<span class='name'>$member->firstname $member->lastname</span>
						<span class='id'> (ID #$member->member_id)</span>
						<br>
						<span class='lodge'>$member->chapter_name Chapter, $member->lodge_name Lodge</span>";
					
			if ($member->registration_complete==0 && $member->conclave_year==$EVENT['year']) 
				print "<br><font color=red>".$EVENT['year']." Registration 
						Incomplete: Last attempt on ".date('m/d/Y', strtotime($member->reg_date))."</font>";
			elseif ($member->registration_complete==1 && $member->conclave_year==$EVENT['year']) 
				print "<br><strong><font color=green>Registered for ".$EVENT['year']."</font></strong>";
			
			print "</li>";
		}
	}
	print "</ul>";

	// exit out since we don't need to render other output
	exit;
	
}


if ($_REQUEST['submitted']) {
	$sql = "SELECT * FROM members
			LEFT JOIN chapters ON members.chapter = chapters.chapter_id
			LEFT JOIN `sm-online`.lodges ON members.lodge = `sm-online`.lodges.lodge_id
			WHERE ";
		if ($_REQUEST['firstname'] != '' || $_REQUEST['lastname'] != '') {
			$sql .= "(firstname LIKE '%{$_REQUEST['firstname']}%'
					 AND lastname LIKE '%{$_REQUEST['lastname']}%')";
		} else {
			$sql .= "member_id = '{$_REQUEST['member_id']}'";
		}
	$members = $db->query($sql);
		if (DB::isError($members)) dbError("Problem searching for members in find_member.php", $members, $sql);
	$num_results = $members->numRows();
}

?>

<html>
<head>
	<title>Find a Member</title>
	<base href="https://<?=$_GLOBALS['secure_base_href']?>/register/admin/">
	<link rel="stylesheet" type="text/css" href="admin.css">
	
	<script language="JavaScript">
	function updateAndGo(memberid) {
		opener.document.getElementById('member_id').value = memberid;
		var do_not_redirect = opener.document.getElementById('do_not_redirect').value;
		if (do_not_redirect != "true") {
			opener.document.forms[0].submit();
		}
		window.close();
	}	
	</script>
	
</head>

<body style="padding: 10px;" onLoad="document.getElementById('member_id').select();">
	
	<? if ($num_results === 0): ?>
	<h2>Find a Member</h2>
	<div class="error_summary"><h4>No Results Found</h4>
		Sorry, but no members could be found that matched your query.  Please try again.</div>
		
	<? elseif ($num_results > 0): ?>
	<h2>Results</h2>
		<table class="cart" style="width: 100%;">
		<tr><th>ID</th><th>Name</th><th>Chapter</th><th>Lodge</th><th>Choose</th></tr>
		<? while ($member = $members->fetchRow()) {
			print "<tr>";
			print "<td>$member->member_id</td>";
			print "<td>$member->firstname $member->lastname</td>";
			print "<td>$member->chapter_name</td>";
			print "<td>$member->lodge_name</td>";
			print "<td><input class=\"default\" type=button value=\"Select\"
					onClick=\"updateAndGo($member->member_id);\">
					</td>";
			print "</tr>";
		}
		?>
		</table><br />

	<? else: ?>
		<h2>Find a Member</h2>
	<? endif; ?>
	
	<form action="section/find_member.php" method=get>

		<fieldset class="default">
		<legend>Search Criteria</legend>

			<label for="member_id">Member ID:</label>
			<input name="member_id" id="member_id" value="<?= $_REQUEST['member_id'] ?>"><br />

			<label for="firstname">First Name:</label>
			<input name="firstname" id="firstname" value="<?= $_REQUEST['firstname'] ?>"><br />

			<label for="lastname">Last Name:</label>
			<input name="lastname" id="lastname" value="<?= $_REQUEST['lastname'] ?>"><br />

			<input type="submit" id="submit_button" name="submitted" value="Search">

		</fieldset>

	</form>
	
</body>
</html>