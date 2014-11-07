<?

session_start();
$heading_css = 'headings.css';

require "getheadings.php";

ob_start("getheadings");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>PHP + CSS Dynamic Text Replacement (P+C DTR)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="print.css" rel="stylesheet" type="text/css" media="print" />
<link href="headings.css" rel="stylesheet" type="text/css" media="screen, projection" />
</head>
<body>
	<h1>Isn't this cool?</h1>
</body>
</html>
<?

ob_end_flush();

?>