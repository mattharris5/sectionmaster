<?
#############################################################################
# SectionMaster.org Registration System
# ADMIN BACKEND
# Copyright (c) 2005-2007 Clostridion Design & Support, LLC.
# 	All included files and modules of this system share the above copyright.
#	All rights are reserved by Clostridion Design & Support, LLC.
#############################################################################

/* Start the session! */
	session_start();
	ob_start();

/* Includes */
    ini_set("include_path", ".:/usr/local/php/lib/php/:/var/www/html/register/");
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
	include "includes/time_until.php";
	require "includes/User.php";
	require "includes/redirect.php";
	include_once "includes/charts.php";
	include "includes/doRelativeDate.php";


/* P+C DTR Stuff for dynamic header generation */
	$heading_css = 'headings.css';
	require "/var/www/html/templates/default/pcdtr/getheadings.php";
	ob_start("getheadings");


/* Ensure secure connection */
	if ($_SERVER['HTTP_X_FORWARDED_HOST'] != $_GLOBALS['secure_host']) {
		$newurl = "https://" . $_GLOBALS['secure_base_href'] . $_SERVER['REQUEST_URI'];
		header( "Location: $newurl" );
	}

/* Connect to SM-Online DB */
	$sm_db =& db_connect("sm-online");	// Global SM-Online db

/* Create new User */
	$user = new User($sm_db);

/* Connect to Section DB */
	$db =& db_connect($user->info->section);  // section's unique db

/* Event or Section Info */
/*	if ($_REQUEST['event']) {
		$EVENT 	 = event_info($sm_db, $_REQUEST['event']);
		$SECTION = section_id_convert($EVENT['section_name']);
		$db =& db_connect($SECTION);		// Section's unique db
	} else if ($_REQUEST['section']) {
	    $SECTION = $_REQUEST['section'];
	    $db =& db_connect($SECTION);
	}
*/
/* include header */
if ($no_header != true)
	include "header.php";