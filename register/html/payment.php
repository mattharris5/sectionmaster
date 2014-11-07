<?
/*	SM-Online Registration System                      www.sectionmaster.org
	------------------------------------------------------------------------

	PAYMENT PAGE (html/payment.php)

	------------------------------------------------------------------------    */

	# Validate
	validate("payment_method", "", "Please choose a payment method.");
	if ($_REQUEST['payment_method'] == 'cc') {
		validate("agree", "", "You must agree to the terms of the refund policy.<br>&nbsp;", false);
		validate("ssl_card_number", "", "Please enter your card number.");
		validate("ssl_first_name", "", "Please enter the name on the card.<br>");
		validate("ssl_last_name", "", "Please enter the name on the card.");
		validate("ssl_avs_address", "", "Please enter the billing address for this card.");
		validate("ssl_avs_zip", "", "Please enter the billing zip code for this card.");
		validate("exp_month", "", "Enter your expiration month.");
		validate("exp_year", "", "Enter your expiration year.");
		validate("ssl_cvv2cvc2", "", "Enter your Card Security Code.");
	}
	
	// confirm inventory
	$sql = "SELECT ordered_item_id, products.item, quantity, show_in_store, 
					IF (ordered_items.product_option_id IS NULL 
						OR ordered_items.product_option_id = 0, 
						products.inventory,
						product_options.inventory) AS inventory
			FROM ordered_items
			LEFT JOIN products ON products.product_id = ordered_items.product_id
			LEFT JOIN product_options ON product_options.product_option_id = ordered_items.product_option_id
			WHERE order_id = '{$_SESSION['order_id']}'
			AND coupon = 0 AND product_code <> 'CONCLAVE_REG'
			HAVING (inventory < ordered_items.quantity) OR (show_in_store = 0)";
	$res = $db->query($sql);
	//print "<pre>$sql</pre>";


	if (postValidate()) {
		
		// update inventory
		if ($res->numRows() != 0) {
			$ERROR['inventory'] = "<div class=\"error_summary\"><h3>Out of Stock</h3>
									Due to lack of inventory, your order had to be changed.  
									The following change(s) have been made: <ul>";

			while($inv = $res->fetchRow()) {
				if ($inv->show_in_store == 0)
				{
					$sql = "DELETE FROM ordered_items WHERE ordered_item_id = $inv->ordered_item_id";
					$ERROR['inventory'] .= "<li>$inv->item was removed from your order</li>";
				}
				elseif ($inv->inventory > 0) {
					$sql = "UPDATE ordered_items SET quantity = $inv->inventory
							WHERE ordered_item_id = $inv->ordered_item_id";
					$ERROR['inventory'] .= "<li>$inv->item was reduced to a quantity of $inv->inventory</li>";
				} else {
					$sql = "DELETE FROM ordered_items WHERE ordered_item_id = $inv->ordered_item_id";
					$ERROR['inventory'] .= "<li>$inv->item was removed from your order</li>";
				}
				$db->query($sql);
			}
			$ERROR['inventory'] .= "</ul></div>";
		}

		// process cc
		if ($_REQUEST['payment_method'] == 'cc' && !$ERROR['inventory']) {
			
			// check if the order is already marked as complete so we don't double charge the card
			$sql = "SELECT complete FROM orders WHERE order_id = '{$_SESSION['order_id']}'";
			$order_complete = $db->getOne($sql);

			if ($order_complete != '1') {		// note: this used to be "if ($member->registration_complete != '1')", but that made no sense
							
				// Authorize.net processing begins here
				
				$post_url = "https://secure.authorize.net/gateway/transact.dll";

				$post_values = array(
					
					// the API Login ID and Transaction Key must be replaced with valid values
					"x_login"			=> "66qEb7xS3n3",
					"x_tran_key"		=> "2B46B887kUKKvx6C",

					"x_version"			=> "3.1",
					"x_delim_data"		=> "TRUE",
					"x_delim_char"		=> "|",
					"x_relay_response"	=> "FALSE",

					"x_type"			=> "AUTH_CAPTURE",
					"x_method"			=> "CC",
					"x_card_num"		=> $_REQUEST['ssl_card_number'],
					"x_exp_date"		=> $_REQUEST['exp_month'] . $_REQUEST['exp_year'],
					"x_card_code"		=> $_REQUEST['ssl_cvv2cvc2'],

					"x_amount"			=> $_REQUEST['ssl_amount'],
					"x_invoice_num"		=> $EVENT['section_name'] . "/"
						. str_pad($EVENT['id'], 3, '0', STR_PAD_LEFT) . "-" 
						. str_pad($_SESSION['order_id'], 7, '0', STR_PAD_LEFT),
					"x_description"		=> $EVENT['section_name'] . " " 
						. $EVENT['formal_event_name']  . " Order No. " 
						. str_pad($_SESSION['order_id'], 7, '0', STR_PAD_LEFT),
					"x_po_num"			=> $EVENT['section_name'] . " " 
						. $EVENT['formal_event_name']  . " Order No. " 
						. str_pad($_SESSION['order_id'], 7, '0', STR_PAD_LEFT),
					"x_first_name"		=> $_REQUEST['ssl_first_name'],
					"x_last_name"		=> $_REQUEST['ssl_last_name'],
					"x_address"			=> $_REQUEST['ssl_avs_address'],
					"x_city"			=> $member->city,
					"x_state"			=> $member->state,
					"x_zip"				=> $_REQUEST['ssl_avs_zip'],
					"x_email"			=> $member->email,
					"x_phone"			=> $member->primary_phone,
					"x_cust_id"			=> $member->member_id,
					"x_customer_ip"		=> $_SERVER["HTTP_X_FORWARDED_FOR"]
					// Additional fields can be added here as outlined in the AIM integration
					// guide at: http://developer.authorize.net
				);

				// This section takes the input fields and converts them to the proper format
				// for an http post.  For example: "x_login=username&x_tran_key=a1B2c3D4"
				$post_string = "";
				foreach( $post_values as $key => $value )
					{ $post_string .= "$key=" . urlencode( $value ) . "&"; }
				$post_string = rtrim( $post_string, "& " );

				// This sample code uses the CURL library for php to establish a connection,
				// submit the post, and record the response.
				// If you receive an error, you may want to ensure that you have the curl
				// library enabled in your php configuration
				$request = curl_init($post_url); // initiate curl object
					curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
					curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
					curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
					curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
					$post_response = curl_exec($request); // execute curl post and store results in $post_response
					// additional options may be required depending upon your server configuration
					// you can find documentation on curl options at http://www.php.net/curl_setopt
				curl_close ($request); // close curl object

				// This line takes the response and breaks it into an array using the specified delimiting character
				$cc_result = explode($post_values["x_delim_char"],$post_response);

				// The results are output to the screen in the form of an html numbered list.
				/*print "<pre>";
				print_r($cc_result);
				print "</pre>";*/
								
				// check for success
				if ($cc_result[0] == '1') {

					// ok - proceed to final	
					require "includes/complete_order.php";
					exit;
					
				}

			} /* else {
				gotoPage("final");
			
			}*/
		}
		
		// pay-at-door
		else {
		
			if (!$ERROR['inventory']) {
			
				// ok - proceed to final
				require "includes/complete_order.php";
			}
		
		}
	}
	
	
	// Check response from Authorize.net
	if ($cc_result[0] == '1') {

		// ok - proceed to final	
		require "includes/complete_order.php";

	} elseif ($cc_result[0] != '') {  // declined or error
		
		$ERROR['cc'] = "<div class=\"error_summary\"><h3>Error Processing Card</h3>";
		
		$ERROR['cc'] .= "<b>Code ". $cc_result[2] . ": " . $cc_result[3] . "</b><br>";
		
		$ERROR['cc'] .= "Please try your transaction again.</div>";
		
		$payment_ok = false;

	}
	

	// Update quantities if requested
	if ($_REQUEST['command'] == 'update_quantities' && isset($_REQUEST['quantity'])) {
		foreach($_REQUEST['quantity'] as $ordered_item_id => $new_quantity) {
			if ($new_quantity == 0)
				$sql = "DELETE FROM ordered_items WHERE ordered_item_id = '$ordered_item_id'";
			else
				$sql = "UPDATE ordered_items SET quantity = '$new_quantity'
						WHERE ordered_item_id = '$ordered_item_id'";
			$res = $db->query($sql);
				if (DB::isError($res)) { dbError("Couldn't update quantities", $res, $sql); }
		}
	}

	// Add auto-coupons and requested coupons
	if ($_REQUEST['coupon_code'] != '')
		$sql = "SELECT * FROM products 
				WHERE coupon = '1' AND UPPER(product_code) = '" . strtoupper($_REQUEST['coupon_code']) . "' AND deleted <> 1";
	else if ($EVENT['type'] == 'tradingpost')
		$sql = "SELECT * FROM products WHERE product_id = -9999";	// we dont want to autoadd any coupons in the trading post!!
	else
		$sql = "SELECT * FROM products WHERE coupon = '1' AND auto_add_coupon = '1' AND inventory > 0 AND deleted <> 1";
		
	$coupons = $db->query($sql);
		if (DB::isError($coupons)) { dbError("Couldn't get coupon data", $coupon, $sql); }
	
	if ($coupons->numRows() == 0 && $_REQUEST['coupon_code'] != '') {
		$coupon_error = 'does_not_exist';

		// that doesn't exist yo!
		$ERROR['coupon'] = "<div class=\"error_summary\">
									<h3>Coupon Doesn't Exist</h3>
									Sorry, but that coupon does not exist ({$_REQUEST['coupon_code']}).
									Please try again or contact customer support, or submit your order 
									without a coupon code.</div>";

	}
	
	while($coupon = $coupons->fetchRow()) {
		
		$ok_to_add = true;
		
		// already in cart?  
		$sql = "SELECT product_id FROM ordered_items
				WHERE product_id = '$coupon->product_id' AND order_id = '{$_SESSION['order_id']}'";
		$res = $db->query($sql);
			if (DB::isError($res)) { dbError("Couldn't check if coupon in cart", $res, $sql); }
			
		if ($res->numRows() == 0) {

			// eligible for coupon?
			if ($coupon->coupon_criteria != '') {
				$sql = "SELECT IF($coupon->coupon_criteria, 'yes','no') AS eligible 
						FROM members WHERE member_id = {$_SESSION['member_id']}";						
				$res = $db->getRow($sql);
					if (DB::isError($res)) { dbError("Couldn't check coupon eligibility", $res, $sql); }
			}
			
			if ($res->eligible == 'no') {	
				$ok_to_add = false;
				$coupon_error = 'not_eligible';

			}
			
		} else {
			$ok_to_add = false;
			$coupon_error = 'already_in_cart';
		}
		
		if ($ok_to_add == true) {
		
			// OK, add it!
				$sql = "INSERT INTO ordered_items
						SET quantity = '1',
						order_id = '{$_SESSION['order_id']}',
						product_id = '$coupon->product_id',
						price = '$coupon->price'";
				$result = $db->query($sql);
					if (DB::isError($result)) { dbError("Couldn't add coupon to ordered items", $result, $sql); }
		}
		
		if ($_REQUEST['coupon_code'] != '') {
			if ($coupon_error == 'not_eligible')  {
			
				// not eligible - print error!
					$ERROR['coupon'] = "<div class=\"error_summary\">
											<h3>Not Eligible for Coupon</h3>
											Sorry, but you are not eligible to use that coupon ($coupon->product_code)
											or it is invalid.  
											Please try again or contact customer support.</div>";
			}
			
			elseif ($coupon_error == 'already_in_cart') {
	
				// not eligible - print error!
					$ERROR['coupon'] = "<div class=\"error_summary\">
											<h3>Coupon Already in Order</h3>
											That coupon is already in your cart.  You can not add it again.</div>";
			}
		}

	}
	
	// do a real quick validation of any of the validatable coupons
	// this makes sure that people dont cheat the system by going back and changing eligibility data and resubmitting
	$sql = "SELECT ordered_item_id, coupon_criteria FROM ordered_items
			LEFT JOIN products USING (product_id)
			WHERE order_id = '{$_SESSION['order_id']}'
			AND coupon_criteria != ''";
	$res = $db->query($sql);
		if (DB::isError($res)) dbError("Couldn't re-validate all coupons in cart", $res, $sql);
	while ($coupon = $res->fetchRow()) {
		$sql = "SELECT IF($coupon->coupon_criteria, 'yes','no') AS eligible 
				FROM members WHERE member_id = {$_SESSION['member_id']}";						
		$res2 = $db->getRow($sql);
			if (DB::isError($res2)) dbError("Couldn't check coupon eligibility (again)", $res2, $sql);
		if ($res2->eligible == 'no') {
			$res3 = $db->query("DELETE FROM ordered_items WHERE ordered_item_id = '$coupon->ordered_item_id' LIMIT 1");
				if (DB::isError($res3)) dbError("Couldn't delete ineligible coupon from order", $res3, "");
		}
	}


