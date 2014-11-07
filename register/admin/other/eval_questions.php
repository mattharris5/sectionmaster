<?
$title = "Evaluation Questions";
require "../pre.php";

$EVENT 	 = event_info($sm_db, $user->info->current_event);

$user->checkPermissions("report_eval");

// delete question if requested
if ($_REQUEST['command'] == 'delete' && isset($_REQUEST['question_id'])) {
	$user->getPermissions("report_eval",5);
	$delete = $db->query("UPDATE evaluation_questions SET deleted = '1' WHERE question_id = '{$_REQUEST['question_id']}' LIMIT 1");
		if (DB::isError($delete)) dbError("Could not delete question in $title", $delete, "");
}

// select questions
$sql = "SELECT * FROM evaluation_questions WHERE deleted != '1' AND event_id = '".$EVENT['id']."' ORDER BY heading, `order`";
$questions = $db->query($sql);
	if (DB::isError($questions)) dbError("Could not get question listing in $title", $questions, $sql);

?>

<h1>Evaluation Question Listing</h1>
To preview what your evaluation form will look like, use the <a href="section/massmail2">Mass E-mailer</a> tool to send yourself an e-mail with the <b>[evaluation_link]</b> tag.<br><br>
To see the evaluation questions used for previous events, first <a href="event/select">select another event</a> and then view the questions from that year.<br><br>
<input class="default" type=button onClick="window.location.href='/sectionmaster.org/register/admin/other/edit_question.php?command=add';" value="Add a New Question">

<table class="cart">
	<tr>
		<th>Heading</th>
		<th>Ordering</th>
		<th>Question</th>
		<th>Edit</th>
	</tr>
	
<?
while ($question = $questions->fetchRow()) { $i++;

	print $i%2==0 ? "<tr id=\"odd\">" : "<tr id=\"even\">";
	
	print "<td>";
	print ($question->heading=='eval')?"Main Evaluation":"Staff Interest";
	print "</td>";
	print "<td>$question->order</td>";
	
	// question
	print "<td id=\"description\"><font id=\"item\">".stripslashes($question->question)."</font><br>
								  <ul>
										<li><strong>Type: </strong>";
	switch($question->type)
	{
		case 'textarea':
			print "Text (long)";
			break;
		case 'text':
			print "Text (short)";
			break;
		case 'radio':
			print "Radio button group";
			break;
		case 'checkbox':
			print "Checkbox(es)";
			break;
		case 'select':
			print "Drop-down";
			break;
	}
	print "</li>";
	if (($question->type=='select') or ($question->type=='radio') or ($question->type=='checkbox'))
	{
		print "<li><strong>Options:</strong> ";
		if ($question->options != '') print $question->options;
		else print "<font color=red><b>NONE DEFINED!</b></font>";
		print "</li>";
		
	}
	print "								</ul>
	</td>";
	
	// edit
	print "<td>
			<input class=\"default\" type=button onClick=\"window.location='/sectionmaster.org/register/admin/other/edit_question.php?question_id=$question->question_id';\" value=\"Edit\">";
	
	// delete
	if ($user->getPermissions("report_eval") >= 5) {
		print "<input class=\"default\" type=button onClick=\"if(confirm('Are you sure you want to delete this question?')) window.location='/sectionmaster.org/register/admin/other/eval_questions.php?question_id=$question->question_id&command=delete';\" value=\"Delete\">";
	}
	
	print "</td></tr>";

}
?>


<? require "../post.php"; ?>