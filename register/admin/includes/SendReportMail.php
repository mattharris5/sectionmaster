<?
require_once("phpmailer/phpmailer.php");
require_once("class.html2text.php");

//
/** Send the email */
//
function SendReportMail($To, $Subject, $ContentHTML, $ContentText, $ReplyToName, $ReplyToEmail) {

	global $EVENT;

	/* One-time setup of the mailer */
	$mail = new PHPMailer();
	//$mail->IsSMTP();
	$mail->SMTPAuth=false;
	$mail->IsHTML(true);
	//$mail->Username=EMAIL_USERNAME;
	//$mail->Password=EMAIL_PASSWORD;
	$mail->Host = localhost;
	
	$h2t = new html2text();

	$mail->FromName = "SectionMaster Reports";
	$mail->From = ("stats@sectionmaster.org");
	$mail->AddReplyTo($ReplyToEmail, $ReplyToName);
	$mail->AddAddress($To,"");
	$mail->Subject  = $EVENT['formal_event_name'] . " Registration Statistics:" . $Subject;
	
	$h2t->html = $bodyHTML;
	$bodyTEXT = $h2t->get_text();
	$mail->Body = $ContentHTML;
	$mail->AltBody = $ContentText;
	$mail->Send();
	$mail->ClearAddresses();
//	$mail->ClearAttachments();

/*
	// specify MIME version 1.0 
	$headers = "From: SectionMaster Reports <stats@sectionmaster.org>\r\n"; 
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
	$headers .= chunk_split(base64_encode($ContentText)); 

	// HTML version of message 
	$headers .= "--$boundary\r\n" . 
	   "Content-Type: text/html; charset=ISO-8859-1\r\n" . 
	   "Content-Transfer-Encoding: base64\r\n\r\n"; 
	$headers .= chunk_split(base64_encode($ContentHTML)); 

	// send message
	if (mail($To, "{$EVENT['formal_event_name']} Registration Statistics: $Subject", "", $headers)) {
		return true;
	} else {
		return false;
	}
*/
	return true;
}

?>