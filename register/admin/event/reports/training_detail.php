<?
$order_by = $_REQUEST['order_by'] == '' ? "course_number" : $_REQUEST['order_by'];
if (isset($_REQUEST['session_id'])) $sessionidsql = "AND ((session_1='".$_REQUEST['session_id']."') or (session_2='".$_REQUEST['session_id']."') or (session_3='".$_REQUEST['session_id']."') or (session_4='".$_REQUEST['session_id']."'))";
if (isset($_REQUEST['college_id'])) $collegeidsql = "AND college_major='".$_REQUEST['college_id']."'";
// $sql = "SELECT DISTINCT members.member_id, concat(firstname, ' ', lastname) as fullname, truncate(datediff(now(),birthdate)/365,0) as age, email, primary_phone, lodge_name, lodge_id, date_format(ordeal_date,'%b %Y') as ordeal, oahonor
// 		FROM members, orders, conclave_attendance
// 		LEFT JOIN ordered_items ON orders.order_id=ordered_items.order_id
// 		LEFT JOIN products ON products.product_id=ordered_items.product_id
// 		LEFT JOIN `sm-online`.lodges ON lodge_id=lodge
// 		WHERE members.member_id=conclave_attendance.member_id 
// 		AND conclave_attendance.conclave_order_id=orders.order_id
// 		AND conclave_year=".$EVENT['year']."
// 		AND registration_complete=1
// 		$sessionidsql $collegeidsql
// 		ORDER BY lastname, firstname LIMIT 0 , 1000";


$sql = "SELECT DISTINCT members.member_id, concat(firstname, ' ', lastname) as fullname, truncate(datediff(now(),birthdate)/365,0) as age, email, primary_phone, lodge_name, lodge_id, date_format(ordeal_date,'%b %Y') as ordeal, oahonor
		FROM members
		INNER JOIN conclave_attendance ON members.member_id = conclave_attendance.member_id
	   	LEFT JOIN orders ON conclave_attendance.conclave_order_id = orders.order_id 
		LEFT JOIN ordered_items ON orders.order_id = ordered_items.order_id
		LEFT JOIN products ON products.product_id = ordered_items.product_id
		LEFT JOIN `sm-online`.lodges ON lodge_id = members.lodge	
		WHERE
		conclave_year=".$EVENT['year']."
		AND registration_complete=1
		$sessionidsql $collegeidsql
		ORDER BY lastname, firstname LIMIT 0 , 1000";
				
$sessionsql = "SELECT training_sessions.session_id as sess_id, college_name, college_prefix, time_slot, training_times.time AS train_time, course_number, session_name, trainer_name, max_size, description,
				IFNULL(session_count, '<strong><font color=red>0</font></strong>') AS session_count,
				IFNULL(IF(session_count >= max_size, '<font color=red>FULL</font>', max_size - session_count), max_size) AS remaining,
				max_size - session_count AS remaining_count
		FROM training_sessions
		LEFT JOIN (
				SELECT session_id, COUNT(session_id) AS session_count
				FROM training_sessions, ( $all_registered ) AS temp1
				WHERE (time = 1 AND session_1 = session_id)
					OR (time = 2 AND session_2 = session_id)
					OR (time = 3 AND session_3 = session_id)
					OR (time = 4 AND session_4 = session_id)
				GROUP BY session_id
		) AS temp2 USING (session_id)
		LEFT JOIN colleges ON colleges.college_id = training_sessions.college
		LEFT JOIN training_times ON training_times.time_id = training_sessions.time
		LEFT JOIN training_locations ON location=location_id
		WHERE training_sessions.deleted<>1 AND training_sessions.session_id='".$_REQUEST['session_id']."' $staff
		ORDER BY college, $order_by, time_slot";

$colleges_sql = "
SELECT
    college_id, college_prefix, colleges.college_name AS college_name
    , count(conclave_attendance.college_major) AS count
FROM
    colleges
    INNER JOIN conclave_attendance 
        ON (colleges.college_id = conclave_attendance.college_major)
WHERE (conclave_attendance.registration_complete =1
    AND conclave_attendance.conclave_year =".$EVENT['year'].")
	AND college_id = '".$_REQUEST['college_id']."'
GROUP BY college_name
ORDER BY college_name ASC;";

$sessioninfo = $db->query($sessionsql);
	if (DB::isError($sessioninfo)) dbError("Could not retrieve session info in $title", $sessioninfo, $sessionsql);

$collegeinfo = $db->query($colleges_sql);
	if (DB::isError($collegeinfo)) dbError("Could not retrieve college info in $title", $collegeinfo, $colleges_sql);

$sessions = $db->query($sql);
	if (DB::isError($sessions)) dbError("Could not retrieve college/session enrollment detail list in $title", $sessions, $sql);

	if (($_REQUEST['session_id']=='') and ($_REQUEST['college_id']=='')) die("No Session ID or College ID set.");


while ($sessioninf = $sessioninfo->fetchRow()) {
	print "<h3>$sessioninf->college_prefix$sessioninf->course_number$sessioninf->time_slot: ". stripslashes($sessioninf->session_name) ."</h3>
	<!-- TABLE -->
	<table class=\"cart\" style=\"width:30%;\">
		<tr>
			<td><strong>Time</strong></td>
			<td>$sessioninf->train_time</td>
		</tr>
		<tr>
			<td><strong>Trainer</strong></td>
			<td>$sessioninf->trainer_name</td>
		</tr>
		<tr>
			<td><strong>Location</strong></td>
			<td>$sessioninf->location_name</td>
		</tr>
		<tr>
			<td><strong>Current Count</strong></td>
			<td>$sessioninf->session_count/$sessioninf->max_size</td>
		</tr>
		<tr>
			<td><strong>Remaining</strong></td>
			<td>$sessioninf->remaining</td>
		</tr>
	</table>
	
	";
}

while ($collegeinf = $collegeinfo->fetchRow()) {
	print "<h3>$collegeinf->college_name ($collegeinf->college_prefix)</h3>
	Current Count: $collegeinf->count<br><br>";
}
?>
<a href="event/reports.php?report=training">&lt;&lt; Back to Main Training Enrollment Report</a>
<br>
<!-- TABLE -->
<table class="cart" style="width:90%;">
	<tr>
		<th>Name</th>
        <th>Age</th>
        <th>E-mail</th>
        <th>Phone</th>
        <th>Lodge</th>
        <th>Ordeal Date</th>
        <th>Honor</th>
	</tr>

	<?
	while ($session = $sessions->fetchRow()) {
		print "<tr class=\"sub1\" id=\"link\">
					<td><a href=\"section/members.php?member_id=$session->member_id\">$session->fullname</a></td>
					<td>$session->age</td>
					<td><a href=\"mailto:$session->email\">$session->email</a></td>
					<td>$session->primary_phone</td>
					<td><a href=\"event/reports.php?report=lodge&lodge_id=$session->lodge_id\">$session->lodge_name</a></td>
					<td>$session->ordeal</td>
					<td>$session->oahonor</td>
				</tr>";
	}
	?>
	
</table>