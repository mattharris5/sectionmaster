<?
$title = "Event > Training";
require "../pre.php";
$user->checkPermissions("training");
?>

<h1>Training</h1>

<ul>
	<li><a href="event/training/colleges.php">Training Colleges</a></li>
	<li><a href="event/training/degrees.php">Degrees Offered</a></li>
	<li><a href="event/training/times.php">Session Times</a></li>
	<li><a href="event/training/locations.php">Classroom Locations</a></li>
	<li><a href="event/training/sessions.php">Sessions</a></li>
</ul>
<br>
A public listing of your training schedule is available here: 
<a href="http://www.sectionmaster.org/register/training?event=<?=$user->info->current_event?>" target="_blank">http://www.sectionmaster.org/register/training?event=<?=$user->info->current_event?></a>
	

<?
require "../post.php";
?>

