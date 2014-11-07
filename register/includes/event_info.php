<?
####################################
#
# Event Info function
#  --> gets event info from db
#
####################################

function event_info($dbObj, $event_id, $error_add = "") {

	$sql = "SELECT * FROM events WHERE id=$event_id";
	
	$res = $dbObj->getRow($sql, DB_FETCHMODE_ASSOC);
	
	if (DB::isError($res)) {
		$error_add = " ($error_add)";
		dbError("Could not retrieve event info$error_add" . print_r($_SESSION), $res, $sql);
	}
	 
	return $res;
	
}



####################################
#
# Section ID Convert function
# --> converts "W-1A" to "w1a"
#
####################################

function section_id_convert($name) {

	// remove the hyphen
	$id = str_replace("-", "", $name);
	
	// make lowercase
	$id = strtolower($id);
	
	// return clean value
	return $id;
	
}