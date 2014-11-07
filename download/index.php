<?

# DB Connection
require_once "../register/includes/db_connect.php";  // be sure to point to correct path
$sm_db =& db_connect("sm-online");    // Global SM-Online db

# Download SectionMaster Script

$softwareversion = "4.16";
$releasedate = "17 May 2013";
$filename = "SectionMaster4.16.zip";

$download_url = "http://www.sectionmaster.org/content/$filename";
$download_path = "/var/www/html/content/$filename";

if ($_GET['command'] == 'process_download') {

	//send e-mail notification
	$to 		= "rob@sectionmaster.org";
	$subject 	= "New SectionMaster Download (" . $softwareversion . ") from Section {$_GET['section']}";
	$headers	= "From: {$_GET['name']} <{$_GET['email']}>";
	$timestamp	= date("D M j G:i:s T Y");
	$message	= "SectionMaster has been downloaded by:
		Date/Time:    $timestamp
		Name:         {$_GET['name']} <{$_GET['email']}>
		Section:      {$_GET['section']}
		Position:     {$_GET['position']}
		Version:      $softwareversion";
	mail($to, $subject, $message, $headers);
	
	//save info to database
	import_request_variables("G");
	$sql = "INSERT INTO downloads VALUES ('$softwareversion', '" . date("Y-m-d G:i:s") . "', '{$_GET['section']}', '{$_GET['name']}', '{$_GET['email']}', '{$_GET['position']}')";
	$insert = $sm_db->query($sql);
	
}
	
?>

<html>
<head>
<title>Download SectionMaster Client</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="../templates/default/style.css">
</head>

<body>

<div id="wrap"> 
  <!-- #wrap - for centering -->
  <!-- Content -->
  <div id="content"> 
	<!-- #content wrapper -->
	<img src="../images/header.gif"> 

<!-- PAGE SPECIFIC CODE -->
<script LANGUAGE=JAVASCRIPT>
<!-- begin hiding from Scott's lame browser

// for email validation
function validEmail(email) 
{
	invalidChars = " /:,;#!$%^&*()|\?<>~`+"

	if (email == "") { 							// can be empty because cgi tests for that
		return true
	}

	for (i=0; i < invalidChars.length; i++)	{	// does it contain any invalid characters?
		badChar = invalidChars.charAt(i)
		if (email.indexOf(badChar,0) > -1) {
			return false
		}
	}

	atPos = email.indexOf("@",1)				// there must be one "@" symbol
	if (atPos == -1) {
		return false
	}
	
	if (email.indexOf("@",atPos+1) != -1) {		// and only one "@" symbol
		return false
	}
		
	periodPos = email.indexOf(".",atPos)
	if (periodPos == -1) {						// and at least one "." after the "@" 
		return false
	}
	
	if (periodPos+3 > email.length)	{			// must be at least 2 characters after the "."
		return false
	}
	return true
}

function validateReqEmail(form) {
	if (!validEmail(form.r_email.value)) {
		alert("Please enter your email address here. A valid email address includes your login, the @ symbol, and your Internet Service Provider.")
		form.r_email.focus()
		form.r_email.select()
		return false
	}
	return true
}
// end of email validation

// Validate the amount in purchasing
function isNum(passedVal) 
{					
	if (passedVal == "") {
		return false
	}

	for (i=0; i < passedVal.length; i++) {
		if (((passedVal.charAt(i) < "0") || (passedVal.charAt(i) > "9")) && ((passedVal.charAt(6) == "-") || (passedVal.charAt(6) == ""))) {
			return false
		}
	}
	return true
}

function ValidateAddress(form) {
    // Validate  Address
	if (form.name.value == "") {
	    alert("Please enter your name.")
		form.name.focus()
		form.name.select()
		return false
	}
	if ((!validEmail(form.email.value)) || (form.email.value == "")) {
		alert("Invalid email address. Please enter your email address here. A valid email address looks like 'someone@somewhere.com'.")
		form.email.focus()
		form.email.select()
		return false
	}
	if (form.section.value == "XXX") {
		alert("Please tell us what section in which you will be using SectionMaster.")
		form.region.focus()
		return false
	}
	if (form.position.value == "") {
		alert("Please tell us your position in the OA.")
		form.position.focus()
		form.position.select()
		return false
	}

	return true
}


