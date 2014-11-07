<?
$no_header = true;
$title = "Event > Edit Recurring Events";
require "../pre.php";
$user->checkPermissions("event_prefs");

# Add new
if ($_REQUEST['command'] == 'Add') {
	$sql = "INSERT INTO recurring_events 
			SET title = " . $sm_db->quote($_REQUEST['title']) . ",
				section_name = '" . $user->info->section . "'";
	$res = $sm_db->query($sql);
		if (DB::isError($res)) dbError("Could not add new recurring event in $title", $res, $sql);
}

# Delete exising
if ($_REQUEST['command'] == 'delete') {
	$sql = "DELETE FROM recurring_events WHERE id = {$_REQUEST['id']} LIMIT 1";
	$res = $sm_db->query($sql);
		if (DB::isError($res)) dbError("Could not delete recurring event in $title", $res, $sql);
}

# Fetch events
$recurring_events = $sm_db->query("SELECT * FROM recurring_events WHERE section_name = '$user->section'");
?>

<html>
<head>
	<title>Recurring Events</title>
	<base href="https://<?=$_GLOBALS['secure_base_href']?>/register/admin/">
	<link rel="stylesheet" type="text/css" href="admin.css">
</head>

<body style="padding: 10px;">

<h2>Recurring Events</h2>

<form action="event/recurring_events.php" method=post class="default">
	<table>
		<tr><th>Event Title</th>
			<th>Delete</th></tr>
			

		<? 
		while ($recurring_events && $recurring_event = $recurring_events->fetchRow()) {
			print "<tr id='separator'>
						<td>$recurring_event->title</td>
						<td><a href=\"event/recurring_events.php?command=delete&id=$recurring_event->id\"
							 onClick=\"return confirm('Are you sure you want to delete this recurring event?');\">
							 Delete</a></td>
					</tr>";
					
			# Select events that are in this recurring event
			$events = $sm_db->query("SELECT * FROM events WHERE recurring_event_id = '$recurring_event->id'");
			while ($event = $events->fetchRow()) {
				print "<tr id='sub1'>
						<td colspan=2>- $event->formal_event_name</td>
						</tr>";
			}
			
		} ?>
		
	</table>
	
	<br>
	
	<table>
		<tr><td>Add New: <input name="title"></td>
			<td><input type=submit name="command" value="Add"></td>
		</tr>
	</table>	
		
	
</form>

</body>
</html>