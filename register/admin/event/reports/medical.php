<?
include_once ("../../includes/member_functions.php");

# Get member list
$sql = "SELECT *
		FROM ( $all_registered ) AS temp1
		WHERE food_allergy<>0 OR special_care<>0 OR special_diet_explain<>'' OR sleeping_device<>0 OR conditions_explanation<>''
		ORDER BY lastname, firstname";
//print "<pre>$sql</pre>";
$members = $db->query($sql);
	if (DB::isError($members)) dbError("Could not retrieve member list in $title", $members, $sql);


?>

<h3>Medical Issues Report</h3>
<p>This report lists <strong>only registrants who have indicated one or more medical or dietary issue</strong>, including age, personal contact information, and emergency contact information.</p>

<!-- TABLE -->
<table class="cart" style="width:90%;">
	
	<thead>
	<tr>
		<th>Name</th>
		<th>Birthdate (Age as of <?= date("m/d/Y") ?>)</th>
		<th>Contact Information</th>
		<th>Emergency Contact (relationship)</th>
	</tr>
	</thead>
	
	<tbody>
	
	<?
	// print out members for this chapter
	while ($member = $members->fetchRow()) {
		unset($ctext); unset($first);
		print "\n\n\t\t<tr id=\"link\" onClick=\"window.location='section/members.php?member_id=$member->member_id';\" style='border-bottom:0; padding: 8px;'>
				<td style='border-bottom:0;'><a href=\"section/members.php?member_id=$member->member_id\"
					alt=\"View member details for $member->firstname $member->lastname\">
					<strong style='font-size:13px'>$member->lastname, $member->firstname</strong></a>
					<br>$member->lodge_name Lodge
					<br>$member->chapter_name Chapter
					</td>
				<td style='border-bottom:0;'>" . date("m/d/Y", strtotime($member->birthdate)) . " 
					(" . getAge($member) . ")</td>
				<td style='border-bottom:0;'>$member->email
										 <br>$member->primary_phone</td>
				<td style='border-bottom:0;'>$member->emer_contact_name ($member->emer_relationship)
										 <br>$member->emer_phone_1
										 <br>$member->emer_phone_2</td>
			</tr>";
		print "<tr>
			<td><img width=150 height=0></td>
			<td colspan=3 style='color:#666;'>";

			$conds = array(	"food_allergy" => "Food Allergy",
							"sleeping_device" => "Sleeping Device",
							"special_care" => "Special Care Required",);
							
			foreach($conds as $field => $title) {
				if ($member->$field == '1') {
					if (!$first) $first=true; else $ctext .= ", ";
					$ctext .= "$title";
				}
			}
			if ($ctext) print "Conditions: $ctext";
			
			if ($member->conditions_explanation != "")
				print "<br>Condition Details: $member->conditions_explanation";
			
			if ($member->special_diet_explain != '')
				print "<br>Dietary Restrictions: $member->special_diet_explain";
										
		print "</td></tr>";
		$count++;
	}
	?>
	
	</tbody>
	
	<tfoot>
	<tr id="total">
		<td colspan=3>TOTAL PARTICIPANTS:</td>
		<td id='count'><?= $count ?></td>
	</tr>
	</tfoot>
	
</table>

<? unset($count); ?>

<?php

function monthsYearsAgo($original){
	$yearsPrecise = (time() - $original) / (60*60*24*365);
	$years = floor($yearsPrecise);
	$yearsPrint = $years > 1 ? "years" : "year";
	$fractionOfYear = $yearsPrecise - $years;
	$months = floor($fractionOfYear * 12);
	$monthsPrint = $months > 1 ? "months" : "month";
	return "$years $yearsPrint, $months $monthsPrint ago";
}

?>