<? 
/*	SM-Online Registration System                      www.sectionmaster.org
	------------------------------------------------------------------------

	SHIPPING METHOD PAGE (html/shipping.php)

	------------------------------------------------------------------------										*/

	# Make sure there are actually trading post items in this person's cart!
	$sql = "SELECT COUNT(ordered_item_id) AS item_count FROM ordered_items
			LEFT JOIN products USING (product_id) WHERE order_id = '$order_id'
			AND product_code != 'CONCLAVE_REG' AND coupon != 1";
	$res = $db->getRow($sql);
		if (DB::isError($res)) dbError("Couldn't get item_count", $res, $sql);
	$item_count = $res->item_count;
	if ($item_count < 1)
		gotoNextPage("shipping");
	

	# Validate Data
		validate("shipping_method","","<h3>Oops!  Please try again.</h3>You must choose how you'd like to receive your trading post items.", false, true);
        
	# Post-Validate
		if (postValidate()) {
			$update = $db->query("UPDATE orders SET
									shipping_method = '". get('shipping_method') . "'
								  WHERE order_id = {$_SESSION['order_id']}");
								  
			if (DB::isError($update)) { dbError("Could not save shipping_method information", $update, ""); }

			gotoNextPage("shipping");
		}

?>

<h2>Shipping Method</h2>

	<p><?=$EVENT['section_name']?> allows you to choose how you'd like to receive your trading post items.  Please choose
		whether you'd like to have the items shipped to you (for a small shipping fee) or picked up at the next event.
		<em>Please note: If you choose to pickup your items at the event, it is <strong>your</strong> responsibility to 
		pick them up.</em></p>

		<? print $ERROR['shipping_method']; ?>

	<center>
	<form name="form1" action="<? print $_GLOBALS['form_action']; ?>" method="<? print $_GLOBALS['form_method']; ?>">


	<fieldset><legend>Shipping Method</legend>

		<?
		// get current shipping method for order
		$sql = "SELECT shipping_method FROM orders WHERE order_id = '{$_SESSION['order_id']}'";
		$res = $db->getRow($sql);
			if (DB::isError($res)) dbError("Couldn't get shipping_method for order", $res, $sql);
		?>

		<input type=radio id="shipping_method" name="shipping_method" value="ship"
		style="width: 10px; padding: 0; margin-right: 10px;"
		<? if ($res->shipping_method == 'ship') print "checked"; ?>>
			<? $shipping_cost = calculateShippingCost($_SESSION['order_id'], $db, false); ?>
			Please ship my items. ($<?=number_format($shipping_cost, 2)?>)
			<br />

		<input type=radio id="shipping_method" name="shipping_method" value="pickup" 
		style="width: 10px; padding: 0; margin-right: 10px;"
		<? if ($res->shipping_method == 'pickup') print "checked"; ?>>
			I will pick up my items at the next event. ($0.00)
		
	</fieldset>
	

	<!-- Hiddens -->
	<input type="hidden" name="section" value="<? print $SECTION ?>">
	<input type="hidden" name="page" value="shipping">
	
	<input id="submit_button" name="submitted" type="submit" value="Continue to Next Step >">

	</form>
	</center>