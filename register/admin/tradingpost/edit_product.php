<?
//turn on file uploads
ini_set("file_uploads","1");

$_add = $_REQUEST['command']=='add' ? true : false;
$title = $_add ? "Trading Post > Edit Product" : "Trading Post > Add Product";
require "../pre.php";
$user->checkPermissions("products", 3);

// check that there's a product_id
if (!$_REQUEST['product_id'] && !$_add)
	redirect("tradingpost/products");

// update product info
if ($_REQUEST['submitted']) {
	
	ini_set('memory_limit', '100M');
	ini_set('post_max_size', '16M');
	ini_set('upload_max_filesize', '6M');
	$uploadsDirectory = '/var/www/html/register/images/'.$user->info->section.'/';
	
	// uploaded thumbnail image
	if ($_FILES['thumb_upload']['name'] != '')
	{
		@move_uploaded_file($_FILES['thumb_upload']['tmp_name'], '/var/www/html/register/images/'.$user->info->section.'/'.$_FILES['thumb_upload']['name']);
		$image_thumbnail = 'register/images/'.$user->info->section.'/'.$_FILES['thumb_upload']['name'];
	}
	else $image_thumbnail = $_REQUEST['image_thumbnail'];
	
	// uploaded full-size image
	if ($_FILES['full_upload']['name'] != '')
	{
		@move_uploaded_file($_FILES['full_upload']['tmp_name'], '/var/www/html/register/images/'.$user->info->section.'/'.$_FILES['full_upload']['name']);
		$image_full = 'register/images/'.$user->info->section.'/'.$_FILES['full_upload']['name'];
	}
	else $image_full = $_REQUEST['image_full'];
	
	$nextid = $db->getOne("SELECT MAX(product_id)+1 FROM products");
	$sqlcmd = $_add ? "INSERT INTO" : "UPDATE";
	$sql = "$sqlcmd products SET
				product_code = '" . addslashes($_REQUEST['product_code']) . "',
				item = '" . addslashes($_REQUEST['item']) . "',
				description = '" . addslashes($_REQUEST['description']) . "',
				price = '" . addslashes($_REQUEST['price']) . "',
				show_in_store = '" . addslashes($_REQUEST['show_in_store']) . "',
				inventory = '" . addslashes($_REQUEST['inventory']) . "',
				image_thumbnail = '" . addslashes($image_thumbnail) . "',
				image_full = '" . addslashes($image_full) . "',
				ship_weight = '" . addslashes($_REQUEST['ship_weight']) . "',
				ship_cost = '" . addslashes($_REQUEST['ship_cost']) . "',
				option_name = '" . addslashes($_REQUEST['option_name']) . "',
				category_id = '" . addslashes($_REQUEST['category_id']) . "',
				last_updated = now(),
				event_id_only = '" . $_REQUEST['event_id_only'] . "'";
				if ($_add) $sql .= ", ordering = $nextid ";
				if (!$_add) $sql .= " WHERE product_id = '{$_REQUEST['product_id']}'";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not update/insert product info in $title", $res, $sql);

	// redirect
	if ($_add && $_REQUEST['option_name']!='') {
		$new_product_id = $db->getOne("SELECT last_insert_id()");
		redirect("tradingpost/edit_product", "product_id=$new_product_id");
	} else { //	if ($_REQUEST['option_name']=='') {
		redirect("tradingpost/products");
	}
	
}

// get product data
if (!$_add) {
	$sql = "SELECT * FROM products WHERE product_id='{$_REQUEST['product_id']}'";
	$product = $db->getRow($sql);
		if (DB::isError($product)) dbError("Could not get product info in $title", $product, $sql);
}

// delete product option
if ($_REQUEST['delete_option']) {
	$res = $db->query("DELETE FROM product_options WHERE product_option_id = '{$_REQUEST['delete_option']}' LIMIT 1");
		if (DB::isError($res)) dbError("Could not delete product option in $title", $res);
}

