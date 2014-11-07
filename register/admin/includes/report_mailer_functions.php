<?

$reports = array (
	"summary"		=> "Summary",
	"lodge"			=> "By Lodge",
	"chapter"		=> "By Chapter",
	"out_of_section"=> "Out-of-Section",
	"tradingpost"	=> "Trading Post",
	"training"		=> "Training",
	"diet"			=> "Dietary Needs",
	"medical"		=> "Medical Issues",
	"medical-all"	=> "Medical Issues & ALL Emergency Contacts",
	"eval"			=> "Evaluation" );
$formal_title = $reports[$report_type];

# Get event data
$EVENT 	 = event_info($sm_db, $user->info->current_event);
$SECTION = section_id_convert($EVENT['section_name']);

# Current Year variable
$event_year = $EVENT['year'];
$event_date = $EVENT['start_date'];
$year_ago = strtotime($EVENT['start_date']) - (365*24*60*60);

// include extra report data
require "reports/report_data.php";

// get the actual report content
ob_start();
include "reports/email_header.php";
include "reports/$report_type.php";
include "reports/email_footer.php";
$report_content_original = ob_get_contents();
$report_content_html = $report_content_original;
ob_end_clean();

// strip out tags that shouldn't appear in the online version
$pattern = "%(<\!--SM:webonly-->)(.*?)(<\!--\/SM:webonly-->)%is";
$report_content_html = preg_replace ($pattern, "", $report_content_html);
$pattern = "%(<\!--SM:plaintext-->)(.*?)(<\!--\/SM:plaintext-->)%is";
$report_content_html = preg_replace ($pattern, "", $report_content_html);

// generate the text only version by looking for "<!--SM:plaintext-->" tags
$pattern = "%(<\!--SM:plaintext-->)(.*?)(<\!--\/SM:plaintext-->)%is";
if (preg_match_all($pattern, $report_content_original, $plaintext_snippets) > 0) {
	foreach($plaintext_snippets[0] as $id => $snippet) {
		$text .= "\r\n" . $snippet;
	}
}
$text = strip_tags($text);  // cleanup the found text
$text = wordwrap($text, 72);
$report_content_text = $text;

?>