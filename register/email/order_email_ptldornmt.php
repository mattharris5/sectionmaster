<?
$Order_Body		=	

"
                        ONLINE ORDER #$order_info[ordernum]
                          christophers-inc.com

TO PROCESS THIS ORDER:
$SECUREPATH/admin/admin.php

ORDER RECEIVED FROM:
$order_info[firstname] $order_info[lastname]
$order_info[company]
$order_info[street1]
$order_info[street2]
$order_info[city], $order_info[state]  $order_info[zip]
($order_info[phone_area]) $order_info[phone1]-$order_info[phone2] x. $order_info[phone_ext]
$order_info[email]
	
PAYMENT INFORMATION:
Payment Method: $order_info[payment_method]
$assembled_payment_info	

$assembled_cart

";



?>
