<html>
<head>
<title>SectionMaster</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="templates/default/style.css">
</head>

<body>

<div id="wrap"> 
  <!-- #wrap - for centering -->
  <!-- Content -->
  <div id="content"> 
	<!-- #content wrapper -->
	<img src="images/header.gif"> 
	<!-- Begin #main-content -->
	<div id="main-content"> 
	  <h2>Welcome to SectionMaster</h2>
      
<? /*
      <div class="error_summary" style=" text-align:left; font-size:10px; line-height:10px">
      <h2><font color="red">FRAUD ALERT - Please read</font></h2>
      <p>If you have reached this website because you have discovered one or more $0.01 transactions on your credit card statement, rest assured that these transactions have been deleted, voided, or refunded.  SectionMaster advises you to <b>IMMEDIATELY CLOSE YOUR CREDIT CARD ACCOUNT</b> if this has happened to you, as you are now a victim of Internet fraud.</p>
      <p>If you are a SectionMaster user, or have registered for an event on this website, <b>you are not at risk</b> as best we can tell, but it is always a good general practice to regularly monitor your credit card statement for potential fraudulent activity from any source.</p>
      <p>Between August 7-9, 2008, a malicious hacker discovered a backdoor to utilize our merchant credit card processor to make transactions appear to come from "SECTION MASTER" on credit card statements.  This is a form of phishing, which enables hackers to determine which credit cards are valid so that they may use the accounts to make larger purchases in the future.  We have since addressed this security vulnerability.  Note that the affected credit card numbers were <b>not stolen from us</b>, but from other sources.  <b>Complete credit card numbers stored by our merchant processor are never visible to hackers&mdash;not even to us</b>.</p>
      <p>We respect your privacy, and encourage you to <a href="about/privacy.php">read our full privacy policy</a>.  We will never share your personal information with anyone, and are doing our civic duty to make sure that these hackers are prosecuted to the maximum extent allowable by law.  As such, we have filed a formal report with the federal agency that handles Internet crime.  We are also working with our bank to ensure they take all possible measures to prevent this from happening in the future with other credit card merchants.</p>
      <p>If you feel you are a victim of internet crime, we also encourage you to file your own report with the <a href="http://www.ic3.gov" target="_blank">Internet Crime Complaint Center (IC3)</a>, a partnership between the Federal Bureau of Investigation (FBI), the National White Collar Crime Center (NW3C), and the Bureau of Justice Assistance (BJA).</p>
      </div>
*/      
      
			/* Connect to SM-Online DB */
			require_once "register/includes/db_connect.php";  // be sure to point to correct path
			$sm_db =& db_connect("sm-online");    // Global SM-Online db
		
			// Online Registration
			$sql = "SELECT * FROM events WHERE type = 'event' AND online_reg_open_date < now() AND online_reg_close_date > now() AND id<>6 AND id<>99 ORDER BY start_date";
			$events = $sm_db->query($sql);

			while ($thisevent = $events->fetchRow())
			{
				if (is_null($regopenmessage)) print "<p><b>Register Here</b></p>
				<p>Below is a list of current events open for registration:</p>";
				$regopenmessage=1;
				print "<p>&bull;&nbsp; <a href=\"http://start.sectionmaster.org/$thisevent->id\">Section <b>$thisevent->section_name</b> $thisevent->formal_event_name - $thisevent->conclave_date ($thisevent->city_and_state)</a></p>\n";
				$count++;
			}

			// Trading Posts
			$sql = "SELECT * FROM events WHERE type = 'tradingpost' AND online_reg_open_date < now() AND online_reg_close_date > now() AND id<>8 ORDER BY section_name";
			$events = $sm_db->query($sql);

			while ($thisevent = $events->fetchRow())
			{
				if (is_null($tpopenmessage)) print "<p><b>Online Trading Post</b></p>
				<p>You can purchase section merchandise without registering for an event, or place an order even if you have already registered.  Below is a list of current sections with open Trading Posts:</p>";
				$tpopenmessage=1;
				print "<p>&bull;&nbsp; <a href=\"http://start.sectionmaster.org/$thisevent->id\">";
				if (strstr($thisevent->section_name,"R")=='R') print $thisevent->formal_event_name;
				else print "Section <b>$thisevent->section_name</b> Trading Post";
				print "</a></p>\n";
				$count++;
			}

			//if (is_null($count)) print "There are no events currently open for registration.";
		?>
	  <p><b>What is SectionMaster and how did I get here?</b></p>
	  <p>SectionMaster is a one-stop online event registration and trading post 
		merchandise sales management system built specifically with Order of the 
		Arrow sections in mind.&nbsp; Your section may have partnered with us 
		to provide these and other services.</p>
	  <p><b>Sections: Get Started Today</b></p>
	  <p>Want to empower your section with SectionMaster's online event registration, 
		trading post, and payment services?&nbsp; Read all about it in our <a href="content/gettingstarted.pdf" target="_blank"> 
		"Getting Started with SectionMaster"</a> fact sheet.  Also <a href="content/SectionMaster SOS.pptx" target="_blank">download a PowerPoint presentation</a> which provides an explanation of the system's capabilities.</p>
	  <p><b>Give SectionMaster a Test Run</b></p>
	  <p>Try a <a href="http://start.sectionmaster.org/6">SectionMaster Online Registration demo</a>, complete with training class selection, trading post ordering, and more.
	  Try a <a href="http://start.sectionmaster.org/8">SectionMaster Trading Post demo</a>, a standalone storefront providing inventory tracking and shipping options.</p>
	  <p><b>Download SectionMaster Client</b></p>
	  <p>If you're here to download a copy of the popular desktop database administration 
		software for your section, <a href="download">click here</a>.&nbsp; This 
		software is free for sections to use.&nbsp; Version 4.09 and above integrate seamlessly with SM-Online.</p>
	</div>
	<!-- End #main-content -->
  </div>
  <!-- End #content -->
  <div class="clear">&nbsp;</div>
<? include("footer.php"); ?>
</div>
<!-- End #wrap -->

</body>
</html>
