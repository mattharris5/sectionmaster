<?
###################################################
# Mass Mailer Script
###################################################
# Author: Matt Harris
#         matt@cdsportland.com
# Date: 09/02/2005
# Edited: RJR 3/30/2009 - added pre-defined queries
#		  RJR 4/20/2010 - added HTML editor
###################################################

$title = "Mass E-Mailer";

# Includes
require "../pre.php";
require "../../includes/eval_html.php";
include("../../includes/fckeditor/fckeditor.php") ;

?>
<h1>SectionMaster Mass E-Mailer</h1>
<br>
<?

# Check for event
$EVENT 	 = event_info($sm_db, $user->info->current_event);
$SECTION = section_id_convert($EVENT['section_name']);

/*print "<pre>";
print_r($user->info);
print "</pre>";*/

# Import request vars
	import_request_variables("G");
	$command = $_REQUEST['command'];
	
# If command is to send, get a count and confirm
	if ($command == "send") {
		$email_from = htmlspecialchars($email_from);
		if ($from_name=='' OR $email_to=='' OR $email_from=='' OR $email_subject=='' OR $email_body=='') {
			echo "Please fill in all data.";
		} else {
			
			$lower_bound = (int)$lower_bound;
			switch($email_to)
			{
				case "this_year_attendees":
					$last_year = $EVENT['year'];
					$sel_query = "
					SELECT members.member_id, firstname, lastname, email
					FROM members, orders, conclave_attendance 
					WHERE members.member_id=conclave_attendance.member_id 
					AND conclave_attendance.conclave_order_id=orders.order_id
					AND conclave_year=$last_year
					AND lastname<>''
					AND firstname<>''
					AND email<>''
					AND check_in_date<>'0000-00-00 00:00:00'
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				case "last_year_attendees":
					$last_year = $EVENT['year'] - 1;
					$sel_query = "
					SELECT members.member_id, firstname, lastname, email
					FROM members, orders, conclave_attendance 
					WHERE members.member_id=conclave_attendance.member_id 
					AND conclave_attendance.conclave_order_id=orders.order_id
					AND conclave_year=$last_year
					AND lastname<>''
					AND firstname<>''
					AND email<>''
					AND check_in_date<>'0000-00-00 00:00:00'
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				case "last_two_year_attendees":
					$last_year = $EVENT['year'] - 2;
					$sel_query = "
					SELECT DISTINCT members.member_id, firstname, lastname, email
					FROM members, orders, conclave_attendance 
					WHERE members.member_id=conclave_attendance.member_id 
					AND conclave_attendance.conclave_order_id=orders.order_id
					AND conclave_year>=$last_year
					AND lastname<>''
					AND firstname<>''
					AND email<>''
					AND check_in_date<>'0000-00-00 00:00:00'
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				case "last_three_year_attendees":
					$last_year = $EVENT['year'] - 3;
					$sel_query = "
					SELECT DISTINCT members.member_id, firstname, lastname, email
					FROM members, orders, conclave_attendance 
					WHERE members.member_id=conclave_attendance.member_id 
					AND conclave_attendance.conclave_order_id=orders.order_id
					AND conclave_year>=$last_year
					AND lastname<>''
					AND firstname<>''
					AND email<>''
					AND check_in_date<>'0000-00-00 00:00:00'
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				case "last_four_year_attendees":
					$last_year = $EVENT['year'] - 4;
					$sel_query = "
					SELECT DISTINCT members.member_id, firstname, lastname, email
					FROM members, orders, conclave_attendance 
					WHERE members.member_id=conclave_attendance.member_id 
					AND conclave_attendance.conclave_order_id=orders.order_id
					AND conclave_year>=$last_year
					AND lastname<>''
					AND firstname<>''
					AND email<>''
					AND check_in_date<>'0000-00-00 00:00:00'
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				case "currently_registered":
					$last_year = $EVENT['year'];
					$sel_query = "
					SELECT members.member_id, firstname, lastname, email
					FROM members, orders, conclave_attendance 
					WHERE members.member_id=conclave_attendance.member_id 
					AND conclave_attendance.conclave_order_id=orders.order_id
					AND conclave_year=$last_year
					AND registration_complete=1
					AND lastname<>''
					AND firstname<>''
					AND email<>''
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				case "not_yet_registered":
					$last_year = $EVENT['year'];
					$sel_query = "
					SELECT member_id, lastname, firstname, email
					FROM members
					WHERE member_id NOT
					IN (
					SELECT member_id
					FROM conclave_attendance
					WHERE registration_complete =1
					AND conclave_year = $last_year
					)
					AND email <> ''
					AND firstname <> ''
					AND lastname <> ''
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				case "registration_incomplete":
					$last_year = $EVENT['year'];
					$sel_query = "
					SELECT members.member_id, firstname, lastname, email
					FROM members, orders, conclave_attendance 
					WHERE members.member_id=conclave_attendance.member_id 
					AND conclave_attendance.conclave_order_id=orders.order_id
					AND conclave_year=$last_year
					AND registration_complete=0
					AND lastname<>''
					AND firstname<>''
					AND email<>''
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				// just me
				default:
					$sel_query = "
					SELECT members.member_id, firstname, lastname, email
					FROM members
					WHERE member_id = $email_to
					LIMIT 1";
					break;
			}
			
			$select = $db->query(stripslashes($sel_query));
				if (DB::isError($select)) dbError("Error with query", $select, $sel_query);
			$count = $select->numRows();
			print "<h3>Mass Mail will be sent to <b>$count</b> email addresses. Continue?</h3>";
			print "<p><center>
			<input type=button value=\"Go Back\"
					onClick=\"window.location='".$_GLOBALS['form_action']."admin/section/massmail2.php?".$_SERVER['QUERY_STRING']."&command=&massmail_id=$massmail_id'\" 
					style=\"width: 150px; float:none;\">
			OR
			<input type=button value=\"Continue\"
					onClick=\"window.location='".$_GLOBALS['form_action']."admin/section/massmail2.php?".$_SERVER['QUERY_STRING']."&command=continue&massmail_id=$massmail_id'\" 
					style=\"width:150px; float:none;\"><br>";
			while ($users = $select->fetchRow()) {
				print "$users->firstname $users->lastname &lt;$users->email&gt;<br>";
			}
			$hide_form = TRUE;
			
		}
	}

