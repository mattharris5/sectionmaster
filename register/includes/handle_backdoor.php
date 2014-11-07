<?

//open
if ($_REQUEST['backdoor'] == 'open') {
	$_SESSION['backdoor'] = 'open';
}

//close
if ($_REQUEST['backdoor'] == 'closed') {
	$_SESSION['backdoor'] = 'closed';
	unset($_SESSION['backdoor']);
}

?>