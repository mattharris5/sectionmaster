<?

#################################
# Select Order Info from DB
#################################
	$sql = "SELECT * FROM orders
				LEFT JOIN addresses ON orders.shipping_address = addresses.address_id
			WHERE orders.ordernum = '$_COOKIE[ordernum]'";
	$order_result = mysql_query($sql, $conn) or die(mysql_error());
	
	// Make Array
	$order_info = mysql_fetch_array($order_result);


###############################
# Read and print cart contents
###############################
	$ordernum = $_COOKIE[ordernum];
	$running_total = 0;
	
	$sql = "SELECT * FROM order_items
			LEFT JOIN products ON order_items.product_id=products.product_id
			WHERE ordernum='$ordernum'";
	$result = mysql_query($sql,$conn) or die(mysql_error());
	

#################################
# Assemble Payment Info Table
#################################

	if ($order_info[payment_method] != 'mail' && $order_info[payment_method] != 'check') {
		// if using credit card:
		list($cc1, $cc2, $cc3, $cc4) = split (" ", $order_info[cc], 4); 
		$assembled_payment_info = "Card Number: **** **** **** $cc4";

	} else {
		$assembled_payment_info = "
		**********************************************************************
		*            You have selected a mail-in payment method.             *
		*  THIS ORDER WILL NOT BE PROCESSED UNTIL PAYMENT HAS BEEN RECEIVED. *
		**********************************************************************
		*                   Please mail your $$order_info[total_price] payment to:              *
		*                     Christopher's, Inc.                            *
		*                     4932 SE 51st Ave.                              *
		*                     Portland, OR  97206                            *
		**********************************************************************";
	}

	
#################################
# Write Receipt E-Mail (TO CUSTOMER)
#################################

	$Receipt_MailFrom 	=	"Portland Ornament Sales <sales@christophers-inc.com>";
	$Receipt_MailTo		=	$order_info['email'];
	$Receipt_Subject 	= 	"Receipt for Christopher's, Inc. Order #$_COOKIE[ordernum]";

	include ("receipt_email.php");	

#################################
# Write Order Email (TO BETTY)
#################################

	$Order_MailFrom 	=	$order_info['firstname'] . " " . $order_info['lastname'] . "<" . $order_info['email'] . ">";
	$Order_MailTo		=	"ptldornmt@aol.com";
	$Order_Subject 		= 	"Christopher's, Inc. Online Order from " . $order_info['firstname'] . " " . $order_info['lastname'] . " (#" . $_COOKIE['ordernum'] . ")";

	include ("order_email.php");
	

#################################
# Send both E-Mails
#################################

	mail($Receipt_MailTo, $Receipt_Subject, $Receipt_Body, "From: $Receipt_MailFrom");
	mail($Order_MailTo, $Order_Subject, $Order_Body, "From: $Order_MailFrom");
		
	$command = "proceed";

?>