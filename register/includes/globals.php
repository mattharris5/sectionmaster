<?

############################################
#
# SM-Online Global Values
#
############################################

global $_GLOBALS;

/* Where to send https requests */
$_GLOBALS["secure_base_href"] 	= "ssl4.westserver.net/sectionmaster.org";
$_GLOBALS["secure_host"]		= "ssl4.westserver.net";

/* Default form action and method */
$_GLOBALS["form_action"]		= "https://" . $_GLOBALS["secure_base_href"] . "/register/";
$_GLOBALS["form_method"]		= "get";

/* Basedir when including files */
$_GLOBALS["basedir"]			= "/var/www/html/";

/* Link for login email and such */
$_GLOBALS["entry_url"]			= "http://sectionmaster.org/register";

/* Session Length (in seconds) */
$_GLOBALS["session_length"]		= 60*30; // 30 minutes of idle time


?>