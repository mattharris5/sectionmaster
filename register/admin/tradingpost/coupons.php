<?
$title = "Trading Post > Coupons";
require "../pre.php";
$user->checkPermissions("coupons");

// delete coupon if requested
if ($_REQUEST['command'] == 'delete' && isset($_REQUEST['product_id'])) {
	$user->getPermissions("products",5);
	$delete = $db->query("UPDATE products SET deleted = '1' WHERE product_id = '{$_REQUEST['product_id']}' LIMIT 1");
		if (DB::isError($delete)) dbError("Could not delete coupon in $title", $delete, "");
}

// select coupons
$sql = "SELECT * FROM products WHERE coupon = '1' AND deleted != '1'";
$products = $db->query($sql);
	if (DB::isError($products)) dbError("Could not get coupon listing in $title", $products, $sql);

?>

<h1>Coupon Listing</h1>
<input class="default" type=button onClick="window.location.href='/sectionmaster.org/register/admin/tradingpost/edit_coupon.php?command=add';" value="Add a New Coupon" syle="float: right;">

<table class="cart">
	<tr>
		<th colspan=2>Item/Description</th>
		<th>Amount</th>
		<th>Inventory</th>
		<th>Edit</th>
	</tr>
	
<?
while ($item = $products->fetchRow()) { $i++;

	print $i%2==0 ? "<tr id=\"odd\">" : "<tr id=\"even\">";
	
	// picture thumbnail
	print "<td id=\"image\"><img src=\"../../templates/default/images/coupon.gif\" height=35></td>";
	
	// item & description
	print "<td id=\"description\"><font id=\"item\">$item->item</font><br>
	 		   					  <font id=\"description\">$item->description<br>
								  <ul>
										<li><strong>Coupon Code:</strong> $item->product_code</li>
										<li><strong>Eligibility Criteria:</strong> $item->coupon_criteria</li>
										". ($item->auto_add_coupon==1 ? "<li><font color=red>AUTO-ADD</font></li>" : "") . "
									</ul>
	</td>";
	

	// price
	print "<td>" . number_format($item->price, 2) . "</td>";
	
	// inventory
	$inventory = $item->inventory;
	print "<td>$inventory</td>";

	// edit
	print "<td>
			<input class=\"default\" type=button onClick=\"window.location='/sectionmaster.org/register/admin/tradingpost/edit_coupon.php?product_id=$item->product_id';\" value=\"Edit\">";
	
	// delete
	if ($user->getPermissions("coupons") >= 5) {
		print "<input class=\"default\" type=button onClick=\"if(confirm('Are you sure you want to delete the $item->item coupon?')) window.location='/sectionmaster.org/register/admin/tradingpost/coupons.php?product_id=$item->product_id&command=delete';\" value=\"Delete\">";
	}
	
	print "</td></tr>";

}
?>


<? require "../post.php"; ?>