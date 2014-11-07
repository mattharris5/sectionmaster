<?
function getheadings($buffer) {
	global $heading_css;
	
	$tags = array ('<h1>'); // only switch out h1's
							// originally: array('<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>');
	$pattern = '/(<\/?(?: .*|h1|h2|h3|h4|h5|h6)>)/ims';
	
	if (!is_array($_SESSION['pcdtr']) || $_GET['debug']) {
		if (is_readable($heading_css)) {
			$style_array = file($heading_css);
		
			if (is_array($style_array)) {
				foreach ($style_array as $k => $prop) {
					if (in_array('<'.trim(str_replace('{', '', $prop)).'>', $tags)) {
						$curr = trim(str_replace('{', '', $prop));
					} else {
						$dets = explode(':', $prop);
						if ($curr && $dets[0] && $dets[1]) {
							$_SESSION['pcdtr'][$curr][trim($dets[0])] = trim(str_replace('pt', '', str_replace('}', '', str_replace(';', '', $dets[1]))));
						}
					}
				}
			}
		} 
	}

	$html_array = preg_split ($pattern, trim ($buffer), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	
	if (is_array($html_array)) {
		foreach ($html_array as $k => $html) {
			if (in_array($html, $tags)) {
				$next_k = $k + 1;
				$clean_tag = str_replace('>', '', str_replace('<', '', $html));
				
				$page .= '<'.$clean_tag.' style="background-image:url(../../templates/default/pcdtr/image.php?text='.urlencode(strip_tags($html_array[$next_k])).'&amp;tag='.$clean_tag.');">';
			} else {
				$page .= $html;
			}
		}
	}
	
	return $page;
}
?>