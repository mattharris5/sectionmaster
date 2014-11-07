<?

	############################################
	# FUNCTION: Generate a random password
	############################################
	function makeRandomPassword() {
	  $salt = "abchefghjkmnpqrstuvwxyz0123456789";
	  srand((double)microtime()*1000000); 
		$i = 0;
		while ($i <= 7) {
				$num = rand() % 33;
				$tmp = substr($salt, $num, 1);
				$pass = $pass . $tmp;
				$i++;
		}
		return $pass;
	}
	
	
	############################################
	# FUNCTION: Change Password and Send E-Mail
	############################################
	function changepass($ID_number) {
	
		global $conn, $submitted_email, $email, $password_words, $MailTo;
		
		# Reset the Password
		
		// generate a password out of two four-letter words from the words.dat file
		$new_password = makeRandomPassword();
		
		// NOW, RESET THE PASSWORD
		$reset = "UPDATE members SET password=PASSWORD('$new_password'), active='Y' WHERE ID_number=$ID_number";
		$result = mysql_query($reset, $conn) or die("reset password: " . mysql_error());
		
		// return the new password
		return $new_password;
	} 
	
?>