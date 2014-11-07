<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">



<head>

  	<title>

  		<?

		if ($closed_message)
		
			print "$closed_message";

  		else if ($EVENT['type'] == 'tradingpost')

  			print "{$EVENT['formal_event_name']}";

  		else 

  			print "Register for {$EVENT['formal_event_name']}";

  		?>

  	</title>

	<meta charset="utf-8" />

  	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

  	<meta name="MSSmartTagsPreventParsing" content="true" />



	<base href="https://<? print $_GLOBALS['secure_base_href']; ?>/">



	<link rel="stylesheet" type="text/css" href="templates/<?=$EVENT['template']?>/style.css">

	

	<style><?=$EVENT['extra_css']?></style>



</head>



<body id="body"><center>

<?
	# CLOSED FOR MAINTENANCE
	if ($closed_message)
		print "<div style=\"width:100%; text-align: center; background-color:red;\">$closed_message</div>";
?>

<?
	# IN PAPER REGISTRATION MODE
	if ($_SESSION['type'] == 'paper')
		print "<div style=\"width:100%; text-align: center; background-color:orange;\">PAPER REGISTRATION INPUT MODE</div>";

?>

	

	

	

	<div id="wrap"> <!-- #wrap - for centering -->

	

	<!-- Header -->

	<div id="header">

	

	</div>

	

	<!-- Content -->

	<div id="content"> <!-- #content wrapper -->


		<!-- Begin #main-content -->

		<div id="main-content">

		<?

		/*  Show Progress Bar  */

		if ($_SESSION['member_id']) {

			include "includes/progress_bar.php"; 

		}	


		/* Check cookie capability */ ?>
		<center><img src="register/includes/check_cookie.php"
					 style="padding: 0; margin: 0;"></center>