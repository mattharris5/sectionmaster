<?
$title = "Change My Password";
require "../pre.php";

// reset password
if ($_REQUEST['new_password1'] != '' 
	&& $_REQUEST['new_password1'] == $_REQUEST['new_password2']
	&& strlen($_REQUEST['new_password1']) >= 8) {
		$sql = "UPDATE users 
				SET password = '" . md5($_REQUEST['new_password1']) . "' 
				WHERE id = '{$user->id}' LIMIT 1";
		$res = $user->db->query($sql);
			if (DB::isError($res)) dbError("Could not update password in $title", $res, $sql);
	$ERROR['password'] = "<p align=center><b><font color=green>Your password was successfully reset.</font></b></p>";
} elseif ($_REQUEST['new_password1'] != '') {
	$ERROR['password'] = "<div class=\"error_summary\"><h4>Problem with Password</h4>
							Sorry, but your new password could not be saved.  This could be due to the following reasons:
							<ul><li>Passwords must be at least 8 characters long
								<li>The two passwords entered do not match</ul></div>";
}

?>

<h1>Change My Password</h1>

<form action="other/changepass.php" method=post>

	<fieldset>
		<legend>Change Password</legend>

		<p>To reset your password, enter a new password in the boxes below.  For security reasons, passwords must be at least 8
		characters long.  When finished, click "Reset Password".</p>

		<?= $ERROR['password'] ?>

		<label for="new_password1">New Password:</label>
		<input name="new_password1" id="new_password1" type=password><br />

		<label for="new_password2">Retype New Password:</label>
		<input name="new_password2" id="new_password2" type=password><br />

	</fieldset>

	<input type="submit" id="submit_button" name="submitted" value="Reset Password"><br>

</form>

<?
require "../post.php";
?>

