<?
$title = "Switch ID Numbers";
require "../pre.php";
$user->checkPermissions("switch_id");

import_request_variables('P');

if (isset($submit))
{
	if ((is_numeric($id1)) and (is_numeric($id2)) and ($id1 != $id2))
	{
		if (($id1 != 1) and ($id2 != 1))
		{
			// id1 gets updated to 999999999
			$sql_auction1 = $db->query("UPDATE auction_items SET buyer_id=999999999 WHERE buyer_id=$id1;");
			$sql_auction2 = $db->query("UPDATE auction_items SET donor_id=999999999 WHERE donor_id=$id1;");
			$sql_conclave_attendance = $db->query("UPDATE conclave_attendance SET member_id=999999999 WHERE member_id=$id1;");
			$sql_evaluation_responses = $db->query("UPDATE evaluation_responses SET member_id=999999999 WHERE member_id=$id1;");
			$sql_members = $db->query("UPDATE members SET member_id=999999999 WHERE member_id=$id1;");
			$sql_orders = $db->query("UPDATE orders SET member_id=999999999 WHERE member_id=$id1;");
			$sql_secret_questions = $db->query("UPDATE secret_questions SET member_id=999999999 WHERE member_id=$id1;");
			$sql_training_attendance = $db->query("UPDATE training_attendance SET member_id=999999999 WHERE member_id=$id1;");
			$sql_smonlineusers = $db->query("UPDATE `sm-online`.users SET member_id=999999999 WHERE member_id=$id1 AND section='".$user->info->section."';");
				
			// id2 gets updated to orignal id1
			$sql_auction1 = $db->query("UPDATE auction_items SET buyer_id=$id1 WHERE buyer_id=$id2;");
			$sql_auction2 = $db->query("UPDATE auction_items SET donor_id=$id1 WHERE donor_id=$id2;");
			$sql_conclave_attendance = $db->query("UPDATE conclave_attendance SET member_id=$id1 WHERE member_id=$id2;");
			$sql_evaluation_responses = $db->query("UPDATE evaluation_responses SET member_id=$id1 WHERE member_id=$id2;");
			$sql_members = $db->query("UPDATE members SET member_id=$id1 WHERE member_id=$id2;");
			$sql_orders = $db->query("UPDATE orders SET member_id=$id1 WHERE member_id=$id2;");
			$sql_secret_questions = $db->query("UPDATE secret_questions SET member_id=$id1 WHERE member_id=$id2;");
			$sql_training_attendance = $db->query("UPDATE training_attendance SET member_id=$id1 WHERE member_id=$id2;");
			$sql_smonlineusers = $db->query("UPDATE `sm-online`.users SET member_id=$id1 WHERE member_id=$id2 AND section='".$user->info->section."';");
				
			// id1 (999999999) gets updated to id2
			$sql_auction1 = $db->query("UPDATE auction_items SET buyer_id=$id2 WHERE buyer_id=999999999;");
			$sql_auction2 = $db->query("UPDATE auction_items SET donor_id=$id2 WHERE donor_id=999999999;");
			$sql_conclave_attendance = $db->query("UPDATE conclave_attendance SET member_id=$id2 WHERE member_id=999999999;");
			$sql_evaluation_responses = $db->query("UPDATE evaluation_responses SET member_id=$id2 WHERE member_id=999999999;");
			$sql_members = $db->query("UPDATE members SET member_id=$id2 WHERE member_id=999999999;");
			$sql_orders = $db->query("UPDATE orders SET member_id=$id2 WHERE member_id=999999999;");
			$sql_secret_questions = $db->query("UPDATE secret_questions SET member_id=$id2 WHERE member_id=999999999;");
			$sql_training_attendance = $db->query("UPDATE training_attendance SET member_id=$id2 WHERE member_id=999999999;");
			$sql_smonlineusers = $db->query("UPDATE `sm-online`.users SET member_id=$id2 WHERE member_id=999999999 AND section='".$user->info->section."';");
			
			// member table autoincrement reset
			$autoincrement = $db->query("SELECT MAX(member_id)+1 AS nextauto FROM members");
			$next_autoincrement = $autoincrement->fetchRow();
			$nextauto = $next_autoincrement->nextauto;
			$sql = "ALTER TABLE members AUTO_INCREMENT = ". $nextauto;
			$db->query($sql);
			
			// get the final results
			$member1 = $db->query("SELECT firstname, lastname, member_id FROM members WHERE member_id=$id1");
			$m1 = $member1->fetchRow();
			$member2 = $db->query("SELECT firstname, lastname, member_id FROM members WHERE member_id=$id2");
			$m2 = $member2->fetchRow();
		
			// done!
			$error = "<div class='info_box'><h3>The ID #s have been successfully switched!<br><br>
			Results of this change:";
			if ($m1->member_id != '') $error .= "<br>&bull; <a href='section/members.php?member_id=$m1->member_id'>ID # $m1->member_id: $m1->firstname $m1->lastname</a>";
			if ($m2->member_id != '') $error .= "<br>&bull; <a href='section/members.php?member_id=$m2->member_id'>ID # $m2->member_id: $m2->firstname $m2->lastname</a>";
			$error .= "</h3></div>";
		}
		else $error = "<div class='error_summary'><h3>Sorry, ID #1 is a system reserved number and cannot be changed using this tool.</h3></div>";
	}
	else $error = "<div class='error_summary'><h3>Both ID numbers must be numeric entries and may not be the same.</h3></div>";
}

?>
<h1>Switch Member ID Numbers</h1>

<?=$error?>

<p>Enter the two ID numbers you would like to swap records for.  You may enter an ID number which does not exist at all and it will be assigned to the other ID number you enter.  Note that ALL records associated with a member ID will be affected, for the current and all past events and orders, in the following tables:</p>
<ul>
	<li>auction_items (buyer_id and donor_id)</li>
	<li>conclave_attendance (member_id)</li>
	<li>evaluation_responses (member_id)</li>
	<li>members (member_id)</li>
	<li>orders (member_id)</li>
	<li>secret_questions (member_id)</li>
	<li>training_attendance (member_id)</li>
	<li>sm-online.users (member_id)</li>
</ul>
<br><br>
<form action="other/switchid.php" method="post">
	<label>First ID #:</label><input type="text" name="id1" style="width: 50px;"><br>
	<label>Second ID #:</label><input type="text" name="id2" style="width: 50px;"><br><br>
	<label></label><input type="submit" name="submit" value="Submit" style="width: 100px;">	
</form>

<?
require "../post.php";
?>