# Process the emails
	if ($command == "continue") {
		$hide_form = TRUE;
		
		print "<h3>Results:</h3>
		<h4><font color=red>(Please wait for this page to finish loading...)</font></h4>
		<b>Once the page finishes loading, you may wish to <a href=\"section/massmail2.php?email_to=$email_to&from_name=".urlencode($from_name)."&email_from=$email_from&email_subject=".urlencode($email_subject)."&email_body=".urlencode($email_body)."\">GO BACK to your original message - CLICK HERE</a>.</b><br>
		<br>";
		
		$lower_bound = (int)$lower_bound;
			switch($email_to)
			{
				case "this_year_attendees":
					$last_year = $EVENT['year'];
					$sel_query = "
					SELECT members.member_id, firstname, lastname, email
					FROM members, orders, conclave_attendance 
					WHERE members.member_id=conclave_attendance.member_id 
					AND conclave_attendance.conclave_order_id=orders.order_id
					AND conclave_year=$last_year
					AND lastname<>''
					AND firstname<>''
					AND email<>''
					AND check_in_date<>'0000-00-00 00:00:00'
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				case "last_year_attendees":
					$last_year = $EVENT['year'] - 1;
					$sel_query = "
					SELECT members.member_id, firstname, lastname, email
					FROM members, orders, conclave_attendance 
					WHERE members.member_id=conclave_attendance.member_id 
					AND conclave_attendance.conclave_order_id=orders.order_id
					AND conclave_year=$last_year
					AND lastname<>''
					AND firstname<>''
					AND email<>''
					AND check_in_date<>'0000-00-00 00:00:00'
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				case "last_two_year_attendees":
					$last_year = $EVENT['year'] - 2;
					$sel_query = "
					SELECT DISTINCT members.member_id, firstname, lastname, email
					FROM members, orders, conclave_attendance 
					WHERE members.member_id=conclave_attendance.member_id 
					AND conclave_attendance.conclave_order_id=orders.order_id
					AND conclave_year>=$last_year
					AND lastname<>''
					AND firstname<>''
					AND email<>''
					AND check_in_date<>'0000-00-00 00:00:00'
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				case "last_three_year_attendees":
					$last_year = $EVENT['year'] - 3;
					$sel_query = "
					SELECT DISTINCT members.member_id, firstname, lastname, email
					FROM members, orders, conclave_attendance 
					WHERE members.member_id=conclave_attendance.member_id 
					AND conclave_attendance.conclave_order_id=orders.order_id
					AND conclave_year>=$last_year
					AND lastname<>''
					AND firstname<>''
					AND email<>''
					AND check_in_date<>'0000-00-00 00:00:00'
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				case "last_four_year_attendees":
					$last_year = $EVENT['year'] - 4;
					$sel_query = "
					SELECT DISTINCT members.member_id, firstname, lastname, email
					FROM members, orders, conclave_attendance 
					WHERE members.member_id=conclave_attendance.member_id 
					AND conclave_attendance.conclave_order_id=orders.order_id
					AND conclave_year>=$last_year
					AND lastname<>''
					AND firstname<>''
					AND email<>''
					AND check_in_date<>'0000-00-00 00:00:00'
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				case "currently_registered":
					$last_year = $EVENT['year'];
					$sel_query = "
					SELECT members.member_id, firstname, lastname, email
					FROM members, orders, conclave_attendance 
					WHERE members.member_id=conclave_attendance.member_id 
					AND conclave_attendance.conclave_order_id=orders.order_id
					AND conclave_year=$last_year
					AND registration_complete=1
					AND lastname<>''
					AND firstname<>''
					AND email<>''
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				case "not_yet_registered":
					$last_year = $EVENT['year'];
					$sel_query = "
					SELECT member_id, lastname, firstname, email
					FROM members
					WHERE member_id NOT
					IN (
					SELECT member_id
					FROM conclave_attendance
					WHERE registration_complete =1
					AND conclave_year = $last_year
					)
					AND email <> ''
					AND firstname <> ''
					AND lastname <> ''
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				case "registration_incomplete":
					$last_year = $EVENT['year'];
					$sel_query = "
					SELECT members.member_id, firstname, lastname, email
					FROM members, orders, conclave_attendance 
					WHERE members.member_id=conclave_attendance.member_id 
					AND conclave_attendance.conclave_order_id=orders.order_id
					AND conclave_year=$last_year
					AND registration_complete=0
					AND lastname<>''
					AND firstname<>''
					AND email<>''
					ORDER BY lastname, firstname
					#LIMIT $lower_bound , 300";
					break;
				// just me
				default:
					$sel_query = "
					SELECT members.member_id, firstname, lastname, email
					FROM members
					WHERE member_id = $email_to
					LIMIT 1";
					break;
			}
		
		$select = $db->query(stripslashes($sel_query));
			if (DB::isError($select)) dbError("Error with query", $select, $sel_query);
		while($users = $select->fetchRow()) {
			
			$login_link = "http://sectionmaster.org"
				. "/register/?id=" . $users->member_id
				. "&key=" . makeKey(10, $users->member_id, $sm_db);
			$eval_login_link = $login_link . "&gotoInit=eval";
			
			$rasterized_body = eval_html_safe(stripslashes($email_body));
			
			$mail_headers = "From: ". $from_name . " <do_not_reply@sectionmaster.org>";
			$mail_headers .= "\r\nReply-To: ". $from_name . " <". $email_from . ">";
			$mail_headers .= "\r\nContent-Type: text/html"; 
			$pretty_name = "$users->firstname $users->lastname &lt;$users->email&gt;";
			
			if (mail(stripslashes("$users->firstname $users->lastname <$users->email>"), stripslashes($email_subject), stripslashes($rasterized_body), stripslashes($mail_headers))) {
				
				// Echo to the user
				echo "$pretty_name - <font color=green><b>success</b></font><br>";
				
			} else {
				echo "$pretty_name - <font color=red><b>fail</b></font><br>";
			}
		}
		//echo "<P><textarea style=\"height:500px; width:500px;\" >" . stripslashes($email_body) . "</textarea>";
	}

