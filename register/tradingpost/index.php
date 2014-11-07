<?
/* Includes */
	require_once "../includes/db_connect.php";
	require "../includes/globals.php";
	require "../includes/validate.php";
	require "../includes/req.php";
	require "../includes/event_info.php";
	require "../includes/member_functions.php";
	include "../includes/time_until.php";

/* Connect to SM-Online DB */
	$sm_db =& db_connect("sm-online");	// Global SM-Online db


/* Event Info */
	if ($_REQUEST['event']) {
		$EVENT 	 = event_info($sm_db, $_REQUEST['event']);
		$SECTION = section_id_convert($EVENT['section_name']);
		$db =& db_connect($SECTION);		// Section's unique db
	}

# Check for event
if (!$EVENT) die("Choose event.");

?>

<style>
/* ---( Trading Post )--- */
div.trading_post {
	display: inline;
	border: 1px solid #cacaca;
	padding: 10px;
	float: left;
	margin: 0 20px 20px 0;
	min-width: 160px; 
	min-height: 130px; 
	text-align: center;
	line-height: 13px;
}

div.trading_post img {
	border: 0;
	cursor: hand;
}

div.trading_post #title {
	font-weight: bold;
}

div.trading_post #description, div.trading_post #price {
	color: #444;
}

div.trading_post select.product_option {
	width: 90%;
	max-width: 90%;
	margin: 4px;
	float: none;
	display: inline;
}

div.trading_post label {
	display: inline;
	width: auto;
	float: none;
	margin: 4px;
	padding: 0;
}

div.trading_post #price {
	float:none;
	margin: 4px;
}

</style>

<table border=0 cellpadding=0 cellspacing=0 class="trading_post" align="center">

<?
$sql = "SELECT * FROM products
		WHERE (inactive != 1
			AND deleted != 1
			AND show_in_store = 1
			AND coupon != 1
			AND (event_id_only is null OR event_id_only = '0'))
		OR (inactive != 1
			AND deleted != 1
			AND show_in_store = 1
			AND coupon != 1
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
	
	// thumbnail
	if ($product->image_thumbnail != '') {
		print "<a href=\"http://www.sectionmaster.org/$product->image_full\" class=\"zoom_link\" target=\"_blank\">
		<img src=\"http://www.sectionmaster.org/$product->image_thumbnail\" height=100></a>";
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
