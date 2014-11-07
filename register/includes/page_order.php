<?
########################################
#
# Page Order for SM-Online
#
########################################

// EVENT REG ORDER
$page_order = array (

	"idcreate"		=>	"ID Verification",
	"contact-info"	=>	"Contact Info",
	"health-info"	=>	"Health Info",
	"training"		=>	"Training Signup",
	"eventreg"		=>	"Activity Signup",
	"tradingpost"	=>	"Trading Post",
	"shipping"		=>	"Shipping/Pickup",
	"payment"		=>	"Payment/Finish",
	
);

// TRADING POST ORDER
$tp_page_order = array (
	
	"tradingpost"	=>	"Select Items for Purchase",
	"tp-info"		=>	"Contact Info",
	"shipping"		=>	"Shipping/Pickup",
	"payment"		=>	"Payment/Finish"

);

###### Now, calculate the correct page order for THIS section ######

function updatePageOrder() {

	global $EVENT, $page_order, $tp_page_order;

	// Remove training step
		if ($EVENT['do_training'] != '1') {
			array_remove("training", $page_order);
		}
		
	// Remove Event Reg step
		if ($EVENT['do_eventreg'] != '1') {
			array_remove("eventreg", $page_order);
		}
		
	// Remove Trading Post step
		if ($EVENT['do_tradingpost'] != '1') {
			array_remove("tradingpost", $page_order);
		}
		
	// Remove Shipping Step
		if (($EVENT['allow_pre_event_shipping'] != '1') 
		 || ($EVENT['type'] == 'tradingpost' && $EVENT['allow_at_event_pickup'] != '1')
		 || ($EVENT['do_online_payment'] != '1')
		 || ($EVENT['type'] == 'event' && $EVENT['do_tradingpost'] != '1')) {
			array_remove("shipping", $page_order);
			array_remove("shipping", $tp_page_order);
		}
	
	// Update trading post page order
		if ($EVENT['type'] == 'tradingpost') {
			$page_order = $tp_page_order;
		}
}
	

	
##### function array_remove() #######
function array_remove($search, &$array) {

	$keys = array_keys($array);

	$key = array_search($search, $keys);
	
	array_splice($array, $key, 1);
	
}