function updateSections(form) {
	
    var sectiondd = form.section;
    var regiondd   = form.region;
    
    sectiondd.options.length = 0; // Clear the menu
        
    if (regiondd.value == "XXX") {
    	sectiondd.options[0] = new Option("--Choose your Region first--","XXX");
    } else if (regiondd.value == "C") {
		sectiondd.options[0] = new Option("--What is your Section?--","XXX");
		<?
	
		$sql = "SELECT DISTINCTROW section FROM lodges WHERE section LIKE 'C%' ORDER BY section";
		$sections = $sm_db->query($sql);
		
		$count=1;
		while ($thissection = $sections->fetchRow())
		{
			print "sectiondd.options[$count] = new Option('$thissection->section','$thissection->section');\n\t\t";
			$count++;
		}

		?>

    } else if (regiondd.value == "NE") {
		sectiondd.options[0] = new Option("--What is your Section?--","XXX");	
		<?
	
		$sql = "SELECT DISTINCTROW section FROM lodges WHERE section LIKE 'NE%' ORDER BY section";
		$sections = $sm_db->query($sql);
		
		$count=1;
		while ($thissection = $sections->fetchRow())
		{
			print "sectiondd.options[$count] = new Option('$thissection->section','$thissection->section');\n\t\t";
			$count++;
		}

		?>

    } else if (regiondd.value == "SR") {
		sectiondd.options[0] = new Option("--What is your Section?--","XXX");	
		<?
	
		$sql = "SELECT DISTINCTROW section FROM lodges WHERE section LIKE 'SR%' ORDER BY section";
		$sections = $sm_db->query($sql);
		
		$count=1;
		while ($thissection = $sections->fetchRow())
		{
			print "sectiondd.options[$count] = new Option('$thissection->section','$thissection->section');\n\t\t";
			$count++;
		}

		?>

    } else if (regiondd.value == "W") {
		sectiondd.options[0] = new Option("--What is your Section?--","XXX");	
		<?
	
		$sql = "SELECT DISTINCTROW section FROM lodges WHERE section LIKE 'W%' ORDER BY section";
		$sections = $sm_db->query($sql);
		
		$count=1;
		while ($thissection = $sections->fetchRow())
		{
			print "sectiondd.options[$count] = new Option('$thissection->section','$thissection->section');\n\t\t";
			$count++;
		}

		?>
	}	
}
// end hiding from Scott's inferior browser-->
</script>
<!-- /PAGE SPECIFIC CODE -->

	<!-- Begin #main-content -->
	<div id="main-content"> 
	  <h2>Download SectionMaster Client</h2>

<?
$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
$filesize = round(filesize($download_path)/pow(1024, ($i = floor(log(filesize($download_path), 1024)))), 2) . $filesizename[$i];

if ($_GET['command'] == 'process_download') {
	print "		
		Thank you, {$_GET['name']}, for downloading SectionMaster.  We hope you enjoy using it!

		<P><B>SectionMaster $softwareversion</B>
		<BR>Total download size: $filesize</P>
		
		<P><I>If your download does not begin automatically,
		<a href=\"$download_url\">click here</a>.</i></p>
		
		<SCRIPT LANGUAGE=\"javascript\" TYPE=\"text/javascript\">
		setTimeout(\"window.location='$download_url'\", 3000);
		</SCRIPT>
		</div>
			<!-- End #main-content -->
		  </div>
		  <!-- End #content -->
		  <div class=clear>&nbsp;</div>
		  <div id=footer> 
			<p>Copyright &copy; 2005 SectionMaster<br>
			  SectionMaster is a division of Clostridion Design & Support, LLC. <br>
			  <a href=../about/privacy.php>Privacy Policy</a> | <a href=../about/refund.php>Refund 
			  Policy &amp; Customer Service</a></p>
		  </div>
		</div>
		<!-- End #wrap -->
		
		</body>
		</html>

		";
		
	exit;
}
else 
{
print '
<!--p><font color=orange><b>Note:</b> The production version of this software is no longer being offered for download.
The program is in active development, but is only made available to sections signed up for the SectionMaster Online service.
<b>You can still download the outdated version, but it is no longer compatible with SectionMaster Online.</b></font></p-->
<p><b>Current Available Version: '.$softwareversion.' ('.$releasedate.')</b></p>
<p>Please submit the following data in order to download SectionMaster.  
We only ask for this information so we know you are interested in 
our software, and to keep you notified of updates.
Your e-mail address will not be sold.  For more information, please
see our <a href=../about/privacy.php>Privacy Policy</a>.</p>

<FORM METHOD=GET ONSUBMIT="return ValidateAddress(this)" name="form1">

<TABLE WIDTH=100% BORDER=0 CELLPADDING=1 CELLSPACING=2 BGCOLOR=#FFFFFF>
<TR valign=top>
<TD ALIGN=RIGHT><B>Full Name:</B></TD>
<TD><INPUT TYPE="text" NAME="name" SIZE=50 MAXLENGTH=40></TD>
</TR>


<TR valign=top>
<TD ALIGN=RIGHT><B>E-mail Address:</B></TD> 
<TD><INPUT TYPE="text" NAME="email" SIZE=50 MAXLENGTH=40></TD>
</TR> 


<TR valign=top>
<TD ALIGN=RIGHT><B>Region:</B></TD>
<TD>
<SELECT NAME="region" onChange="updateSections(this.form);">
<OPTION VALUE="XXX">-- What is your Region? --</OPTION>
<OPTION VALUE="W">Western Region</OPTION>
<OPTION VALUE="SR">Southern Region</OPTION>
<OPTION VALUE="NE">Northeast Region</OPTION>
<OPTION VALUE="C">Central Region</OPTION>
</SELECT>
</TD>
</TR>

<TR valign=top>
<TD ALIGN=RIGHT><B>Section:</B></TD>
<TD>
<SELECT NAME="section">
<OPTION VALUE="XXX">-- Choose Section First --</OPTION>
</SELECT>
</TD>
</TR>

<TR valign=top>
<TD ALIGN=RIGHT VALIGN=TOP><B>Position or Title:</B></TD>
<TD><INPUT TYPE="text" NAME="position" SIZE=50>
</TD>
</TR>

<TR valign=top>
<TD></TD>
<TD><INPUT TYPE=submit VALUE="Download SectionMaster">
</TD>
</TR>

</TABLE>

<INPUT TYPE=HIDDEN NAME="command" VALUE="process_download">
</FORM>
</div>
	<!-- End #main-content -->
  </div>
  <!-- End #content -->
  <div class="clear">&nbsp;</div>';
include("../footer.php");
print'</div>
<!-- End #wrap -->

</body>
</html>';
}
?>