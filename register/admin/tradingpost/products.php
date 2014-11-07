<?
$title = "Trading Post > Products";
require "../pre.php";
$user->checkPermissions("products");

// handle a re-arrange request
if ($_REQUEST['product_id'] && $_REQUEST['ordering']) {

	// figure out orderings to use
	$db->query("select @x:= 0");  $db->query("select @y:= 0");
	$current_ordering = $db->getOne("SELECT ordering FROM products WHERE product_id='{$_REQUEST['product_id']}'");
	$op = $_REQUEST['ordering'] == 'down' ? "+" : "-";
	$sql = "SELECT product_id, rownum $op 1 AS up, 
				(SELECT ordering FROM (
					select  @y:=@y+1 AS rownum, product_id, ordering from products where coupon != '1' and deleted != '1' order by ordering) AS rows1 
				 WHERE up = rows1.rownum
				) AS new_ordering
			FROM (select  @x:=@x+1 AS rownum, product_id, ordering from products where coupon != '1' and deleted != '1' order by ordering) AS rows1
			WHERE product_id = '{$_REQUEST['product_id']}'";
	$new_ordering = $db->getRow($sql);

	// make our switch (IF it exists)
	$product_to_switch_with = $db->getOne("SELECT product_id FROM products WHERE ordering='$new_ordering->new_ordering' LIMIT 1");
	if ($product_to_switch_with != 0) {
		$sql = "UPDATE products SET ordering = '$current_ordering' WHERE product_id='$product_to_switch_with'";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Couldn't update product ordering in $title", $res, $sql);		
	}
	
	// update requested product's ordering
	$sql = "UPDATE products SET ordering = '$new_ordering->new_ordering' WHERE product_id='{$_REQUEST['product_id']}'";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Couldn't update product ordering in $title", $res, $sql);

}


// delete product if requested
if ($_REQUEST['command'] == 'delete' && $_REQUEST['product_id'] != '') {
	$user->checkPermissions("products", 5, "You cannot delete products without full permissions on the products table.");
	$del = $db->query("UPDATE products SET deleted = 1 WHERE product_id = '{$_REQUEST['product_id']}' LIMIT 1");
		if (DB::isError($del)) dbError("Could not delete product in $title", $del, "");
}


// select product
$sql = "SELECT * FROM products WHERE coupon != '1' && deleted != '1' ORDER BY ordering ASC";
$products = $db->query($sql);
	if (DB::isError($products)) dbError("Could not get product listing in $title", $products, $sql);

?>

<h1>Product Listing</h1>
<input class="default" type=button onClick="window.location.href='/sectionmaster.org/register/admin/tradingpost/edit_product.php?command=add';" value="Add a New Product" syle="float: right;">

<table class="cart">
	<tr>
		<th colspan=2>Order</th>
		<th>Item/Description</th>
		<th>Price</th>
		<th>Inventory</th>
		<th>Shown In Store</th>
		<th>Edit</th>
	</tr>
	
<?
while ($item = $products->fetchRow()) { $i++;

	print $i%2==0 ? "<tr id=\"odd\">" : "<tr id=\"even\">";
	
	// order buttons
	print "<td style=\"width: 16px;\">";
	if ($i != 1) print "<a href=\"tradingpost/products.php?product_id=$item->product_id&ordering=up\" alt=\"Move Up\">
				<img src=\"../../templates/default/images/up.gif\" style=\"border:0;\"></a>";
	if ($i != $products->numRows())	print "<a href=\"tradingpost/products.php?product_id=$item->product_id&ordering=down\"
				alt=\"Move Down\">
				<img src=\"../../templates/default/images/down.gif\" style=\"border:0;\"></a></td>";

	// picture thumbnail
	if ($item->image_thumbnail != '') 
		print "<td id=\"image\"><img src=\"../../$item->image_thumbnail\" height=35></td>";
	else
		print "<td id=\"image\"><img src=\"templates/default/images/nophoto.gif\" height=35></td>";
	
	// item & description
	print "<td id=\"description\"><font id=\"item\">$item->item</font><br>
	 		   					  <font id=\"description\">$item->description";
		if ($item->option_name != '') {
			$sql = "SELECT * FROM product_options WHERE product_id = '$item->product_id'";
			$options = $db->query($sql);
				if (DB::isError($options)) dbError("Could not get product options for $item->item in $title", $options, $sql);
			print "<ul>";
			$running_inventory[$item->product_id] = 0;
			while ($option = $options->fetchRow()) {
				$asoftext="";
				$asof="";
				if (($option->last_updated!='0000-00-00 00:00:00') and ($option->last_updated!="")) $asoftext = " <i>as of ".date('n/j/Y',strtotime($option->last_updated))."</i></font>";
				print "<li>$option->option_value ($" . number_format($option->price,2) . "): $option->inventory in stock$asoftext</li>";
				$running_inventory[$item->product_id] += $option->inventory;
			}
			print "</ul>";
		}
		print "</font></td>";

	// price
	$price = $item->option_name!='' ? "<i>n/a</i>" : "$".number_format($item->price, 2);
	print "<td>$price</td>";
	
	// inventory
	$inventory = $running_inventory[$item->product_id] ? $running_inventory[$item->product_id] : $item->inventory;
	$inventory = $inventory==0 ? "<font color=red>OUT OF STOCK</font>" : $inventory;
	$inventory = $inventory<=5 ? "<strong>$inventory</strong>" : $inventory;
	$asoftext="";
	$asof="";
	if (($item->last_updated!='0000-00-00 00:00:00') and ($item->last_updated!="")) $asoftext = "<br><font style='text-size: 8pt;'><i>as of ".date('n/j/Y',strtotime($item->last_updated))."</i></font>";
	$inventory.=$asoftext;
	print "<td style=\"text-align: center;\">$inventory</td>";

	// in store
	print "<td style=\"text-align: center;\">"
	 		. ($item->show_in_store == 1 ? "Y": "<strong><font color=red>N</font></strong>") . "</td>";

	// edit
	print "<td>
			<input class=\"default\" type=button onClick=\"window.location='/sectionmaster.org/register/admin/tradingpost/edit_product.php?product_id=$item->product_id';\" value=\"Edit\">";
			
	if ($user->getPermissions("products") >= 5) {
		print "	<input class=\"default\" type=button
		  		 onClick=\"if (confirm('Are you sure you want to delete the $item->item?'))
								window.location='tradingpost/products.php?command=delete&product_id=$item->product_id';\" value=\"Delete\">";
	}
	
	print "</td>";
	print "</tr>";

}
?>





<? require "../post.php"; ?>