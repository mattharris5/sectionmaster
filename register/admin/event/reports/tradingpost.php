<?php
$sql = "SELECT products.product_id AS product_id, option_name, item, products.price, image_thumbnail, 
				SUM(ordered_items.quantity) AS sold, inventory AS remaining
		FROM orders, ordered_items, products
		WHERE complete = 1
		AND	ordered_items.order_id = orders.order_id
		AND ordered_items.product_id = products.product_id
		#AND show_in_store=1
		AND coupon = 0
		AND product_code != 'CONCLAVE_REG'
		AND products.deleted != 1
		AND ordered_items.deleted != 1
		AND orders.deleted != 1
		AND order_year = '{$EVENT['year']}'
		GROUP BY ordered_items.product_id
		ORDER BY sold DESC";					// NOTE: this only selects orders from this event's "year"
$products = $db->query($sql);
	if (DB::isError($res)) dbError("Creating tradingpost report (ADMIN AREA)", $res, $sql);

?>

<h3><?= $EVENT['year'] ?> Trading Post Report</h3>

<table class="cart">
	<tr>
		<th colspan=2>Item</th>
		<th>Price</th>
		<th>Sold</th>
		<th>Remaining</th>
	</tr>
	<!--SM:plaintext-->Item                                            Sold   Remaining<!--/SM:plaintext-->
	<!--SM:plaintext-->----------------------------------------------------------------<!--/SM:plaintext-->

<?
while($item = $products->fetchRow()) {
	
	print "<!--SM:plaintext-->" . 
			str_pad($item->item, 45)
			. str_pad($item->sold, 10, ' ', STR_PAD_BOTH)
			. str_pad($item->remaining, 10, ' ', STR_PAD_BOTH)
 			. "<!--/SM:plaintext-->";
	
	// picture thumbnail
	if ($item->image_thumbnail != '') 
		print "<td id=\"image\"><img src=\"../../$item->image_thumbnail\" height=35></td>";
	else
		print "<td id=\"image\"><img src=\"templates/default/images/nophoto.gif\" height=35></td>";
	
	// item & description
	print "<td id=\"description\"><font id=\"item\">$item->item</font><br>
	 		   					  <font id=\"description\">$item->description";

	if ($item->option_name != '') {
		$sql = "SELECT SUM(ordered_items.quantity) AS sold, option_value, product_options.price,
					product_options.inventory AS remaining
				FROM orders, ordered_items, products, product_options
				WHERE complete = 1
				AND ordered_items.order_id = orders.order_id
				AND ordered_items.product_id = products.product_id
				AND ordered_items.product_option_id = product_options.product_option_id
				#AND show_in_store=1
				AND products.deleted != 1
				AND ordered_items.deleted != 1
				AND orders.deleted != 1
				AND product_options.product_id = $item->product_id
				AND order_year = '{$EVENT['year']}'
				GROUP BY ordered_items.product_option_id
				ORDER BY item ASC, option_value ASC";
		$res2 = $db->query($sql);
			if (DB::isError($res2)) dbError("Creating tradingpost report (ADMIN AREA) - prod options", $res2, $sql);

		print "<ul>";

		while($item_option = $res2->fetchRow()) {
			
			print "<li>$item_option->option_value ($" . number_format($item_option->price,2) . "): $item_option->sold sold, $item_option->remaining remaining</li>";
			
			$running_inventory[$item->product_id] += $item_option->remaining;
			
			print "<!--SM:plaintext-->     " . str_pad($item_option->option_value, 40)
										 . str_pad($item_option->sold, 10, ' ', STR_PAD_BOTH)
										 . str_pad($item_option->remaining, 10, ' ', STR_PAD_BOTH) . "<!--/SM:plaintext-->";
										
			$total_sold += $item_option->price * $item_option->sold;
		}
		print "</ul>";
	}

	// price
	$price = $item->option_name!='' ? "<i>n/a</i>" : "$".number_format($item->price, 2);
	if ($item->option_name=='') $total_sold += $item->price * $item->sold;
	print "<td>$price</td>";
	
	// sold
	print "<td>$item->sold</td>";
	
	// inventory
	$inventory = $running_inventory[$item->product_id] ? $running_inventory[$item->product_id] : $item->remaining;
	$inventory = $inventory==0 ? "<font color=red>OUT OF STOCK</font>" : $inventory;
	$inventory = $inventory<=5 ? "<strong>$inventory</strong>" : $inventory;
	print "<td style=\"text-align: center;\">$inventory</td>";

	
	print "</tr>";

}

?>

	<tr id="total">
		<td colspan=2>TOTAL SOLD:</td>
		<td colspan=3 id='count'>$<?= number_format($total_sold,2) ?></td>
	</tr>


</table>