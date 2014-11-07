<?

function eval_buffer($string) {
	global $users;
	global $login_link, $eval_login_link;
	ob_start();
	eval("$string[2];");
	$return = ob_get_contents();
	ob_end_clean();
	return $return;
}

function eval_print_buffer($string) {
	global $users;
	global $login_link, $eval_login_link;
	ob_start();
	eval("print $string[2];");
	$return = ob_get_contents();
	ob_end_clean();
	return $return;
 }

function eval_html($string) {
	global $users;
	global $login_link, $eval_login_link;
	$string = preg_replace_callback('/(<<)(.*?)>>/si',
									'eval_print_buffer',
									$string);
	$string = preg_replace_callback('/(<\?=)(.*?)\?>/si',
									'eval_print_buffer',
									$string);
	return preg_replace_callback('/(<\?php|<\?)(.*?)\?>/si',
								 'eval_buffer',
								 $string);
}

function eval_html_safe($string) {
	global $users;
	global $login_link, $eval_login_link;
	
	$string = str_replace("[register_link]",$login_link,$string);
	$string = str_replace("[evaluation_link]",$eval_login_link,$string);
	$string = str_replace("[first_name]",$users->firstname,$string);
	$string = str_replace("[last_name]",$users->lastname,$string);
	
	return $string;
}


?>