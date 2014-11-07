<!--SM:htmlonly-->

<!-- base href -->
<base href="https://ssl4.westserver.net/sectionmaster.org/register/admin/">

<!-- css -->
<link rel="stylesheet" type="text/css" href="admin.css">
<style>
	body { padding: 10px; }
	p.footer { color: #444; }
	h2, h3, h4 { margin: 2px; }
	div.email-header { background: #FFC; border: 1px solid #111; width: default; padding: 10px; margin-bottom: 10px; }
	.email-header h2 { color: #006699; }
	.email-header h3 { margin: 7px 0 2px 0; }
	.email-header p { margin: 2px 0 0 0; }
</style>
<!--/SM:htmlonly-->


<!-- body -->
<div class="email-header">
<h2><?= $EVENT['formal_event_name'] ?> Registration Statistics: <?= $formal_title ?> Report</h2>
<h4>As of <?= date("D M j G:i:s T Y") ?></h4>

<h3>TOTAL REGISTERED: <?= $total_count ?></h3>
<p><em><?= "Reminder: " . time_until($EVENT['casual_event_name'], 
										date('j', strtotime($EVENT['start_date'])),
										date('m', strtotime($EVENT['start_date'])),
										date('Y', strtotime($EVENT['start_date']))); ?></em></p>
</div>

<!--SM:plaintext-->
<?= $EVENT['formal_event_name'] ?> Registration Statistics: <?= $formal_title ?> Report
As of <?= date("D M j G:i:s T Y") ?>

TOTAL REGISTERED: <?= $total_count ?>

<?= "Reminder: " . time_until($EVENT['casual_event_name'], 
										date('j', strtotime($EVENT['start_date'])),
										date('m', strtotime($EVENT['start_date'])),
										date('Y', strtotime($EVENT['start_date']))); ?>

<!--/SM:plaintext-->