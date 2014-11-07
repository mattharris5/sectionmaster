<?
if (!isset($housing_id)) $housing_id = $_REQUEST['housing_id'];

# Get chapter list
$sql = "SELECT * FROM housing 
		ORDER BY housing_name";
$housing = $db->query($sql);
	if (DB::isError($housing)) dbError("Could not retrieve housing location list in $title", $housing, $sql);
	
# Get this housing location's info
$sql = "SELECT * FROM housing 
		WHERE housing_id = '{$housing_id}'";
$this_housing = $db->getRow($sql);
	if (DB::isError($this_housing)) dbError("Could not retrieve this_housing info in $title", $this_housing, $sql);

# Get member list
$sql = "SELECT *
		FROM ( $all_registered ) AS temp1
		WHERE housing_id = '$this_housing->housing_id'
		ORDER BY lastname, firstname";
$members = $db->query($sql);
	if (DB::isError($members)) dbError("Could not retrieve member list in $title", $members, $sql);


?>

<!--SM:webonly-->
<!-- Chapter Selection Dropdown -->
<a href="event/housing/locations.php">&lt;&lt; Back to Housing Location Summary</a>
<br>
<br>
<form action="event/reports.php" class="default">
	<select name="housing_id" onChange="this.form.submit();" class="default">
		<? if (!$housing_id) print "<option value=\"\">-- Select a Housing Location --</option>"; ?>
		<? 
		while ($house = $housing->fetchRow()) {
			print "<option value=\"$house->housing_id\"";
			if ($housing_id == $house->housing_id) print " selected";
			print ">$house->housing_name</option>";
		}
		?>
		</optgroup>
	</select>
	<input type="hidden" name="report" value="housing">
</form>
<br>
<!--/SM:webonly-->
<? if (!$housing_id) return; ?>

<!--SM:webonly-->
<!-- CHART -->
<!--<div style="float:right;">
	<?= InsertSMChart("event/charts/event_signup_timeline.php?time=" . microtime() . "&event={$EVENT['id']}", 400, 300, '', true) ?>
	<br>
	<?= InsertSMChart("event/charts/registrations_by_lodge.php?time=" . microtime() . "&event={$EVENT['id']}", 400, 250, '', true) ?>
</div>-->
<!--/SM:webonly-->

<h3><?= $this_housing->housing_name ?></h3>
<?=$this_housing->housing_description?><br>
Capacity: <?=$this_housing->housing_capacity?><br><br>
	
<!-- TABLE -->
<table class="cart" style="width:90%;">
	
	<tr>
		<th>Name</th>
		<th><center>Gender</center></th>
		<th><center>Age at Event</center></th>
		<th>Chapter/Lodge</th>
		<th>Register Date</th>
	</tr>
	
	<?
	// print out members for this chapter
	while ($member = $members->fetchRow()) {
		print "<tr id=\"link\" onClick=\"window.location='section/members.php?member_id=$member->member_id';\">
				<td><a href=\"section/members.php?member_id=$member->member_id\"
					alt=\"View member details for $member->firstname $member->lastname\">
					$member->firstname $member->lastname</a></td>
				<td><center>$member->gender</center></td>
				<td><center>$member->age_bracket</center></td>
				<td>$member->chapter_name / $member->lodge_name</td>
				<td>$member->reg_date</td>
			</tr>";
		$count++;
	}
	?>
	
	<tr id="total">
		<td colspan=4>TOTAL ASSIGNED:</td>
		<td id='count'><?= $count ?></td>
	</tr>
	
</table>
