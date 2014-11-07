<?

########################################
#
# SM-Online Data Validation Functions
#
########################################

// sample red "star"
$ERROR['sample'] = "<font style=\"color: red; font-size: 125%; font-weight: bold;\">*</font>";


function validate($field, $criteria, $message, $show_star = true, $show_as_summary = false) {
	// define $ERROR var to hold error messages
	global $ERROR, $errors_found, $_GLOBALS;

	if (isset($_REQUEST['submitted'])) {

		// if $criteria is a function, handle differently
		if(strstr($criteria, "()")) {
			$tempFunc = substr($criteria, 0, strlen($criteria)-2);
			$result = call_user_func($tempFunc, $_REQUEST[$field]);
		} 
		
		// otherwise, just check if the value is blank
		else if(!strstr($criteria, "()") && $_REQUEST[$field] == $criteria) {
			$result = false;

		} 
		
		// no errors then
		else {
			$result = "valid";
		}

		// if there was an error, print it	
		if ($result != "valid") {
		
			$errors_found = true;
		
			// Construct html code for error			
			if ($show_star == true)
				$ERROR[$field] = "<div class=\"error_message\">" .	$message . "</div>";
			elseif ($show_as_summary == true)
				$ERROR[$field] = "<div class=\"error_summary\">" .	$message . "</div>";
			else
				$ERROR[$field] = "<div class=\"error_message\" id=\"no_arrow\">" .	$message . "</div>";
	
		}
			
	} else {
			
		// not submitted yet
		if ($show_star == true)
			$ERROR[$field] = "<div class=\"error_message\" id=\"required\">*</div>";

	}			
}

########################################

function postValidate() {

	global $ERROR, $errors_found, $_GLOBALS;

	if ($errors_found != true && isset($_REQUEST['submitted'])) {

		// if no errors found, return true
		return true;
	
	} else if ($errors_found == true && isset($_REQUEST['submitted'])) {
	
		// if errors found, show them at the top of the page
		$ERROR['error_summary'] = "<div class=\"error_summary\"><font class=\"error_summary\">
			<h3>Oops!  Please try again.</h3>There was a problem with the data that you submitted.  Please correct the errors marked in red and submit the form again.";
		
/*		Please correct the errors in the following fields and submit this form again: ";
		
		$first = true;
		
		foreach($ERROR as $field => $message) {
			
			if ($field != 'error_summary') {
				
				$ERROR['error_summary'] .= $first == false ? ", " : "";
				
				$ERROR['error_summary'] .= $field;
				
				$first = false;
				
			}
		}
*/		
		$ERROR['error_summary'] .= "</font></div>";
		
		// return bad
		return false;
	
	}
}


############################################
#
# Custom Validation Functions
#
############################################

/* validEmail function */
function validEmail($email) {
	if ($email == "") {
		return false;
	}
	
	// Check address syntax
	if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
		return false;
	}

	// Validate using DNS and SMTP to confirm that this e-mail address in fact exists and accepts mail
	/* THIS IS CAUSING ISSUES FOR SBCGLOBAL USERS AND POSSIBLY OTHERS - AS OF 9/5/2012
	  // don't report the PHP errors
	  error_reporting(0);
	  
	  // gets domain name
	  list($username,$domain)=split('@',$email);
	  // checks for if MX records in the DNS
	  $mxhosts = array();
	  if(!getmxrr($domain, $mxhosts))
	  {
		// no mx records, ok to check domain
		if (!fsockopen($domain,25,$errno,$errstr,30))
		{
		  return false;
		}
		else
		{
		  return true;
		}
	  }
	  else
	  {
		// mx records found
		foreach ($mxhosts as $host)
		{
		  if (fsockopen($host,25,$errno,$errstr,30))
		  {
			return true;
		  }
		}
		return false;
	  }
	*/
	return true;
}

/* validBirthdate function */
function validBirthdate($arg) {
	$bday_month = req('bday_month', true);
	$bday_day = req('bday_day', true);
	$bday_year = req('bday_year', true);

	$bday = mktime(0, 0, 0, $bday_month, $bday_day, $bday_year);
	if ($bday_month=='' || $bday_day=='' || $bday_year=='') {
		return false;
	}
	if ($bday > (time() - (11*365*24*60*60))) {
		return false;
	}
	return true;
}

function over13($arg) {
	$bday_month = req('bday_month', true);
	$bday_day = req('bday_day', true);
	$bday_year = req('bday_year', true);
	$age = getAge("$bday_month/$bday_day/$bday_year");
	
	if ($age < 13) {
		return false;
	}
	return true;


}
		
?>