?>

<h2>You're Almost Done!</h2>

<p>This is the last step!  Below is a summary of your order.  Please enter your payment information below and click <b>Complete My Order!</b>  After your order has been processed, you'll quickly receive a confirmation/receipt e-mail.

<?=$ERROR['coupon']?>
<?=$ERROR['error_summary']?>
<?=$ERROR['inventory']?>
<?=$ERROR['cc']?>


<form name="form1" action="<? print $_GLOBALS['form_action']; ?>" method="<? print $_GLOBALS['form_method']; ?>">

<table class="cart">

	<tr><th id="description" colspan=2>Item</th>
		<th>Quantity</th>
		<th>Price</th>
		<th>Total</th>
	</tr>

	<?
	$sql = "SELECT ordered_item_id, products.product_id, item, description,
				   product_options.product_option_id, quantity, ordered_items.price,
				   option_name, option_value, image_thumbnail, show_in_store, coupon
			FROM ordered_items
			LEFT JOIN products ON ordered_items.product_id = products.product_id
			LEFT JOIN product_options ON ordered_items.product_option_id = product_options.product_option_id
			WHERE order_id = '{$_SESSION['order_id']}'
			ORDER BY coupon ASC, show_in_store ASC";
	$ordered_items = $db->query($sql);
		if (DB::isError($ordered_items)) { dbError("Couldn't get ordered item product info", $ordered_items, $sql); }
	
	if ($ordered_items->numRows() == 0) {
	    print "<tr><td id='description' colspan=4 style='border-right: 1px; padding: 10px;'>
	                    <center><b>You have no items in your Shopping Cart.</b>
	                    <br>Please <a href=\"{$_GLOBALS['form_action']}?goto=tradingpost\">add more merchandise</a> 
	                        to your order before continuing.
	            </td></tr>";
		$no_items = true;
	}
		
	// show a row for each item
	while ($item = $ordered_items->fetchRow()) { $i++;
	
		// <tr>
		if ($item->coupon == '1')
			print "<tr id=\"coupon\"";
		else
			print $i%2==0 ? "<tr id=\"odd\"" : "<tr id=\"even\"";
		if ($item->show_in_store != 0)
			print " onClick=\"var elem = document.getElementById('quantity[$item->ordered_item_id]'); elem.select();\"";
		print ">";
		
		// picture thumbnail
		if ($item->image_thumbnail != '') 
			print "<td id=\"image\"><img src=\"$item->image_thumbnail\" height=35></td>";
		else if ($item->coupon != 0)
			print "<td id=\"image\"><img src=\"templates/default/images/coupon.gif\" height=35></td>";
		else
			print "<td id=\"image\"><img src=\"templates/default/images/nophoto.gif\" height=35></td>";
		
		// item & description
		print "<td id=\"description\"><font id=\"item\">".stripslashes($item->item)."</font><br>
				   					  <font id=\"description\">".stripslashes($item->description);
		if ($item->option_name != '')
			print "<li>$item->option_name: $item->option_value</li>";
		print "</font></td>";
				   
		// quantity
		print "<td id=\"quantity\">";
		if ($item->show_in_store != 0) {
			$quantities_exist = true;
			print "<input id=\"quantity[$item->ordered_item_id]\" name=\"quantity[$item->ordered_item_id]\" value=\"$item->quantity\"
					onClick=\"this.select();\"
					onChange=\"this.form.submit()\">";
			print "<a id=\"remove_button\" href=\"
				javascript:
				var elem=document.getElementById('quantity[$item->ordered_item_id]');
				if (confirm('Are you sure you want to delete your $item->item from your purchase?'))
					{ elem.value='0'; elem.form.submit(); }
				\">&nbsp;</a>";
		} else {
			print "$item->quantity";
		}
		print "</td>";
		
		// price
		print "<td>" . number_format($item->price, 2) . "</td>";
		
		// item total
		$item_total = $item->quantity * $item->price;
		print "<td id=\"item_total\">$" . number_format($item_total, 2) . "</td>";
		
		// add to subtotal
		$subtotal = $subtotal + $item_total;
		
	}
	?>
	
	<tr id="subtotal">
		<td colspan=2 style="text-align:left;">
			<? if ($EVENT['do_tradingpost'] == 1 || $EVENT['type'] == 'tradingpost') 
				print "<input type=button id=\"button\" value=\"Add More Merchandise\"
				 		onClick=\"window.location='{$_GLOBALS['form_action']}?goto=tradingpost&gotoParams=addmore=true';\">"; ?>
			<input type=hidden class="hidden" name="command" value="update_quantities">
			<? if ($quantities_exist==true) print "<input type=submit id=\"button\" value=\"Update Quantities\" style=\"float:none;\">"; ?>
		</td>
		<td colspan=2>Subtotal</td>
		<td>$<?=number_format($subtotal, 2)?></td></tr>
		
	<tr id="shipping">
		<td colspan=2 rowspan=2 id="coupon">
			<div>
				<table cellpadding=0 cellspacing=0><tr><td id="image">&nbsp;</td>
				<td>
					<i>If you have received a coupon code, enter it below.</i><br>
					<label for="coupon_code" class="default">Coupon Code:</label>
					<input name="coupon_code" id="coupon_code" class="default">
					<input type="submit" value="Redeem" id="redeem" class="default">
				</td></tr></table>
			</div>
			</td>
		<td colspan=2>Shipping</td>
		<td><? 
			// calculate ship_cost
			$shipping_cost = calculateShippingCost($_SESSION['order_id']); 
			
			// find out if pickup or not
			$sql = "SELECT shipping_method FROM orders WHERE order_id = '{$_SESSION['order_id']}'";
			$res = $db->getRow($sql);
				if (DB::isError($res)) dbError("Couldn't get shipping_method for order", $res, $sql);
			$shipping_method = $res->shipping_method;
		    
			// print out ship_cost
			if ($shipping_method == 'pickup')
				print "PICKUP<br>($0.00)";
			else
				print "$" . number_format($shipping_cost, 2);
			?></td></tr>
		
	<tr id="total"><td colspan=2>Total Due</td>
		<td><?	
				// add up total cost
				$total_cost = $subtotal + $shipping_cost; 
				// confirm not negative!
				$total_cost = number_format((($total_cost < 0) ? 0 : $total_cost), 2);
				?>
			$<?=$total_cost?></td></tr>
	