// add new option
if ($_REQUEST['new_option_value'] != '') {
	$sql = "INSERT INTO product_options SET
				product_id = '" . addslashes($_REQUEST['product_id']) . "',
				option_value = '" . addslashes($_REQUEST['new_option_value']) . "',
				price = '" . addslashes($_REQUEST['new_price']) . "',
				inventory = '" . addslashes($_REQUEST['new_inventory']) . "'";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not add product option in $title", $res, $sql);
}

// update existing product_options
if ($_REQUEST['handle_options'] && $_REQUEST['new_option_value'] == '') {
	foreach ($_REQUEST['option_value'] as $product_option_id => $option_value) {
		$sql = "UPDATE product_options SET
					option_value = '" . addslashes($_REQUEST['option_value'][$product_option_id]) . "',
					price = '" . addslashes($_REQUEST['price'][$product_option_id]) . "',
					inventory = '" . addslashes($_REQUEST['inventory'][$product_option_id]) . "'
				WHERE product_option_id = '$product_option_id' LIMIT 1";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Could not update product option in $title", $res, $sql);
	}
}

?>

<h1><?= $_add ? "Add" : "Edit" ?> Product</h1>
<h2><?= $product->item ?></h2>

<form action="tradingpost/edit_product.php" enctype="multipart/form-data" method="post">

	<input type="hidden" class="hidden" name="product_id" value="<?= $product->product_id ?>">
	
	<label for="item">Item Name:</label>
	<input name="item" id="item" value="<?= stripslashes($product->item) ?>"><br />

	<label for="product_code">Product Code (optional):</label>
	<input name="product_code" id="product_code" value="<?= stripslashes($product->product_code) ?>"><br />	
	
	<label for="description">Description:</label>
	<input name="description" id="description" value="<?= stripslashes($product->description) ?>"><br />
	
	<label for="price">Price:</label>
	<input name="price" id="price" value="<?= $product->price ?>"><br>
	
	<label for="show_in_store">Show in Store:</label>
	<input type=hidden name="show_in_store" value="0">
	<input type=checkbox name="show_in_store" id="show_in_store" value="1" 
		<?= $product->show_in_store=='1'?"checked":"" ?>><br>
		<span id="comment">Some items should not be shown in the online store, such as 
				the Conclave Registration item.</span><br>

	<label for="event_id_only">Only Show In Event:</label>
	<select name="event_id_only" id="event_id_only">
		<option value=""> </option>
		<?
		if ($user->peekPermissions("superuser") > 0)
			$sql = "SELECT * FROM events WHERE type = 'event'";
		else
			$sql = "SELECT * FROM events
					WHERE LOWER(REPLACE(section_name, '-', ''))='{$user->info->section}'
					AND type = 'event'";
		$events = $sm_db->query($sql);
			if (DB::isError($events)) dbError("Could not get list of events in $title", $events, $sql);

		while($event = $events->fetchRow()) {
			print "<option value=\"$event->id\"";
			if ($product->event_id_only == $event->id) print " selected";
			print ">$event->section_name $event->formal_event_name</option>";
		}
		?>
	</select><br>
		<span id="comment">If you only want this item to appear in the store for a specific event,
		choose that event here.  This may be useful for event-specific products, such as optional housing
		options.  If left blank (default), this product will show up in every event's
		trading post.</span><br>
		
	<label for="category_id">Product Category:</label>
	<select name="category_id" id="category_id">
		<option value=""> </option>
		<?
		$sql = "SELECT * FROM product_categories";
		$categories = $db->query($sql);
			if (DB::isError($categories)) dbError("Could not get list of product categories in $title", $categories, $sql);

		while($category = $categories->fetchRow()) {
			print "<option value=\"$category->category_id\"";
			if ($product->category_id == $category->category_id) print " selected";
			print ">$category->category</option>";
		}
		?>
	</select><br>
		<span id="comment">Select which category to which this product belongs.  This will translate to the finance reports
			which can be generated within the SectionMaster desktop software used on-site at your event.</span><br>

		
	<label for="inventory">Current Inventory:</label>
	<input name="inventory" id="inventory" value="<?= stripslashes($product->inventory) ?>"><br />

	<label for="thumb_upload">Image (Thumbnail - 100px width max):</label>
	<input type="hidden" name="image_thumbnail" id="image_thumbnail" value="<?= stripslashes($product->image_thumbnail) ?>">
	<input id="thumb_upload" type="file" name="thumb_upload">
	<?
	if (file_exists("/var/www/html/".stripslashes($product->image_thumbnail)) and ($product->image_thumbnail != ''))
	{
		print "<label for='thumb_preview'></label><img id='thumb_preview' src='../../".stripslashes($product->image_thumbnail)."' width='100'>";
	}
	?>
	<br />
	
	<label for="full_upload">Image (Full-size - 600px width max):</label>
	<input type="hidden" name="image_full" id="image_full" value="<?= stripslashes($product->image_full) ?>">
	<input id="full_upload" type="file" name="full_upload">
	<?
	if (file_exists("/var/www/html/".stripslashes($product->image_full)) and ($product->image_full != ''))
	{
		print "<label for='full_preview'></label><img id='full_preview' src='../../".stripslashes($product->image_full)."' width='600'>";
	}
	?>
	<br />
	
	<label for="ship_weight">Weight (in ounces):</label>
	<input name="ship_weight" id="ship_weight" value="<?= $product->ship_weight ?>"><br />
	<span id="comment">The "weight" of this item is used when calculating shipping costs for 
		orders, but only applies if the shipping calculation method for the event considers
		item weights.</span><br>
	
	<label for="ship_cost">Shipping Cost:</label>
	<input name="ship_cost" id="ship_cost" value="<?= $product->ship_cost ?>"><br />
	<span id="comment">The shipping cost of this item is used when calculating shipping costs for 
		orders, but only applies if the shipping calculation method for the event is based on a
		per-item shipping calculation.</span><br>

	<label for="option_name">Option Name:</label>
	<input name="option_name" id="option_name" value="<?= $product->option_name ?>"><br />
	<span id="comment">Products can have different options.   For instance, a t-shirt product might
		have different sizes or colors for sale.  You can setup one option for each product.  To set
		up the options, enter the option name (like "Size") and click Save Changes.</span><br>
	<? // if ($product->option_name == '') print "<input type=hidden name=\"setup_options\" value=true>"; ?>
	
	<input type="submit" id="submit_button" name="submitted" value="Save Changes">
	
	<? if ($_add) print "<input type=\"hidden\" name=\"command\" value=\"add\">"; ?>
	
