<?php 

###############################################################################
# SectionMaster.org Registration System                 www.sectionmaster.org #
###############################################################################
# Copyright (c) 2005-2007 Clostridion Design & Support, LLC.
# All included files and modules of this system share the above copyright.
# All rights are reserved by Clostridion Design & Support, LLC.
###############################################################################

#error_reporting(E_ALL ^ E_NOTICE);

session_start();
ob_start();
require "includes/maintenance.php";
require "includes/handle_backdoor.php";
/*if ($_SESSION['backdoor'] != 'open') {
	die ("Registration is temporarily closed.  We apologize for the inconvenience.");
}*/

/* Includes */
	require_once "includes/db_connect.php";
	require "includes/globals.php";
	require "includes/validate.php";
	require_once "includes/page_order.php";
	require "includes/gotoPage.php";
	require "includes/req.php";
	require "includes/event_info.php";
	include "includes/help_link.php";
	require_once "includes/makeKey.php";
	require "includes/session_destroy.php";
	require "includes/member_functions.php";
	require "includes/calculateShippingCost.php";

/* Don't cache me!! */
	header("Expires: Mon, 25 Jul 1984 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

/* Ensure secure connection */
	if ($_SERVER['HTTP_X_FORWARDED_HOST'] != $_GLOBALS['secure_host']) {
		$newurl = "https://" . $_GLOBALS['secure_base_href'] . $_SERVER['REQUEST_URI'];
		header( "Location: $newurl" );
	}

/* Create test cookie to ensure browser capability */
	setcookie("check", "check");	
	
/* Connect to SM-Online DB */
	$sm_db =& db_connect("sm-online");	// Global SM-Online db


/* Start Session */

	// destroy session if requested
	if ($_REQUEST['session'] == 'destroy') { destroyReg(); }
	
	// redirect if requested
	if ($_REQUEST['goto'] != '') { gotoPage($_REQUEST['goto'], $_REQUEST['gotoParams']); }

/*********** new code to get the event id because the browser window wasnt already opened with active session variables*/

	if ($_REQUEST['id'] && $_REQUEST['key']) {
		
		// get info from sessions table
		$sql = "SELECT * FROM sessions
				WHERE member_id='{$_REQUEST['id']}' AND passkey='{$_REQUEST['key']}'";

		$result = $sm_db->query($sql);
			if (DB::isError($result)) { dbError("validate login from id/key pair", $result, $sql); }
		
		if ($result->numRows() == 0) {
			
			// if no record
			//gotoPage("default", "login_error=no_session_record&event_id={$_SESSION['event_id']}");
		
		} else {
			
			// record exists
			$sess = $result->fetchRow();
			$_SESSION['session_id'] = $sess->session_id;
			$_SESSION['event_id'] = $sess->event_id;
			$_SESSION['member_id'] = $sess->member_id;
			
		}

		// setup $EVENT variable
		//$EVENT = event_info($sm_db, $sess->event_id, "login_handler, step3");
		
		// setup session data
		$_SESSION['session_id'] = $sess->session_id;
		$_SESSION['event_id'] = $sess->event_id;
		$_SESSION['member_id'] = $sess->member_id;

	}

/******************************************/

if (isset($_REQUEST['event_id'])) $_SESSION['event_id'] = $_REQUEST['event_id'];

/* Get event id */
	//if (!$_SESSION['event_id'] && !$_REQUEST['event_id'] && !($_REQUEST['id'] && $_REQUEST['key'])) {
	//if (!$_SESSION['event_id'] && !($_REQUEST['id'] && $_REQUEST['key'])) {	
	if (1==2) {
		print "<pre>";
		print "session-event_id: " . $_SESSION['event_id'];
		print "</pre>";
		die("hmmm");
		header("Location: http://www.sectionmaster.org/"); // redirect to event listing
	} else {
		$event_id = !$_SESSION['event_id'] ? $_REQUEST['event_id'] : $_SESSION['event_id'];
	}

/* Set global-ish things */
	if (!($_REQUEST['id'] && $_REQUEST['key'])) {
		$EVENT 	 = event_info($sm_db, $event_id);
		$SECTION = section_id_convert($EVENT['section_name']);
		$PAGE    = $_SESSION['page'];
		$COMMAND = $_REQUEST['command'];
		$db =& db_connect($SECTION);		// Section's unique db
	}
		
/* Handle Login */
	require_once "includes/login_handler.php";

/* Set global-ish things */
	if (($_REQUEST['id'] && $_REQUEST['key'])) {
		$EVENT 	 = event_info($sm_db, $_SESSION['event_id']);
	}

/* Update the page order */
	updatePageOrder();

/* Handle an Initial gotoPage request */
    if ($_REQUEST['gotoInit'])
        gotoPage($_REQUEST['gotoInit'], "id={$_REQUEST['id']}&key={$_REQUEST['key']}&goto={$_REQUEST['gotoInit']}");
	
/* Include Content */
	include("html/header.php");
	
	$reqFile = "html/" . $PAGE . ".php";
	if (is_file($reqFile)) {
		include $reqFile;
	} elseif ($PAGE != '') {
		gotoPage("","login_error=404&page_not_found=$PAGE");
	} elseif ($EVENT['type'] == 'tradingpost') {
		include "html/tp-default.php";
	} else {
		include "html/default.php";
	}
	
	include("html/footer.php");
	
if ($_SESSION['backdoor'] == 'open') {
	print "BACKDOOR OPEN<br>";
	print "</center><pre style='text-align: left;'>";
	print "SESSION: "; print_r($_SESSION); 
	print "REQUEST: "; print_r($_REQUEST); 
	print "EVENT: "; print_r($EVENT); 
	print "PAGE_ORDER: "; print_r($page_order); 
	print "MEMBER: "; print_r($member); 
	print "</pre>";
}