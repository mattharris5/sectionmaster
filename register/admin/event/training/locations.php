<?
$title = "Training > Classroom Locations";
require "../../pre.php";
$user->checkPermissions("training");

// add new location
if ($_REQUEST['submitted'] && $_REQUEST['training_location'] != '') {
	$sql = "INSERT INTO training_locations SET training_location = '{$_REQUEST['training_location']}'";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not add new location in $title", $res, $sql);
	redirect("event/training/locations", "added=" . urlencode($_REQUEST['training_location']));
}

// update locations
if ($_REQUEST['submitted']) {
	foreach($_REQUEST['location_id'] as $id => $location_name) {
		$res = $db->query("UPDATE training_locations SET training_location = '$location_name' WHERE location_id = '$id'");
			if (DB::isError($res)) dbError("Could not update times in $title", $res, "");
	}
}

// delete location
if ($_REQUEST['command'] == 'delete' && $_REQUEST['location_id']) {
	$sql = "UPDATE training_locations SET deleted = '1' WHERE location_id = '{$_REQUEST['location_id']}' LIMIT 1";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not delete location in $title", $res, "");
	redirect("event/training/locations");
}

// delete ALL locations
if ($_REQUEST['command'] == 'delete_all') {
	$sql = "UPDATE training_locations SET deleted = '1'";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not delete all locations in $title", $res, "");
	redirect("event/training/locations");
}

// Get list of locations
$sql = "SELECT * FROM training_locations WHERE deleted != '1' ORDER BY training_location";
$locations = $db->query($sql);
	if (DB::isError($locations)) dbError("Could not get locations listing in $title", $locations, $sql);
?>

<h1>Classroom Locations</h1>
<a href="event/training.php">&lt; Back to Training</a><br>

<form action="event/training/locations.php" class="default">
	<b>Add New Location:</b>
	<input name="training_location" value="Location Name"
		style="color: #aaa;"
		onFocus="if (this.value=='Location Name') { this.style.color='#000'; this.value=''; this.select(); }">
	<input type=submit name="submitted" value="Add">
</form><br>

<? if ($_REQUEST['added'])
	print "<b>Added new location: " . $_REQUEST['added'];
?>

<? if ($locations->numRows() != 0): ?>

<table class="cart">
	<form action="event/training/locations.php">
	<tr>
		<th>Location</th>
		<th><input class="default" type=submit value="Delete All" onClick="return confirm('Are you sure you want to delete ALL of the locations?');"></th>
	</tr>
	<input type=hidden name="command" value="delete_all">
	</form>
	
<form action="event/training/locations.php">
	
<?
while ($location = $locations->fetchRow()) { $i++;

	print $i%2==0 ? "<tr id=\"odd\">" : "<tr id=\"even\">";
		
	// location
	print "<td><input name=\"location_id[$location->location_id]\" value=\"$location->training_location\"></td>";
	
	// delete
	print "<td><input class=\"default\" type=button
				 onClick=\"if (confirm('Are you SURE you want to PERMANENTLY delete " . addslashes($location->training_location) . "?'))	
		window.location='/sectionmaster.org/register/admin/event/training/locations.php?command=delete&location_id=$location->location_id';\"
				value=\"Delete\">";
	
	print "</tr>";
	
}
?>
</table><br>

<input type=submit name="submitted" value="Save Changes">
</form>
<? endif; ?>

<? require "../../post.php"; ?>