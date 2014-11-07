<?php


/* ok, redirect */
if(isset($_SERVER['REDIRECT_URL'])) {
	
    $requestArray = explode('/' , $_SERVER['REDIRECT_URL']);

	// if using sectionmaster.org/start/
    if(isset($requestArray[2]) && is_numeric($requestArray[2])){
        $event_id = $requestArray[2];
		header("Location:http://www.sectionmaster.org/register/?event_id=$event_id&session=destroy");
	}
	
	// if using start.sectionmaster.org/
    else if(isset($requestArray[1]) && is_numeric($requestArray[1])){
        $event_id = $requestArray[1];
		header("Location:http://www.sectionmaster.org/register/?event_id=$event_id&session=destroy");
	}
	

	else {
		header("Location:http://www.sectionmaster.org/");
	}
	
}