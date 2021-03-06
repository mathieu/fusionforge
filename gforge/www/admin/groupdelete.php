<?php
/**
 * Site Admin group properties editing page
 *
 * Copyright 1999-2001 (c) VA Linux Systems
 *
 * @version   $Id$
 *
 * This file is part of GForge.
 *
 * GForge is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GForge is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GForge; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once('../env.inc.php');
require_once $gfwww.'include/pre.php';
require_once $gfcommon.'include/license.php';
require_once $gfwww.'admin/admin_utils.php';

session_require(array('group'=>'1','admin_flags'=>'A'));

$group_id=getIntFromGet('group_id');

$group =& group_get_object($group_id);
if (!$group || !is_object($group)) {
	exit_error('Error','Could Not Get Group');
} elseif ($group->isError()) {
	exit_error('Error',$group->getErrorMessage());
}

if (getStringFromPost('submit')) {
	$sure = getIntFromPost('sure');
	$reallysure = getIntFromPost('reallysure');
	$reallyreallysure = getIntFromPost('reallyreallysure');
	if (!$group->delete($sure, $reallysure, $reallyreallysure)) {
		exit_error('Error',$group->getErrorMessage());
	} else {
		plugin_hook('delete_link',$_GET['group_id']) ;
		header("Location: ".util_make_url("/admin/?feedback=DELETED"));
	}
}

site_admin_header(array('title'=>_('Permanently Delete Project')));

echo '<h2>'._('Permanently and irretrievably delete project').': '.$group->getPublicName().'</h2>' ;?>

<p>
<form action="<?php echo getStringFromServer('PHP_SELF').'?group_id='.$group_id; ?>" method="post">
<input type="checkbox" value="1" name="sure"> <?php echo _('Confirm Delete'); ?><br />
<input type="checkbox" value="1" name="reallysure"> <?php echo _('Confirm Delete'); ?><br />
<input type="checkbox" value="1" name="reallyreallysure"> <?php echo _('Confirm Delete'); ?><br />

<input type="submit" name="submit" value="<?php echo _('Permanently Delete'); ?>" />
</form></p>

<?php

site_admin_footer(array());

?>
