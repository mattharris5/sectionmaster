<?
/** This script handles what to do if we need to close the system for maintenance.  **/


# look for a "closeall" file in the /register/ directory
	if (!is_file("closeall"))
		return;  // no closeall file, so just proceed like normal

# get the current http request's IP
	$myIP = $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

# check the file for any allowed ip's
	$ipfile = file("closeall");
	reset($ipfile);
	foreach ($ipfile as $line) {
		$line = rtrim($line);
		$line = ltrim($line);
		if ($line == "" || $line == "\n" || strstr($line,"#") === 0) {
			next($ipfile);
		} else {
			if ($myIP == $line) $allow = true;
		}
	}
	
# if this IP isn't allowed, then don't let em in!
	if (!$allow) {
		
		die("<html>
				<head>
					<title>Temporary Maintenance</title>
					<base href=\"https://ssl4.westserver.net/sectionmaster.org/\">
					<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/default/style.css\">
				</head>
				
				<body>
				<center>
				<div class=\"error_summary\" style=\"width:450px; margin: 200px; text-align:left;\"><h3>System Offline</h3>
					<p>We're sorry, but SectionMaster is temporarily undergoing system maintenance.</p>
					
					<p>If you are in the middle of an event registration or trading post order, you will be able to continue where you left off when the maintenance is complete.  To do so, you can leave your web browser open and hit the Refresh button when the maintenance is complete, or you can close your web browser and return to this site later and enter your e-mail address to log back in to where you left off.</p>
					<p>We apologize for the inconvenience this may cause, but we sincerely appreciate your patience.</p>
					<br></div>
				</body>
			</html>
			");
		
	} else {

		$closed_message = "WARNING: SM-ONLINE IS DOWN FOR MAINTENANCE TO THE PUBLIC!";
//		print "<center><b>$closed_message</b></center>";
		
	}


?>