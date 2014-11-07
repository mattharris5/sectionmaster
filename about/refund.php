<html>
<head>
<title>SectionMaster Customer Service</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<base href="http://sectionmaster.org/">
<link rel="stylesheet" type="text/css" href="templates/default/style.css">
</head>

<body>

<div id="wrap"> <!-- #wrap - for centering -->

	<!-- Content -->
	
	<div id="content"> <!-- #content wrapper -->
	<img src="images/header.gif">
		<!-- Begin #main-content -->
		<div id="main-content"> 




<?
if ($_REQUEST['event_id'] != '') $eventid = $_REQUEST['event_id'];
else if ($_REQUEST['event'] != '') $eventid = $_REQUEST['event'];
if ($eventid) {
	
	require_once "../register/includes/db_connect.php";
	require "../register/includes/event_info.php";

	$sm_db =& db_connect("sm-online");	// Global SM-Online db
	$EVENT 	 = event_info($sm_db, $eventid);

}
?>

	  <h1>SectionMaster Customer Service</h1>

	  <h2>Online Registration Support</h2>
	  <p>Users of SectionMaster experiencing difficulties completing online registrations 
		or orders should contact their local SectionMaster coordinator.&nbsp; 
		This person will try to help resolve the support issue first before escalating 
		the problem directly to SectionMaster staff.
		<? if ($EVENT) {
	print "<ul><ul><b>{$EVENT['support_contact_name']}<br>
		   <!--img src=\"../templates/default/pcdtr/image.php?text={$EVENT['support_contact_email']}&amp;tag='font-size=12pt'\">
		   <br-->
		   {$EVENT['support_contact_email']}<br>
		   {$EVENT['support_contact_phone']}</b></ul></ul>";
	}?>
	  <h2>Refund Policy &amp; Procedure</h2>
	  <p>Users of the SectionMaster website may request full or partial refund 
		for purchases processed through the SectionMaster website. Refunds may 
		be requested within 15 days past the last day of the event for which the 
		purchase was made. 
	  <p>Since refunds are at the discretion of the individual organization using services provided by SectionMaster, the decision power lies in the hands of the section as to whether the refund will be granted.

	  <p><u>To request a refund, you must directly contact your registration coordinator 
		in your section.</u> 
		<? if ($EVENT) {
	print "<ul><ul><b>{$EVENT['reg_contact_name']}<br>
		   {$EVENT['reg_contact_email']}<br>
		   {$EVENT['reg_contact_phone']}</b></ul></ul>";
	}?>
	  <p>The coordinator for your section will then contact us at SectionMaster and 
  the refund will be resolved.&nbsp; If you need to contact us directly, you may 
  do so using the following contact information, though your coordinator will 
  be able to more quickly process your request.&nbsp; We will first verify refund requests 
  with the section coordinator or authorized staff if the request is sent directly to SectionMaster.</p>

<ul><ul>
		  <p><b>Clostridion Design &amp; Support, LLC<br>
			DBA SectionMaster<br>
			1219 N 161st St<br>
			Seattle, WA  98133<br>
			USA<br>
			E-mail: admin @ sectionmaster.org<br>
			Telephone: +1 (800) 300-7497</b></p>
</ul></ul>

<p>Your registration contact will determine the best method to process your refund. 
  The refund may come in the form of a check to mailed to your billing address 
  directly from the sponsoring organization (most likely the council holding the 
  funds for the event) or a credit back to your card, depending on the timing of your request. Refunds will 
  not be processed after the sponsoring organization's account with SectionMaster 
  has closed or 45 days after the last day of the event, whichever comes first. 
  After this time, refunds are the sole responsibility of the sponsoring organization 
  to disburse. 
<h2>Event Registration Fee Refunds</h2>

	  <p>In most cases, refunds for event registration fees are only granted under 
		extreme circumstances (i.e., hospitalization, family member death, etc.). 
		Once an event registration has been made, it is assumed that the registrant 
		will attend the event. Event registrations serve as a form of RSVP, helping 
		the organization to plan for the event. Revenue collected from pre-registration 
		fees is used months prior to the event to finance operations and purchases 
		(i.e., program material, site contract fees, and other unavoidable fixed 
		costs) necessary to have a successful event. 
	  <p>If there is an extreme case in which a registrant may experience undue 
		financial burden after paying for an event registration fee and is unable 
		to attend, the registrant should contact the event registration coordinator. 
	  <h2>Physical Goods and Other Partial Refunds</h2>

<p>In cases where physical goods are ordered, such as trading post merchandise, you must follow the same procedure as outlined above.  Your registration representative will determine the amount and type of refund to be processed.  In the event that physical goods and event registration fees are purchased in the same card transaction, a partial refund may be granted if necessary.  Again, it is the responsibility of the sponsoring organization using the services of SectionMaster to determine refund eligibility.  Shipping charges, if included, may not be refunded if the purchased items have already shipped, but this is up to the discretion of the sponsoring organization.  The cardholder is responsible for returning any goods that are refunded.

<h2>Disputes</h2>

<p>Cardholders always have the right to file a claim with their card issuer about any charges appearing on their statement, but the cardholder should first make every effort to try to have a refund processed by the sponsoring organization.  Should a dispute arise between the cardholder and the sponsoring organization, any chargeback fees incurred by SectionMaster as a result of such a dispute will be the responsibility of the sponsoring organization, as outlined in the SectionMaster Usage Agreement contract.

	</div>
	<!-- End #main-content -->
	
	</div><!-- End #content -->
	
	<div class="clear">&nbsp;</div>
	
<? include("../footer.php"); ?>
    </div><!-- End #wrap -->

</body>
</html>
