<?

session_start();

class User { 
	var $db = null; // PEAR::DB pointer 
	var $failed = false; // failed login attempt 
	var $date; // current date GMT 
	var $id = 0; // the current user's id 
	var $user_ip; // the remote_addr of the user (a bit of a hack to deal with westhost's shared ssl)
	var $info; // info from the users, members, etc. tables

	function User(&$db) { 
		$this->db = $db; 
		$this->date = gmdate("'Y-m-d'");
		$this->user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

		if ($_SESSION['logged'] == true) {
			$this->_checkSession(); 
		} elseif ( isset($_COOKIE['mtwebLogin']) ) { 
			$this->_checkRemembered($_COOKIE['mtwebLogin']); 
		}
		
		if ($_SESSION['uid'] == 0 || !isset($_SESSION['uid']))
			$this->_logout();
	}

	function _checkLogin($username, $password, $remember) { 
		$username = $this->db->quote($username); 
		$password = $this->db->quote(md5($password)); 
		$sql = "SELECT * FROM users WHERE " . 
				"username = $username AND " . 
				"password = $password"; 
		$result = $this->db->getRow($sql); 

		if ( is_object($result) ) { 
			$this->_setSession($result, $remember); 
			return true; 
		} else { 
			$this->failed = true; 
			$this->_logout("Invalid username and/or password.", true); 
			return false; 
		} 
	}
	
	function _setSession(&$values, $remember, $init = true) { 
		$this->id = $values->id; 
		$_SESSION['uid'] = $this->id; 
		$_SESSION['username'] = htmlspecialchars($values->username); 
		$_SESSION['cookie'] = $values->cookie; 
		$_SESSION['logged'] = true; 

		if ($remember) { 
			$this->updateCookie($values->cookie, true); 
		} 
		
		if ($init) { 
			$session = $this->db->quote(session_id()); 
			$ip = $this->db->quote($this->user_ip);

			$sql = "UPDATE users SET session = $session, ip = $ip WHERE id = $this->id"; 
			$this->db->query($sql); 
		}
		
		$this->updateUserInfo();
		$sql = "UPDATE users SET last_activity = now() WHERE id = $this->id"; 
		$this->db->query($sql);
		
	}
	
	function updateCookie($cookie, $save) { 
		$_SESSION['cookie'] = $cookie; 
		if ($save) { 
			$cookie = serialize(array($_SESSION['username'], $cookie) ); 
			setcookie('mtwebLogin', $cookie, time() + 31104000, ''); 
		}
	}

	function _checkRemembered($cookie) { 
		list($username, $cookie) = @unserialize($cookie); 
		if (!$username or !$cookie) return; 
	
		$username = $this->db->quote($username); 
		$cookie = $this->db->quote($cookie); 
		$sql = "SELECT * FROM users WHERE " . 
			"(username = $username) AND (cookie = $cookie)"; 
		$result = $this->db->getRow($sql); 
	
		if (is_object($result) ) { 
			$this->_setSession($result, true); 
		}
	}
	
	function _checkSession() { 
		$username = $this->db->quote($_SESSION['username']); 
		$cookie = $this->db->quote($_SESSION['cookie']); 
		$session = $this->db->quote(session_id()); 
		$ip = $this->db->quote($this->user_ip); 
		$sql = "SELECT * FROM users WHERE " . 
				"(username = $username) AND " . //(cookie = $cookie) AND " . 
				"(session = $session) AND (ip = $ip)"; 
		$result = $this->db->getRow($sql); 
		if (is_object($result) ) { 
			$this->_setSession($result, false, false); 
			$this->section = $result->section;
		} else { 
			$this->_logout(); 
		} 
	}
	
	function _logout($msg = '', $redirect = true) {
		session_defaults();
		global $_GLOBALS;
		$url = $_SERVER['REQUEST_URI'];
		if ($redirect)
			header("Location:" . $_GLOBALS["form_action"] . "admin/login.php?msg=$msg&url=$url");
	}
	
	function checkPermissions($permission, $minimum = 1, $msg = "") {
		$user_id = $this->db->quote($_SESSION['uid']);
		$sql = "SELECT $permission AS permission, superuser FROM permissions WHERE user_id = $user_id";
		$res = $this->db->getRow($sql);
			if (DB::isError($res)) dbError("Could not verify permissions", $res, $sql);
		if ($res->permission < $minimum && $res->superuser == 0)
			die("<h2>You do not have permission to access this page! ($permission)</h2>
				 Required permissions level: $minimum<br>Your permissions level: $res->permission<br><br>$msg");
	}
	
	function peekPermissions($permission) {
		$user_id = $this->db->quote($_SESSION['uid']);
		$sql = "SELECT $permission AS permission, superuser FROM permissions WHERE user_id = $user_id";
		$res = $this->db->getRow($sql);
			if (DB::isError($res)) dbError("Could not verify permissions", $res, $sql);
		return $res->superuser == 5 ? true : $res->permission > 0;
	}
	
	function getPermissions($permission) {
		$user_id = $this->db->quote($_SESSION['uid']);
		$sql = "SELECT $permission AS permission, superuser FROM permissions WHERE user_id = $user_id";
		$res = $this->db->getRow($sql);
			if (DB::isError($res)) dbError("Could not verify permissions", $res, $sql);
		return $res->superuser == 5 ? 5 : $res->permission;
	}

	function updateUserInfo() {
		$user_id = $this->db->quote($_SESSION['uid']);
		$section = $this->db->getOne("SELECT section FROM users WHERE id = $user_id");
			if (DB::isError($section)) dbError("Could not select section", $section, $sql);
		$sql = "SELECT * FROM users
		 		LEFT JOIN $section.members ON users.member_id = $section.members.member_id
				WHERE id = $user_id";
		$this->info = $this->db->getRow($sql);
			if (DB::isError($this->info)) dbError("Could not update user info", $this->info, $sql);
	}
	
	function setCurrentEvent($event_id) {
		$sql = "UPDATE users SET current_event = '$event_id' WHERE id = $this->id"; 
		$this->db->query($sql); 
		
		// if superuser, also reset the user's section
		if ($this->peekPermissions("superuser") > 0) {
			$new_section = $this->db->getOne("SELECT LOWER(REPLACE(section_name, '-', '')) FROM events WHERE id='$event_id'");
			$this->db->query("UPDATE users SET section = '$new_section' WHERE id = $this->id");
		}
	}
}


function session_defaults() { 
	$_SESSION['logged'] = false; 
	$_SESSION['uid'] = 0; 
	$_SESSION['username'] = ''; 
	$_SESSION['cookie'] = 0; 
	$_SESSION['remember'] = false; 
}

?>