<?php
/**
  *
  * Request recovery of the lost password
  *
  * This page sends confirmation email with link to reset password
  * for account.
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id$
  *
  */


require_once('pre.php');

if ($loginname) {

	$u = user_get_object_by_name($loginname);

	if (!$u || !is_object($u)){
		exit_error("Invalid user","That user does not exist.");
	}

	// First, we need to create new confirm hash

	$confirm_hash = md5($session_hash . strval(time()) . strval(rand()));

	$u->setNewEmailAndHash($u->getEmail(), $confirm_hash);
	if ($u->isError()) {
		exit_error('Error',$u->getErrorMessage());
	} else {

		$message = stripcslashes($Language->getText('account_lostpw', 'message', array($GLOBALS['HTTP_HOST'], $confirm_hash, $GLOBALS[sys_name])));

		util_send_message($u->getEmail(),$Language->getText('account_lostpw', 'subject', $GLOBALS[sys_name]),$message);

		$HTML->header(array('title'=>"Lost Password Confirmation",'pagename'=>'account_lostpw'));

		echo $Language->getText('account_lostpw','notify');

		$HTML->footer(array());
		exit();
	}
}


$HTML->header(array('title'=>"Lost Account Password",'pagename'=>'account_lostpw'));

echo $Language->getText('account_lostpw','warn');
?>

<form action="<?php echo $PHP_SELF; ?>" method="post">
<p>
<?php echo $Language->getText('account_login', 'loginname'); ?>
<br />
<input type="text" name="loginname" />
<br />
<br />
<input type="submit" name="submit" value="<?php echo $Language->getText('account_lostpw','sendhash'); ?>" />
</p>
</form>

<p><a href="/"><?php echo $Language->getText('general', 'return', $GLOBALS[sys_name]); ?></a></p>

<?php

$HTML->footer(array());

?>
