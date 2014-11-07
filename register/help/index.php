<?
####################################
#
# SM-Online Help System
#
####################################

	require_once "../includes/db_connect.php";
	require "../includes/globals.php";
	require "../includes/event_info.php";

	$sm_db =& db_connect("sm-online");	// Global SM-Online db
	$EVENT = event_info($sm_db, $_REQUEST['event_id']);
	$template = $_REQUEST['event_id'] ? $EVENT['template'] : "default";

	ob_start();
	
	// include the content file
	if (!include "{$_REQUEST['topic']}.html") 
		print "Sorry, no metching help topics found.";
	
	// flush the buffer
	$_OUTPUT['html_content'] = ob_get_contents();
	ob_end_clean();
	
	
####################################
?>	
	
<html>
<head>
	<title>Help > <? print $title ?></title>

	<link rel="stylesheet" type="text/css" href="../../templates/<?=$template?>/style.css">

</head>

<body class="help_content">

	<div class="toolbar">
	
		<div class="content">Online Help</div>
		
		<div class="button" id="right"><a href="javascript:window.close();" onClick="window.close();">Close Window</a></div>
		
	</div>


	<div class="main">

		<h2><? print $title; ?></h2>
		
		<? print $_OUTPUT['html_content']; ?>

	</div>
	
</body>
</html>