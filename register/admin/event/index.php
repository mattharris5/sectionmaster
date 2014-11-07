<?
$title = "Event > Summary";
require "../pre.php";
$user->checkPermissions("event_prefs");

if ($user->info->current_event == 0)
	redirect("event/select");

// get event info
$sql = "SELECT * FROM events WHERE id={$user->info->current_event}";
$info = $sm_db->getRow($sql);
	if (DB::isError($info)) dbError("Could not get event info in $title", $info, $sql);

// calculate if reg is open or not
if (strtotime($info->online_reg_open_date) < time() && strtotime($info->online_reg_close_date) > time() )
	$registration_open = true;
else
	$registration_open = false;

// get standalone trading post link if possible
$sql = "SELECT * FROM events WHERE section_name='$info->section_name' AND type='tradingpost' AND year=$info->year";
$tpinfo = $sm_db->getRow($sql);
	if (DB::isError($info)) dbError("Could not get tradingpost info in $title", $tpinfo, $sql);
?>

<h1>Event Summary: <?= $info->formal_event_name ?></h1>

	<div style="float:right;">
	<?= InsertSMChart("event/charts/event_signup_timeline_multiyear.php?time=" . microtime() . "&event=$info->id", 400, 300, '', true) ?>
	</div>

	<h3>Online Registration is <?= $registration_open ? "<font color=green>OPEN</font>" : "<font color=red>CLOSED</font>" ?></h3>
	<?=($registration_open==0) ? "<span id='comment' style=\"padding-left: 0px;\">To open registration, go to Event Prefs -> Online Details -> Online Registration Open Date/Time and set it to a date/time in the past.</span>" : "" ?>
	<ul>
	<li>Registration Opens: <b><?=date('D, M j, Y h:ia T',strtotime($info->online_reg_open_date))?></b></li>
	<li>Registration Closes: <b><?=date('D, M j, Y h:ia T',strtotime($info->online_reg_close_date))?></b></li>
	</ul>

	<h3>Online Registration Link: <a href="http://start.sectionmaster.org/<?=$user->info->current_event?>" target="_blank">http://start.sectionmaster.org/<?=$user->info->current_event?></a></h3>
	<? if ($tpinfo->id != '') print "<h3>Standalone Trading Post Link: <a href=\"http://start.sectionmaster.org/$tpinfo->id\" target=\"_blank\">http://start.sectionmaster.org/$tpinfo->id</a></h3><span id='comment' style=\"padding-left: 0px;\">The standalone trading post allows users to place multiple orders regardless of whether they have registered for the event.  Trading post products and inventories are shared between online registration and the standalone trading post, but the standalone trading post has its own Event Prefs.  Switch to that event to manage it using the \"(Select another event)\" link in the menubar to the left.</span>";
	?>

	<h4>Event Features</h4>
	<ul><li>Training Class Signup is 
				<b><?= $info->do_training==1 ? "<font color=green>ON" : "<font color=red>OFF" ?></font></b>
		<li>Online Payment Processing is 
				<b><?= $info->do_online_payment==1 ? "<font color=green>ON" : "<font color=red>OFF" ?></font></b>
		<li>Pay-at-door Payment is 
				<b><?= $info->do_payatdoor==1 ? "<font color=green>ON" : "<font color=red>OFF" ?></font></b>
		<li>Custom Payment Method is 
				<b><?= $info->custom_payment_method1!='' ? "<font color=green>ON" : "<font color=red>OFF" ?></font></b>
		<li>Online Trading Post is 
				<b><?= $info->do_tradingpost==1 ? "<font color=green>ON" : "<font color=red>OFF" ?></font></b>
		<li>Online Evaluation (after the event) is 
				<b><?= $info->do_eval==1 ? "<font color=green>ON" : "<font color=red>OFF" ?></font></b>
	</ul>

	<h4>Event Functions</h4>
	<ul><li><a href="event/info.php">Edit this Event</a>
		<li><a href="event/reports.php">View Reports</a>
	</ul>
	
<?
require "../post.php";
?>