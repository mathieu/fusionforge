<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id$

require "pre.php";
session_require(array('group'=>'1','admin_flags'=>'A'));
$HTML->header(array('title'=>$GLOBALS['system_name'].$Language->getText('admin_userlist','userlist')));

/**
 * performAction() - Updates the indicated user status
 *
 * @param               string  $newStatus - the new user status
 * @param               string  $statusString - the status string to display
 * @param               string  $user_id - the user id to act upon
 */
function performAction($newStatus, $statusString, $user_id) {
	global $Language;
	db_query("UPDATE users set status='".$newStatus."' WHERE user_id='".$user_id."'");
	echo "<h2>" .$Language->getText('admin_userlist','user_updated',array($GLOBALS['statusString']))."</h2>";
}

function show_users_list ($result) {
	global $Language;
	echo '<p>' .$Language->getText('admin_userlist','key') .':
		<font color="#00ff00">'.$Language->getText('admin_userlist','active'). '</font>
		<font color="grey">' .$Language->getText('admin_userlist','deleted') .'</font>
		<font color="red">' .$Language->getText('admin_userlist','suspended'). '</font> '
		.$Language->getText('admin_userlist','pending').'</p>';

	$tableHeaders = array(
		$Language->getText('admin_userlist', 'login'),
		$Language->getText('admin_userlist', 'add_date'),
		'&nbsp;',
		'&nbsp;',
		'&nbsp;',
		'&nbsp;'
	);

	echo $GLOBALS['HTML']->listTableTop($tableHeaders);

	$count = 0;

	while ($usr = db_fetch_array($result)) {
		print '<tr '. $GLOBALS['HTML']->boxGetAltRowStyle($count) . '><td bgcolor="';
		if ($usr['status'] == 'A') print "#00ff00";
		if ($usr['status'] == 'D') print "grey";
		if ($usr['status'] == 'S') print "red";
		print "\"><a href=\"useredit.php?user_id=$usr[user_id]\">";
		if ($usr[status] == 'P') print "*";
		echo $usr['user_name'].'</a>';
		echo '</td>';
		echo '<td width="15%" align="center">';
		echo ($usr['add_date'] ? date($GLOBALS['sys_datefmt'], $usr['add_date']) : '-');
		echo '</td>';
		echo '<td width="15%" align="center"><a href="/developer/?form_dev='.$usr['user_id'].'">[' .$Language->getText('admin_userlist','devprofile'). ']</a></td>';
		echo '<td width="15%" align="center"><a href="userlist.php?action=activate&amp;user_id='.$usr['user_id'].'">[' .$Language->getText('admin_userlist','activate'). ']</a></td>';
		echo '<td width="15%" align="center"><a href="userlist.php?action=delete&amp;user_id='.$usr['user_id'].'">[' .$Language->getText('admin_userlist','delete') .']</a></td>';
		echo '<td width="15%" align="center"><a href="userlist.php?action=suspend&amp;user_id='.$usr['user_id'].'">[' .$Language->getText('admin_userlist','suspend'). ']</a></td>';
		echo '</tr>';
		
		$count ++;
	}
	
	echo $GLOBALS['HTML']->listTableBottom();

}

// Administrative functions

if ($action=='delete') {
	performAction('D', "DELETED", $user_id);
} else if ($action=='activate') {
	performAction('A', "ACTIVE", $user_id);
} else if ($action=='suspend') {
	performAction('S', "SUSPENDED", $user_id);
}

/*
	Add a user to this group
*/
if ($action=='add_to_group') {
	db_query("INSERT INTO user_group (user_id,group_id) VALUES ($user_id,$group_id)");
}

/*
	Show list of users
*/
print "<p>" .$Language->getText('admin_userlist','user_list_for_group');
if (!$group_id) {
	print "<strong>" .$Language->getText('admin_userlist','all_groups'). "</strong>";
	print "\n</p>";

	if ($user_name_search) {
		$result = db_query("SELECT user_name,user_id,status,add_date FROM users WHERE user_name ILIKE '".$user_name_search."%' OR realname ILIKE '".$user_name_search."%' OR realname ILIKE '% ".$user_name_search."%' ORDER BY user_name");
	} else {
		$result = db_query("SELECT user_name,user_id,status,add_date FROM users ORDER BY user_name");
	}
	show_users_list ($result);
} else {
	/*
		Show list for one group
	*/
	print "<strong>" . group_getname($group_id) . "</strong></p>";


	$result = db_query("SELECT users.user_id AS user_id,users.user_name AS user_name,users.status AS status, users.add_date AS add_date "
		. "FROM users,user_group "
		. "WHERE users.user_id=user_group.user_id AND "
		. "user_group.group_id=$group_id ORDER BY users.user_name");
	show_users_list ($result);

	/*
        	Show a form so a user can be added to this group
	*/
	?>
	<hr />
	<p>
	<form action="<?php echo $PHP_SELF; ?>" method="post">
	<input type="hidden" name="action" value="add_to_group" />
	<input name="user_id" type="TEXT" value="" />
	<br />
	Add User to Group (<?php print group_getname($group_id); ?>):
	<br />
	<input type="hidden" name="group_id" value="<?php print $group_id; ?>" />
	<br />
	<input type="submit" name="Submit" value="<?php echo $Language->getText('admin_userlist','submit'); ?>" />
	</form>
	</p>
	<?php
}

$HTML->footer(array());

?>
