<?php
/**
 * Commit user's email change
 *
 * This page should be accessed with confirmation URL sent to user in email
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
require_once $gfcommon.'include/account.php';

$confirm_hash = getStringFromRequest('confirm_hash');

if (!$confirm_hash) {
	// XXX ogi: What's $ch?
	//$confirm_hash = $ch;
	$confirm_hash = getStringFromRequest('ch');
}
if (!$confirm_hash) {
	exit_missing_param();
}
$confirm_hash = html_clean_hash_string($confirm_hash);

$res_user = db_query("SELECT * FROM users WHERE confirm_hash='$confirm_hash'");
if (db_numrows($res_user) > 1) {
	exit_error("Error","This confirm hash exists more than once.");
}
if (db_numrows($res_user) < 1) {
	exit_error("Error","Invalid confirmation hash.");
}
$u =& user_get_object(db_result($res_user, 0, 'user_id'), $res_user);
if (!$u || !is_object($u)) {
    exit_error('Error','Could Not Get User');
} elseif ($u->isError()) {
    exit_error('Error',$u->getErrorMessage());
}

if (!$u->setEmail($u->getNewEmail())) {
	exit_error(
		'Could Not Complete Operation',
		$u->getErrorMessage()
	);
}
//plugin webcal change user mail
	else {
		plugin_hook('change_cal_mail',user_getid());
	}

site_user_header(array('title'=>_('Email Change Complete')));
?>

<p>
<?php
printf (_('Welcome, %1$s. Your email change is complete. Your new email address on file is <strong>%2$s</strong>. Mail sent to &lt;%3$s&gt; will now be forwarded to this account.'),$u->getUnixName(),$u->getEmail(),$u->getUnixName().'@'.$GLOBALS['sys_users_host']) ?>
</p>

<p><?php echo util_make_link ('/',_('Return')); ?></p>

<?php

site_user_footer(array());

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
