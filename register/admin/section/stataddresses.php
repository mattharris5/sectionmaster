<?
#########################################
# Contact Addresses for Stat E-Mails
#########################################
$addr = array();
$addr_names = array();


# Get list of contacts from DB
$res = $db->query("SELECT * FROM  `contacts` ORDER BY contact_type");

while ($contact = $res->fetchRow()) {
	
	if ($contact->org_id == '') {
	
		// put in the comma
		$addr[$contact->contact_type] .= $addr[$contact->contact_type]=='' ? '' : ', ';
		$addr_names[$contact->contact_type] .= $addr_names[$contact->contact_type]=='' ? '' : ', ';
		
		// add the name to the list of recipients
		$addr[$contact->contact_type] .= "$contact->name <$contact->email>";
		$addr_names[$contact->contact_type] .= "$contact->name";

	} else {
	
		// put in the comma
		$addr[$contact->contact_type][$contact->org_id] .= $addr[$contact->contact_type][$contact->org_id]=='' ? '' : ', ';
		$addr_names[$contact->contact_type][$contact->org_id] .= $addr_names[$contact->contact_type][$contact->org_id]=='' ? '' : ', ';
		
		// add the name to the list of recipients
		$addr[$contact->contact_type][$contact->org_id] .= "$contact->name <$contact->email>";
		$addr_names[$contact->contact_type][$contact->org_id] .= "$contact->name";
		
	}
}

// DEBUG INFO:
// print "<pre>"; print_r($addr); print "</pre>"; 

?>