<?

# Get lodge counts
$sql = "SELECT lodge_id, lodge_name, COUNT(lodge) AS count, SUM(youth) AS youth_count, SUM(adult) AS adult_count, SUM(new_ordeal) AS new_ordeal_count, section, IF(section='{$EVENT['section_name']}', 'true', 'false') AS in_section
		FROM ( $all_registered ) AS temp1
		GROUP BY lodge
		ORDER BY in_section DESC, lodge_id='' DESC, section, lodge_name";
$counts = $db->query($sql);
	if (DB::isError($res)) dbError("Could not retrieve lodge stats in $title", $res, $sql);
	//print "<pre>$sql</pre>";
?>

<h3>Currently Registered (By Lodge)</h3>
<!-- TABLE -->

<table class="cart">
	<tr>
		<th colspan=2>Lodge</th>
		<th>Youth</th>
		<th>Adult</th>
		<th>New Ordeal</th>
		<th>Total Count</th>
		<th>Percentage</th>
	</tr>
	<!--SM:plaintext-->Lodge                     Youth  Adult  Ordeal  Total  Percent<!--/SM:plaintext-->
	<!--SM:plaintext-->--------------------------------------------------------------<!--/SM:plaintext-->
	
	<tr id="separator">
		<td colspan=7>In Section <?= $EVENT['section_name'] ?></td>
		<!--SM:plaintext-->In Section <?= $EVENT['section_name'] ?><!--/SM:plaintext-->
	</tr>
	
	<?
	while ($count = $counts->fetchRow()) { $section_print = '';
		if ($count->in_section == 'false') {
			if (!$reached_out_of_section) {
				print "<tr id='separator'><td colspan=7>Out of Section <a href=\"event/reports.php?report=out_of_section\" 
						alt=\"View detailed report for out-of-section registrants\">(Full Out-of-Section Report)</a></td></tr>";
				print "<!--SM:plaintext-->\nOut of Section<!--/SM:plaintext-->";
				$reached_out_of_section = true;
			}
		$section_print = "(" . $count->section . ")";
		}
		
		if ($count->lodge_id == '') {
			print "<tr id='separator'><td colspan=7>Non-Members</td></tr>";
			print "<!--SM:plaintext-->\nNon-Members<!--/SM:plaintext-->";
			$section_print = "";
		}
		

		$percentage = ($count->count / $total_count)*100;
		$percentage_pretty = $percentage > 1.0 ? number_format($percentage) : "< 1";
		$total['youth'] += $count->youth_count;
		$total['adult'] += $count->adult_count;
		$total['new_ordeal'] += $count->new_ordeal_count;

		if ($count->lodge_name == '') {
			$count->lodge_name = "(Non-Members)";
			$count->lodge_id = "non_members";
		}

		print "<tr id=\"link\" onClick=\"window.location='event/reports.php?report=lodge&lodge_id=$count->lodge_id';\">
					<td> </td>
					<td><a href=\"event/reports.php?report=lodge&lodge_id=$count->lodge_id\" 
							alt=\"View detailed report for $count->lodge_name\">$count->lodge_name $section_print</a></td>
							
					<td id='count'>$count->youth_count</td>
					<td id='count'>$count->adult_count</td>
					<td id='count'><a href=\"event/reports.php?report=lodge&lodge_id=$count->lodge_id&new_ordeal=only\" 
							alt=\"View new member report for $count->lodge_name\">$count->new_ordeal_count</a></td>
					<td id='count'>$count->count</td>
					<td id='count'>"; print ($percentage_pretty=='< 1') ? "&lt; 1" : $percentage_pretty; print "%</td>
				</tr>";
		print "<!--SM:plaintext-->  " . str_pad($count->lodge_name, 24) . " "
					. str_pad($count->youth_count, 4, " ", STR_PAD_LEFT)
					. str_pad($count->adult_count, 7, " ", STR_PAD_LEFT)
					. str_pad($count->new_ordeal_count, 8, " ", STR_PAD_LEFT)
					. str_pad($count->count, 7, " ", STR_PAD_LEFT)
					. str_pad($percentage_pretty."%", 9, " ", STR_PAD_LEFT)
					. "<!--/SM:plaintext-->";
	}
	?>
	
	<tr id="total">
		<td colspan=2>TOTALS:</td>
		<td id='count'><?= $total['youth'] ?></td>
		<td id='count'><?= $total['adult'] ?></td>
		<td id='count'><?= $total['new_ordeal'] ?></td>
		<td id='count'><?= $total_count ?></td>
		<td></td>
	</tr>
	<!--SM:plaintext--><!--/SM:plaintext-->
	<!--SM:plaintext-->--------------------------------------------------------------<!--/SM:plaintext-->
	<?  print "<!--SM:plaintext-->" . str_pad("TOTALS:", 26) . " "
				. str_pad($total['youth'], 4, " ", STR_PAD_LEFT)
				. str_pad($total['adult'], 7, " ", STR_PAD_LEFT)
				. str_pad($total['new_ordeal'], 8, " ", STR_PAD_LEFT)
				. str_pad($total_count, 7, " ", STR_PAD_LEFT)
				. "<!--/SM:plaintext-->";
	?>
	
	
</table>

<br>

<!--SM:webonly-->
<!-- CHART -->
<table>
    <tr>
	<td><?= InsertSMChart("event/charts/event_signup_timeline.php?time=" . microtime() . "&event={$EVENT['id']}", 350, 300, '', true) ?></td>
	<td><?= InsertSMChart("event/charts/registrations_by_lodge.php?time=" . microtime() . "&event={$EVENT['id']}", 350, 250, '', true) ?></td>
	</tr>
	<tr>
	<td colspan=2>
		<?= InsertSMChart("event/charts/youth_adult_ratio.php?event={$EVENT['id']}", 250, 150, '', true) ?>
		<?= InsertSMChart("event/charts/new_ordeal_ratio.php?event={$EVENT['id']}", 250, 150, '', true) ?></td>
	</tr>
</table>
<!--/SM:webonly-->