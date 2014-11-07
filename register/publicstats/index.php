<?
#########################################
# Send OAReg Stat Emails Program
#########################################

/* Includes */
	require_once "../includes/db_connect.php";
	require "../includes/globals.php";
	require "../includes/validate.php";
	require "../includes/req.php";
	require "../includes/event_info.php";
	require "../includes/member_functions.php";
	include "../includes/time_until.php";

/* Connect to SM-Online DB */
	$sm_db =& db_connect("sm-online");	// Global SM-Online db


/* Event Info */
	if ($_REQUEST['event']) {
		$EVENT 	 = event_info($sm_db, $_REQUEST['event']);
		$SECTION = section_id_convert($EVENT['section_name']);
		$db =& db_connect($SECTION);		// Section's unique db
	}

# Check for event
if (!$EVENT) die("Choose event.
<br><br>
Usage:<br>
<pre>
http://www.sectionmaster.org/register/publicstats/?event=<i><b>eventID</b></i>&newordeal=true&date=true&countdown=true&title=true&percentages=true

<b><i>Variables:</i></b>
	event - REQUIRED
	
	(all other variables are optional, and default values are false if not used)
	newordeal
	date
	countdown
	title
	percentages
	youthadultcounts
	newmemcounts
</pre>");

# Current Year variable
$curr_year = $EVENT['year'];
$year_ago = strtotime($EVENT['start_date']) - (365*24*60*60);

# Get lodge info
$lodges = array();
$sql = "
SELECT DISTINCT lodge_id, lodge_name
FROM (
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
	chapters.chapter_id,
	chapters.chapter_name,
	chapters.active_membership,
	chapters.chapter_chief
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
	    WHERE 
	        conclave_attendance.conclave_year = '{$EVENT['year']}'
	        AND conclave_attendance.registration_complete = 1
	) as temp1
	WHERE section='{$EVENT['section_name']}'
";

$res = $db->query($sql);
	if (DB::isError($res)) dbError("Could not retrieve lodge list (PUBLIC STATS)", $res, "");
	
while ($lodge = $res->fetchRow()) {
	$lodge_id = $lodge->lodge_id;
	$lodges[$lodge_id] = $lodge->lodge_name . " Lodge";
}
	$lodges['0'] = "Out of Section Guests"; // out of section lodge

# Get the counts

	$lodge_count = array();
	$chapter_count = array();
	$youth_count = array();
	$adult_count = array();
	$newmem_count = array();
	
	#######################################
	# Fill the counts
	#######################################
		$sql = "SELECT date_add(birthdate,INTERVAL 21 YEAR) as adultbday, members.*, reg_date, payment_method
				FROM members, orders, conclave_attendance
				WHERE members.member_id = conclave_attendance.member_id
					AND conclave_attendance.conclave_order_id = orders.order_id
					AND conclave_year = {$EVENT['year']}
					AND registration_complete = 1
				ORDER BY reg_date ASC";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Could not fill counts (ADMIN AREA)", $res, "");
		
		while ($member = $res->fetchRow()) {
			
			$curr_chapter = $member->chapter;
			$curr_lodge = $member->lodge;
			
			// Increase the count for this chapter
			if ($chapter_count[$curr_chapter] == '') {
				$chapter_count[$curr_chapter] = 1;
			} else {	
				$chapter_count[$curr_chapter]++;
			}
			
			// Increase the count for this lodge
			if ($lodge_count[$curr_lodge] == '') {
				$lodge_count[$curr_lodge] = 1;
				if ($member->adultbday >= $EVENT['start_date'])
				{
					if ($youth_count[$curr_lodge] == '') $youth_count[$curr_lodge] = 1;
					else $youth_count[$curr_lodge]++;
					$total_youth++;
				}
				elseif ($member->adultbday < $EVENT['start_date'])
				{
					if ($adult_count[$curr_lodge] == '') $adult_count[$curr_lodge] = 1;
					else $adult_count[$curr_lodge]++;
					$total_adult++;
				}
				$total_count++;
			} else {	
				$lodge_count[$curr_lodge]++;
				$total_count++;
				if ($member->adultbday >= $EVENT['start_date'])
				{
					if ($youth_count[$curr_lodge] == '') $youth_count[$curr_lodge] = 1;
					else $youth_count[$curr_lodge]++;
					$total_youth++;
				}
				elseif ($member->adultbday < $EVENT['start_date'])
				{
					if ($adult_count[$curr_lodge] == '') $adult_count[$curr_lodge] = 1;
					else $adult_count[$curr_lodge]++;
					$total_adult++;
				}
				
			}
			if ($total_youth=='') $total_youth=0;
			if ($total_adult=='') $total_adult=0;
			

			// check for new ordeal member status
			if (strtotime($member->ordeal_date) > $year_ago) {
				$new_member_count = $new_member_count + 1;
				
				if ($newmem_count[$curr_lodge] == '') $newmem_count[$curr_lodge] = 1;
				else $newmem_count[$curr_lodge]++;
			}
	
		}
		
		//Out of section count
		$sql = "
			SELECT count(member_id) as count
			FROM (
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
				chapters.chapter_id,
				chapters.chapter_name,
				chapters.active_membership,
				chapters.chapter_chief
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
				    WHERE 
				        conclave_attendance.conclave_year = '{$EVENT['year']}'
				        AND conclave_attendance.registration_complete = 1
						AND `$SECTION`.members.lodge<>0
				) as temp1
				WHERE section<>'{$EVENT['section_name']}'
		";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Could not calculate Out of Section count (ADMIN AREA)", $res, "");
		$outofsection=$res->fetchRow();
		$lodge_count['0'] = $outofsection->count;
		
		$sql = "
			SELECT count(member_id) as count
			FROM (
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
				chapters.chapter_id,
				chapters.chapter_name,
				chapters.active_membership,
				chapters.chapter_chief
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
				    WHERE 
				        conclave_attendance.conclave_year = '{$EVENT['year']}'
				        AND conclave_attendance.registration_complete = 1
						AND `$SECTION`.members.lodge<>0
						AND unix_timestamp(ordeal_date) > $year_ago
				) as temp1
				WHERE section<>'{$EVENT['section_name']}'
		";

		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Could not calculate Out of Section New Member count (ADMIN AREA)", $res, "");
		$outofsection=$res->fetchRow();
		if ($outofsection->count > 0) $newmem_count['0'] = $outofsection->count;
		
		//Unknown count
		$sql = "
			SELECT count(member_id) as count
			FROM (
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
				chapters.chapter_id,
				chapters.chapter_name,
				chapters.active_membership,
				chapters.chapter_chief
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
				    WHERE 
				        conclave_attendance.conclave_year = '{$EVENT['year']}'
				        AND conclave_attendance.registration_complete = 1
						AND `$SECTION`.members.lodge=0
				) as temp1
		";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Could not calculate Non-Members count (ADMIN AREA)", $res, "");
		$nonmembers=$res->fetchRow();
		$nonmemberscount = $nonmembers->count;
		
				$sql = "
			SELECT count(member_id) as count
			FROM (
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
				chapters.chapter_id,
				chapters.chapter_name,
				chapters.active_membership,
				chapters.chapter_chief
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
				    WHERE 
				        conclave_attendance.conclave_year = '{$EVENT['year']}'
				        AND conclave_attendance.registration_complete = 1
						AND `$SECTION`.members.lodge=0
						AND date_add(birthdate,INTERVAL 21 YEAR) < '{$EVENT['start_date']}'
				) as temp1
		";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Could not calculate Non-Members Adult count (ADMIN AREA)", $res, "");
		$nonmembers=$res->fetchRow();
		$nonmembersadultcount = $nonmembers->count;
		
		$sql = "
			SELECT count(member_id) as count
			FROM (
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
				chapters.chapter_id,
				chapters.chapter_name,
				chapters.active_membership,
				chapters.chapter_chief
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
				    WHERE 
				        conclave_attendance.conclave_year = '{$EVENT['year']}'
				        AND conclave_attendance.registration_complete = 1
						AND `$SECTION`.members.lodge=0
						AND date_add(birthdate,INTERVAL 21 YEAR) >= '{$EVENT['start_date']}'
				) as temp1
		";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Could not calculate Non-Members Youth count (ADMIN AREA)", $res, "");
		$nonmembers=$res->fetchRow();
		$nonmembersyouthcount = $nonmembers->count;
		
		$sql = "
			SELECT count(member_id) as count
			FROM (
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
				chapters.chapter_id,
				chapters.chapter_name,
				chapters.active_membership,
				chapters.chapter_chief
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
				    WHERE 
				        conclave_attendance.conclave_year = '{$EVENT['year']}'
				        AND conclave_attendance.registration_complete = 1
						AND `sm-online`.lodges.section <> '{$EVENT['section_name']}'
						AND date_add(birthdate,INTERVAL 21 YEAR) >= '{$EVENT['start_date']}'
				) as temp1
		";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Could not calculate Out of Section Youth count (ADMIN AREA)", $res, "");
		$outofsection=$res->fetchRow();
		$outofsectionyouthcount = $outofsection->count;
		
				$sql = "
			SELECT count(member_id) as count
			FROM (
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
				chapters.chapter_id,
				chapters.chapter_name,
				chapters.active_membership,
				chapters.chapter_chief
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
				    WHERE 
				        conclave_attendance.conclave_year = '{$EVENT['year']}'
				        AND conclave_attendance.registration_complete = 1
						AND `sm-online`.lodges.section <> '{$EVENT['section_name']}'
						AND date_add(birthdate,INTERVAL 21 YEAR) < '{$EVENT['start_date']}'
				) as temp1
		";
		$res = $db->query($sql);
			if (DB::isError($res)) dbError("Could not calculate Out of Section Adult count (ADMIN AREA)", $res, "");
		$outofsection=$res->fetchRow();
		$outofsectionadultcount = $outofsection->count;

	
	#######################################
	# Make percent breakdowns
	#######################################
	foreach($lodges as $lodge_id => $lodge_name) {
		if ($lodge_count[$lodge_id]!=0) $percent[$lodge_id] = $lodge_count[$lodge_id] / $total_count;
		else $percent[$lodge_id]=0;
	}
	if ($new_member_count!=0) $new_member_percent = ceil(($new_member_count / $total_count)*100);
	else {$new_member_percent=0; $new_member_count=0;}
	
	$body['count_breakdown'] = "<table id=SMStats cellspacing=5>\n";
	$body['count_breakdown'] .= "<tr><td></td>";
	if ($youthadultcounts=='true') $body['count_breakdown'] .= "<td align=right>Y</td><td align=right>A</td>";
	if ($newmemcounts=='true') $body['count_breakdown'] .= "<td>New</td>";
	$body['count_breakdown'] .= "<td";
	if ($percentages=='true') $body['count_breakdown'] .= " colspan=2";
	$body['count_breakdown'] .= "></td>";
	$body['count_breakdown'] .= "</tr>";
	foreach ($percent as $lodge_name => $percent) {
		if ($lodge_count[$lodge_name]!=0)
		{
			if ($youth_count[$lodge_name]=='') $youth_count[$lodge_name] = 0;
			if ($adult_count[$lodge_name]=='') $adult_count[$lodge_name] = 0;
			if ($newmem_count[$lodge_name]=='') $newmem_count[$lodge_name] = 0;
			$lodge_count_for_percent = $lodge_count[$lodge_name];
			$body['count_breakdown'] .= "\n\t\t<tr>\n\t\t\t<td align=left>" 	. $lodges[$lodge_name] 
												. "</td>";
			if ($youthadultcounts=='true')
			{
				$body['count_breakdown'] .= "\n\t\t\t<td align=right>";
				if ($lodge_name == 'Out of Section Guests') $youth_count[$lodge_name] = $outofsectionyouthcount;
				$body['count_breakdown'] .= $youth_count[$lodge_name]
										. "</td><td align=right>";
				if ($lodge_name == 'Out of Section Guests') $adult_count[$lodge_name] = $outofsectionadultcount;
				$body['count_breakdown'] .= $adult_count[$lodge_name]
										. "</td>";
			}
			if ($newmemcounts=='true') $body['count_breakdown'] .= "\n\t\t\t<td align=right>"
												. $newmem_count[$lodge_name]
												. "</td>";
			$body['count_breakdown'] .= "\n\t\t\t<td align=right><b>"
												. $lodge_count_for_percent
												. "</b></td>";
			if ($percentages=='true') $body['count_breakdown'] .= "\n\t\t\t<td align=right>"
												. ceil($percent*100) . "%"
												. "</td>";
			$body['count_breakdown'] .= "\n\t\t</tr>";
		}

	}
	if ($nonmemberscount!=0)
	{
		$body['count_breakdown'] .= "\n\t\t<tr>\n\t\t\t<td align=left>Non-Member(s)</td>\n\t\t\t";
		if ($youthadultcounts=='true') $body['count_breakdown'] .= "<td align=right>$nonmembersyouthcount</td><td align=right>$nonmembersadultcount</td>";
		if ($newmemcounts=='true') $body['count_breakdown'] .= "<td></td>";
		$body['count_breakdown'] .= "\n\t\t\t<td align=right><b>$nonmemberscount</b></td>";
		if ($percentages=='true') $body['count_breakdown'] .= "<td align=right>".ceil(($nonmemberscount/$total_count)*100)."%</td>";
		$body['count_breakdown'] .= "\n\t\t</tr>";
	}
	$body['count_breakdown'] .= "\n\t\t<tr id=SMTotal>\n\t\t\t<td ";
	$body['count_breakdown'] .= "align=left><b>TOTALS</b></td>\n\t\t\t";
	if ($youthadultcounts=='true') $body['count_breakdown'] .= "<td align=right>$total_youth</td><td align=right>$total_adult</td>";
	if ($newmemcounts=='true') $body['count_breakdown'] .= "<td align=right>$new_member_count</td>";
		//else $body['count_breakdown'] .= "<td></td>";
	$body['count_breakdown'] .= "<td align=right><b>" . $total_count . "</b></td>";
	$body['count_breakdown'] .= "\n\t\t</tr>";
	if ($newordeal=='true') {$body['count_breakdown'] .= "\n\t\t<tr>\n\t\t\t<td colspan=3 align=left>$new_member_count New Ordeal Members ($new_member_percent%)</td>\n\t\t</tr>";}
	$body['count_breakdown'] .= "\n</table>";

######################################
# Print stuff out
######################################
if ($title=='true') {print "\n\n<h3>{$EVENT['formal_event_name']} Registration Statistics</h3>\n";}
print $body['count_breakdown'];
if ($countdown=='true') {print "\n" . time_until($EVENT['casual_event_name'], 
						date('j', strtotime($EVENT['start_date'])),
						date('m', strtotime($EVENT['start_date'])),
						date('Y', strtotime($EVENT['start_date']))) . "\n";}
if ($date=='true') {print "\n<br><i>As of ". date("D, M j g:i:s A T Y") . "</i>";}

?>