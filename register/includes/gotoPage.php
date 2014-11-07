<?
####################################
#
# GotoPage function - redirects to proper page
#
####################################

function gotoPage($page, $params = "") {
	
	global $_GLOBALS, $SECTION, $sm_db;
	
	$newurl = $_GLOBALS['form_action'];// . "?page=$page";
	
	if ($params != "") {
		$newurl .= "?$params";
	}

	$_SESSION['page'] = $page;
	
	if ($_REQUEST['event_id']) {
		$_SESSION['event_id'] = $_REQUEST['event_id'];
	}

    // update session record timestamp and current page if exists
    if ($_SESSION['session_id'] && $sm_db) {
        $res = $sm_db->query("UPDATE sessions SET timestamp='".time()."', current_reg_step='$page' 
							  WHERE session_id='{$_SESSION['session_id']}' LIMIT 1");
            if (DB::isError($res)) dbError("Couldn't update session timestamp and current page.", $res, $sql);
    }
		/** Note: this was modified 3/31/06 by mharris because tradingpost events were not allowing people
			to go back to previous steps.  gotoNextPage worked because it set the session's "current_reg_step",
			but this gotoPage would not because it did not. */
//print $newurl;
	header("Location: $newurl");
}


####################################
#
# GotoNextPage - redirects to the next page in the process
#
####################################

function gotoNextPage($page, $params = "") {

	require_once "page_order.php";
//	updatePageOrder();
	global $db, $sm_db, $EVENT, $page_order;
	
	// get next page
	$page_order_keys = array_keys($page_order);
	$i = array_search($page, $page_order_keys);
	$newpage = $page_order_keys[$i+1];

	// update "current Page" in member record
	$sql = "UPDATE conclave_attendance 
			SET current_reg_step='$newpage', reg_date=NOW() 
			WHERE member_id='{$_SESSION['member_id']}' AND conclave_year='{$EVENT['year']}'";
	$update = $db->query($sql);
		if (DB::isError($update)) { dbError("Could not update current reg step in member record", $update, $sql); }

	// update "current Page" in session record
	$sql = "UPDATE sessions
			SET current_reg_step='$newpage' 
			WHERE session_id = '{$_SESSION['session_id']}'";
	$update = $sm_db->query($sql);
		if (DB::isError($update)) { dbError("Could not update current reg step in session record", $update, $sql); }

	// goto new page
	gotoPage($newpage, $params);

}

?>