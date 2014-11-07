<?
/* 
Calculate Shipping Cost Function 
--------------------------------
This function calculates the shipping cost for an order and updates the value in the order record.
	@param order_id, database
	@global EVENT[], db
	@return ship_cost

Shipping Calculation Methods:
	by_weight	=	sum of item weights (in ounces), multiplied by ship_calculation
	flat_amt		=	return ship_calculation
	flat_rate	=	number of items, multiplied by ship_calculation
	per_item		=	sum of each item's ship_cost

***Note: All shipping costs must meet the min_ship_cost stipulation.

*/

function calculateShippingCost($order_id = 99999999, $database = '', $update_db = true) {
	
	// get globals
	global $EVENT, $db;
	
	// get database object
	$db = $database=='' ? $db : $database;
	
	// get shipping prefs from EVENT
	$method = $EVENT['ship_calculation_method'];
	$calculation = $EVENT['ship_calculation'];
	$min_ship_cost = $EVENT['min_ship_cost'];
	
	// calculate shipping cost
	$ship_cost = 0.0;
	switch ($method) {
		
		// by_weight
		case "by_weight":
			$sql = "SELECT SUM(quantity * ship_weight) AS ship_weight FROM ordered_items
					  LEFT JOIN products using (product_id)
					  WHERE order_id = '$order_id'
					  AND product_code != 'CONCLAVE_REG' AND coupon != 1";
			$res = $db->getRow($sql);
				if (DB::isError($res)) dbError("Couldn't get sum of ship_weights", $res, $sql);
			$ship_weight_sum = $res->ship_weight;
			$ship_cost = $ship_weight_sum * $calculation;
			break;
			
		// flat_amt
		case "flat_amt":
			$ship_cost = $calculation;
			break;
			
		// flat_rate
		case "flat_rate":
			$sql = "SELECT SUM(quantity) AS item_count FROM ordered_items
					LEFT JOIN products USING (product_id) WHERE order_id = '$order_id'
					AND product_code != 'CONCLAVE_REG' AND coupon != 1";
			$res = $db->getRow($sql);
				if (DB::isError($res)) dbError("Couldn't get item_count", $res, $sql);
			$item_count = $res->item_count;
			$ship_cost = $item_count * $calculation;
			break;
			
		// per_item
		case "per_item":
			$sql = "SELECT SUM(quantity * ship_cost) AS ship_cost_sum FROM ordered_items
			 		  LEFT JOIN products USING (product_id)
					  WHERE order_id = '$order_id'
	  				  AND product_code != 'CONCLAVE_REG' AND coupon != 1";
			$res = $db->getRow($sql);
				if (DB::isError($res)) dbError("Couldn't get ship_cost_sum", $res, $sql);
			$ship_cost = $res->ship_cost_sum;
			break;
	}
	
	// confirm greater than min_ship_cost
	$ship_cost = $ship_cost < $min_ship_cost ? $min_ship_cost : $ship_cost;
	
	// update DB
	if ($update_db == true) {

		// if shipping_method is not "ship" for this order, then ship_cost is zero
		$sql = "SELECT shipping_method FROM orders WHERE order_id = '$order_id'";
		$res = $db->getRow($sql);
			if (DB::isError($res)) dbError("Couldn't get shipping_method for order", $res, $sql);
		if ($res->shipping_method != 'ship') {
			$ship_cost = 0;
		}

		// update order record with shipping cost
		$sql = "UPDATE orders SET shipping = '$ship_cost' WHERE order_id = '$order_id' LIMIT 1";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Couldn't update shipping cost in order record", $res, $sql);
	}
	
	// return value to user
	return $ship_cost;
		
}

?>