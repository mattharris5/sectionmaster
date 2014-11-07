<?
#############################################################################
# SectionMaster.org Registration System
# ADMIN BACKEND
# Copyright (c) 2005-2007 Clostridion Design & Support, LLC.
# 	All included files and modules of this system share the above copyright.
#	All rights are reserved by Clostridion Design & Support, LLC.
#############################################################################

$title = "Home";
require "pre.php";

// handle logout
if ($_REQUEST['logout'] == 'logout')
	{
		$user->_logout("You have been logged out.");
	}

// redirect to the event page
redirect("event/index");

?>

<?
require "post.php";
?>