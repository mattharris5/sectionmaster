<?
$title = "Select Event";
require "../pre.php";
$user->checkPermissions("event_prefs");

if ($user->peekPermissions("superuser") > 0)
	$sql = "SELECT * FROM events ORDER BY section_name ASC, year DESC";
else
	$sql = "SELECT * FROM events
			WHERE LOWER(REPLACE(section_name, '-', ''))='{$user->info->section}'
			ORDER BY section_name ASC, year DESC";
$events = $sm_db->query($sql);
	if (DB::isError($events)) dbError("Could not get list of events in $title", $events, $sql);

if ($_REQUEST['submitted']) {
	$user->setCurrentEvent($_REQUEST['event_id']);
	redirect('event/index');
}

?>

<h1>Please select an event</h1>

<form action="event/select.php">
	
	<div class="checkboxes">
	<table>
	
	<? 
	while ($event = $events->fetchRow()) {
		if ($event->section_name != $current_section) {
			print "<tr id='separator'><td colspan=2>Section $event->section_name</td></tr>";
			$current_section = $event->section_name;
		}
		
		print "\n\n\t\t\t<tr><td> </td><td><input type=radio name='event_id' value='$event->id' onClick=\"this.form.submit();\">
					$event->section_name $event->formal_event_name</td></tr>";
	}
	?>
	</table>
	</div>
	
	<input type=hidden name="submitted" value="true">
	
</form>



<?
require "../post.php";
?>