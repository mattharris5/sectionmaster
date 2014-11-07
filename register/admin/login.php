<?
// deny Internet Explorer users
if (strpos($_SERVER["HTTP_USER_AGENT"],'MSIE') !== false)
{
	print '
	<html>
		<head>
			<title>SectionMaster Online :: Upgrade Your Browser</title>
			<link rel="stylesheet" type="text/css" href="admin.css">
		</head>
		<body>
			<center>
				<div id="login_box">
					<h1>SM-Online Admin Login</h1>
					<p>Sorry, but we detected you are using Internet Explorer as your web browser.
					Several features of SM-Online Admin make use of functions that are only fully-supported by
					standards-based web browsers.  Unfortunately, Internet Explorer contains proprietary code
					that does not render some web pages correctly.</p>
					<p>Please use a different web browser.  <a href="http://www.getfirefox.com" target="_blank">Mozilla
					Firefox</a> and <a href="http://www.apple.com/safari" target="_blank">Apple Safari</a> are known
					to work without a hitch on this site and versions are available for both Windows and Mac.
					In addition, these browsers are by far more secure and feature-rich than Internet
					Explorer, and not subject to the countless exploits that Microsoft software is prone to.</p>
					<p><a href="http://www.getfirefox.com" target="_blank"><img src="../images/default/getfirefox.png"
					border="0" alt="Click here to get Firefox"></a></p>
					<p>We sincerely apologize for any inconvenience this may cause you.  If you do not have another
					web browser program on your computer, please click one of the above links to download the software
					for free.</p>
				</div>
			</center>
		</body>
	</html>

	';
	die();
}

ini_set("include_path", ".:/usr/local/php/lib/php:/var/www/html/register/");
require_once "includes/db_connect.php";
require "includes/globals.php";
require "includes/User.php";


/* Ensure secure connection */
if ($_SERVER['HTTP_X_FORWARDED_HOST'] != $_GLOBALS['secure_host']) {
	$newurl = "https://" . $_GLOBALS['secure_base_href'] . $_SERVER['REQUEST_URI'];
	header( "Location: $newurl" );
}

/* Instantiate! */
$sm_db = db_connect("sm-online");

/* Try to login */
if ($_REQUEST['submitted']) {
	$user = new User($sm_db);
	$username = $_REQUEST['section']=='' ? $_REQUEST['username'] : $_REQUEST['username']."@".$_REQUEST['section'];
	if ($user->_checkLogin($username, $_REQUEST['password'], $_REQUEST['remember'])) {
		if ($_REQUEST['url']!='' && !$_REQUEST['msg'] && $_REQUEST['url']!='/')
			$newurl = "https://" . $_GLOBALS['secure_base_href'] . $_REQUEST['url'];
		else 
			$newurl = "index.php";
		header("Location:$newurl"); //https://" . $_GLOBALS['secure_base_href'] . $newurl);
	} else {
		$ERROR['login'] = "Invalid login credentials.  Please try again.";
	}
}

if ($_REQUEST['msg'] != '')
	$ERROR['login'] = $_REQUEST['msg'];
?>

<html>
<head>
	<title>SectionMaster Online :: Login</title>
	<link rel="stylesheet" type="text/css" href="admin.css">
</head>

<body onLoad="document.getElementById('username').select();">
	
	<center>
	<div id="login_box">
	
	<h1>SM-Online Admin Login</h1>

	<? if ($ERROR['login'] != '')
		print "<font color=red><center>{$ERROR['login']}</center></font><br>";
	?>
	
	<form method=post>
		<label for="username">Username</label>
		<input name="username" id="username" value="<?=$_REQUEST['username']?>" style="width: auto;" tabindex=1><br />
		
		<label for="password">Password</label>
		<input type="password" name="password" id="password" style="width: auto;" tabindex=2><br />

		<label for="section">Section</label>
		<select name="section" id="section" tabindex=3>
			<?
			$sql = "SELECT DISTINCT section AS formal_name, LOWER(REPLACE(section, '-', '')) AS id
					FROM lodges ORDER BY section";
			$sections = $sm_db->query($sql);
			while ($section = $sections->fetchRow()) {
				print "\n\t\t\t<option value=\"$section->id\"";
				if ($_REQUEST['section'] == $section->id) print " selected";
				print ">$section->formal_name</option>";
			}
			?>
        	<option value="wr">Western Region</option>
		</select><br />

		<input type="hidden" name="remember" value="false">
		
		<input type="submit" name="submitted" value="Login" id="submit_button" style="margin-left: 150px;">
	</form>
	
	<br /><br />
	</div>
	
</body>
</html>