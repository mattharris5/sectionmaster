<?
# Get member list
$sql = "SELECT *
		FROM ( $all_registered ) AS temp1
		WHERE !(special_diet_explain = 'None' 
			OR special_diet_explain = 'NONE'
			OR special_diet_explain = 'none'
			OR special_diet_explain = 'N/A'
			OR special_diet_explain = 'n/a'
			OR special_diet_explain = '')
		ORDER BY lastname, firstname";

$members = $db->query($sql);
	if (DB::isError($members)) dbError("Could not retrieve member list in $title", $members, $sql);


?>

<h3>Dietary Needs of Registered Participants</h3>

<!-- TABLE -->
<table class="cart" style="width:90%;">
	
	<tr>
		<th>Name</th>
		<th>E-mail</th>
		<th>Phone</th>
		<th>Special Diet Details</th>
	</tr>
	
	<?
	// print out members for this chapter
	while ($member = $members->fetchRow()) {
		print "<tr id=\"link\" onClick=\"window.location='section/members.php?member_id=$member->member_id';\">
				<td><a href=\"section/members.php?member_id=$member->member_id\"
					alt=\"View member details for $member->firstname $member->lastname\">
					$member->firstname $member->lastname</a></td>
				<td>$member->email</td>
				<td>$member->primary_phone</td>
				<td>$member->special_diet_explain</td>
			</tr>";
		$count++;
	}
	?>
	
	<tr id="total">
		<td colspan=3>TOTAL SPECIAL DIET PARTICIPANTS:</td>
		<td id='count'><?= $count ?></td>
	</tr>
	
</table>

<? unset($count); ?>