<?
$title = "Event > Registrations";
require "../pre.php";
$user->checkPermissions("registrations");

import_request_variables('GP');
$EVENT 	 = event_info($sm_db, $user->info->current_event);

// delete in-progress registration if requested to do so
if (isset($_POST['delete_id']))
{
	$delsql = "DELETE FROM conclave_attendance WHERE registration_complete=0 AND conclave_year='{$EVENT['year']}' AND member_id='{$_POST['delete_id']}'";
	$delete_member = $db->query($delsql);
}

/**
 * Get list of registrations
 */
$sql = "			
		SELECT 
		members.*,
		DATE_FORMAT('$event_date', '%Y') 
					- DATE_FORMAT(birthdate, '%Y') 
					- (DATE_FORMAT('$event_date', '00-%m-%d') < DATE_FORMAT(birthdate, '00-%m-%d')) AS age,
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
		chapters.chapter_id,
		chapters.chapter_name,
		chapters.active_membership,
		chapters.chapter_chief,
		staff_classifications.staff_classification,
		housing_name
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
				LEFT JOIN housing
					USING (housing_id)
		    WHERE 
		        conclave_attendance.conclave_year = '{$EVENT['year']}' ";
	if (isset($orderby))
	{
		if ($orderby == 'name') $order = "lastname, firstname";
		elseif ($orderby == 'namedesc') $order = "lastname DESC, firstname DESC";
		elseif ($orderby == 'regdate') $order = "reg_date DESC";
		elseif ($orderby == 'regdateasc') $order = "reg_date";
		elseif ($orderby == 'checkindate') $order = "check_in_date";
		elseif ($orderby == 'checkindateasc') $order = "check_in_date DESC";
		elseif ($orderby == 'chapterlodge') $order = "lodge_name, chapter_name, lastname, firstname";
		elseif ($orderby == 'chapterlodgedesc') $order = "lodge_name DESC, chapter_name DESC, lastname, firstname";
		elseif ($orderby == 'housing') $order = "housing_name, lastname, firstname";
		elseif ($orderby == 'housingdesc') $order = "housing_name DESC, lastname, firstname";
	}
	else $order = "reg_date DESC";
$registrations_complete = $db->query($sql . "AND conclave_attendance.registration_complete = 1 ORDER BY $order");
$registrations_inprogress = $db->query($sql . "AND conclave_attendance.registration_complete != 1 ORDER BY $order");
//print "<pre>$sql AND conclave_attendance.registration_complete != 1 ORDER BY $order</pre>";
$count['complete'] = $registrations_complete->numRows();
$count['inprogress'] = $registrations_inprogress->numRows();

//print "<pre>$sql</pre>";
?>

<h1>Event Registrations</h1>
<h2>Registrations for <?= $EVENT['formal_event_name'] ?></h2>

<div class="tabber">
	
	<div class="tabbertab table">
		<h2>Complete (<?=$count['complete']?>)</h2>
		
		<table>
			<tr><th>Member ID</th>
				<th>Order ID</th>
				<th><a href="event/registrations.php?orderby=name<?=($orderby=='name')?"desc":""?>">Member Name</a></th>
				<th><a href="event/registrations.php?orderby=chapterlodge<?=($orderby=='chapterlodge')?"desc":""?>">Chapter/Lodge</a></th>
				<th><a href="event/registrations.php?orderby=housing<?=($orderby=='housing')?"desc":""?>">Housing Assignment</a></th>
				<th><a href="event/registrations.php?orderby=regdate<?=($orderby=='regdate')?"asc":""?>">Date Completed</a></th>
				<? if ($EVENT['start_date'] <= date('Y-m-d H:i:s'))
				{
					print "<th><a href='event/registrations.php?orderby=checkindate";
					print ($orderby=='checkindate')?"asc":"";
					print "'>Check-in Date</a></th>";
				} ?>
			</tr>
			
			<? while ($reg = $registrations_complete->fetchRow()): ?>
				<tr><td><a href="section/members.php?member_id=<?=$reg->member_id?>"><?=$reg->member_id?></a></td>
					<td><?=$reg->order_id?></td>
					<td><a href="section/members.php?member_id=<?=$reg->member_id?>"><?=$reg->lastname?>, <?=$reg->firstname?></a> <?=($reg->staff_classification!="Participant")?"<font color='red'>($reg->staff_classification)</font>":""?></td>
					<td><a href="event/reports.php?chapter_id=<?=$reg->chapter_id?>&report=chapter"><?=$reg->chapter_name?></a> / <a href="event/reports.php?lodge_id=<?=$reg->lodge_id?>&report=lodge"><?=$reg->lodge_name?></a></td>
					<td><a href="event/reports.php?housing_id=<?=$reg->housing_id?>&report=housing"><?=$reg->housing_name?></a></td>
					<td><?=$reg->reg_date?></td>
					<? if ($EVENT['start_date'] <= date('Y-m-d H:i:s'))
					{
						print "<td>";
						print ($reg->check_in_date == '0000-00-00 00:00:00') ? "No-Show" : $reg->check_in_date;
						print "</td>";
					} ?>
				</tr>
			<? endwhile; ?>
		
		</table>	
	</div>


	<div class="tabbertab table">
		<h2>In Progress (<?=$count['inprogress']?>)</h2>
		
		<table>

			<tr>
				<th>Delete</th>
				<th>Member ID</th>
				<th>Order ID</th>
				<th><a href="event/registrations.php?orderby=name<?=($orderby=='name')?"desc":""?>">Member Name</a></th>
				<th><a href="event/registrations.php?orderby=chapterlodge<?=($orderby=='chapterlodge')?"desc":""?>">Chapter/Lodge</a></th>
				<th><a href="event/registrations.php?orderby=regdate<?=($orderby=='regdate')?"asc":""?>">Last Progress</a></th>
				<th>Current Step</th>
			</tr>
			
			<? while ($reg = $registrations_inprogress->fetchRow()): ?>
				<tr>
					<td><form name="delpartial<?=$reg->member_id?>" method="POST" action="event/registrations.php<?=(isset($orderby))?"?orderby=$orderby":""?>"><input type="hidden" name="delete_id" value="<?=$reg->member_id?>"><input type="button" name="processdelete" value="Delete" style="width: 50px;" onClick='if (confirm("Are you sure you wish to delete the unfinished registration for <?=$reg->firstname?> <?=$reg->lastname?>? This will only remove the registration record, not the contact information and member ID.")) { document.forms["delpartial<?=$reg->member_id?>"].submit();}'></form></td>
					<td><a href="section/members.php?member_id=<?=$reg->member_id?>"><?=$reg->member_id?></a></td>
					<td><?=$reg->order_id?></td>
					<td><a href="section/members.php?member_id=<?=$reg->member_id?>"><?=$reg->lastname?>, <?=$reg->firstname?></a></td>
					<td><a href="event/reports.php?chapter_id=<?=$reg->chapter_id?>&report=chapter"><?=$reg->chapter_name?></a> / <a href="event/reports.php?lodge_id=<?=$reg->lodge_id?>&report=lodge"><?=$reg->lodge_name?></a></td>
					<td><?=$reg->reg_date?></td>
					<td><?=$reg->current_reg_step?></td>
				</tr>
			<? endwhile; ?>
		
		</table>		
	</div>
	
</div>


<?
require "../post.php";
?>