</table>

</form>


<?=$ERROR['error_summary']?>
<?=$ERROR['cc']?>

<script language="JavaScript">
	// show a "Processing Payment" page on form submit
	function showProcessingPage(form) {
		if (form.payment_method.value == 'cc') {
			document.getElementById('wrap').style.display = 'none';
			document.getElementById('wrap').style.visibility = 'hidden';
			var elem = document.getElementById("process_progress");
			elem.style.display = '';
			elem.style.visibility = 'visible';
			setTimeout('document.images["pbar"].src = "https://<?=$_GLOBALS["secure_base_href"]?>/register/images/default/processing.gif"', 200);
			form.submitted.value = "Please Wait..."; // try to keep them from clicking twice
			form.submitted.onClick = "return false";
			form.submitted.disabled = true;
		}
	}
	
	function displayCC(ddval) {		
		var ccdiv = document.getElementById("cc_inputs");
		if (ddval == "cc") {
			ccdiv.style.visibility = "visible";
			ccdiv.style.display = "";
		} else {
			ccdiv.style.visibility = "hidden";
			ccdiv.style.display = "none";
		}
	}

</script>

<? if (!$no_items): ?>
<form name="form2" action="<? print $_GLOBALS['form_action']; ?>" method="GET" onSubmit="return showProcessingPage(this)">

