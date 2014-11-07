<?
/* Do not show progress bar in certain situations!! */
if ($PAGE == "default" || $PAGE == "final" || $PAGE == "eval") return;

?>


<div class="progress_bar">
	
	<ul>
		
		<li id="title">Progress:</li>
		
		<li id="start_over">
			<a href="register/?session=destroy&event_id=<?=$EVENT['id']?>">
			Start<br />Over</a></li>
			
		<?
		foreach ($page_order as $code => $title) {
	
			$i++;  $css = "";
			$href = "register/?goto=$code";
			
			// if this is the current page
			if ($code == $_SESSION['page']) { 
				$css = " id=\"current\"";
			}
			
			// if we havent reached this page yet
			if ($furthest_step_met) {
				$css = " id=\"not_yet\"";
				$href = "register/";
			}
			
			// if we got to the "furthest step"
			if ($code == $_SESSION['current_reg_step'] || !$_SESSION['current_reg_step']) {
				$furthest_step_met = true;
			}
			
			print "\n\n<li$css><a href=\"$href\">
				Step $i.<br />
				$title</a></li>";
				
		}
		?>

	</ul>
									 
</div>