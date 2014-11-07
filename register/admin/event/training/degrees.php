<?
$title = "Training > Degrees";
require "../../pre.php";
$user->checkPermissions("training");

// Add a new degree
if ($_REQUEST['submitted'] && $_REQUEST['degree_name'] != '') {
	$sql = "INSERT INTO degrees SET degree_name = '{$_REQUEST['degree_name']}', sequence = '{$_REQUEST['sequence']}'";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not add new degree in $title", $res, $sql);
	redirect("event/training/degrees");
}

// Delete a degree
if ($_REQUEST['command'] == 'delete' && $_REQUEST['degree_id'] != '') {
	$sql = "DELETE FROM degrees WHERE degree_id = '{$_REQUEST['degree_id']}' LIMIT 1";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Problem deleting degree in $title", $res, $sql);
	redirect("event/training/degrees");
}

// Move a degree
if ($_REQUEST['degree_id'] && $_REQUEST['sequence']) {
	$operator = $_REQUEST['sequence']=='up' ? "-" : "+";
	$currentSequence = $db->getOne("SELECT sequence FROM degrees WHERE degree_id = '{$_REQUEST['degree_id']}'");
	$degreeToMove = $db->getOne("SELECT degree_id FROM degrees WHERE sequence = $currentSequence $operator 1");
	$update1 = $db->query("UPDATE degrees SET sequence = sequence $operator 1 WHERE degree_id = '{$_REQUEST['degree_id']}'");
	$update2 = $db->query("UPDATE degrees SET sequence = $currentSequence WHERE degree_id = '$degreeToMove'");
	redirect("event/training/degrees");
}

// Update degree names
if ($_REQUEST['update_degrees']) {
	foreach($_REQUEST['degree_name'] as $degree_id => $degree_name) {
		$sql = "UPDATE degrees SET degree_name = '$degree_name' WHERE degree_id = '$degree_id'";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Could not update degree name in $title", $res, $sql);
	}
	redirect("event/training/degrees");
}

// Get list of degrees
$sql = "SELECT * FROM degrees ORDER BY sequence";
$degrees = $db->query($sql);
	if (DB::isError($degrees)) dbError("Could not get degree listing in $title", $degrees, $sql);
?>


<h1>Degrees Offered</h1>
<a href="event/training.php">&lt; Back to Training</a>

<form action="event/training/degrees.php" class="default">
	<b>Add New Degree:</b>
	<input name="degree_name" value="Degree Name"
		style="color: #aaa;"
		onFocus="if (this.value=='Degree Name') { this.style.color='#000'; this.value=''; this.select(); }">
	<input type=hidden class="hidden" name="sequence" value="<?= $degrees->numRows() + 1 ?>">
	<input type=submit name="submitted" value="Add">
</form>

<form action="event/training/degrees.php">
<table class="cart">
	<tr>
		<th colspan=2>Sequence</th>
		<th>Name</th>
		<th>Delete</th>
	</tr>
	
<? $i=0;
while ($degree = $degrees->fetchRow()) { $i++;

	print $i%2==0 ? "<tr id=\"odd\">" : "<tr id=\"even\">";
	
	// order buttons
	print "<td style=\"width: 16px;\">";
	if ($i != 1) print "<a href=\"event/training/degrees.php?degree_id=$degree->degree_id&sequence=up\" alt=\"Move Up\">
				<img src=\"../../templates/default/images/up.gif\" style=\"border:0;\"></a>";
	if ($i != $degrees->numRows())	print "<a href=\"event/training/degrees.php?degree_id=$degree->degree_id&sequence=down\"
				alt=\"Move Down\">
				<img src=\"../../templates/default/images/down.gif\" style=\"border:0;\"></a></td>";
		
	// prefix
	print "<td>$degree->sequence</td>";
		
	// item & description
	print "<td id=\"description\">
				<input name=\"degree_name[$degree->degree_id]\" value=\"$degree->degree_name\">
			</td>";
	
	// edit
	print "<td>
			<input class=\"default\" type=button
			 onClick=\"if (confirm('Are you SURE you want to PERMANENTLY delete the " . addslashes($degree->degree_name) . " degree?'))
			window.location='/sectionmaster.org/register/admin/event/training/degrees.php?command=delete&degree_id=$degree->degree_id';\"
			value=\"Delete\">
			</td>";
	print "</tr>";

}
?>
</table>
<input type=submit name="update_degrees" value="Save Changes">
</form>


<? require "../../post.php"; ?>