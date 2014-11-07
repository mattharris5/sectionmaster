<?
####################################
#
# function req()
#
####################################

function req($var, $return = false) {
	
	global $member;
	
	// check if the db has returned it already
	if ($member->$var) {
		$val = $member->$var;
	
	} else if ($member[$var]) {
		$val = $member[$var];
	}
	
	// default to request var
	if ($_REQUEST[$var] === '') {
		$val = '';
	}
	
	if ($_REQUEST[$var]) {
		$val = $_REQUEST[$var];
	}
	
	if ($return) {
		return stripslashes($val);
	} else {
		print stripslashes($val);
	}

}


function get ($var) {
	
	return req($var, true);
	
}