<?
require "globals.php";
require "db_connect.php";

/* -----------------------------------------------------------------
	ProcessCC.php
	-------------
	This script takes input from the SectionMaster desktop program
	and processes cc payments securely with Authorize.net
    ---------------------------------------------------------------- */

	// Ensure secure connection
	if ($_SERVER['HTTP_X_FORWARDED_HOST'] != $_GLOBALS['secure_host']) {
		$newurl = "https://" . $_GLOBALS['secure_base_href'] . $_SERVER['REQUEST_URI'];
		header( "Location: $newurl" );
	}

	// Check login
	$sm_db =& db_connect('sm-online');
	$sql = "SELECT cc_processors.*, section_name, formal_event_name
			FROM cc_processors 
			JOIN events ON cc_processors.event_id = events.id
			WHERE cc_processors.user_id = '{$_REQUEST['user_id']}'
			AND cc_processors.pin = '{$_REQUEST['pin']}'
			AND cc_processors.event_id = '{$_REQUEST['event_id']}'";
	$res = $sm_db->query($sql);
		if (DB::isError($res)) die("SM_ERROR: Database error. Please try again.");

	// if no rows, bad login
	if ($res->numRows() == 0)
		die("SM_ERROR: Not authorized for live remote processing by SectionMaster.  Check the User ID and PIN under Tools/Options.");
		
	// fetch data
	$data = $res->fetchRow();

	// Check valid input - no blank fields
	$required_fields = array("ccnum",
							 "expdate",
							 "amount", 
							 "order_id", 
							 "address", 
							 "zip",
							 "firstname", 
							 "lastname", 
							 "phone");
	foreach($required_fields as $id => $field) {
		if ($_REQUEST[$field] == '' OR !$_REQUEST[$field]) {
			die ("SM_ERROR: Missing required information: " . strtoupper($required_fields[$id]) );
		}
	}
	
/*	// Send to viaKlix
	header("Location:https://www.viaklix.com/process.asp?" 
		. "ssl_amount=" . urlencode($_REQUEST['amount'])
		. "&ssl_card_number=" . urlencode($_REQUEST['ccnum'])
		. "&ssl_exp_date=" . urlencode($_REQUEST['expdate'])
		. "&ssl_avs_address=" . urlencode(preg_replace("/,/", "", $_REQUEST['address']))
		. "&ssl_avs_zip=" . urlencode(substr($_REQUEST['zip'], 0, 5))
		. "&ssl_first_name=" . urlencode($_REQUEST['firstname'])
		. "&ssl_last_name=" . urlencode($_REQUEST['lastname'])
		. "&ssl_phone=" . urlencode($_REQUEST['phone'])
		. "&ssl_email=" . urlencode($_REQUEST['email'])
		. ($_REQUEST['cvv2']!='' ? '&ssl_cvv2=PRESENT&ssl_cvv2cvc2='.urlencode($_REQUEST["cvv2"])
								: '&ssl_cvv2=NOT+PRESENT')
		. "&ssl_merchant_id=MERCHANT_ID"
		. "&ssl_user_id=MERCHANT_USER_ID"
		. "&ssl_pin=MERCHANT_PIN"
		. "&ssl_show_form=false"
		. "&ssl_country=USA"
		. "&ssl_salestax=0.00"
		. "&ssl_customer_code=1"
		. "&ssl_result_format=ASCII"
		. "&ssl_invoice_number=" . urlencode(
			  $data->section_name . "/"
			. str_pad($data->event_id, 3, '0', STR_PAD_LEFT) . "-" 
			. str_pad($_REQUEST['order_id'], 7, '0', STR_PAD_LEFT))
		. "&ssl_description=" . urlencode(
			  $data->section_name . " "
			. $data->formal_event_name . " Order No. " 
			. str_pad($_REQUEST['order_id'], 7, '0', STR_PAD_LEFT))
	);*/
	
	
				// Authorize.net processing begins here
				
				$post_url = "https://secure.authorize.net/gateway/transact.dll";

				$post_values = array(
					
					// the API Login ID and Transaction Key must be replaced with valid values
					"x_login"			=> "X_LOGIN",
					"x_tran_key"		=> "X_TRAN_KEY",

					"x_version"			=> "3.1",
					"x_delim_data"		=> "TRUE",
					"x_delim_char"		=> "|",
					"x_relay_response"	=> "FALSE",

					"x_type"			=> "AUTH_CAPTURE",
					"x_method"			=> "CC",
					"x_card_num"		=> $_REQUEST['ccnum'],
					"x_exp_date"		=> $_REQUEST['expdate'],
					//"x_card_code"		=> $_REQUEST['ssl_cvv2cvc2'],

					"x_amount"			=> $_REQUEST['amount'],
					"x_invoice_num"		=> $data->section_name . "/"
						. str_pad($data->event_id, 3, '0', STR_PAD_LEFT) . "-" 
						. str_pad($_REQUEST['order_id'], 7, '0', STR_PAD_LEFT),
					"x_description"		=> $data->section_name . " "
						. $data->formal_event_name . " Order No. " 
						. str_pad($_REQUEST['order_id'], 7, '0', STR_PAD_LEFT),
					"x_po_num"			=> $data->section_name . " "
						. $data->formal_event_name . " Order No. " 
						. str_pad($_REQUEST['order_id'], 7, '0', STR_PAD_LEFT),
					"x_first_name"		=> $_REQUEST['firstname'],
					"x_last_name"		=> $_REQUEST['lastname'],
					"x_address"			=> $_REQUEST['address'],
					//"x_city"			=> $member->city,
					//"x_state"			=> $member->state,
					"x_zip"				=> substr($_REQUEST['zip'], 0, 5),
					"x_email"			=> $_REQUEST['email'],
					"x_phone"			=> $_REQUEST['phone'],
					//"x_cust_id"			=> $member->member_id,
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

	// Check response from Authorize.net
	if ($cc_result[0] == '1')
	{
		print "ssl_result_message=APPROVED\n";
		print "ssl_approval_code=".$cc_result[4]."\n";
		print "ssl_invoice_number=".$cc_result[7]."\n";
		print "ssl_description=".$cc_result[8]."\n";
		print "done";
		/*print "<pre>";
		print_r($cc_result);
		print "</pre>";*/
	}
	elseif ($cc_result[0] != '')
	{  // declined or error
		print "DECLINED.  Code ". $cc_result[2] . ": " . $cc_result[3];
	}

?>