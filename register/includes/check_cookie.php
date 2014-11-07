<?

if (!isset($_COOKIE['check']))

	readfile("../images/default/cookies_warning.gif");
	
else

	readfile("../images/default/blank.gif");
	
?>