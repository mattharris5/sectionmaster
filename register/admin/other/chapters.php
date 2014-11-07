<?
$title = "Section > Chapters";
require "../pre.php";
$user->checkPermissions("section_info");

// delete product if requested
if ($_REQUEST['command'] == 'delete' && $_REQUEST['chapter_id'] != '') {
	$user->checkPermissions("section_info", 5, "You cannot delete chapters without full section_info permissions.");
	$del = $db->query("DELETE FROM chapters WHERE chapter_id = '{$_REQUEST['chapter_id']}' LIMIT 1");
		if (DB::isError($del)) dbError("Could not delete chapter in $title", $del, "");
}

$EVENT 	 = event_info($sm_db, $user->info->current_event);

// select product
$sql = "
SELECT
  chapter_id,
  chapter_name,
  lodge,
  active_membership,
  chapter_chief,
  lodge_name,
  council
FROM chapters
  LEFT JOIN `sm-online`.lodges
    ON chapters.lodge = `sm-online`.lodges.lodge_id
WHERE deleted <> 1 AND section='{$EVENT['section_name']}'
ORDER BY lodge_name, chapter_name";
$chapters = $db->query($sql);
	if (DB::isError($chapters)) dbError("Could not get chapter listing in $title", $chapters, $sql);

?>

<h1>Chapters</h1>
<input class="default" type=button onClick="window.location.href='/sectionmaster.org/register/admin/other/edit_chapter.php?command=add';" value="Add a New Chapter">

<table class="cart">
	<tr>
		<th></th>
		<th>Chapter Name</th>
		<th>Active Membership</th>
		<th>Chapter Chief Name</th>
		<th>Edit</th>
	</tr>
	
<?
$prevlodge = 0;
while ($item = $chapters->fetchRow()) { $i++;

	print ($item->lodge<>$prevlodge)?"<tr><td colspan='5'><b><h3>$item->lodge_name Lodge, $item->council Council #$item->lodge</h3></b></td></tr>":"";
	
	print $i%2==0 ? "<tr id=\"odd\">" : "<tr id=\"even\">";
	
	print "<td width='5'>&nbsp;</td>";
	
	// chapter name
	print "<td>$item->chapter_name</td>";

	// active membership
	print "<td>$item->active_membership</td>";
	
	// chapter chief
	print "<td>$item->chapter_chief</td>";

	// edit
	print "<td>
			<input class=\"default\" type=button onClick=\"window.location='/sectionmaster.org/register/admin/other/edit_chapter.php?chapter_id=$item->chapter_id';\" value=\"Edit\">";
			
	if ($user->getPermissions("section_info") >= 5) {
		print "	<input class=\"default\" type=button
		  		 onClick=\"if (confirm('WARNING: Are you sure you want to delete the $item->chapter_name chapter?  Doing so may cause irreversible data loss to any member record which was already assigned to this particular chapter.  Only delete a chapter if it makes sense to do so.'))
								window.location='other/chapters.php?command=delete&chapter_id=$item->chapter_id';\" value=\"Delete\">";
	}
	
	print "</td>";
	print "</tr>";
	
	$prevlodge = $item->lodge;

}

require "../post.php";

?>