<fieldset><legend>Payment Information</legend>

	<?
	// write shipping delivery note
	if ($EVENT['ship_delivery_note'] != '')
		$ship_delivery_note = $EVENT['ship_delivery_note'];
	else
		$ship_delivery_note = "Any ordered merchandise will ship within a week of receipt of your order unless otherwise notified.";
	if ($shipping_method == 'pickup')
		$ship_delivery_note = "Any ordered merchandise will be available for pickup at the event.";
	
	// write payment pending note
	if ($EVENT['do_online_payment'] == 1)
		$payment_pending_note = ", pending payment authorization (if paying by credit card).";
	else 
		$payment_pending_note = ".";
		
	// if tradingpost
	if ($EVENT['type'] == 'tradingpost') {
		$final_note = "When you click the \"Complete My Order!\" button, your trading post order will be submitted and you
				will receive confirmation via e-mail to $member->email in a few minutes$payment_pending_note";
		
	// if event
	} else {
		$final_note = "When you click the \"Complete My Order!\" button, you will be automatically registered for {$EVENT['casual_event_name']}
		 		and will receive confirmation via e-mail to $member->email in a few minutes$payment_pending_note";
	}
	?>

	<p><?= $final_note ?> <?= $ship_delivery_note ?></p>
	
	
	<?
	/* If this section does online payment... */
	if ($EVENT['do_online_payment'] == 1 OR $EVENT['do_payatdoor'] OR $EVENT['custom_payment_method1']): 
	
		// if they are shipping their stuff, don't allow payatdoor or custom payment method
		if ($EVENT['do_payatdoor'] == 1 && $shipping_method == 'ship') 
			print "<p>Please Note: Since you have chosen to have your trading post items shipped to you, you cannot choose
			 		the \"Pay-at-door\" option which is usually available.  If you'd like to use the pay-at-door option, please 
					<a href=\"register/?goto=shipping\">return to the shipping method page</a> and change your shipping selection.</p>";
		else if ($EVENT['custom_payment_method1'] != '' && $shipping_method == 'ship') 
			print "<p>Please Note: Since you have chosen to have your trading post items shipped to you, you cannot choose
			 		the \"{$EVENT['custom_payment_method1']}\" option which is usually available.  If you'd like to use this option, please 
					<a href=\"register/?goto=shipping\">return to the shipping method page</a> and change your shipping selection.</p>";
	
	?>
	
	<!-- Payment Method -->
	<? if ($total_cost == 0): ?>
		<input type="hidden" name="payment_method" value="free"><br>
	<? else: ?>
		<label for="payment_method">Payment Method:</label>
			<select name="payment_method" id="payment_method" onChange="displayCC(this.value);">
				<?
				$opt[get('payment_method')] = "selected";
				
				if ($EVENT['do_online_payment'] == 1) {
					$paymentdd .= "<option value=\"cc\" {$opt['cc']}>Visa, Mastercard, Discover, or American Express</option>";
					$paymentdd_count++;
					$paymentdd_cc = true;
				}
				
				if ($EVENT['do_payatdoor'] == 1 && $shipping_method != 'ship') {
					$paymentdd .= "<option value=\"payatdoor\" {$opt['payatdoor']}>Pay when I arrive at the event</option>";
					$paymentdd_count++;
				}
				
				if ($EVENT['custom_payment_method1'] != '') {
					$paymentdd .= "<option value=\"custom1\" {$opt['custom1']}>{$EVENT['custom_payment_method1']}</option>";
					$paymentdd_count++;					
				}

				if ($_SESSION['type'] == 'paper') {
					$paymentdd .= "<option value=\"check\" {$opt['check']}>Check [PAID]</option>";
					$paymentdd_count++;	
					$paymentdd .= "<option value=\"cash\" {$opt['cash']}>Cash [PAID]</option>";
					$paymentdd_count++;	
					$paymentdd .= "<option value=\"other\" {$opt['other']}>Other [PAID]</option>";
					$paymentdd_count++;	
				}
				
				if ($paymentdd_count > 1) {
					print "<option value=\"\">-- Select Payment Method --</option>";
				}
				print $paymentdd;
				?>
			</select>
			<?=$ERROR['payment_method']?><br>
	<? endif; ?>
			
		<? if ($EVENT['do_online_payment'] == 1): ?>
			<? if ((get('payment_method') == '' || get('payment_method') == 'payatdoor' || get('payment_method') == 'custom1')
			 		&& !($paymentdd_cc == true && $paymentdd_count == 1)) { 
				$myVis="hidden"; $myDis="none"; 
			} ?>
		<div id="cc_inputs" style="visibility: <?=$myVis?>; display: <?=$myDis?>;">
			
			<!-- CC# -->
			<label for="ssl_card_number">Card Number:</label>
				<input type=text name="ssl_card_number" id="ssl_card_number" value="<?=get('ssl_card_number')?>">
				<?=$ERROR['ssl_card_number']?><br>
							
			<!-- Expiration Date -->
			<label for="exp_month">Expiration Date:</label>
				<select name="exp_month" id="exp_month" onChange="updateExpDate(this.form);">
					<option value="">--</option>
					<?
					for ($i=1; $i<=12; $i++) {
						$disp = str_pad($i, 2, "0", STR_PAD_LEFT);
						print "<option value=\"$disp\"";
						if (get('exp_month') == $disp) print " selected";
						print ">$disp</option>";
					}
					?>
				</select>
				<?=$ERROR['exp_month']?>

				<select name="exp_year" id="exp_year" onChange="updateExpDate(this.form);">
					<option value="">--</option>
					<? for ($y=date("Y"); $y<=$EVENT['year']+20; $y++) {
						$opty = get('exp_year') == substr($y, 2, 2) ? "selected" : "";
						print "<option value=\"" . substr($y, 2, 2) . "\" $opty>$y</option>";
					} ?>
				</select>
				<?=$ERROR['exp_year']?><br>
				
			<!-- CVV2 -->
			<label for="ssl_cvv2cvc2">Card Security Code:</label>
				<input type=password name="ssl_cvv2cvc2" id="ssl_cvv2cvc2" value="<?=get('ssl_cvv2cvc2')?>" style="width: 4em;">
				<?=help_link('cvv2')?>
				<?=$ERROR['ssl_cvv2cvc2']?><br>
				
			<!-- Name -->
			<label for="ssl_first_name">Name on Card:</label>
				<span style="display:inline; float: left;">First: </span><input name="ssl_first_name" value="<?= get('ssl_first_name')!='' ? get('ssl_first_name') : get('firstname');?>" style="display:inline; width: 100px;">
				<?=$ERROR['ssl_first_name']?>
				<span style="display:inline; float: left; padding-left: 10px;">Last: </span><input name="ssl_last_name" value="<?= get('ssl_last_name')!='' ? get('ssl_last_name') : get('lastname');?>" style="display:inline; width: 100px;">
				<?=$ERROR['ssl_last_name']?>
				<br>

			<!-- Address -->
			<label for="ssl_avs_address">Billing Address:</label>
				<input name="ssl_avs_address" id="ssl_avs_address" value="<?
					print get('ssl_avs_address')!='' ? get('ssl_avs_address') : get('address');
				?>">
				<?=$ERROR['ssl_avs_address']?>
				<br>
				
			<!-- Zip -->
			<label for="ssl_avs_zip">Billing Zip Code:</label>
				<input name="ssl_avs_zip" id="ssl_avs_zip" value="<?
					print get('ssl_avs_address')!='' ? get('ssl_avs_zip') : get('zip');
				?>">
				<?=$ERROR['ssl_avs_zip']?>
				<br>

			<!-- Payment Note -->
			<div class="payment_note">
				<b>Note:</b> Charges will appear on your credit card statement as a charge from "SECTION MASTER."  All orders are subject to the <a href="about/refund.php?event_id=<?=$EVENT['id']?>" target="_blank">SectionMaster Refund Policy</a>.
				
				<br>
				<div class="checkboxes">
				<span>
					<input type="checkbox" name="agree" value="yes">
					I understand and agree to the terms of the 
					<a href="about/refund.php?event_id=<?=$EVENT['id']?>" target="_blank">Refund Policy</a>.
					<br><?=$ERROR['agree']?><br>
				</span>
				&nbsp;
				</div>
			</div>
			
		</div>
		
		<!-- Processing Data -->
		<input type="hidden" name="ssl_phone" value="<?=$member->primary_phone?>">
		<input type="hidden" name="ssl_email" value="<?=$member->email?>">		
		
		<? endif; ?>
				
	<? 
	/* If this section does NOT do online payment... */
	else: ?>
	
		<input type="hidden" name="no_payment" value="true">
		
	<? endif; ?>
	
	<input type="hidden" name="ssl_amount" value="<?=$total_cost?>">
	<input type="hidden" name="submitted" value="true">
	<input id="submit_button" name="submitted" type="submit" value="Complete My Order!"><br>

</fieldset>
	
</form>

<? endif; ?>