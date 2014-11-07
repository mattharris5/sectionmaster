<?php
/** Handle the Message Panel */

	function sessionMessage($msg, $msg_type) {
		$_SESSION['msg'] = $msg;
		$_SESSION['msg_type'] = $msg_type;
	}

	# Prep for Message panel
	if (isset($msg)) {
		$msgToPrint = $msg;
		$msgTypeToPrint = $msg_type;
	} 
	else if (isset($_SESSION['msg'])) {
		$msgToPrint = $_SESSION['msg'];
		$msgTypeToPrint = $_SESSION['msg_type'];
	} 
	else if (isset($_REQUEST['msg'])) {
		$msgToPrint = $_REQUEST['msg'];
		$msgTypeToPrint = $_REQUEST['msg_type'];
	}

	if ($msgToPrint != '') {
		$bodyOnLoad = "setTimeout('moveMessagePanelOffscreen()', 2500);";
		$mp = "<div id=\"message_panel\" class=\"message {$msgTypeToPrint}\">{$msgToPrint}</div>";
		$mp .= "<script>$bodyOnLoad</script>";
	}

?>

<html>

<head>
    <!-- title -->
	<title>SectionMaster Admin Portal &gt; <?=$title?></title>
	
	<!-- base href -->
	<base href="https://<?=$_GLOBALS['secure_base_href']?>/register/admin/">
	
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="admin.css">
	<link rel="stylesheet" type="text/css" href="admin-print.css" media="print">
	<link rel="stylesheet" type="text/css" href="includes/tabber.css">
	<link href="../../templates/default/pcdtr/print.css" rel="stylesheet" type="text/css" media="print" />
	<link href="../../templates/default/pcdtr/headings.css" rel="stylesheet" type="text/css" media="screen, projection" />

	<!-- ajax -->
	<script src="includes/ajax/prototype.js" type="text/javascript"></script>
	<script src="includes/ajax/scriptaculous.js?load=effects,controls" type="text/javascript"></script>

	<!-- java function -->
	<script language="JavaScript">
	
	function hideDiv(divID) {
		var myDiv = document.getElementById(divID);		
			myDiv.style.visibility = "hidden";
			myDiv.style.display = "none";
	}
	
	function showDiv(divID) {
		var myDiv = document.getElementById(divID);		
			myDiv.style.visibility = "visible";
			myDiv.style.display = "";
	}

	function toggleMenu(menuID) {
		var myMenu = document.getElementById(menuID + "Menu");

	//	if (myMenu.style.visibility == '') {
			myMenu.style.visibility = "hidden";
			myMenu.style.display = "none";
		//} else {
		//	myMenu.style.visibility = "visible";
			//myMenu.style.display = "none";
		//}
	}
	</script>
	
	<!-- tabber -->
	<script type="text/javascript" src="includes/tabber.js"></script>
	<script language="JavaScript" src="includes/getElementsByClassName.js"> </script>
	<script type="text/javascript">
		document.write('<style type="text/css">.tabber{display:none;}</style>');
	</script>

	<!-- This function resizes any tabs to fit the window -->
	<script type="text/javascript">

		function getWindowHeight() {
		  var myWidth = 0, myHeight = 0;
		  if( typeof( window.innerWidth ) == 'number' ) {
		    //Non-IE
		    myWidth = window.innerWidth;
		    myHeight = window.innerHeight;
		  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		    //IE 6+ in 'standards compliant mode'
		    myWidth = document.documentElement.clientWidth;
		    myHeight = document.documentElement.clientHeight;
		  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
		    //IE 4 compatible
		    myWidth = document.body.clientWidth;
		    myHeight = document.body.clientHeight;
		  }
		  // window.alert( 'Width = ' + myWidth );
		  // window.alert( 'Height = ' + myHeight );
			return myHeight;
		}
	
		<!-- This function moves message_panels off the top of the screen -->
		var c=0
		var t
		var max=-300
		function moveMessagePanelOffscreen() {
			if (c > max) {
				document.getElementById('message_panel').style.top=c
				c=c-1
				t=window.setTimeout("moveMessagePanelOffscreen()",10)
			} else {
				window.clearTimeout(t)
			}
		}

		<!-- Prep for tabs -->
		var pageMargin = 200  // this is how much space the header and stuff takes up
		
		function ResizeTabs() {
			var tabs = document.getElementsByClassName('tabbertab')
			var newHeight = getWindowHeight() - pageMargin
			for (var i=0, tabs_len=tabs.length; i<tabs_len; i++) {
				tabs[i].style.height = newHeight + 'px'
			}
		}
							
		window.onresize= ResizeTabs;
	
	</script>

</head>


<body <?= $bodyOnLoad ?>>

	<?= $mp ?>
	<?php
	unset( $_SESSION['msg'], $msgToPrint );
	unset( $_SESSION['msg_type'], $msgTypeToPrint );
	?>
	
	<? include "toc.php"; ?>

	<div id="container">