?>

<?

if ($hide_form !== TRUE):
?>

<form NAME="massmailform" ACTION="<?=$_GLOBALS['form_action'].'admin/section/massmail2.php'?>" METHOD=GET>
	
	<label for="email_to">To:</label>
	<select name="email_to" id="email_to">
		<option value=""></option>
		<option value="<?=$user->info->member_id?>"<?=($email_to==$user->info->member_id)?" selected":""?>>Just me (<?=$user->info->email?>)</option>
		<option value="this_year_attendees"<?=($email_to=='this_year_attendees')?" selected":""?>>This year's attendees (<?=$EVENT['year']?>)</option>
		<option value="last_year_attendees"<?=($email_to=='last_year_attendees')?" selected":""?>>Last year's attendees (<?=$EVENT['year']-1?>)</option>
		<option value="last_two_year_attendees"<?=($email_to=='last_two_year_attendees')?" selected":""?>>Last two year's attendees (<?=$EVENT['year']-2?>-<?=$EVENT['year']-1?>)</option>
		<option value="last_three_year_attendees"<?=($email_to=='last_three_year_attendees')?" selected":""?>>Last three year's attendees (<?=$EVENT['year']-3?>-<?=$EVENT['year']-1?>)</option>
		<option value="last_four_year_attendees"<?=($email_to=='last_four_year_attendees')?" selected":""?>>Last four year's attendees (<?=$EVENT['year']-4?>-<?=$EVENT['year']-1?>)</option>
		<option value="currently_registered"<?=($email_to=='currently_registered')?" selected":""?>>Currently registered for this year</option>
		<option value="not_yet_registered"<?=($email_to=='not_yet_registered')?" selected":""?>>Not yet registered, but attended previously</option>
		<option value="registration_incomplete"<?=($email_to=='registration_incomplete')?" selected":""?>>Registration incomplete (started but never finished)</option>
	</select><br>
		
	<label for="from_name">From Name:</label>
	<input id="from_name" name="from_name" type="text" value="<? print stripslashes($from_name); ?>" style="width: 500px;"><br>

	
	<label for="email_from">From E-mail Address:</label>
	<input id="email_from" name="email_from" type="text" value="<? print stripslashes($email_from); ?>" style="width: 500px;"><br>
	
	<label for="email_subject">Subject:</label>
	<input id="email_subject" name="email_subject" type="text" value="<? print stripslashes($email_subject); ?>" style="width: 500px;"><br>
	
	<label for="email_body">E-Mail Body:<br>
		<br>These tags are available:
			<code>[first_name]</code>
			<code>[last_name]</code>
			<code>[register_link]</code>
			<code>[evaluation_link]</code>
		<br></label>
