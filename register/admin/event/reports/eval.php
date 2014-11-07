<?
function dateDiff($dformat, $endDate, $beginDate)
{
	$date_parts1=explode($dformat, $beginDate);
	$date_parts2=explode($dformat, $endDate);
	$start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
	$end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
	return $end_date - $start_date;
}

# Get evaluation questions
$sql = "SELECT * FROM evaluation_questions
LEFT JOIN evaluation_responses USING (question_id)
LEFT JOIN members USING (member_id)
LEFT JOIN `sm-online`.lodges ON `sm-online`.lodges.lodge_id = members.lodge
WHERE evaluation_questions.event_id={$EVENT['id']} and deleted<>1
ORDER BY heading, `order`, lastname, firstname";

$sql = "SELECT * FROM evaluation_questions
WHERE event_id={$EVENT['id']} AND deleted<>1
ORDER BY heading, `order`";


$questions = $db->query($sql);
if (DB::isError($res)) dbError("Could not retrieve evaluation questions in $title", $res, $sql);

$responsecount = $db->query("SELECT DISTINCT member_id FROM evaluation_responses LEFT JOIN evaluation_questions USING (question_id) WHERE evaluation_questions.event_id={$EVENT['id']} and deleted<>1");
$count = $responsecount->numRows();
print "<h3>$count Total Responses</h3>";

print "	<table class='cart'>
		<tr>
			<th colspan='2'>Responses</th>
		</tr>";

while ($question = $questions->fetchRow() )
{
	print "
		<tr class='separator'>
			<td colspan='2'><br><h3>$question->order) ".stripslashes($question->question)."</h3></td>
		</tr>
		<tr>
			<td";
	if ($question->type == 'textarea') print " colspan='2'>";
	else print ">";
	
	if ($question->heading == 'staff') $orderby = 'lastname, firstname';
	else $orderby = 'evaluation_responses.id';
	$responsessql = "SELECT * FROM evaluation_questions
						LEFT JOIN evaluation_responses USING (question_id)
						LEFT JOIN members USING (member_id)
						LEFT JOIN `sm-online`.lodges ON `sm-online`.lodges.lodge_id = members.lodge
						WHERE evaluation_questions.event_id={$EVENT['id']}
						AND evaluation_questions.question_id = $question->question_id AND deleted<>1
						ORDER BY $orderby";
	$responses = $db->query($responsessql);
	//Reset variables
	$count = 0;
	$sum = 0;
	unset($responsearraycount);
	$maxresponses = $responses->numRows();

	while ($response = $responses->fetchRow())
	{
		$count++;

		$age = dateDiff("/", date('m/d/Y'), date('m/d/Y',strtotime($response->birthdate)))/365;
		if ($age >= 21) $ya = "Adult";
		else $ya = "Youth";

		//Responses
		// Text
		if (($response->type == 'textarea') or ($response->type == 'text'))
		{
			print "<li>$response->response <i>(";
			if ($response->heading == 'staff') print "<a href='section/members.php?member_id=$response->member_id'>$response->firstname $response->lastname</a> - ";
			print $response->lodge_name." - $ya - ".$response->oahonor.")</i></li>";
			if ($count == $maxresponses) print "<b>Responses: $count</b>";
		}
		// Radio
		if ($response->type == 'radio')
		{
			if ($response->heading == 'staff')
			{
				print "<li>$response->response (<a href='section/members.php?member_id=$response->member_id'>$response->firstname $response->lastname</a> - ";
				print $response->lodge_name." - $ya - ".$response->oahonor;
				print ")</li>";
			}
			if ($count == $maxresponses) print "</td><td width='300px'>";
			$responsearraycount[$response->response]++;
			if (is_numeric($response->response))
			{
				$sum += $response->response;
				$average = round($sum / $count,2);
				if ($count == $maxresponses)
				{
					print "<b>Average: $average<br>Responses: $count</b>";
				}
			}
			elseif ($count == $maxresponses) print "<b>Responses: $count</b>";
			if ($count == $maxresponses)
			{
				$options = explode("|",$response->options);
				foreach ($options as $item)
				{
					if ($responsearraycount[$item] == '') $responsearraycount[$item] = 0;
					print "<li>$item: ".$responsearraycount[$item]."</li>";
				}
			}
		}
		// Select
		if ($response->type == 'select')
		{
			if ($response->heading == 'staff')
			{
				print "<li>$response->response (<a href='section/members.php?member_id=$response->member_id'>$response->firstname $response->lastname</a> - ";
				print $response->lodge_name." - $ya - ".$response->oahonor;
				print ")</li>";
			}
			if ($count == $maxresponses) print "</td><td>";
			$responsearraycount[$response->response]++;
			if (is_numeric($response->response))
			{
				$sum += $response->response;
				$average = round($sum / $count,2);
				if ($count == $maxresponses)
				{
					print "<b>Average: $average<br>Responses: $count</b>";
				}
			}
			elseif ($count == $maxresponses) print "<b>Responses: $count</b>";
			if ($count == $maxresponses)
			{
				$options = explode("|",$response->options);
				foreach ($options as $item)
				{
					if ($responsearraycount[$item] == '') $responsearraycount[$item] = 0;
					print "<li>$item: ".$responsearraycount[$item]."</li>";
				}
			}
		}
		// Checkbox
		if ($response->type == 'checkbox')
		{
			print "<li>$response->response";
			if ($response->heading == 'staff')
			{
				print "<li>$response->response (<a href='section/members.php?member_id=$response->member_id'>$response->firstname $response->lastname</a> - ";
				print $response->lodge_name." - $ya - ".$response->oahonor;
				print ")</li>";
			}
			print "</li>";
			if ($count == $maxresponses) print "<b>Responses: $count</b>";
		}
	}
	print "</td></tr>";
}
?>
	
</table>