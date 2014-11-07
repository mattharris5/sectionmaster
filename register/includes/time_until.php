<?php 
function time_until($event, $day, $month, $year = "unset", $min = 0, $hour = 
 0, $sec = 0) { 

	// Fix the problem of being short by a day
	$day++;
	
	/* The above prints the time until a given date. 
	This can calculate to any time of day. */ 

	/* If you didn't put a year above ($year = "unset"), assume it's the current year */ 

	if ($year == "unset") 
		$year = date("Y"); 

	$left = mktime($sec, $min, $hour, $month, $day, $year) - mktime(); 

	/* Lets assum that this is an annual event such as a Birthday and that 
	it will happen again next year. This starts the counter over again after 
	the event takes place. */ 
 
	if (0 > $left) 
		$left = mktime($sec, $min, $hour, $month, $day, $year + 1) - mktime(); 

	/* If there is no time left on the counter (the day has come) 
	Change the "My" if you want, but leave the $event for the event name
	This is set so it will say: "My Birthday" */ 

	if ($left == 0) 
		return "$event"; 

	$ret_str = "There ";

	/* TO CHANGE HOW SPECIFIC YOU WANT THE COUNTDOWN,
		just comment out the time elements you don't want printed. */

	$time = array( 
		"week" => 604800, 
		"day" => 86400
	); 
//		"year" => 31449600, 
//		"hour" => 3600, 
//		"minute" => 60, 
//		"second" => 1); 

	while(list($span, $scale) = each($time)) { 
		$chunk = floor($left / $scale); 
		$left = $left % $scale; 

		if ($chunk != 0): 

	/* if there is more than one day left, have are, for 1 day have is */ 

			$p1 = ($chunk == 1) ? "is " : "are "; 

			if ($chunk > 1) 
				$span = "${span}s"; 

			$sep = ($left == 0) ? " and " : ", "; 

			$ret_str .= ((!ereg("[0-9]", "$ret_str")) ? $p1 : $sep) . 
			"$chunk $span"; 

		endif; 

	} 

	return $ret_str . " until ${event}."; 

 } 

	/* As an example, how long until your Birthday comes? */ 
	/* ("Event", day of month, month); */ 
//echo time_until("Birthday", 12, 9); 
 ?> 