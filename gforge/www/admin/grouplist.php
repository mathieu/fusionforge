<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id$

require "pre.php";    
require($DOCUMENT_ROOT.'/admin/admin_utils.php');

session_require(array('group'=>'1','admin_flags'=>'A'));

site_admin_header(array('title'=>$GLOBALS['system_name'].": Group List"));

// start from root if root not passed in
if (!$form_catroot) {
	$form_catroot = 1;
}

//CB removed from 2.6 and 2.5 was link to a page saying to use new project
//print "<br /><a href=\"groupedit-add.php\">[Add Group]</a>";
print "<p>".$GLOBALS['system_name']." Group List for Category: ";

if ($form_catroot == 1) {

	if (isset($group_name_search)) {
		print "<strong>Groups that begin with $group_name_search</strong>\n";
		// [RM] LIKE is case-sensitive, and we don't want that
		// $res = db_query("SELECT group_name,unix_group_name,group_id,is_public,status,license "
		// . "FROM groups WHERE group_name LIKE '$group_name_search%' "
		// . ($form_pending?"AND WHERE status='P' ":"")
		// . " ORDER BY group_name");
		$res = db_query("SELECT group_name,unix_group_name,group_id,is_public,status,license "
			. "FROM groups WHERE group_name ~* '^$group_name_search%' "
			. ($form_pending?"AND WHERE status='P' ":"")
			. " ORDER BY group_name");
	} else {
		print "<strong>All Categories</strong>\n";
		$res = db_query("SELECT group_name,unix_group_name,group_id,is_public,status,license "
			. "FROM groups "
			. ($status?"WHERE status='$status' ":"")
			. "ORDER BY group_name");
	}
} else {
	print "<strong>" . category_fullname($form_catroot) . "</strong>\n";

	$res = db_query("SELECT groups.group_name,groups.unix_group_name,groups.group_id,"
		. "groups.is_public,"
		. "groups.license,"
		. "groups.status "
		. "FROM groups,group_category "
		. "WHERE groups.group_id=group_category.group_id AND "
		. "group_category.category_id=$GLOBALS[form_catroot] ORDER BY groups.group_name");
}
?>
</p>
<p>
<table width="100%" border="1">
<tr>
<td><strong>Group Name (click to edit)</strong></td>
<td><strong>UNIX Name</strong></td>
<td><strong>Status</strong></td>
<td><strong>Public?</strong></td>
<td><strong>License</strong></td>
<td><strong>Members</strong></td>
</tr>

<?php
while ($grp = db_fetch_array($res)) {
	print "<tr>";
	print "<td><a href=\"groupedit.php?group_id=$grp[group_id]\">$grp[group_name]</a></td>";
	print "<td>$grp[unix_group_name]</td>";
	print "<td>$grp[status]</td>";
	print "<td>$grp[is_public]</td>";
	print "<td>$grp[license]</td>";
	
	// members
	$res_count = db_query("SELECT user_id FROM user_group WHERE group_id=$grp[group_id]");
	print "<td>" . db_numrows($res_count) . "</td>";

	print "</tr>\n";
}
?>

</table></p>

<?php
site_admin_footer(array());

?>
