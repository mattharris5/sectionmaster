<?
if (!isset($chapter_id)) $chapter_id = $_REQUEST['chapter_id'];

# Get chapter list
$sql = "SELECT * FROM chapters 
		LEFT JOIN `sm-online`.lodges ON `sm-online`.lodges.lodge_id = chapters.lodge
		WHERE `sm-online`.lodges.section = '{$EVENT['section_name']}'
		ORDER BY lodge_name, chapter_name";
$chapters = $db->query($sql);
	if (DB::isError($chapters)) dbError("Could not retrieve chapter list in $title", $chapters, $sql);

# Get this chapter's info
$sql = "SELECT * FROM chapters 
		LEFT JOIN `sm-online`.lodges ON `sm-online`.lodges.lodge_id = chapters.lodge 
		WHERE chapter_id = '{$chapter_id}'";
$this_chapter = $db->getRow($sql);
	if (DB::isError($this_chapter)) dbError("Could not retrieve this_chapter info in $title", $this_chapter, $sql);

# Get chapter count
$sql = "SELECT COUNT(chapter) AS count
		FROM ( $all_registered ) AS temp1
		WHERE chapter = '$this_chapter->chapter_id'";
$count = $db->getOne($sql);
	if (DB::isError($count)) dbError("Could not retrieve chapter count in $title", $count, $sql);
	
# Get member list
$sql = "SELECT *
		FROM ( $all_registered ) AS temp1
		WHERE chapter = '$this_chapter->chapter_id'
		AND lodge = '$this_chapter->lodge_id'";
$members = $db->query($sql);
	if (DB::isError($members)) dbError("Could not retrieve chapter list in $title", $members, $sql);

?>

<!--SM:webonly-->
<!-- Chapter Selection Dropdown -->
<br>
<form action="event/reports.php" class="default">
	<select name="chapter_id" onChange="this.form.submit();" class="default">
		<? if (!$chapter_id) print "<option value=\"\">-- Select a Chapter --</option>"; ?>
		<? 
		while ($chapter = $chapters->fetchRow()) {
			if ($chapter->lodge_id != $current_lodgedd_id){
				print "</optgroup><optgroup label=\"$chapter->lodge_name\">";
				$current_lodgedd_id = $chapter->lodge_id;
			} 
			print "<option value=\"$chapter->chapter_id\"";
			if ($chapter_id == $chapter->chapter_id) print " selected";
			print ">$chapter->chapter_name</option>";
		}
		?>
		</optgroup>
	</select>
	<input type="hidden" name="report" value="chapter">
</form>
<br>
<!--/SM:webonly-->
<? if (!$chapter_id) return; ?>

<!--SM:webonly-->
<!-- CHART -->
<!--<div style="float:right;">
	<?= InsertSMChart("event/charts/event_signup_timeline.php?time=" . microtime() . "&event={$EVENT['id']}", 400, 300, '', true) ?>
	<br>
	<?= InsertSMChart("event/charts/registrations_by_lodge.php?time=" . microtime() . "&event={$EVENT['id']}", 400, 250, '', true) ?>
</div>-->
<!--/SM:webonly-->

<h3><?= $this_chapter->chapter_name ?> Chapter</h3>
<ul><li>Lodge: <?= $this_chapter->lodge_name ?>
	<li>Chapter Chief: <?= $this_chapter->chapter_chief ?>
	<li>Active Membership: <?= $this_chapter->active_membership ?>
	<li><b>Currently Registered: <?= $count ?></b> 
			<? if ($count > 0) {
				print "(";
				if ($this_chapter->active_membership > 0) 
					print number_format(($count/$this_chapter->active_membership)*100) . "% of Active Membership,";
				print number_format(($count/$total_count)*100) . "% of Total Registered for " . $EVENT['formal_event_name'] . ")";
			}
			?>
</ul>

<!-- TABLE -->
<table class="cart" style="width:90%;">
	
	<tr>
		<th>Name</th>
		<th>E-mail</th>
		<th>Phone</th>
		<th>Ordeal Date</th>
		<th>Register Date</th>
	</tr>
	
	<?
	// print out members for this chapter
	while ($member = $members->fetchRow()) {
		print "<tr id=\"link\" onClick=\"window.location='section/members.php?member_id=$member->member_id';\">
				<td><a href=\"section/members.php?member_id=$member->member_id\"
					alt=\"View member details for $member->firstname $member->lastname\">
					$member->firstname $member->lastname</a>";
					if ($member->staff_classification != 'Participant') print " <font color='red'>($member->staff_classification)</font>";
					print "</td>
				<td>$member->email</td>
				<td>$member->primary_phone</td>
				<td>".date('m-d-Y',strtotime($member->ordeal_date)); print $member->new_ordeal==1 ? " (New Member)" : ""; print "</td>
				<td>$member->reg_date</td>
			</tr>";
	}
	?>
	
	<tr id="total">
		<td colspan=4>TOTAL REGISTERED:</td>
		<td id='count'><?= $count ?></td>
	</tr>
	
</table>
