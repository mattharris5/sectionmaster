<?
$title = "Housing > Locations";
require "../../pre.php";
$user->checkPermissions("members");

$EVENT 	 = event_info($sm_db, $user->info->current_event);

// add new location
if ($_REQUEST['submitted'] && $_REQUEST['housing_name'] != '') {
	$sql = "INSERT INTO housing SET housing_name = '{$_REQUEST['housing_name']}'";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not add new housing location in $title", $res, $sql);
	redirect("event/housing/locations", "added=" . urlencode($_REQUEST['housing_name']));
}

// update locations
if ($_REQUEST['submitted']) {
	foreach($_REQUEST['housing_id'] as $id => $location_name) {
		$q = "UPDATE housing SET housing_name = '$location_name', housing_description='{$_REQUEST['housing_description'][$id]}', housing_capacity='{$_REQUEST['housing_capacity'][$id]}' WHERE housing_id = '$id'";
		//print "<pre>$q</pre>";
		$res = $db->query($q);
			if (DB::isError($res)) dbError("Could not update location in $title", $res, "");
	}
	print '<div class="info_box"><h2>Housing location changes saved!</h2></div>';
}

// delete location
if ($_REQUEST['command'] == 'delete' && $_REQUEST['housing_id']) {
	$sql = "DELETE FROM housing WHERE housing_id = '{$_REQUEST['housing_id']}' LIMIT 1";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not delete location in $title", $res, "");
	redirect("event/housing/locations");
}

// delete ALL locations
if ($_REQUEST['command'] == 'delete_all') {
	$sql = "DELETE FROM housing";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not delete all locations in $title", $res, "");
	redirect("event/housing/locations");
}

// Get list of locations
$sql = "SELECT housing.*, COUNT(c.housing_id) AS housing_assigned
FROM housing
LEFT JOIN (SELECT * FROM conclave_attendance WHERE registration_complete=1 AND conclave_year='{$EVENT['year']}') c USING (housing_id)
GROUP BY housing_name
ORDER BY housing_name";
$locations = $db->query($sql);
	if (DB::isError($locations)) dbError("Could not get locations listing in $title", $locations, $sql);
?>

<h1>Housing Locations</h1>

<form action="event/housing/locations.php" class="default">
	<b>Add New Location:</b>
	<input name="housing_name" value="Location Name"
		style="color: #aaa;"
		onFocus="if (this.value=='Location Name') { this.style.color='#000'; this.value=''; this.select(); }">
	<input type=submit name="submitted" value="Add">
</form><br>

<? if ($_REQUEST['added'])
	print "<b>Added new location: " . $_REQUEST['added'];
?>

<? if ($locations->numRows() != 0): ?>

<input type=submit name="submitted" value="Save Changes">
<table class="cart">
	<form action="event/housing/locations.php">
	<tr>
		<th>ID</th>
		<th>Location</th>
		<th>Description</th>
		<th>Assigned</th>
		<th>Capacity</th>
		<th></th>
		<th><input class="default" type=submit value="Delete All" onClick="return confirm('Are you sure you want to delete ALL of the locations?');"></th>
	</tr>
	<input type=hidden name="command" value="delete_all">
	</form>
	
<form action="event/housing/locations.php">
	
<?
while ($location = $locations->fetchRow()) { $i++;

	print $i%2==0 ? "<tr id=\"odd\">" : "<tr id=\"even\">";
		
	// location
	print "<td>$location->housing_id</td>";
	print "<td><input name=\"housing_id[$location->housing_id]\" value=\"$location->housing_name\" style=\"width: 250px;\"></td>";
	print "<td><input name=\"housing_description[$location->housing_id]\" value=\"$location->housing_description\" style=\"width: 350px;\"></td>";
	print "<td><center><b>$location->housing_assigned";
	if ($location->housing_assigned == $location->housing_capacity) print "&nbsp;<br><font color=red>FULL</font>";
	elseif ($location->housing_assigned >= $location->housing_capacity) print "<br><font color=red>OVERFULL</font>";
	print "</b></center></td>";
	print "<td><input name=\"housing_capacity[$location->housing_id]\" value=\"$location->housing_capacity\" style=\"width: 30px;\"></td>";
	print "<td><a href=\"event/reports.php?housing_id=$location->housing_id&report=housing\">Report</a></td>";
	
	// delete
	print "<td><input class=\"default\" type=button
				 onClick=\"if (confirm('Are you SURE you want to PERMANENTLY delete " . addslashes($location->housing_name) . "?'))	
		window.location='/sectionmaster.org/register/admin/event/housing/locations.php?command=delete&housing_id=$location->housing_id';\"
				value=\"Delete\">";
	
	print "</tr>";
	
}
?>
</table><br>

<input type=submit name="submitted" value="Save Changes">
</form>
<? endif; ?>

<? require "../../post.php"; ?>