<?
####################################
#
# SM-Online Login Handler
#
####################################

# Step 0: Confirm E-mail Address
# ------------------------------

	if ($_REQUEST['email'] && $COMMAND == 'check_address') {

		$email = $_REQUEST['email'];
	
		// IS EMAIL IN THE DB?
		$sql = "SELECT member_id, email, firstname, lastname FROM members WHERE email = '$email'";
		$result = $db->query($sql);
			if (DB::isError($result)) { dbError("check_address", $result, $sql); }
		
		if ($result->numRows() == 0) {
			// new member
			gotoPage("idcreate", "reason=new_member&event_id={$_REQUEST['event_id']}&email={$_REQUEST['email']}");
		} else {
			// proceed to step 2!
			$COMMAND = 'send_login_email';
		}
		
	} else if (($_REQUEST['email'] || $_SESSION['email']) && $COMMAND == 'new_member') {
	
		// new member
		gotoPage("idcreate", "reason=new_member&event_id={$_REQUEST['event_id']}&email={$_REQUEST['email']}");
	}
	

# Step 1: Send login email
# ------------------------

	if ($COMMAND == 'send_login_email') {

		// include email content
		include "email/login_link.php";
		
		// send the actual e-mail
		mail( $email, $Subject, $Body, $MailHeaders );
		
		// include message send page
		$PAGE = "login-link-sent";
	
	}

	
# Step 2: Validate login from email or new idcreate page
# ------------------------------------------------------

	if ($_REQUEST['id'] && $_REQUEST['key']) {
		
		// clear out the session
//		unset($_SESSION);  // this was added so that people who ended a registration and then came back in from a different login link don't go to the page from the original session.
		
		// get info from sessions table
		$sql = "SELECT * FROM sessions
				WHERE member_id='{$_REQUEST['id']}' AND passkey='{$_REQUEST['key']}'";
				
		$result = $sm_db->query($sql);
			if (DB::isError($result)) { dbError("validate login from id/key pair", $result, $sql); }
		
		if ($result->numRows() == 0) {
			
			// if no record
			gotoPage("default", "login_error=no_session_record&event_id={$_SESSION['event_id']}");
		
		} else {
			
			// record exists
			$sess = $result->fetchRow();
			
		}

    # Step 3: Setup session and assign session vars
    # ---------------------------------------------

		// setup $EVENT variable
		$EVENT = event_info($sm_db, $sess->event_id, "login_handler, step3");
		
		// setup session data
		$_SESSION['session_id'] = $sess->session_id;
		$_SESSION['event_id'] = $sess->event_id;
		$_SESSION['member_id'] = $sess->member_id;
		$_SESSION['order_id'] = $sess->order_id;
		$_SESSION['current_reg_step'] = $sess->current_reg_step;
		$_SESSION['section'] = section_id_convert($EVENT['section_name']);
//		$_SESSION['page'] = $sess->current_reg_step;  // needed to reset the page if user hasn't started a new session since last login (i.e., family member just completed registration and now clicks on son's login link)
		
		// connect to section db
		$db =& db_connect($_SESSION['section']);		// Section's unique db

		// handle an initial goto request
		if ($_GET['gotoInit']) {
		    $sm_db->query("UPDATE sessions SET current_reg_step='{$_GET['gotoInit']}' WHERE session_id='$sess->session_id'");
		    $_SESSION['gotoInit'] = $_GET['gotoInit'];  // add gotoInit to session
		}		
	}	

# Step 3.5: Check that session hasn't expired
# -------------------------------------------

	if ($_SESSION['member_id'] && $_REQUEST['login_error'] != "session_expired") {
		
		// get info from sessions table
		$sql = "SELECT * FROM sessions
				WHERE session_id='{$_SESSION['session_id']}'";
				
		$result = $sm_db->getRow($sql);
			if (DB::isError($result)) { dbError("Checking for expired session", $result, $sql); }
		
		if ($result->timestamp < (time() - $_GLOBALS['session_length']) && !($_REQUEST['id'] && $_REQUEST['key'])) {
			
			// if no record
			gotoPage("default", "login_error=session_expired&event_id={$EVENT['id']}");

		}
	}


