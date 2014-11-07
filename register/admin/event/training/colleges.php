<?
$title = "Training > Colleges";
require "../../pre.php";
$user->checkPermissions("training");

// Delete a college
if ($_REQUEST['command'] == 'delete' && $_REQUEST['college_id'] != '') {
	$sql = "DELETE FROM colleges WHERE college_id = '{$_REQUEST['college_id']}' LIMIT 1";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Problem deleting college in $title", $res, $sql);
	redirect("event/training/colleges");
}

// Add a new college
if ($_REQUEST['submitted'] && $_REQUEST['college_name'] != '') {
	$sql = "INSERT INTO colleges SET college_name = '{$_REQUEST['college_name']}'";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not add new college in $title", $res, $sql);
	$new_college_id = mysql_insert_id();
	redirect("event/training/edit_college", "college_id=$new_college_id");
}

// Get list of colleges
$sql = "SELECT * FROM colleges ORDER BY show_online DESC, college_name";
$colleges = $db->query($sql);
	if (DB::isError($colleges)) dbError("Could not get college listing in $title", $colleges, $sql);
?>

<h1>Training Colleges</h1>
<a href="event/training.php">&lt; Back to Training</a>

<form action="event/training/colleges.php" class="default">
	<b>Add New College:</b>
	<input name="college_name" value="College Name"
		style="color: #aaa;"
		onFocus="if (this.value=='College Name') { this.style.color='#000'; this.value=''; this.select(); }">
	<input type=submit name="submitted" value="Add">
</form>

<table class="cart">
	<tr>
		<th>Prefix</th>
		<th>Name/Description</th>
		<th>Show Online</th>
		<th>Edit</th>
	</tr>
	
<?
while ($college = $colleges->fetchRow()) { $i++;

	print $i%2==0 ? "<tr id=\"odd\">" : "<tr id=\"even\">";
		
	// prefix
	print "<td>$college->college_prefix</td>";
		
	// item & description
	print "<td id=\"description\"><font id=\"item\">$college->college_name</font><br>
	 		   					  <font id=\"description\">$college->college_desc<br>
			</td>";
	

	// show online
	print "<td style=\"text-align: center;\">"
	 		. ($college->show_online == 1 ? "Y": "<strong><font color=red>N</font></strong>") . "</td>";
	
	// edit
	print "<td>
			<input class=\"default\" type=button
			 onClick=\"window.location='/sectionmaster.org/register/admin/event/training/edit_college.php?college_id=$college->college_id';\" value=\"Edit\">
			<input class=\"default\" type=button
			 onClick=\"if (confirm('Are you SURE you want to PERMANENTLY delete the $college->college_name?'))
			window.location='/sectionmaster.org/register/admin/event/training/colleges.php?command=delete&college_id=$college->college_id';\"
			value=\"Delete\">
			</td>";
	print "</tr>";

}
?>
</table>
<br>

<? require "../../post.php"; ?>