<?
/*	SM-Online Registration System                      www.sectionmaster.org
	------------------------------------------------------------------------

	EVALUATION PAGE (html/eval.php)

	------------------------------------------------------------------------										*/

	# Check that eval hasn't been submitted already
		$sql = "SELECT * FROM evaluation_responses
		        LEFT JOIN evaluation_questions USING (question_id)
		        WHERE member_id='{$_SESSION['member_id']}' AND deleted<>1
		              AND evaluation_questions.event_id = '{$EVENT['id']}'";
		$res = $db->query($sql);
		$eval_check = $res->numRows() > 0;
		if ($eval_check) {
			print "<h2>{$EVENT['formal_event_name']} Evaluation</h2>
			        <div class=\"error_summary\"><font class=\"error_summary\">
					 <h3 style='margin:0;'>You've already submitted an evaluation.</h3>
					 Each event attendee can only fill out the evaluation form once.  Thanks
					 for your help improving {$EVENT['casual_event_name']}.  We look forward to
					 seeing you again!</div>";
			return;  // die out of this page
		}

	# Validate Data
		// we don't allow required fields for evaluations
        
	# Post-Validate
		if (postValidate()) {

            // go through each $_REQUEST['response'] and INSERT into db
            foreach($_REQUEST['response'] as $question_id => $response) {
                if ($response != '') {  // don't insert blank responses
                    if (is_array($response)) $response = implode($response,"|");
					$sql = "INSERT INTO evaluation_responses
                            SET question_id = '$question_id',
                                member_id = '{$_SESSION['member_id']}',
                                response = '$response',
                                timestamp = '" . time() . "',
								event_id = '{$EVENT['id']}'";
                    $res = $db->query($sql);
                        if (DB::isError($res)) dbError("Problem recording eval responses.", $res, $sql);
                }
            }
		
			// print thank you
			print "<h2>Thank You!</h2>
			    <p>Thank you for submitting your evaluation!  Your responses will help us to improve our events in future years.</p>
			    <h4>Sincerely,
			    <br>Section {$EVENT['section_name']}</h4>";
			destroyReg(); // kill the reg so they can get back to the homepage in the future.
			return; // exit out
			
		}

?>

<h2><?=$EVENT['formal_event_name']?> Evaluation</h2>

	<p>In an effort to improve <?=$EVENT['casual_event_name']?> in future years, please fill out as much of the following
		survey as possible.  Your responses are not attached to your personal information; you were required to login only 
		as a way to ensure that participants can submit an evaluation once and as a way to provide section leadership with
		your contact information if and only if you offer to help on staff next year on the form below.</p>
	<p>When you're finished, click "Submit" to send in your evaluation.  Please note that you can only submit the evaluation
		form once, so be sure to complete the form completely before clicking Submit.</p>

	<? print $ERROR['error_summary']; ?>
	
	<center>
	<form name="form1" action="<? print $_GLOBALS['form_action']; ?>" method="<? print $_GLOBALS['form_method']; ?>">

	<fieldset><legend>Event Evaluation</legend>

		<?
		/* Go through evaluation_questions and print out the form elements for each. */
		$sql = "SELECT * FROM evaluation_questions WHERE event_id='{$EVENT['id']}' AND heading = 'eval' AND deleted<>1 ORDER BY `order` ASC";
		$res = $db->query($sql);
		    if (DB::isError($res)) dbError("Couldn't select eval questions.", $res, $sql);
		
		while ($q = $res->fetchRow()) {

            print "\n\n<label for='response[$q->question_id]'>" . stripslashes($q->question) . "</label>\n";
            $input_tag = "name='response[$q->question_id]' id='response[$q->question_id]'";

		    switch ($q->type) {
		        case "text":
		            print "<input type='text' $input_tag>";
		            break;
		            
		        case "select":
		            print "<select $input_tag>
		                    <option value=''>-- Select --</option>";
		            $option = explode("|", $q->options);
		            foreach ($option as $key => $value) {
		                $value = stripslashes($value);
		                print "\n\t<option value=\"$value\">$value</option>";
		            }
		            print "\n</select>";
		            break;
		            
		        case "radio":
    	            $option = explode("|", $q->options);
    	            print "<div class='checkboxes'>";
    	            foreach ($option as $key => $value) {
		                $value = stripslashes($value);
		                $span_width = strlen($value)+2 . "em";
		                print "\n<span style='width: $span_width; min-width: $span_width;'>
		                        <input type='radio' $input_tag value=\"$value\"> $value</span>";
	                }
	                print "</div>";
	                break;
	                
	            case "checkbox":
	                $option = explode("|", $q->options);
    	            print "<div class='checkboxes'>";
    	            foreach ($option as $key => $value) {
    	                $value = stripslashes($value);
    	                print "\n<span><input type='checkbox' $input_tag value=\"$value\"> $value</span>";
                    }
                    print "</div>";
                    break;
                    
                case "textarea":
                    print "<textarea $input_tag style='width: 350px;'></textarea>";
                    break;
		    }
		
		print "<br />";   
		}
		?>
		</fieldset>
		
    		<?
    		/* Go through evaluation_questions and print out the form elements for each. */
    		$sql = "SELECT * FROM evaluation_questions WHERE event_id='{$EVENT['id']}' AND heading = 'staff' AND deleted<>1 ORDER BY `order` ASC";
    		$res = $db->query($sql);
    		    if (DB::isError($res)) dbError("Couldn't select eval/staff questions.", $res, $sql);

            if ($res->numRows() > 0) {
                print "<fieldset><legend>Interested in helping out next year?</legend>";

    		while ($q = $res->fetchRow()) {

                    print "\n\n<label for='response[$q->question_id]'>" . stripslashes($q->question) . "</label>\n";
                    $input_tag = "name='response[$q->question_id]' id='response[$q->question_id]'";

        		    switch ($q->type) {
        		        case "text":
        		            print "<input type='text' $input_tag>";
        		            break;

        		        case "select":
        		            print "<select $input_tag>
        		                    <option value=''>-- Select --</option>";
        		            $option = explode("|", $q->options);
        		            foreach ($option as $key => $value) {
        		                $value = stripslashes($value);
        		                print "\n\t<option value=\"$value\">$value</option>";
        		            }
        		            print "\n</select>";
        		            break;

        		        case "radio":
            	            $option = explode("|", $q->options);
            	            print "<div class='checkboxes'>";
            	            foreach ($option as $key => $value) {
        		                $value = stripslashes($value);
        		                $span_width = strlen($value)+2 . "em";
        		                print "\n<span style='width: $span_width; min-width: $span_width;'>
        		                        <input type='radio' $input_tag value=\"$value\"> $value</span>";
        	                }
        	                print "</div>";
        	                break;

        	            case "checkbox":
        	                $option = explode("|", $q->options);
            	            print "<div class='checkboxes'>";
            	            foreach ($option as $key => $value) {
            	                $value = stripslashes($value);
            	                print "\n<span><input type='checkbox' $input_tag value=\"$value\"> $value</span>";
                            }
                            print "</div>";
                            break;

                        case "textarea":
                            print "<textarea $input_tag style='width: 350px;'></textarea>";
                            break;
        		    }

    		    print "<br />";  
        	}
            
            print "</fieldset>";
        }
		?>
		
		<!-- Hiddens -->
		<input type="hidden" name="section" value="<? print $SECTION ?>">
		<input type="hidden" name="page" value="eval">
		
		<input id="submit_button" name="submitted" type="submit" value="Submit Evaluation">

	</form>