<?
if ($_REQUEST['new_ordeal'] == 'only') {
	$new_ordeal_title = " (New Ordeal Members Only)";
	$new_ordeal_only = true;
}
$reports = array (
	"summary"		=> "Summary",
	"lodge"			=> "By Lodge",
	"chapter"		=> "By Chapter",
	"out_of_section" => "Out-of-Section",
	"tradingpost"	=> "Trading Post",
	"training"		=> "Training",
	"training_detail" => "Training Enrollment Details",
	"diet"			=> "Dietary Needs",
	"medical"		=> "Medical Issues",
	"medical-all"	=> "Medical Issues &amp; ALL Emergency Contacts",
	"eval"			=> "Evaluation",
	"housing"		=> "Housing" );
$simple_title = $_REQUEST['report'];
$formal_title = $reports[$simple_title] . $new_ordeal_title;

// include the header stuff
$title = "Event > Reports > $formal_title";
require "../pre.php";

// make sure we've got a valid report
if (!$_REQUEST['report'] || $formal_title == '')
	redirect("event/select_report");

// validate permissions
if ($simple_title == 'out_of_section') {
	$check_permissions_for = "lodge";
} elseif ($simple_title == 'training_detail') {
	$check_permissions_for = "training";
} elseif ($simple_title == 'housing') {
	$check_permissions_for = "chapter";
} else {
	$check_permissions_for = $simple_title;
}
// $check_permissions_for = ($simple_title == 'out_of_section') ? "lodge" : $simple_title;
// $check_permissions_for = ($simple_title == 'training_detail') ? "training" : $simple_title;
$user->checkPermissions("report_" . $check_permissions_for);

# Get event data
$EVENT 	 = event_info($sm_db, $user->info->current_event);
$SECTION = section_id_convert($EVENT['section_name']);

# Current Year variable
$event_year = $EVENT['year'];
$event_date = $EVENT['start_date'];
$event_end_date = $EVENT['end_date'];
$year_ago = strtotime($EVENT['start_date']) - (365*24*60*60);

// include extra report data
require "reports/report_data.php";


?>

<h1><?= $EVENT['formal_event_name'] ?>: <?= $formal_title ?></h1>
<? if ($new_ordeal_only) print "<h2>Only displaying new ordeal members</h2>"; ?>

<? /*	<div class="default">
	<input type=button value="Print this Report"> | 
	E-mail to: <input name="test_to"> <input type="submit" value="Send"> | 
	<input type=button value="E-mail to stored contact list">
	</div>
*/ ?>

<? 
## Get the report content
ob_start();
require "reports/$simple_title.php"; 
$report_content_web = ob_get_contents();
ob_end_clean();

// strip out tags that shouldn't appear in the online version
$pattern = "%(<\!--SM:plaintext-->)(.*?)(<\!--\/SM:plaintext-->)%is";
$report_content_web = preg_replace ($pattern, "", $report_content_web);
$pattern = "%(<\!--SM:textonly-->)(.*?)(<\!--\/SM:textonly-->)%is";
$report_content_web = preg_replace ($pattern, "", $report_content_web);
$pattern = "%(<\!--SM:emailonly-->)(.*?)(<\!--\/SM:emailonly-->)%is";
$report_content_web = preg_replace ($pattern, "", $report_content_web);

// Print out the report content
print $report_content_web;

?>

<!--<?= $sql ?>-->
<?
require "../post.php";
?>

