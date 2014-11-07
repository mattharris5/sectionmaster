<?
$_add = $_REQUEST['command']=='add' ? true : false;
$title = $_add ? "Evaluation Questions > Edit Question" : "Evaluation Questions > Add Question";
require "../pre.php";
$user->checkPermissions("report_eval", 3);

$EVENT 	 = event_info($sm_db, $user->info->current_event);

// check that there's a question_id
if (!$_REQUEST['question_id'] && !$_add)
	redirect("other/eval_questions");

// update question info
if ($_REQUEST['submitted']) {
	$sqlcmd = $_add ? "INSERT INTO" : "UPDATE";
	$sql = "$sqlcmd evaluation_questions SET
				question = '" . addslashes($_REQUEST['question']) . "',
				`order` = '" . addslashes($_REQUEST['order']) . "',
				type = '" . $_REQUEST['type'] . "',
				options = '" . addslashes($_REQUEST['options']) . "',
				heading = '" . $_REQUEST['heading'] . "',
				event_id = '{$EVENT['id']}'";
			if (!$_add) $sql .= " WHERE question_id = '{$_REQUEST['question_id']}'";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not update/insert question info in $title", $res, $sql);

	// redirect
	redirect("other/eval_questions");
}

// get question data
if (!$_add) {
	$sql = "SELECT * FROM evaluation_questions WHERE question_id='{$_REQUEST['question_id']}'";
	$question = $db->getRow($sql);
		if (DB::isError($question)) dbError("Could not get question info in $title", $question, $sql);
}

?>

<h1><?= $_add ? "Add" : "Edit" ?> Question</h1>

<form action="other/edit_question.php">

	<input type="hidden" class="hidden" name="question_id" value="<?= $question->question_id ?>">

	<label for="question">Question:</label>
	<input name="question" id="question" value="<?= stripslashes($question->question) ?>"><br />
	
	<label for="type">Type:</label>
	<select name="type">
		<option value="text"<?=($question->type=='text')?" selected":""?>>Text (short)</option>
		<option value="textarea"<?=($question->type=='textarea')?" selected":""?>>Text (long)</option>
		<option value="select"<?=($question->type=='select')?" selected":""?>>Drop-down</option>
		<option value="radio"<?=($question->type=='radio')?" selected":""?>>Radio button group</option>
		<option value="checkbox"<?=($question->type=='checkbox')?" selected":""?>>Checkbox(es)</option>
	</select><br>
	
	<label for="options">Options:</label>
	<input name="options" id="options" value="<?= stripslashes($question->options) ?>">
	<span id="comment">Separated using the pipe character (|).  If the question type is <B>drop-down, radio, or checkbox,</B> fill in a list of available options here.  For example, a rating question might have options in the format of: <b>1|2|3|4|5</b></span><br />
	
	<label for="order">Ordering:</label>
	<input name="order" id="order" value="<?= stripslashes($question->order) ?>">
	<span id="comment">Enter a number in decimal form to indicate the order that this question will appear on the evaluation form.  For example, 1.1 would appear before 2.0.</span><br />
	
	<label for="heading">Heading</label>
	<select name="heading">
		<option value="eval"<?=($question->heading=='eval')?" selected":""?>>Main Evaluation Form</option>
		<option value="staff"<?=($question->heading=='staff')?" selected":""?>>Staff Interest Form</option>
	</select>
	<span id="comment">This indicates which heading of the evaluation the question will appear under.  Staff Interest Form is the portion of the form where members can answer questions about their desire to serve on staff in the following year.  Responses to these questions are not correlated to responses on the Main Evaluation Form.</span><br>
	
	<input type="submit" id="submit_button" name="submitted" value="Save Changes">
	
	<? if ($_add) print "<input type=\"hidden\" name=\"command\" value=\"add\">"; ?>
	
	
</form>

<? require "../post.php"; ?>