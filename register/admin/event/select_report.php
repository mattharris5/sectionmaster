<?
$title = "Event > Reports > Select a Report";
require "../pre.php";

if ($_REQUEST['submitted'])
	redirect('event/reports', "report={$_REQUEST['report']}");

?>

<h1>Select a Report to View</h1>

<?
$reports = array (
	"summary"		=> "Summary",
	"lodge"			=> "By Lodge",
	"chapter"		=> "By Chapter",
	"out_of_section" => "Out-of-Section",	
	"tradingpost"	=> "Trading Post",
	"training"		=> "Training",
	"diet"			=> "Dietary Needs",
	"medical"		=> "Medical Issues",
	"medical-issues"=> "Medical Issues & ALL Emergency Contacts",
	"housing" 		=> "Housing" );
?>


<form action="event/select_report.php">

	<div class="checkboxes">

	<table>

	<? 
	foreach ($reports as $simple_title => $formal_title) {
		print "<tr><td><input type=radio name=\"report\" value=\"$simple_title\" onClick=\"this.form.submit();\"></td>
					<td>$formal_title</td></tr>";
	}
	?>

	</table>

	</div>

	<input type=hidden name="submitted" value="true">

</form>



<?
require "../post.php";
?>