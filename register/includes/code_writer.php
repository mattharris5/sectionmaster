<pre>

<?

$elements = array (
	"firstname"		=>	"First Name",
	"lastname"		=>	"Last Name",
	"address" => "Mailing Address",
	"city" => "City",
	'zip' => 'Zip Code',
	'primary_phone' => 'Phone',
	'oahonor' => 'oahonor',
	'chapter' => 'chapter',
	'lodge' => 'lodge',
	'ordeal_date' => 'ordeal_date',
	'position' => 'Current OA Position',
	'birthdate' => 'birthdate'
);

foreach ($elements as $short => $full) {
	print '
		&lt;!-- '.$full.' --&gt;
		&lt;label for="'.$short.'"&gt;'.$full.':&lt;/label&gt;
			&lt;input name="'.$short.'" id="'.$short.'" value="&lt;? req(\''.$short.'\') ?&gt;"&gt;
			&lt;? print $ERROR[\''.$short.'\']; ?&gt;&lt;br /&gt;
';			
}
?>



<?

foreach ($elements as $short => $full) {
	print '
	validate("'.$short.'","","Please enter your '.$full.'.");';			
}
?>