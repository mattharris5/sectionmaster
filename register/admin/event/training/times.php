<?
$title = "Training > Times";
require "../../pre.php";
$user->checkPermissions("training");

// update times
if ($_REQUEST['submitted']) {
	foreach($_REQUEST['time'] as $time_id => $time_name) {
		$res = $db->query("UPDATE training_times SET time = '$time_name' WHERE time_id = '$time_id'");
			if (DB::isError($res)) dbError("Could not update times in $title", $res, "");
	}
}

// Get list of colleges
$sql = "SELECT * FROM training_times ORDER BY time_id";
$times = $db->query($sql);
	if (DB::isError($times)) dbError("Could not get times in $title", $times, $sql);
?>

<h1>Session Times</h1>
<a href="event/training.php">&lt; Back to Training</a>


<form action="event/training/times.php" class="default">

<table class="cart">
	<tr>
		<th>Session</th>
		<th>Time</th>
	</tr>
	
<?
while ($time = $times->fetchRow()) { $i++;

	print $i%2==0 ? "<tr id=\"odd\">" : "<tr id=\"even\">";
		
	// session
	print "<td>Session $time->time_slot:</td>";
	
	// time
	print "<td><input name=\"time[$time->time_id]\" value=\"$time->time\"></td>";
	
	print "</tr>";
	
}
?>
</table><br>

<input type=submit name="submitted" value="Save Changes">

</form>

<? require "../../post.php"; ?>