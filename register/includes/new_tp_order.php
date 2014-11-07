<?
	
	// create member record
	$sql1 = "INSERT INTO members 
			SET email = '" . addslashes(get('email')) . "'";
	$res1 = $db->query($sql1);
	$member_id = mysql_insert_id();
	if (DB::isError($res1)) {
		dbError("Error adding new member record ID in idcreate post-validate.", $res1, $sql1);
	}

	// update key and re-enter the reg process
	gotoPage("tradingpost", "id=$member_id&key=" . makeKey(10, $member_id, $sm_db));

?>