# Step 3.6: Check that registration isn't closed
# ----------------------------------------------

	if ((time() < strtotime($EVENT['online_reg_open_date']) || time() > strtotime($EVENT['online_reg_close_date'])) 
		 && $_SESSION['backdoor'] != 'open' 
		 && $_REQUEST['login_error'] != "registration_closed") {
	
	    // ok to proceed - they requested an initial goto
        if ($_REQUEST['gotoInit'] != '') {
            gotoPage($_REQUEST['gotoInit'], "id={$_REQUEST['id']}&key={$_REQUEST['key']}&goto={$_REQUEST['gotoInit']}");
        }
		
		// reg is closed!
		if ($_SESSION['page'] != 'eval' && $_SESSION['page'] != 'final') // unless we're going to an eval page
    	    gotoPage("default", "event_id={$EVENT['id']}&login_error=registration_closed");

	}
	
# Step 4: Check if already registered or get current step
# ------------------------------------------------------

	/* IF NORMAL EVENT */
	if ($_SESSION['member_id'] && $EVENT['type'] == 'event') {
	
		// issue query
		$sql = "SELECT conclave_year, registration_complete, current_reg_step, conclave_order_id
				FROM conclave_attendance 
				WHERE member_id = '{$_SESSION['member_id']}'
					AND conclave_year = '{$EVENT['year']}'";
		$res = $db->query($sql);
			if (DB::isError($res)) { dbError("Could not check if already registered", $res, $sql); }
		$attendance = $res->fetchRow();

		$res2 = $sm_db->getRow("SELECT current_reg_step FROM sessions WHERE session_id = '{$_SESSION['session_id']}'");
			if (DB::isError($res2)) { dbError("Could not check get current step", $res2); }
		$current_reg_step = $res2->current_reg_step != '' ? $res2->current_reg_step : $attendance->current_reg_step;

		// store order_id in session
		$_SESSION['order_id'] = $attendance->conclave_order_id;
		$_SESSION['current_reg_step'] = $attendance->current_reg_step;

		if ($attendance->registration_complete == 1 && $_SESSION['page'] != 'eval') {

			// already reg'd - goto receipt/final page
			$_SESSION['page'] = "final";

		} else if ($attendance->registration_complete != 1 && $current_reg_step != '' && $_SESSION['reg'] != 'in_progress') {

			// make sure we let them go back steps
			$_SESSION['reg'] = 'in_progress';
			
			// goto the right step
			$_SESSION['page'] = $current_reg_step; //$_SESSION['gotoInit']!='' ? $_SESSION['gotoInit'] : $current_reg_step;

		} else if ($res->numRows() == 0) {

			$current_reg_step = $_SESSION['page']!='' ? $_SESSION['page'] : 'idcreate';

			// create a new order record
			$sql = "INSERT INTO orders
					SET order_timestamp = now(),
					order_year = '{$EVENT['year']}',
					event_id = '{$EVENT['id']}',
					member_id = '{$_SESSION['member_id']}',
					order_method = 'Online',
					complete = '0'";
			$res = $db->query($sql);
				if (DB::isError($res)) { dbError("Could not create new order record", $res, $sql); }	
			$order_id = mysql_insert_id();

			// add a record in "conclave_attendance" for this year
			$sql = "INSERT INTO conclave_attendance
					SET member_id = '{$_SESSION['member_id']}',
					conclave_year = '{$EVENT['year']}',
					reg_date = now(),
					current_reg_step = '$current_reg_step',
					conclave_order_id = '$order_id',
					registration_complete = '0'";
			$res = $db->query($sql);
				if (DB::isError($res)) { dbError("Could not insert conclave_attendance record", $res, $sql); }

			// update sessions
			$res = $sm_db->query("UPDATE sessions 
								SET order_id='$order_id',
								current_reg_step = '$current_reg_step'
								WHERE session_id='{$_SESSION['session_id']}'");
				if (DB::isError($res)) { dbError("Could not update session with order id", $res); }

			// get info about conclave_reg product item
			$conclave = $db->getRow("SELECT product_id, price FROM products WHERE product_code='CONCLAVE_REG'");
				if (DB::isError($conclave)) { dbError("Could not get conclave product info", $res, $sql); }

			// add conclave reg ordered item
			$sql = "INSERT INTO ordered_items
					SET order_id = $order_id,
					product_id = '$conclave->product_id',
					quantity = '1',
					price = '$conclave->price'";
			$res = $db->query($sql);
				if (DB::isError($res)) { dbError("Could not add conclave reg item to ordered items", $res, $sql); }

		}

	}

	/* IF TRADING POST EVENT */
	else if ($_SESSION['member_id'] && $EVENT['type'] == 'tradingpost') {
	
		$sql = "SELECT * FROM sessions WHERE session_id = '{$_SESSION['session_id']}'";
		$attendance = $sm_db->getRow($sql);
			if (DB::isError($attendance)) { dbError("Could not get current step", $attendance, $sql); }
		
		if ($attendance->current_reg_step != '' && $attendance->current_reg_step) {

    		// store order_id in session
    		$_SESSION['order_id'] = $attendance->order_id;
		
			// goto the right step
			$_SESSION['reg'] = 'in_progress';
			$_SESSION['page'] = $attendance->current_reg_step;  // for some reason, this was commented out and it broke the tp
			$_SESSION['current_reg_step'] = $attendance->current_reg_step;

		} else {

			// create a new order record
			$sql = "INSERT INTO orders
					SET order_timestamp = now(),
					order_year = '{$EVENT['year']}',
					event_id = '{$EVENT['id']}',
					member_id = '{$_SESSION['member_id']}',
					order_method = 'Online',
					complete = '0'";
			$res = $db->query($sql);
				if (DB::isError($res)) { dbError("Could not create new order record", $res, $sql); }	
			$order_id = mysql_insert_id();

			// save order_id to session record
			$res = $sm_db->query("UPDATE sessions
								SET order_id = '$order_id',
								current_reg_step = 'tradingpost'
								WHERE session_id = '{$_SESSION['session_id']}'");
				if (DB::isError($res)) { dbError("Could not save order id to session record", $res); }
				
			// store order_id in session
    		$_SESSION['order_id'] = $order_id;

			// goto the right step
			$_SESSION['reg'] = 'in_progress';
			$_SESSION['page'] = 'tradingpost';
			$_SESSION['current_reg_step'] = 'tradingpost';

		}
	}


# Step 5: Populate $member variable - all member data!
# ----------------------------------------------------
	
	if ($_SESSION['member_id']) {
		
		$sql = "SELECT * FROM members
				LEFT JOIN secret_questions ON members.member_id = secret_questions.member_id
				LEFT JOIN conclave_attendance ON members.member_id = conclave_attendance.member_id
				WHERE members.member_id = '{$_SESSION['member_id']}'";
		if ($EVENT['type'] == 'event') $sql .= " AND conclave_year = '{$EVENT['year']}'";
		$member = $db->getRow($sql);
			if (DB::isError($member)) { dbError("could not retrieve member info in login_handler", $member, $sql); }
	}


# Step 6: Make sure ID verification info exists
# ---------------------------------------------

	if (($_SESSION && $member) && $EVENT['type'] != 'tradingpost' && $_SESSION['type'] != 'paper') {
	
		// select idverify info
		$sql = "SELECT * FROM secret_questions
				WHERE member_id = '". get('member_id') . "'";
		$res = $db->query($sql);
			if (DB::isError($res)) { dbError("make sure id verification data exists", $res, $sql); }
		
		// if info missing, go to idcreate page
		if ($res->numRows() == 0) {
			$_SESSION['page'] = "idcreate";
			$reason = "noidinfo";
		} else {
			if($_SESSION['current_reg_step'] == '') {
				gotoNextPage('idcreate');
			}
		}
	}

	
# Step 7: Proceed to the right page
# ---------------------------------

	if ($_SESSION && $member)
		$PAGE = $_SESSION['page'];