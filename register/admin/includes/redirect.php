<?

function redirect($page, $params = '') {
	
	global $_GLOBALS;
	
	$newurl = "https://" 
				. $_GLOBALS['secure_base_href'] 
				. "/register/admin/"
				. $page . ".php";
				
	if ($params != '')
		$newurl .= "?$params";
	
	header("Location:$newurl");
	
}
	
?>