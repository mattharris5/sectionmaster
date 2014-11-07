<?
if (!isset($lodge_id)) $lodge_id = $_REQUEST['lodge_id'];

# Get lodge list
$sql = "(SELECT lodges.lodge_id, lodges.lodge_name, lodges.section
			FROM `sm-online`.lodges AS lodges WHERE section = '{$EVENT['section_name']}'
			)
		UNION 
			(SELECT lodge, lodge_name, section FROM ( 
				$all_registered
				GROUP BY members.lodge 
			) AS temp1
		)
		ORDER BY section = '{$EVENT['section_name']}' DESC, section ASC, lodge_name ASC";
$lodges = $db->query($sql);
	if (DB::isError($lodges)) dbError("Could not retrieve lodge list in $title", $lodges, $sql);

# Get chapter counts
$sql = "SELECT chapter_id, chapter_name, lodge_id, lodge_name, COUNT(chapter) AS count
		FROM ( $all_registered ) AS temp1
		WHERE lodge = '{$lodge_id}'
		GROUP BY chapter
		ORDER BY count DESC";
$counts = $db->query($sql);
$counts2 = $db->query($sql);
	if (DB::isError($counts)) dbError("Could not retrieve chapter stats in $title", $counts, $sql);
	
# Do different things if requesting non-members only
if ($lodge_id == 'non_members') {
	$sql = "SELECT chapter_id, chapter_name, lodge_id, lodge_name, COUNT(chapter) AS count
			FROM ( $all_registered ) AS temp1
			WHERE lodge = ''
			GROUP BY chapter
			ORDER BY count DESC";
	$counts = $db->query($sql);
	$counts2 = $db->query($sql);
		if (DB::isError($counts)) dbError("Could not retrieve non-member chapter stats in $title", $counts, $sql);
}

?>

<!--SM:webonly-->
<!-- Lodge Selection Dropdown -->
<br>
<form action="event/reports.php" class="default">
	<label for="lodge_id" style="float:left;">Select a Lodge: </label>
	<select name="lodge_id" onChange="this.form.submit();" class="default">
		<? if (!$lodge_id) print "<option value=\"\">-- Select a Lodge --</option>"; ?>
		<optgroup label="Our Section (<?=$EVENT['section_name']?>)">
		<? 
		while ($lodge = $lodges->fetchRow()) {
			if ($lodge->section != $EVENT['section_name'] && !$optgroup_printed){
				print "</optgroup><optgroup label=\"Other Sections\">";
				$optgroup_printed = true;
			} 
			print "<option value=\"$lodge->lodge_id\"";
			if ($lodge_id == $lodge->lodge_id) print " selected";
			print ">$lodge->lodge_name</option>";
		}
		?>
		</optgroup>
		<optgroup label="Non-members">
			<option value="non_members" <?= $lodge_id=='non_members' ? "selected" : "" ?>>Non-members</option>
		</optgroup>		
	</select>
	<input type="hidden" name="report" value="lodge">
</form>
<br>
<!--/SM:webonly-->
<? if (!$lodge_id) return; ?>

<!--SM:webonly-->
<!-- CHART -->
<!--<div style="float:right;">
	<?= InsertSMChart("event/charts/event_signup_timeline.php?time=" . microtime() . "&event={$EVENT['id']}", 400, 300, '', true) ?>
	<br>
	<?= InsertSMChart("event/charts/registrations_by_lodge.php?time=" . microtime() . "&event={$EVENT['id']}", 400, 250, '', true) ?>
</div>-->
<!--/SM:webonly-->

<h3><?= ($lodge_id == 'non_members') ? ("Non-Members") : ($db->getOne("SELECT lodge_name FROM `sm-online`.lodges WHERE lodge_id = '{$lodge_id}'") . " Lodge") ?></h3>
<!--SM:plaintext--><?= $db->getOne("SELECT lodge_name FROM `sm-online`.lodges WHERE lodge_id = '{$lodge_id}'") ?> Lodge
<!--/SM:plaintext-->

