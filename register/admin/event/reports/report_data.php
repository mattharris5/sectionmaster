<?

/*$all_registered = 
	"SELECT *,
		DATE_FORMAT('$event_date', '%Y') - DATE_FORMAT(birthdate, '%Y') - (DATE_FORMAT('$event_date', '00-%m-%d') < DATE_FORMAT(birthdate, '00-%m-%d')) AS age,
		IF (DATE_FORMAT('$event_date', '%Y') - DATE_FORMAT(birthdate, '%Y') - (DATE_FORMAT('$event_date', '00-%m-%d') < DATE_FORMAT(birthdate, '00-%m-%d')) < 21, '1', '0') AS youth,
		IF (DATE_FORMAT('$event_date', '%Y') - DATE_FORMAT(birthdate, '%Y') - (DATE_FORMAT('$event_date', '00-%m-%d') < DATE_FORMAT(birthdate, '00-%m-%d')) >= 21, '1', '0') AS adult,
		IF (FROM_UNIXTIME($year_ago) < ordeal_date, '1', '0') AS new_ordeal
	FROM orders, conclave_attendance, members
	LEFT JOIN `sm-online`.lodges ON `sm-online`.lodges.lodge_id = members.lodge
	LEFT JOIN chapters ON chapters.chapter_id = members.chapter
	WHERE members.member_id = conclave_attendance.member_id
		AND conclave_attendance.conclave_order_id = orders.order_id
		AND conclave_year = '$event_year'
		AND registration_complete = 1
";*/

$all_registered = 
	"
	SELECT 
	members.*,
	conclave_attendance.conclave_year,
	conclave_attendance.reg_date,
	conclave_attendance.check_in_date,
	conclave_attendance.check_out_date,
	conclave_attendance.staff_class,
	conclave_attendance.college_major,
	conclave_attendance.college_degree, 
	conclave_attendance.degree_reqts_complete,
	conclave_attendance.session_1,
	conclave_attendance.session_2,
	conclave_attendance.session_3,
	conclave_attendance.session_4,
	conclave_attendance.current_reg_step,
	conclave_attendance.conclave_order_id,
	conclave_attendance.registration_complete,
	conclave_attendance.housing_id,
	orders.order_id,
	orders.order_timestamp,
	orders.order_year,
	orders.event_id,
	orders.total_collected,
	orders.shipping,
	orders.shipping_method,
	orders.cc,
	orders.exp_month,
	orders.exp_year,
	orders.paid,
	orders.cc_processed,
	orders.order_method,
	orders.payment_method,
	orders.payment_notes,
	orders.complete,
	orders.deleted,
	`sm-online`.lodges.lodge_id,
	`sm-online`.lodges.lodge_name,
	`sm-online`.lodges.section,
	`sm-online`.lodges.council,
	staff_classification,
	chapters.chapter_id,
	chapters.chapter_name,
	chapters.active_membership,
	chapters.chapter_chief,
	DATE_FORMAT('$event_date', '%Y') - DATE_FORMAT(members.birthdate, '%Y') - (DATE_FORMAT('$event_end_date', '00-%m-%d') < DATE_FORMAT(members.birthdate, '00-%m-%d')) AS age,
	CASE
		WHEN DATE_FORMAT('$event_date', '%Y') - DATE_FORMAT(members.birthdate, '%Y') - (DATE_FORMAT('$event_end_date', '00-%m-%d') < DATE_FORMAT(members.birthdate, '00-%m-%d')) < 18 THEN 'Under 18'
		WHEN DATE_FORMAT('$event_date', '%Y') - DATE_FORMAT(members.birthdate, '%Y') - (DATE_FORMAT('$event_end_date', '00-%m-%d') < DATE_FORMAT(members.birthdate, '00-%m-%d')) >=21 THEN 'Over 21'
		ELSE '18-21'
	END AS age_bracket,
		
	IF (DATE_FORMAT('$event_date', '%Y') - DATE_FORMAT(members.birthdate,'%Y') - (DATE_FORMAT('$event_date', '00-%m-%d') < DATE_FORMAT(members.birthdate, '00-%m-%d')) < 21, '1', '0') AS youth,
	IF (DATE_FORMAT('$event_date', '%Y') - DATE_FORMAT(members.birthdate,'%Y') - (DATE_FORMAT('$event_date', '00-%m-%d') < DATE_FORMAT(members.birthdate, '00-%m-%d')) >= 21, '1', '0') AS adult,
	IF (FROM_UNIXTIME($year_ago) < members.ordeal_date, '1', '0') AS new_ordeal
	FROM 
	 members
	 INNER JOIN conclave_attendance
	            ON members.member_id = conclave_attendance.member_id
	        LEFT JOIN orders
	            ON conclave_attendance.conclave_order_id = orders.order_id 
	        LEFT JOIN `sm-online`.lodges
	            ON members.lodge = `sm-online`.lodges.lodge_id
	        LEFT JOIN chapters
	            ON members.chapter = chapters.chapter_id
		    LEFT JOIN staff_classifications
			    ON conclave_attendance.staff_class = staff_classifications.staff_class_id
	    WHERE 
	        conclave_attendance.conclave_year = '$event_year'
	        AND conclave_attendance.registration_complete = 1
	";
	
	
	if ($_REQUEST['new_ordeal'] == 'only') $all_registered .= " AND FROM_UNIXTIME($year_ago) < ordeal_date ";

//print "<pre>$all_registered</pre>";

$total_count = $db->query($all_registered);
	if (DB::isError($total_count)) dbError("Could not retrieve registered members.", $total_count, $all_registered);
$total_count = $total_count->numRows();

?>