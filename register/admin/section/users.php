<?
$title = "Section > Authorized Users";
require "../pre.php";
$user->checkPermissions("users");

// delete user if requested
if ($_REQUEST['command'] == 'delete' && $_REQUEST['uid'] != '') {
	$user->checkPermissions("users", 5, "You cannot delete users without full permissions on the users table.");
	$del = $user->db->query("DELETE FROM users WHERE id = '{$_REQUEST['uid']}' LIMIT 1");
		if (DB::isError($del)) dbError("Could not delete user in $title", $del, "");
}


// get user list
$sql = "SELECT * FROM users
		LEFT JOIN {$user->info->section}.members USING (member_id)
		LEFT JOIN permissions ON users.id = permissions.user_id";
	if ($user->peekPermissions("superuser") == 0) $sql .= " WHERE section = '{$user->info->section}' AND superuser < 1";
	$sql .= ' ORDER BY section, username';
$users = $user->db->query($sql);
	if (DB::isError($users)) dbError("Could not get user list in $title", $users, $sql);

?>


<h1>Authorized Users</h1>
<input class="default" type=button onClick="window.location.href='/sectionmaster.org/register/admin/section/add_user.php';" value="Add a New User" syle="float: right;">

<table class="cart">
	<tr>
		<th>Username</th>
		<th>Member ID</th>
		<th>Name</th>
		<th>E-mail</th>
		<? if ($user->getPermissions("users") >= 3)
			print "<th>Edit</th>"; ?>
	</tr>
	
<?
while ($this_user = $users->fetchRow()) { $i++;

	print $i%2==0 ? "<tr id=\"odd\">" : "<tr id=\"even\">";
		
	// username
	print "<td id=\"description\"><font id=\"item\">$this_user->username</font></td>";
	
	// member id
	print "<td>$this_user->member_id</td>";

	// name
	print "<td>$this_user->firstname $this_user->lastname</td>";
	
	// email
	print "<td>$this_user->email</td>";
	
	// edit & delete
	if ($user->getPermissions("users") >= 3) {
		print "<td>
				<input class=\"default\" type=button
		  		 	onClick=\"window.location='section/edit_user.php?uid=$this_user->id';\" value=\"Edit\" locked readonly>";

		if ($user->getPermissions("users") >= 5) {
			print "	<input class=\"default\" type=button
			  		 onClick=\"if (confirm('Are you sure you want to PERMANENTLY delete $this_user->username?'))
									window.location='section/users.php?command=delete&uid=$this_user->id';\" value=\"Delete\">";
		}
		print "</td>";
	}
	print "</tr>";

}
?>

<?
require "../post.php";
?>