<?

	$oFCKeditor = new FCKeditor('email_body') ;
	$oFCKeditor->BasePath	= "https://ssl4.westserver.net/sectionmaster.org/register/includes/fckeditor/";
	$oFCKeditor->Value = stripslashes($email_body);
	$oFCKeditor->Height = "500px";
	$oFCKeditor->Width = "550px";
	$oFCKeditor->Create() ;
?>
	
	<!--textarea id="email_body" name="email_body" style="height:500px; width: 500px;"><? print stripslashes($email_body); ?></textarea--><br>
	
	<!--label for="lower_bound">Start at Record #:</label>
	<input id="lower_bound" name="lower_bound" style="width: 40px" type="text" value="0"><br>
	<span id="comment">In a complete list of e-mail addresses resulting from the query you selected in the "To:" field, indicate which record or row number
		to start sending from, with the first row being 0.  The mass mailer system will only send out 300 e-mails per batch request, so as to avoid server timeouts.
		If there are more than 300 to send out, you will need to resubmit the mass mail request, starting where you left off.  For instance, first send a batch
		starting from row 0, followed by a batch starting from row 300.  This will effectively send your message out to up to 600 addresses (i.e., rows 0-599).</span><br>
	-->
	<input type="hidden" name="command" value="send">
	<input type="hidden" name="section" value="<?=$SECTION?>">
	<? if ($EVENT): ?><input type="hidden" name="event" value="<?=$EVENT['id']?>"><? endif; ?>
	
	
	<center><input type="submit" value="Send Mass Mail" style="width:200px; float:none;"></center>
	
</form>

<? endif; ?>