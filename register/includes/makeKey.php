<?
#####################################
#
# MakeKey function
#
#####################################

function makeKey($x = 10, $member_id = "", $db = "", $session_id = "") {

	global $EVENT, $_GLOBALS;

	$salt = "abchefghjkmnpqrstuvwxyz0123456789";
  	srand((double)microtime()*1000000); 
	

	// generate key first
	for ($i=0; $i<=$x; $i++) {
		$num = rand() % 33;
		$tmp = substr($salt, $num, 1);
		$pass = $pass . $tmp;
	}
	
/*	// if they already have a session
	if ($session_id != "" && $member_id != "") {
		$sql = "UPDATE sessions
				SET timestamp = '". time() ."'
				WHERE session_id = '$session_id'
				AND member_id = '$member_id'";
		$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not update session record $session_id", $res, $sql);

	// need new session
	} else*/
	if ($member_id != "") {
		$sql = "INSERT INTO sessions
				SET member_id = '$member_id',
					passkey = '$pass',
					event_id = '{$EVENT['id']}',
					timestamp = '". time() ."'";
		$res = $db->query($sql);
		if (DB::isError($res)) dbError("Could not update passkey for member $member_id", $res, $sql);
	}

	// return the key	
	return $pass;
}