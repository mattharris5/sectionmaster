<?
$title = "Event > Training > Edit College";
require "../../pre.php";
$user->checkPermissions("training", 3);

// check that there's a product_id
if (!$_REQUEST['college_id'])
	redirect("event/training/colleges");

// update product info
if ($_REQUEST['submitted']) {
	$sql = "UPDATE colleges SET
				college_prefix = '" . strtoupper(addslashes($_REQUEST['college_prefix'])) . "', 
				college_name = '" . addslashes($_REQUEST['college_name']) . "',
				college_desc = '" . addslashes($_REQUEST['college_desc']) . "',
				show_online = '" . $_REQUEST['show_online'] . "'
			WHERE college_id = '{$_REQUEST['college_id']}'";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not update college info in $title", $res, $sql);

	// redirect
	redirect("event/training/colleges");
}

// get product data
$sql = "SELECT * FROM colleges WHERE college_id='{$_REQUEST['college_id']}'";
$college = $db->getRow($sql);
	if (DB::isError($college)) dbError("Could not get college info in $title", $college, $sql);

?>

<h1>Edit College</h1>
<h2><?= $college->college_name ?></h2>

<form action="event/training/edit_college.php">

	<input type="hidden" class="hidden" name="college_id" value="<?= $college->college_id ?>">

	<label for="college_prefix">Prefix:</label>
	<input name="college_prefix" id="college_prefix" value="<?= stripslashes($college->college_prefix) ?>"><br />		

	<label for="college_name">Name:</label>
	<input name="college_name" id="college_name" value="<?= stripslashes($college->college_name) ?>"><br />		

	<label for="college_desc">Description:</label>
	<textarea name="college_desc" id="college_desc" style="height: 150px;"><?= stripslashes($college->college_desc) ?></textarea><br />		

	<label for="show_online">Show Online?</label>
	<input type=hidden name="show_online" value="0">
	<input type=checkbox name="show_online" id="show_online" value="1" 
		<?= $college->show_online=='1'?"checked":"" ?>><br>
	
	<input type="submit" id="submit_button" name="submitted" value="Save Changes">
	
</form>

<? require "../../post.php"; ?>