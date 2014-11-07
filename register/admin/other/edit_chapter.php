<?
$_add = $_REQUEST['command']=='add' ? true : false;
$title = $_add ? "Section Info > Edit Chapter" : "Section Info > Add Chapter";
require "../pre.php";
$user->checkPermissions("section_info", 3);

$EVENT 	 = event_info($sm_db, $user->info->current_event);

// check that there's a chapter_id
if (!$_REQUEST['chapter_id'] && !$_add)
	redirect("other/chapters");

// update chapter info
if ($_REQUEST['submitted']) {
	
	$sqlcmd = $_add ? "INSERT INTO" : "UPDATE";
	$sql = "$sqlcmd chapters SET
				chapter_name = '" . $_REQUEST['chapter_name'] . "',
				active_membership = '" . $_REQUEST['active_membership'] . "',
				chapter_chief = '" . $_REQUEST['chapter_chief'] . "',
				lodge = '" . $_REQUEST['lodge'] . "'";
				if (!$_add) $sql .= " WHERE chapter_id = '{$_REQUEST['chapter_id']}'";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not update/insert chapter info in $title", $res, $sql);

	// redirect
	/*if ($_add) {
		$new_chapter_id = $db->getOne("SELECT last_insert_id()");
		redirect("other/edit_chapter", "chapter_id=$new_chapter_id");
	} else { //	if ($_REQUEST['option_name']=='') {*/
		redirect("other/chapters");
	//}
	
}

// Lodge Data:
$sql = "SELECT *
		FROM lodges
		WHERE section='{$EVENT['section_name']}'
		ORDER BY lodge_name";
$lodge_res = $sm_db->query($sql);
	if (DB::isError($lodge_res)) { dbError("Could not retreive lodge names in $title", $lodge_res, $sql); }

// get chapter data
if (!$_add) {
	$sql = "SELECT * FROM chapters WHERE chapter_id='{$_REQUEST['chapter_id']}'";
	$chapter = $db->getRow($sql);
		if (DB::isError($chapter)) dbError("Could not get chapter info in $title", $chapter, $sql);
}
?>

<h1><?= $_add ? "Add" : "Edit" ?> Chapter</h1>
<h2><?= $chapter->chapter_name ?></h2>

<form action="other/edit_chapter.php" method="post">

	<input type="hidden" class="hidden" name="chapter_id" value="<?= $chapter->chapter_id ?>">
	
	<label for="chapter_name">Chapter Name:</label>
	<input name="chapter_name" id="chapter_name" value="<?= stripslashes($chapter->chapter_name) ?>"><br />
	
	<label for="lodge">Lodge:</label>
	<select name="lodge">
	<option value="">-- Select a Lodge --</option>
					<?					
					while($thislodge = $lodge_res->fetchRow()) {
						print "<option value=\"$thislodge->lodge_id\"";
						print ($chapter->lodge==$thislodge->lodge_id)?" selected":"";
						print ">$thislodge->lodge_name Lodge, $thislodge->council Council #$thislodge->lodge_id</option>";
					}
					?>
	</select><br>

	<label for="active_membership">Active Membership:</label>
	<input name="active_membership" id="active_membership" value="<?= stripslashes($chapter->active_membership) ?>"><br />	
	
	<label for="chapter_chief">Chapter Chief Name:</label>
	<input name="chapter_chief" id="chapter_chief" value="<?= stripslashes($chapter->chapter_chief) ?>"><br />
	
	<input type="submit" id="submit_button" name="submitted" value="Save Changes">
	
	<? if ($_add) print "<input type=\"hidden\" name=\"command\" value=\"add\">"; ?>
	
</form>

<? require "../post.php"; ?>