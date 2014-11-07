<?
########################################
#
# Connect to the Database 
#
########################################

function &db_connect($database = "sm-online") { 
	 require_once 'DB.php'; 
	 PEAR::setErrorHandling(PEAR_ERROR_RETURN); 
	 
	$dsn['phptype'] = "mysql";
	$dsn['dbsyntax'] = "mysql";
	$dsn['protocol'] = "tcp";
	$dsn['hostspec'] = "localhost";
	$dsn['database'] = $database;
	$dsn['username'] = "web";
	$dsn['password'] = "PASSWORD";
	$dsn['proto_opts'] = false;
	$dsn['option'] = false;

	 $db = DB::connect($dsn);
	 if (DB::isError($db)) {
	 	die("Could not connect: " .$db->getMessage());
	 }
	 $db->setFetchMode(DB_FETCHMODE_OBJECT); 
	 return $db; 
 } 


function dbError($message, $result, $sql) {
	
	global $EVENT, $PAGE;
	
	// setup mail
	$to = "SM-Online Error <error@sectionmaster.org>";
	$headers = "From: SM-Online Error <error@sectionmaster.org>";
	$errno = strtoupper(uniqid("SM"));
	$subject = "DB Error: $message in " . __FILE__ . "[#$errno]";
	$body = "
DB ERROR IN SM-ONLINE
---------------------
Error #:	$errno
Error:      $message
Time:       " . date("D M j g:i:s a T Y") . "
File:       " . __FILE__ . " (Line " . __LINE__ . ")
Event:      {$EVENT['id']}

DETAILS
--------
SQL Error:  " . $result->getMessage() . "
Message:    " . $message . "
Query:      " . wordwrap(str_replace("\t", "", $result->getUserInfo())) . "

SESSION VARIABLES
-----------------
_SESSION: " . print_r($_SESSION, true) ."
_REQUEST: " . print_r($_REQUEST, true) ."
_MEMBER: " . print_r($member, true) . "
_BROWSER: " . $_SERVER['HTTP_USER_AGENT'] . " ";
	
	// send mail
	mail ($to, $subject, $body, $headers);

	// kill script and print errors
	die ("<h1>Error Processing Request</h1>
		<h3>$message</h3>
		This is usually caused by a cookie issue or browser compatibility issue.
		If you do not have cookies enabled on your browser, please enable cookies from
		our site and restart the registration process.  If the problem presists, we
		recommend using another browser or another computer.
		<p>If you continue to have this problem and need further assistance, please contact
			<a href='mailto:webmaster@sectionmaster.org'>webmaster@sectionmaster.org</a>
			and reference error number $errno.
		<hr>" . date("D M j G:i:s T Y")) . "</i>";
/*	die ("<h1>". $result->getMessage() . "</h1>
		<h3>$message:</h3>
		<pre>" . wordwrap(str_replace("\t", "", $result->getUserInfo())) . "</pre>
		<hr><i>If you receive this error, please notify 
			<a href='mailto:webmaster@sectionmaster.org'>webmaster@sectionmaster.org</a>. 
		" . date("D M j G:i:s T Y")) . "</i>";
*/		
}

?>
