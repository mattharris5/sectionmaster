<?php
	$server = "localhost";
	$user = "web";
	$password = "PASSWORD";
	$database = "sm-online";

	$mysql = mysql_connect($server, $user, $password, $database);

*	if ($mysql == true)
		echo "Successfully conntected to database: " . $database . ".";
	else
		echo "Unable to connect to database " . $database . " with username " . $user . " and password " . $password . ". Please check the connection information and try again.";
