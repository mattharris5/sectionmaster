<?
$_add = $_REQUEST['command']=='add' ? true : false;
$title = $_add ? "Trading Post > Edit Coupon" : "Trading Post > Add Coupon";
require "../pre.php";
$user->checkPermissions("coupons", 3);

// check that there's a product_id
if (!$_REQUEST['product_id'] && !$_add)
	redirect("tradingpost/coupons");

// update product info
if ($_REQUEST['submitted']) {
	$auto_add_coupon = $_REQUEST['coupon_criteria']=='' ? "0" : $_REQUEST['auto_add_coupon'];
	$sqlcmd = $_add ? "INSERT INTO" : "UPDATE";
	$sql = "$sqlcmd products SET
				product_code = '" . strtoupper(addslashes($_REQUEST['product_code'])) . "',
				item = '" . addslashes($_REQUEST['item']) . "',
				description = '" . addslashes($_REQUEST['description']) . "',
				price = '" . addslashes($_REQUEST['price']) . "',
				show_in_store = 0,
				inventory = '" . addslashes($_REQUEST['inventory']) . "',
				coupon_criteria = '" . $_REQUEST['coupon_criteria'] . "',
				auto_add_coupon = '" . $auto_add_coupon . "',
				coupon = 1 ";
			if (!$_add) $sql .= " WHERE product_id = '{$_REQUEST['product_id']}'";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not update/insert coupon info in $title", $res, $sql);

	// redirect
	redirect("tradingpost/coupons");
}

// get product data
if (!$_add) {
	$sql = "SELECT * FROM products WHERE product_id='{$_REQUEST['product_id']}'";
	$product = $db->getRow($sql);
		if (DB::isError($product)) dbError("Could not get coupon info in $title", $product, $sql);
}

?>

<h1><?= $_add ? "Add" : "Edit" ?> Coupon</h1>
<h2><?= $product->product_code ?></h2>

<form action="tradingpost/edit_coupon.php">

	<input type="hidden" class="hidden" name="product_id" value="<?= $product->product_id ?>">

	<label for="product_code">Coupon Code:</label>
	<input name="product_code" id="product_code" value="<?= stripslashes($product->product_code) ?>"><br />		
	
	<label for="item">Coupon Name:</label>
	<input name="item" id="item" value="<?= stripslashes($product->item) ?>"><br />

	<label for="description">Description:</label>
	<input name="description" id="description" value="<?= stripslashes($product->description) ?>"><br />
	
	<label for="price">Price:</label>
	<input name="price" id="price" value="<?= $product->price ?>"><br>
	<span id="comment">This should be a negative value (e.g., -10.00) unless you have a reason for making a positive-value coupon.</span><br>

	<label for="coupon_criteria">Coupon Eligibility Criteria (optional):</label>
	<input name="coupon_criteria" id="coupon_criteria" value="<?= stripslashes($product->coupon_criteria) ?>" onChange="if (this.value != '') {showDiv('auto_add_coupon');} else {hideDiv('auto_add_coupon');}"><br />
	<span id="comment">A coupon can only be added to an order if it meets the above criteria.  If left blank, then this coupon can be added by anyone who knows the coupon code.  The criteria statement takes the form of a SQL WHERE clause.  For example, "lastname='Goodman'" or "ordeal_date>'2005-09-01'".  For assistance setting up coupon criteria, contact SectionMaster Support.</span><br>

	<div id="auto_add_coupon" <? if($product->coupon_criteria == '') print "style='visibility: hidden; display: none;'"?>>
	<label for="auto_add_coupon">Auto-Add:</label>
	<input type=hidden name="auto_add_coupon" value="0">
	<input type=checkbox name="auto_add_coupon" id="auto_add_coupon" value="1" 
		<?= $product->auto_add_coupon=='1'?"checked":"" ?>><br>
		<span id="comment">Coupons can automatically be added to a member's order.  For instance, a New Member coupon should be automatically added to give a new member discount, but a special guest coupon should not.  If members want to add a coupon that is not auto-added, they must type in the coupon code on the payment/checkout page to apply the discount.</span><br>
	</div>

	<label for="inventory">Number of Coupons Available:</label>
	<input name="inventory" id="inventory" value="<?= stripslashes($product->inventory) ?>"><br />

	
	<input type="submit" id="submit_button" name="submitted" value="Save Changes">
	
	<? if ($_add) print "<input type=\"hidden\" name=\"command\" value=\"add\">"; ?>
	
	
</form>

<? require "../post.php"; ?>