</form>

<? if ($product->option_name != ''): ?>
<br><br style="clear:both;">
<fieldset><legend>Product Options: <?= $product->option_name ?></legend>
<form action="tradingpost/edit_product.php" class="default">
	<input type="hidden" class="hidden" name="handle_options" value="true">
	<input type="hidden" class="hidden" name="product_id" value="<?= $product->product_id ?>">
		
	<?
	$sql = "SELECT * FROM product_options WHERE product_id = '$product->product_id'";
	$options = $db->query($sql);
		if (DB::isError($options)) dbError("Could not get product options for $product->item in $title", $options, $sql);
	
	print "<table><tr><th>Option Value</th><th>Price</th><th>Inventory</th><th>Modify</th></tr>";
	
	// print out the current options
	while ($option = $options->fetchRow()) {
		print "<tr> <td><input name=\"option_value[$option->product_option_id]\" value=\"$option->option_value\"></td>
					<td><input name=\"price[$option->product_option_id]\" value=\"$option->price\"></td>
					<td><input name=\"inventory[$option->product_option_id]\" value=\"$option->inventory\"></td>
					<td><a href=\"tradingpost/edit_product.php?product_id=$product->product_id&delete_option=$option->product_option_id\">Delete</a>
				</tr>";
	}
	// have an "add new" row
	print "<tr> <td><input name=\"new_option_value\"></td>
				<td><input name=\"new_price\"></td>
				<td><input name=\"new_inventory\"></td>
				<td><input type=\"submit\" value=\"Add\"></td>
			</tr>";
	
	print "</table>";
	?>

<br>
<input type=submit value="Save Product Options Changes">

</form>
</fieldset>
<? endif; ?>

<? require "../post.php"; ?>