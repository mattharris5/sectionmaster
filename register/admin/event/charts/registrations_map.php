<?php
/*
//require_once "../../includes/db_connect.php";
//require_once "../../includes/event_info.php";

# connect to DBs
$sm_db =& db_connect();
$EVENT 	 = event_info($sm_db, $_REQUEST['event']);
$SECTION = section_id_convert($EVENT['section_name']);
$db =& db_connect($SECTION);  // section's unique db
*/
// Start XML file, create parent node
$doc = domxml_new_doc("1.0");
$node = $doc->create_element("markers");
$parnode = $doc->append_child($node);

// Select all the rows in the markers table
$query = "SELECT members.member_id, concat(firstname, ' ', lastname) as fullname, concat(address, ' ', address2, ', ', city, ', ', state, ' ', zip) as fulladdress, latitude, longitude, lodge_name
		FROM members, orders, conclave_attendance 
		LEFT JOIN `sm-online`.lodges ON lodge=lodge_id
		WHERE members.member_id=conclave_attendance.member_id 
			AND conclave_attendance.conclave_order_id=orders.order_id
			AND conclave_year='$event_year'
			AND registration_complete=1
			AND latitude<>0 AND longitude<>0";
$result = $db->query($query);
	if (DB::isError($result)) dbError("Problem getting event registration data for map", $result, $query);
if (!$result) {
  die('Invalid query: ' . mysql_error());
}

header("Content-type: text/xml");

// Iterate through the rows, adding XML nodes for each
while ($person = $result->fetchRow()){
  // ADD TO XML DOCUMENT NODE
  $node = $doc->create_element("marker");
  $newnode = $parnode->append_child($node);

  $newnode->set_attribute("name", $person->fullname);
  $newnode->set_attribute("address", $person->fulladdress);
  $newnode->set_attribute("lat", $person->latitude);
  $newnode->set_attribute("lng", $person->longitude);
}

$xmlfile = $doc->dump_mem();
echo $xmlfile;

?>