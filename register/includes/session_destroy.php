<?
############################################
#
# destroyReg() function
# Removes all traces of this registration
#
############################################

function destroyReg() {

	global $sm_db, $_SESSION;
	
	// clear from sessions table
	$sql = "DELETE FROM sessions
			WHERE member_id = '{$_SESSION['member_id']}'
			AND event_id = '{$_SESSION['event_id']}'
			LIMIT 1";
	$res = $sm_db->query($sql);
		if (DB::isError($res)) { dbError("Could not destroy session data", $res, $sql); }

	// destroy session
	unset($_SESSION);
	session_destroy();
	
	// restart session
	session_start();
	session_regenerate_id();
}

function destroySessionDataOnly() {

	global $_SESSION;

	unset($_SESSION['session_id']);
	unset($_SESSION['event_id']);
	unset($_SESSION['member_id']);
	unset($_SESSION['order_id']);
	unset($_SESSION['current_reg_step']);
	unset($_SESSION['section']);
	unset($_SESSION['reg']);
	unset($_SESSION['page']);
}



############################################
# session_regenerate_id()						// since not PHP 4.3.2
############################################
   if (!function_exists('session_regenerate_id')) {
       function php_combined_lcg() {
           $tv = gettimeofday();
           $lcg['s1'] = $tv['sec'] ^ (~$tv['usec']);
           $lcg['s2'] = posix_getpid();

           $q = (int) ($lcg['s1'] / 53668);
           $lcg['s1'] = (int) (40014 * ($lcg['s1'] - 53668 * $q) - 12211 * $q);
           if ($lcg['s1'] < 0)
               $lcg['s1'] += 2147483563;

           $q = (int) ($lcg['s2'] / 52774);
           $lcg['s2'] = (int) (40692 * ($lcg['s2'] - 52774 * $q) - 3791 * $q);
           if ($lcg['s2'] < 0)
               $lcg['s2'] += 2147483399;

           $z = (int) ($lcg['s1'] - $lcg['s2']);
           if ($z < 1) {
               $z += 2147483562;
           }

           return $z * 4.656613e-10;
       }

       function session_regenerate_id() {
           $tv = gettimeofday();
           $buf = sprintf("%.15s%ld%ld%0.8f", $_SERVER['REMOTE_ADDR'], $tv['sec'], $tv['usec'], php_combined_lcg() * 10);
           session_id(md5($buf));
           if (ini_get('session.use_cookies'))
               setcookie('PHPSESSID', session_id(), NULL, '/');
           return TRUE;
       }
   }