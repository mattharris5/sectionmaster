<?

	$order = $db->getRow("SELECT * FROM orders WHERE order_id = '{$_SESSION['order_id']}'");
		if (DB::isError($order)) { dbError("Could not get order data", $order, ''); }


###############################
# Write out the Cart
###############################
	
	$assembled_cart = "
Qty Product                                              Price   Total
------------------------------------------------------------------------";
	
	$sql = "SELECT ordered_item_id, products.product_id, item, description,
				   product_options.product_option_id, quantity, ordered_items.price,
				   option_name, option_value, image_thumbnail, show_in_store, coupon
			FROM ordered_items
			LEFT JOIN products ON ordered_items.product_id = products.product_id
			LEFT JOIN product_options ON ordered_items.product_option_id = product_options.product_option_id
			WHERE order_id = '{$_SESSION['order_id']}'
			ORDER BY coupon ASC, show_in_store ASC";
	$ordered_items = $db->query($sql);
	//	if (DB::isError($ordered_items)) { dbError("Couldn't get ordered item product info", $ordered_items, $sql); }
		
	// show a row for each item
	while ($item = $ordered_items->fetchRow()) { $i++;
	
		$row = str_pad("$item->quantity", 3, " ", STR_PAD_BOTH) . " ";
		
		$item_desc = $item->item;
		if ($item->option_name != '')
			$item_desc .= " ($item->option_name: $item->option_value)";
		
		$row .= str_pad($item_desc, 50) . " ";

		$row .= str_pad(number_format($item->price, 2), 7, " ", STR_PAD_LEFT) . " ";
		
		$item_total = $item->quantity * $item->price;
		$row .= str_pad(number_format($item_total, 2), 7, " ", STR_PAD_LEFT) . " ";
		
		$subtotal = $subtotal + $item_total;
		
		$assembled_cart .= "\n$row";
	}

	$assembled_cart .= "\n------------------------------------------------------------------------";

	// subtotal
	$assembled_cart .= "\n" . str_pad("Subtotal: ", 60, " ", STR_PAD_LEFT)
					.  str_pad(number_format($subtotal, 2), 10, " ", STR_PAD_LEFT);

	// shipping
	$shipping = $order->shipping;
	$assembled_cart .= "\n" . str_pad("Shipping: ", 60, " ", STR_PAD_LEFT)
					.  str_pad(number_format($shipping, 2), 10, " ", STR_PAD_LEFT);

	// total
	$order_total = $subtotal + $shipping;
	$due_paid = $order->paid!='1' ? "Due" : "Paid";
	$assembled_cart .= "\n" . str_pad("Total $due_paid: ", 60, " ", STR_PAD_LEFT)
					.  str_pad(number_format((($order_total < 0) ? 0 : $order_total), 2), 10, " ", STR_PAD_LEFT);

#################################
# Write the E-Mail
#################################


$Receipt_Body		=	

"Dear $member->firstname,

Thank you for placing an order online with {$EVENT['org_name']}. This is your e-mail confirmation as well as your receipt.  We recommend that you print this e-mail and keep a copy for your records.  To access this information again in the future, visit http://www.sectionmaster.org/register?event_id={$EVENT['id']} and enter your e-mail address.

";

$Receipt_Body .= "ORDER # " . $EVENT['section_name'] . "/" 
	. str_pad($EVENT['id'], 3, '0', STR_PAD_LEFT) . "-"
	. str_pad($_SESSION['order_id'], 7, '0', STR_PAD_LEFT) . " - RECEIPT
Order Date: $order->order_timestamp
";

$Receipt_Body .= "
- PERSONAL INFORMATION -
$member->firstname $member->lastname (ID# $order->member_id)
$member->address";
$Receipt_Body .= $member->address2 != '' ? "\n$member->address2" : "";
$Receipt_Body .= "
$member->city, $member->state  $member->zip
$member->primary_phone
$member->email

- PAYMENT INFORMATION -
";
// cc
if ($order->payment_method == 'cc') {
$Receipt_Body .= "Credit Card: $order->cc";
$Receipt_Body .= $order->paid == '1' ? " [PAID]" : "";
$Receipt_Body .= "\nTotal Collected: $" . number_format($order->total_collected, 2);

// check
	} elseif ($order->payment_method == 'check') {
$Receipt_Body .= "Check";
$Receipt_Body .= $order->paid == '1' ? " [PAID]" : "";
$Receipt_Body .= "\nTotal Collected: $" . number_format($order->total_collected, 2);

// cash
	} elseif ($order->payment_method == 'cash') {
$Receipt_Body .= "Cash";
$Receipt_Body .= $order->paid == '1' ? " [PAID]" : "";
$Receipt_Body .= "\nTotal Collected: $" . number_format($order->total_collected, 2);

// other - paid
	} elseif ($order->payment_method == 'other') {
$Receipt_Body .= "Other";
$Receipt_Body .= $order->paid == '1' ? " [PAID]" : "";
$Receipt_Body .= "\nTotal Collected: $" . number_format($order->total_collected, 2);

// custom1
	} elseif ($order->payment_method == 'custom1') {
$Receipt_Body .= $EVENT['custom_payment_method1'];
$Receipt_Body .= "\nTotal Due: $" . number_format($order->total_collected, 2);

// free
	} elseif ($order->payment_method == 'free') {
$Receipt_Body .= "NO CHARGE";
$Receipt_Body .= "\nTotal Due: None";

// pay-at-door
	} else {
$Receipt_Body .= "Payment Method: Pay-at-Door
Total Due: $" . number_format($order_total, 2) . 
"\n(Pay when you arrive at the event.)";
	}
	
$Receipt_Body .= "

- YOUR ORDER -
$assembled_cart
";

if ($order->payment_method == 'cc') {
$Receipt_Body .= "
Charges will appear on your credit card statement as a charge from \"SECTION MASTER.\"  All orders are subject to the SectionMaster Refund Policy available at http://www.sectionmaster.org/about/refund.php?event_id={$EVENT['id']}.
";
}

$Receipt_Body .= "
Should you have any questions about your purchase, please send an e-mail
to {$EVENT['reg_contact_email']}.  Thank you!


------------------------------------------------------------------------
      Event registration services for {$EVENT['org_name']} are provided by 
              SectionMaster - www.sectionmaster.org                     
------------------------------------------------------------------------

";

?>