<!-- TABLE -->
<table class="cart" style="width:50%;">
	
	<tr>
		<th colspan=2>Chapter</th>
		<th>Count</th>
		<th>Percentage</th>
	</tr>
	<!--SM:plaintext-->Chapter                                   Count     Percentage<!--/SM:plaintext-->
	<!--SM:plaintext-->--------------------------------------------------------------<!--/SM:plaintext-->
	
	<?
	$running_total=0;
	while ($count = $counts->fetchRow()) {

		$running_total += $count->count;
		$percentage = ($count->count / $total_count)*100;
		$percentage_pretty = $percentage > 1.0 ? number_format($percentage) : "< 1";

		if ($count->chapter_id == 0) {
			print "<tr><td colspan=2><i>No Chapter</i></td>
						<td id='count'>$count->count</td>
						<td id='count'>$percentage_pretty%</td>
					</tr>";
		} else {
			print "<tr id=\"link\"
						onClick=\"window.location='event/reports.php?report=chapter&chapter_id=$count->chapter_id';\">
						<td colspan=2><a href=\"event/reports.php?report=chapter&chapter_id=$count->chapter_id\" 
								alt=\"View detailed report for $count->chapter_name\">$count->chapter_name</a></td>
						<td id='count'>$count->count</td>
						<td id='count'>$percentage_pretty%</td>
					</tr>";
		}
		print "<!--SM:plaintext-->" . str_pad($count->chapter_name, 35)
									. str_pad($count->count, 10, " ", STR_PAD_LEFT)
									. str_pad($percentage_pretty."%", 10, " ", STR_PAD_LEFT)
			. "<!--/SM:plaintext-->";
	}
	?>
	
	<tr id="total">
		<td colspan=3>TOTAL REGISTERED:</td>
		<td id='count'><?= $running_total ?></td>
	</tr>
	<!--SM:plaintext--><!--/SM:plaintext-->
	<!--SM:plaintext-->--------------------------------------------------------------<!--/SM:plaintext-->
	<?  print "<!--SM:plaintext-->" . str_pad("TOTAL REGISTERED:", 35) . " "
									. str_pad($running_total, 10, " ", STR_PAD_LEFT)
									. "<!--/SM:plaintext-->"; ?>
	
	
</table>

<table class="cart" style="width:50%;">
	
	<tr>
		<th colspan=2>Chapter</th>
		<th>Count</th>
		<th>Percentage</th>
	</tr>
	<!--SM:plaintext--><!--/SM:plaintext-->
	<!--SM:plaintext-->Chapter                                   Count     Percentage<!--/SM:plaintext-->
	<!--SM:plaintext-->--------------------------------------------------------------<!--/SM:plaintext-->
	
	<?
	$running_total = 0;
	while ($count = $counts2->fetchRow()) {

		$running_total += $count->count;
		$percentage = ($count->count / $total_count)*100;
		$percentage_pretty = $percentage > 1.0 ? number_format($percentage) : "< 1";

		if ($count->chapter_id == 0) {
			print "<tr id=\"separator\"><td colspan=2><i>No Chapter</i></td>
						<td id='count'>$count->count</td>
						<td id='count'>$percentage_pretty%</td>
					</tr>";
		} else {
			print "<tr id=\"link\" class=\"separator\"
						onClick=\"window.location='event/reports.php?report=chapter&chapter_id=$count->chapter_id';\">
						<td colspan=2><a href=\"event/reports.php?report=chapter&chapter_id=$count->chapter_id\" 
								alt=\"View detailed report for $count->chapter_name\">$count->chapter_name</a></td>
						<td id='count'>$count->count</td>
						<td id='count'>$percentage_pretty%</td>
					</tr>";
		}
		print "<!--SM:plaintext-->" . str_pad($count->chapter_name, 35)
									. str_pad($count->count, 10, " ", STR_PAD_LEFT)
									. str_pad($percentage_pretty."%", 10, " ", STR_PAD_LEFT)
									. "<!--/SM:plaintext-->";
		
			
		// print out members for this chapter
		$sql = "SELECT *
				FROM ( $all_registered ) AS temp1
				WHERE chapter = '$count->chapter_id'
					AND lodge = '$count->lodge_id'
				ORDER BY lastname, firstname";
		$members = $db->query($sql);
			if (DB::isError($members)) dbError("Could not retrieve member names for chapter $count->chapter_id in $title", $members, $sql);

		while ($member = $members->fetchRow()) {
			print "<tr class=\"sub1\" id=\"link\"
					onClick=\"window.location='section/members.php?member_id=$member->member_id';\">
					<td> </td>
					<td><a href=\"section/members.php?member_id=$member->member_id\"
						alt=\"View member details for $member->firstname $member->lastname\">
						$member->firstname $member->lastname</a>";
						if ($member->staff_classification != 'Participant') print " <font color='red'>($member->staff_classification)</font>";
						print "</td>
					<td colspan=2> </td>
				</tr>";
			print "<!--SM:plaintext-->  $member->firstname $member->lastname";
			if ($member->staff_classification != 'Participant') print " ($member->staff_classification)";
			print "<!--/SM:plaintext-->";
			
		}
		print "<!--SM:plaintext--><!--/SM:plaintext-->";
		
	}
	?>
	
	<tr id="total">
		<td colspan=3>TOTAL REGISTERED:</td>
		<td id='count'><?= $running_total ?></td>
	</tr>
	<!--SM:plaintext-->--------------------------------------------------------------<!--/SM:plaintext-->
	<?  print "<!--SM:plaintext-->" . str_pad("TOTAL REGISTERED:", 35) . " "
									. str_pad($running_total, 10, " ", STR_PAD_LEFT)
									. "<!--/SM:plaintext-->"; ?>

	
</table>
