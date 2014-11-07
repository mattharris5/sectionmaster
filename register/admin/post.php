<?
if ($_REQUEST['debug'] == "true") {
	print "<pre>";
	print_r($_REQUEST); print_r($_SESSION); print_r($user);
	print "</pre>";
}
?>


<? include "footer.php"; ?>


<!-- /container -->
</div>

<?
ob_end_flush();
?>