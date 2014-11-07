<?
if (!isset($lodge_id)) $lodge_id = $_REQUEST['lodge_id'];

# Get lodge list
$sql = "(SELECT lodges.lodge_id, lodges.lodge_name, lodges.section
			FROM `sm-online`.lodges AS lodges WHERE section = '{$EVENT['section_name']}'
			)
		UNION 
			(SELECT lodge_id, lodge_name, section FROM ( 
				$all_registered
				GROUP BY members.lodge 
			) AS temp1
		)
		ORDER BY section = '{$EVENT['section_name']}' DESC, section ASC, lodge_name ASC";
$lodges = $db->query($sql);
	if (DB::isError($lodges)) dbError("Could not retrieve lodge list in $title", $lodges, $sql);

# Get list of section counts
$sql = "SELECT lodge_id, lodge_name, section, COUNT(section) AS count
		FROM ( $all_registered ) AS temp1
		WHERE section != '{$EVENT['section_name']}'
		GROUP BY section
		ORDER BY section ASC";
$counts = $db->query($sql);
	if (DB::isError($counts)) dbError("Could not retrieve out-of-section stats in $title", $counts, $sql);

# Get list of out-of-section lodge counts
$sql = "SELECT lodge_id, lodge_name, section, COUNT(lodge_id) AS count
		FROM ( $all_registered ) AS temp1
		WHERE section != '{$EVENT['section_name']}'
		GROUP BY lodge
		ORDER BY lodge ASC";
$counts2 = $db->query($sql);
	if (DB::isError($counts)) dbError("Could not retrieve out-of-section stats in $title", $counts, $sql);



?>

<!--SM:webonly-->
<!-- CHART -->
<!--<div style="float:right;">
	<?= InsertSMChart("event/charts/event_signup_timeline.php?time=" . microtime() . "&event={$EVENT['id']}", 400, 300, '', true) ?>
	<br>
	<?= InsertSMChart("event/charts/registrations_by_lodge.php?time=" . microtime() . "&event={$EVENT['id']}", 400, 250, '', true) ?>
</div>-->
<!--/SM:webonly-->

<h3>Out of Section Registrants</h3>

<!-- TABLE -->
<table class="cart" style="width:50%;">
	
	<tr>
		<th colspan=2>Section</th>
		<th>Count</th>
		<th>Percentage</th>
	</tr>
	<!--SM:plaintext-->Section                                    Count     Percentage<!--/SM:plaintext-->
	<!--SM:plaintext-->--------------------------------------------------------------<!--/SM:plaintext-->
	
	<?
	while ($count = $counts->fetchRow()) {

		$running_total += $count->count;
		$percentage = ($count->count / $total_count)*100;
		$percentage_pretty = $percentage > 1.0 ? number_format($percentage) : "< 1";

		print "<tr><td colspan=2>Section $count->section</a></td>
					<td id='count'>$count->count</td>
					<td id='count'>$percentage_pretty%</td>
				</tr>";
		print "<!--SM:plaintext-->" . str_pad($count->section, 35)
									. str_pad($count->count, 10, " ", STR_PAD_LEFT)
									. str_pad($percentage_pretty."%", 10, " ", STR_PAD_LEFT)
			. "<!--/SM:plaintext-->";
	}
	?>
	
	<tr id="total">
		<td colspan=3>TOTAL OUT-OF-SECTION:</td>
		<td id='count'><?= $running_total ?></td>
	</tr>
	<!--SM:plaintext--><!--/SM:plaintext-->
	<!--SM:plaintext-->--------------------------------------------------------------<!--/SM:plaintext-->
	<?  print "<!--SM:plaintext-->" . str_pad("TOTAL OUT-OF-SECTION:", 35) . " "
									. str_pad($running_total, 10, " ", STR_PAD_LEFT)
									. "<!--/SM:plaintext-->"; ?>
	
	
</table>


<table class="cart" style="width:50%;">
	
	<tr>
		<th colspan=2>Lodge</th>
		<th>Count</th>
		<th>Percentage</th>
	</tr>
	<!--SM:plaintext--><!--/SM:plaintext-->
	<!--SM:plaintext-->Lodge                                     Count     Percentage<!--/SM:plaintext-->
	<!--SM:plaintext-->--------------------------------------------------------------<!--/SM:plaintext-->
	
	<?
	$running_total = 0;
	while ($count = $counts2->fetchRow()) {

		$running_total += $count->count;
		$percentage = ($count->count / $total_count)*100;
		$percentage_pretty = $percentage > 1.0 ? number_format($percentage) : "< 1";

		print "<tr class=\"separator\">
					<td colspan=2>$count->lodge_name ($count->section)</a></td>
					<td id='count'>$count->count</td>
					<td id='count'>$percentage_pretty%</td>
				</tr>";

		print "<!--SM:plaintext-->" . str_pad($count->lodge_name, 35)
									. str_pad($count->count, 10, " ", STR_PAD_LEFT)
									. str_pad($percentage_pretty."%", 10, " ", STR_PAD_LEFT)
									. "<!--/SM:plaintext-->";
		
			
		// print out members for this chapter
		$sql = "SELECT *
				FROM ( $all_registered ) AS temp1
				WHERE lodge = '$count->lodge_id'
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
		<td colspan=3>TOTAL OUT-OF-SECTION:</td>
		<td id='count'><?= $running_total ?></td>
	</tr>
	<!--SM:plaintext-->--------------------------------------------------------------<!--/SM:plaintext-->
	<?  print "<!--SM:plaintext-->" . str_pad("TOTAL OUT-OF-SECTION:", 35) . " "
									. str_pad($running_total, 10, " ", STR_PAD_LEFT)
									. "<!--/SM:plaintext-->"; ?>

	
</table>
