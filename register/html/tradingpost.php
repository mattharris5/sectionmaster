<? 
/*	SM-Online Registration System                      www.sectionmaster.org
	------------------------------------------------------------------------

	Trading Post Page

	------------------------------------------------------------------------ */ 
	
	# "No thanks" button
	if ($_REQUEST['no_thanks'] == true)
		gotoNextPage("tradingpost"); 
	
	# Update data
	if ($_REQUEST['submitted']) {

		foreach($_REQUEST['quantity'] as $product_id => $quantity) {

			if ($quantity != '') {

				$product_option = $_REQUEST['product_option'][$product_id];
				
			// get previous quantity
			// ----------------------
				$sql = "SELECT * FROM ordered_items
						WHERE order_id = '{$_SESSION['order_id']}'
						AND product_id = '$product_id'";
					if ($product_option != '')
						$sql .= " AND product_option_id = '$product_option'";
				$ordered_item = $db->getRow($sql);
					if (DB::isError($ordered_item)) { dbError("Could not retreive previous quantity", $ordered_item, $sql); }
				$prev_quantity = $ordered_item->quantity;
				
			// get product price
			// -----------------
				if ($product_option != '')
					$sql = "SELECT price FROM product_options WHERE product_option_id = '$product_option'";
				else
					$sql = "SELECT price FROM products WHERE product_id = '$product_id'";
					
				$product = $db->getRow($sql);
					if (DB::isError($product)) { dbError("Could not retreive product price", $product, $sql); }
				
			// figure out sql statement
			// ------------------------
				if($prev_quantity > 0) {
					$sql = "UPDATE ordered_items 
							SET quantity = '" . ($prev_quantity + $quantity) . "'
							WHERE order_id = '{$_SESSION['order_id']}'
							AND product_id = '$product_id'";
					if ($product_option != '')
						$sql .= " AND product_option_id = '$product_option'";

				} else {
					$sql = "INSERT INTO ordered_items
							SET quantity = '$quantity',
							order_id = '{$_SESSION['order_id']}',
							product_id = '$product_id',
							price = '$product->price'";
					if ($product_option != '')
						$sql .= ", product_option_id = '$product_option'";
				}
				
			// issue query
			// -----------
				$res = $db->query($sql);
					if (DB::isError($res)) { dbError("Could not update quantity", $res, $sql); }
					
			// update order ship_method if necessary
			// -------------------------------------
				if ($EVENT['type'] == 'tradingpost') {
					$res = $db->query("UPDATE orders SET shipping_method = 'ship' WHERE order_id = '{$_SESSION['order_id']}' LIMIT 1");
						if (DB::isError($res)) { dbError("Could not update initial shipping_method", $res, ""); }
				}
					
			}
		}
		
		// go to the next step
		if ($_REQUEST['addmore'])
			gotoPage("payment");
		else
			gotoNextPage("tradingpost"); 
		
	}

?>



	
<script type="text/javascript">

	function increaseQuantity(prodid) {
		var elem = document.getElementById("quantity[" + prodid + "]")
		var x = elem.value;
		x++
		elem.value = x
	}

</script>

<h2 style="display: inline; float: left;">Trading Post</h2>

<? if ($EVENT['type'] != 'tradingpost'): ?>
<form action="<? print $_GLOBALS['form_action']; ?>" method=post style="display: inline; float: right;">
	<input type=hidden name="no_thanks" value="true">
	<input type=submit value="No Thanks... Proceed to Payment" style="float: right; width: auto; margin-top: 10px;">
</form>
<? endif; ?>
<br />

	<p>Would you like to add any merchandise to your order?  Click on an image or input a quantity for each item you'd like, then click <b>Add to My Order</b>.
	
	<form name="form1" action="<? print $_GLOBALS['form_action']; ?>" method="<? print $_GLOBALS['form_method']; ?>">

	<table border=0 cellpadding=0 cellspacing=0 class="trading_post">
	
	<?
	$sql = "SELECT * FROM products
			WHERE (inactive != 1
				AND deleted != 1
				AND show_in_store = 1
				AND coupon != 1
				AND (inventory > 0 OR option_name <> '')
				AND (event_id_only is null OR event_id_only = '0'))
			OR (inactive != 1
				AND deleted != 1
				AND show_in_store = 1
				AND coupon != 1
				AND (inventory > 0 OR option_name <> '')
				AND event_id_only = '{$EVENT['id']}')
			ORDER BY ordering ASC";
	$res = $db->query($sql);
		if (DB::isError($res)) { dbError("Could not retreive products", $res, $sql); }

	$i = 0;
	# Print out product listing
	while ($product = $res->fetchRow()) { $i++; $out_of_stock = false; $prod_has_options = false;

		if ($i%3 == 1)
			print "<tr>";

		// <div>
		print "\n\n<td valign=top><div class=\"trading_post\">";
		
		// zoom link
		if ($product->image_full != '') {
			print "<a href=\"$product->image_full\" class=\"zoom_link\" target=\"_blank\">
					<span class='icon'>&nbsp;&nbsp;&nbsp;</span></a>";
		}
						
		// thumbnail
		if ($product->image_thumbnail != '') {
			print "<a href=\"javascript: increaseQuantity($product->product_id);\">
					<img src=\"$product->image_thumbnail\" height=100></a>";
		}
		
		print "<br>";
		
		// title
		print "<font id=\"title\">".stripslashes($product->item)."</font><br>";
		
		// description
		if ($product->description != '')
			print "<font id=\"description\">$product->description</font><br>";
			
		// option
		if ($product->option_name != '') {
			$prod_has_options = true;
			$opt_res = $db->query("SELECT * FROM product_options
								   WHERE product_id = $product->product_id
								   AND inventory > 0");
				if (DB::isError($opt_res)) { dbError("Could not retreive product options", $opt_res,''); }
			
			if ($opt_res->numRows() == 0) {
				$out_of_stock = true;
			} else {
				print "<label for=\"product_option[$product->product_id]\">$product->option_name:</label>";
				
				print "<select name=\"product_option[$product->product_id]\" 
					id=\"product_option[$product->product_id]\" class=\"product_option\">";
			
				while ($option = $opt_res->fetchRow()) {
					print "<option value=\"$option->product_option_id\">
								$option->option_value - $" . number_format($option->price,2) . "</option>";
				}
				
				print "</select>";
			}

			print "<br>";
		}
			
		// quantity
		if ((!$prod_has_options && $product->inventory < 1) || ($prod_has_options && $out_of_stock)) {
			$out_of_stock = true;
			print "<i>Out of Stock</i>";
		} else {
			print "<label for=\"quantity[$product->product_id]\">Qty:</label>
				<input name=\"quantity[$product->product_id]\" id=\"quantity[$product->product_id]\" class=\"quantity\">";
		}

		// price
		if ($product->option_name != '' || $out_of_stock)
			print "<font id=\"price\"></font>";
		else if ($product->price == 0)
			print "<font id=\"price\"><i>FREE!</i></font>";
		else
			print "<font id=\"price\">$" . number_format($product->price, 2) . "</font>";		
		
		// </div>
		print "</div></td>";
		
		if ($i%3 == 0)
			print "</tr>";
		
	}
	?>
	
	</table>
	
	<input id="submit_button" name="submitted" type="submit" value="Add to My Order >">
	<? if ($_REQUEST['addmore']) print "<input type=hidden name=\"addmore\" value=\"true\">"; ?>
	</form>