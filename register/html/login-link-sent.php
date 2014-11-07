<? 
/*	SM-Online Registration System                      www.sectionmaster.org
	------------------------------------------------------------------------

	Login Link Sent

	------------------------------------------------------------------------
	Instructs the user to go to his or her e-mail inbox and use the login
	link to get into the system.											*/ ?>
	
<?
	// store session variables if needed
	$_SESSION['email'] = get('email');
	$_SESSION['event_id'] = $EVENT['id'];
	
	$myFromVar = $EVENT['type']=='tradingpost' ? "Trading Post" : "Online Registration";
	$fromString = "{$EVENT['org_name']} $myFromVar &lt;$SECTION@sectionmaster.org&gt;";
?>
	
<h2>Check your e-mail! (or see below if you can't)</h2>

	<p>We were successfully able to find your e-mail address in our database, so we've sent you an e-mail 
	with a special link that will allow you to securely log in to our system.  At this time, please go to 
	your e-mail inbox (for <? req('email'); ?>) and click the link to login.  If there are multiple people 
	in your family that use the same e-mail address, you will each receive your own login link in the e-mail.</p>
	
	<p><b>Note:</b> If you can't find the automated e-mail, <b>check your junk mail folder</b> for
	a message from <b><?=$fromString?></b>.</font></p>
	

<h2>Having trouble with the e-mail?</h2>
<p>You can still register, hassle-free!  Choose one of these options:</h2>
	
<ul>
		
		<? //check if there is idverify data before offering link
			$sql = "SELECT * FROM members
					LEFT JOIN secret_questions on members.member_id = secret_questions.member_id
					WHERE email = '" . get('email') . "'
					AND question1 != ''
					ORDER BY lastname, firstname";
			$result = $db->query($sql);
				if (DB::isError($result)) { dbError("Get member idverify data", $questions, $sql); }
				
		// Check for no idverify data
		if ($result->numRows() != 0) {

			print "<li><a href=\"register/?goto=idverify&event_id={$EVENT['id']}
					&email=" . get('email') . "\">Answer my secret questions to regain access to my account</a>.";
					
		}
		
		?>
		
		<li><a href="register/?command=new_member&event_id=<?=$EVENT['id']?>&email=<?=get('email')?>">The link in the e-mail I received isn't working or I can't access my e-mail inbox for <? req('email'); ?> and I don't remember the answers to my secret questions.  Create a new member record for me</a>.
			<?=($EVENT['do_training']) ? "<font color=red><b>Note:</b></font> Your training records from previous years will not be used if you choose this option.":"";?>

		<? if ($EVENT['type'] == 'event'): ?>
		<li><a href="register/?command=new_member&event_id=<?=$EVENT['id']?>&email=<?=get('email')?>">I have already registered myself for this event.  I want to register someone else using the same e-mail address</a>.
		<? endif; ?>

		</ul>