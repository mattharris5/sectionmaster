<?
// import viaKLIX url data
import_request_variables("GP");
// we don't do this anymore -- we store it in $cc_result


############################################
# Complete Order Commands
############################################


/*

ORDER COMPLETION PROCESS:

1. 	conclave_attendance.registration_complete = '1'
	conclave_attendance.current_reg_step = 'final'
	
2.	orders.total_collected
	orders.cc
	orders.paid 
	orders.cc_processed = '1'
	orders.payment_method
	orders.complete = '1'
	orders.order_timestamp = now()
	
3.  products.inventory
	
4.  Send receipt email
	Send admin email
	
5.	gotoPage('final')
	
*/

if ($EVENT['type'] != 'tradingpost') {
	# update `conclave_attendance`
	#####################################
	$sql = "UPDATE conclave_attendance
			SET registration_complete = '1', current_reg_step = 'final', conclave_order_id = '{$_SESSION['order_id']}', reg_date=NOW()
			WHERE member_id = '$member->member_id' AND conclave_year = {$EVENT['year']}";
	$res = $db->query($sql);
		if (DB::isError($res)) { dbError("Order Completion: couldn't update conclave_attendance", $res, $sql); }
}

	# update `sessions`
	#####################################
	$sql = "UPDATE sessions
			SET current_reg_step = 'final'
			WHERE session_id = {$_SESSION['session_id']}";
	$res = $sm_db->query($sql);
		if (DB::isError($res)) { dbError("Order Completion: couldn't update sessions", $res, $sql); }

	# update `orders`
	#######################################
	$sql = "UPDATE orders
			SET complete = '1', order_timestamp = now(), 
				shipping = '" . calculateShippingCost($_SESSION['order_id']) . "', ";
		
		if ($_SESSION['type'] == 'paper') // paper registration
			$sql .= " order_method = 'Paper', ";

		// free
		if ($payment_method == 'free')
			$sql .= "paid = '1', payment_method = 'free'";
		// custom
		else if ($payment_method == 'custom1') // section's custom payment method
			$sql .= "paid = '0', payment_method = 'custom1'";
		// cc
		else if ($cc_result[0] == '1')  // cc customer
			$sql .= "total_collected = '{$cc_result[9]}', paid = '1', cc_processed = '1', payment_method = 'cc', payment_notes = 'Approval: ".$cc_result[4]."', transaction_id='".$cc_result[6]."'";
		// check
		else if ($payment_method == 'check') // check
			$sql .= "paid = '1', payment_method = 'check', total_collected = '{$_REQUEST['ssl_amount']}'";
		// cash
		else if ($payment_method == 'cash') // cash
			$sql .= "paid = '1', payment_method = 'cash', total_collected = '{$_REQUEST['ssl_amount']}'";
		// other
		else if ($payment_method == 'other') // other (paid)
			$sql .= "paid = '1', payment_method = 'other', total_collected = '{$_REQUEST['ssl_amount']}'";
		// payatdoor
		else  // payatdoor customer
			$sql .= "paid = '0', payment_method = 'payatdoor'";
		
		$sql .= " WHERE order_id = '{$_SESSION['order_id']}'";
	$res = $db->query($sql);
		if (DB::isError($res)) { dbError("Order Completion: couldn't update orders", $res, $sql); }


	# update inventory counts
	#######################################
	$sql = "SELECT ordered_item_id,
				IF (product_option_id IS NULL OR product_option_id =  '0',
					'products',  'product_options') AS my_table,
				IF (product_option_id IS NULL OR product_option_id =  '0',
					'product_id',  'product_option_id') AS my_column,
				IF (product_option_id IS NULL OR product_option_id =  '0',
					product_id, product_option_id) AS my_id,
				quantity FROM ordered_items WHERE order_id = {$_SESSION['order_id']}";
	$res = $db->query($sql);
		if (DB::isError($res)) { dbError("Order Completion: couldn't select ordered items for inventory processing", $res, $sql); }
	while ($inv = $res->fetchRow()) {
		$db->query("UPDATE $inv->my_table SET inventory = inventory - $inv->quantity, last_updated=now()
					WHERE $inv->my_column = $inv->my_id 
					AND inventory - $inv->quantity >= 0");
	}


	# emails
	######################################
	$Subject = "Your {$EVENT['casual_event_name']} Receipt from SectionMaster";
	$MailHeaders = "From: {$EVENT['org_name']} <$SECTION@sectionmaster.org> \n";
	$MailHeaders.= "Reply-To: {$EVENT['support_contact_email']} \n";
	$MailHeaders.= "Bcc: registrations@sectionmaster.org \n";
	

	// receipt to member
	include ("email/event_receipt.php");	
	mail( $member->email, $Subject, $Receipt_Body, $MailHeaders );

	// trading post email to section contact (ONLY IF TP ITEMS IN CART)
	$sql = "SELECT COUNT(*) AS count FROM ordered_items
			LEFT JOIN products USING (product_id)
			WHERE order_id = '{$_SESSION['order_id']}' AND product_code != 'CONCLAVE_REG' AND coupon != '1'";
	$res = $db->getRow($sql); if (DB::isError($res)) dbError("Order Completion: sending tp email", $res, $sql);
	if ($res->count > 0) {
		include ("email/tp_email.php");
		mail( $EVENT['tp_contact_email'], "Trading Post Order", $Receipt_Body, "From: SectionMaster Order Processing <orders@sectionmaster.org>\nReply-To: $member->email\nBcc: registrations@sectionmaster.org");
	}
	
	# goto final page (or, the "receipt")
	#####################################
	gotoPage("final");

?>