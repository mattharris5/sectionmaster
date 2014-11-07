<?
##########################################
# Mass Mailer Script
##########################################
# Author: Matt Harris
#         matt@cdsportland.com
# Date: 09/02/2005
##########################################

# Includes
require "../pre.php";
require "../../includes/eval_html.php";

?>
<h1>SectionMaster Mass Mailer Program</h1>
<br>
<?

# Check for event
$EVENT 	 = event_info($sm_db, $user->info->current_event);
$SECTION = section_id_convert($EVENT['section_name']);


# Import request vars
	import_request_variables("G");
	$command = $_REQUEST['command'];
	
# If command is to send, get a count and confirm
	if ($command == "send") {
		$email_from = htmlspecialchars($email_from);
		if ($desc=='' OR $sel_query=='' OR $email_from=='' OR $email_subject=='' OR $email_body=='') {
			echo "Please fill in all data.";
		} else {
			// Write to the log database
			/*mysql_select_db('w1a', $conn);
			$insert = "INSERT INTO mass_mails VALUES('','$desc','$sel_query','$email_from','$email_subject','$email_body')";
			$result = mysql_query($insert, $conn) or die("Could not record log: " . mysql_error() . ": " . $insert);
			$massmail_id = mysql_insert_id($conn);
			mysql_select_db('oa', $conn);*/

			$select = $db->query(stripslashes($sel_query));
				if (DB::isError($select)) dbError("Error with query", $select, $sel_query);
			$count = $select->numRows();
			print "Mass Mail will be sent to <b>$count</b> email addresses. Continue?";
			print "<p><center><input type=button value=\"Continue\"
					onClick=\"window.location='".$_GLOBALS['form_action']."admin/section/massmail.php?".$_SERVER['QUERY_STRING']."&command=continue&massmail_id=$massmail_id'\" 
					style=\"width:300px; float:none;\"><br>";
			while ($users = $select->fetchRow()) {
				print "$users->firstname $users->lastname &lt;$users->email&gt;<br>";
			}
			$hide_form = TRUE;
			
		}
	}

# Process the emails
	if ($command == "continue") {
		$hide_form = TRUE;
		
		print "<h3>Results:</h3><br>";
		
		$select = $db->query(stripslashes($sel_query));
			if (DB::isError($select)) dbError("Error with query", $select, $sel_query);
		while($users = $select->fetchRow()) {
			
			$login_link = "http://sectionmaster.org"
				. "/register/?id=" . $users->member_id
				. "&key=" . makeKey(10, $users->member_id, $sm_db);
			$eval_login_link = $login_link . "&gotoInit=eval";
			
			$rasterized_body = eval_html(stripslashes($email_body));
			$mail_headers = "From: ". $email_from;
			$pretty_name = "$users->firstname $users->lastname&lt;$users->email&gt;";
			
			if (mail(stripslashes("$users->firstname $users->lastname <$users->email>"), stripslashes($email_subject), stripslashes($rasterized_body), stripslashes($mail_headers))) {

				// Write to the log database
/*				mysql_select_db('w1a', $conn);
				$insert = "INSERT INTO massmail_history VALUES('','$massmail_id','".$users['ID_number']."','".$users['email']."','".time()."')";
				mysql_query($insert, $conn) or die("Could not record log: " . mysql_error() . ": " . $insert);
				mysql_select_db('oa', $conn);
*/				
				// Echo to the user
				echo "$pretty_name - <b>success</b><br>";
				
			} else {
				echo "$pretty_name - <b>fail</b><br>";
			} 
		}
		echo "<P><textarea style=\"height:500px; width:500px;\" >" . stripslashes($email_body) . "</textarea>";
	}

?>

<?

if ($hide_form !== TRUE):
?>

<form NAME="massmailform" ACTION="<?=$_GLOBALS['form_action'].'admin/section/massmail.php'?>" METHOD=GET>
	
	<label for="desc">Description (for logs):</label>
	<input id="desc" name="desc" type="text" value="<? print stripslashes($desc); ?>" style="width: 500px;"><br>
	
	<label for="sel_query">SQL Select Query:</label>
	<textarea id="sel_query" name="sel_query" style="width: 500px;"><? print stripslashes($sel_query); ?></textarea><br>
	
	<label for="email_from">E-Mail From:</label>
	<input id="email_from" name="email_from" type="text" value="<? print stripslashes($email_from); ?>" style="width: 500px;"><br>
	
	<label for="email_subject">Subject:</label>
	<input id="email_subject" name="email_subject" type="text" value="<? print stripslashes($email_subject); ?>" style="width: 500px;"><br>
	
	<label for="email_body">E-Mail Body:<br>
		<i>PHP Code is allowed <b>inside</b> php tags or special tags.
		<br><b>$users</b> object is available from db select.
		<br><b>$login_link</b> is available as well.</i>
		<br><font color=red>To use event-specific things, like login_link, you MUST specify event in URL.</font>
		<br>Example: <code>&lt;&lt;$users->firstname&gt;&gt;</code> or <code>&lt;&lt;$login_link&gt;&gt;</code> or <code>&lt;&lt;$eval_login_link&gt;&gt;</code>
		<br></label>
	<textarea id="email_body" name="email_body" style="height:500px; width: 500px;"><? print stripslashes($email_body); ?></textarea><br>
	
	<input type="hidden" name="command" value="send">
	<input type="hidden" name="section" value="<?=$SECTION?>">
	<? if ($EVENT): ?><input type="hidden" name="event" value="<?=$EVENT['id']?>"><? endif; ?>
	
	
	<center><input type="submit" value="Send Mass Mail" style="width:200px; float:none;"></center>
	
</form>

<? endif; ?>