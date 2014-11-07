<?
$no_header = true;
require_once "../pre.php";
require_once "reports/report_data.php";	


class ReportMail {
	
	
	var $ToEmail;
	var $ToName;
	var $FromEmail;
	var $FromName;
	var $Content;
	var $Subject;
	var $ReportType;
	var $ReportTitle;
	var $Section;
	var $EventID;
	
	var $ReportTitles = array (
			"full"			=> "Full",
			"summary"		=> "Summary",
			"lodge"			=> "Lodge",
			"chapter"		=> "Chapter",
			"tradingpost"	=> "Trading Post",
			"training"		=> "Training",
			"diet"			=> "Dietary Needs",
			"medical"		=> "Medical Conditions",
			"eval"			=> "Evaluation" );

	/** Constructor **/
	function ReportMail($Type, $EventID, $ToEmail) {

		global $EVENT, $SECTION;
		$EVENT 	 = event_info($sm_db, $user->info->current_event);
		$SECTION = section_id_convert($EVENT['section_name']);
		

		$this->ReportType = $Type;
		$this->ReportTitle = $ReportTitles[$Type];
		$this->FromEmail = "stats@sectionmaster.org";
		$this->FromName = "$Section Stats Reporting";
		$this->EventID = $EventID;
		$this->Subject = "$EventTitle Registration Report: $this->ReportTitle Report as of XXX";
	}

	/** Set any class variable */
	function Set($varName, $value) {
		$this->$varName = $value;
	}

	/** Sets both the HTML and text versions of the email */
	function SetContent($content) {
		$this->Content['text'] = $content;
//		$this->Content['html'] = get_include_contents("emails/html-header.php");
		$this->Content['html'].= nl2br($content);
//		$this->Content['html'].= get_include_contents("emails/html-footer.php");
	}
	
	/** Allows user to set special content for the HTML version */
	function SetHTMLContent($content) {
//		$this->Content['html'] = get_include_contents("emails/html-header.php");
		$this->Content['html'].= $content;
//		$this->Content['html'].= get_include_contents("emails/html-footer.php");
	}

	/** Show a preview */
	function PrintPreview() {
		print $this->PrintEmailHeader();
		print "<hr>";
		print $this->Content['html'];
	}

	/** Print out the e-mail "header" */
	function PrintEmailHeader() {
		print "<code>
			<strong>From: 	</strong>	$this->FromName &lt;$this->FromEmail&gt;<br>
			<strong>To: 	</strong>	$this->ToName &lt;$this->ToEmail&gt;<br>
			<strong>Subject: </strong>	$this->Subject<br>
		</code>";
	}

	/** Send the email */
	function SendMail() {

		// specify MIME version 1.0 
		$headers = "From: $this->FromEmail\r\n"; 
		$headers .= "MIME-Version: 1.0\r\n"; 

		// unique boundary 
		$boundary = uniqid("SM-REPORTS"); 

		// tell e-mail client this e-mail contains//alternate versions 
		$headers .= "Content-Type: multipart/alternative" . 
		   "; boundary = $boundary\r\n\r\n"; 

		// message to people with clients who don't understand MIME 
		$headers .= "This is a MIME encoded message.\r\n\r\n"; 

		// plain text version of message 
		$headers .= "--$boundary\r\n" . 
		   "Content-Type: text/plain; charset=ISO-8859-1\r\n" . 
		   "Content-Transfer-Encoding: base64\r\n\r\n"; 
		$headers .= chunk_split(base64_encode($this->Content['text'])); 

		// HTML version of message 
		$headers .= "--$boundary\r\n" . 
		   "Content-Type: text/html; charset=ISO-8859-1\r\n" . 
		   "Content-Transfer-Encoding: base64\r\n\r\n"; 
		$headers .= chunk_split(base64_encode($this->Content['html'])); 

		// send message
		if (mail($this->ToEmail, $this->Subject, "", $headers)) {
			return true;
		} else {
			return false;
		}
	}
}

?> 