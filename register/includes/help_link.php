<?
####################################
#
# function help_link()
#
####################################

function help_link($topic, $message = "<span class='icon'>&nbsp;&nbsp;&nbsp;</span>") {
	
	global $EVENT;
	
	// print link
	return "<div class=\"help_link_container\">
				<a href=\"register/help/?topic=$topic&event_id={$EVENT['id']}\" target=\"help\"
					onClick=\"window.open('register/help/?topic=$topic&event_id={$EVENT['id']}','help', 'locationbar=0, menubar=0, personalbar=0, statusbar=0, toolbar=0, scrollbars=1, width=475, height=300');\">
				$message</a>
			</div>";
	
}