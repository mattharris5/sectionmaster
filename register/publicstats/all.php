<?

/* Connect to SM-Online DB */
require_once "../includes/db_connect.php";  // be sure to point to correct path
$sm_db =& db_connect("sm-online");    // Global SM-Online db

// Online Registration
$sql = "SELECT * FROM events WHERE type = 'event' AND online_reg_open_date < now() AND online_reg_close_date > now() AND id<>6 ORDER BY start_date";
$events = $sm_db->query($sql);

while ($thisevent = $events->fetchRow())
{
	print "<h2>Section <b>$thisevent->section_name</b> $thisevent->formal_event_name<br>
	$thisevent->conclave_date ($thisevent->city_and_state)</a></h2>\n";
	include ("http://www.sectionmaster.org/register/publicstats/?event=$thisevent->id&countdown=true&newordeal=true");
	print "<br><br>";
}

?>