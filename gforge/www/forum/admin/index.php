<?php
/**
 * GForge Forums Facility
 *
 * Copyright 2002 GForge, LLC
 * http://gforge.org/
 *
 * @version   $Id$
 */


/*
	Message Forums
	By Tim Perdue, Sourceforge, 11/99

	Massive rewrite by Tim Perdue 7/2000 (nested/views/save)

	Complete OO rewrite by Tim Perdue 12/2002

	Heavy RBAC changes 3/17/2004
*/

require_once('../../env.inc.php');
require_once $gfwww.'include/pre.php';
require_once $gfwww.'forum/include/ForumHTML.class.php';
require_once $gfcommon.'forum/Forum.class.php';
require_once $gfwww.'forum/admin/ForumAdmin.class.php';
require_once $gfcommon.'forum/ForumFactory.class.php';
require_once $gfcommon.'forum/ForumMessageFactory.class.php';
require_once $gfcommon.'forum/ForumMessage.class.php';
require_once $gfcommon.'include/TextSanitizer.class.php'; // to make the HTML input by the user safe to store

$group_id = getIntFromRequest('group_id');
$group_forum_id = getIntFromRequest('group_forum_id');
$deleteforum = getStringFromRequest('deleteforum');
$feedback = getStringFromRequest('feedback');

global $HTML;

if ($group_id) {
	//
	//  Set up local objects
	//
	$g =& group_get_object($group_id);
	if (!$g || !is_object($g) || $g->isError()) {
		exit_no_group();
	}

	$p =& $g->getPermission( session_get_user() );
	if (!$p || !is_object($p) || $p->isError()) {
		exit_permission_denied();
	}

	if (getStringFromRequest('post_changes')) {
		/*
			Update the DB to reflect the changes
		*/

		if ($deleteforum) {
			/*
				Deleting entire forum
			*/
			$fa = new ForumAdmin($group_id);
			$feedback .= $fa->ExecuteAction("delete_forum");
			$group_forum_id=0;
			$deleteforum=0;
		} else if (getStringFromRequest('add_forum')) {
			if (!form_key_is_valid(getStringFromRequest('form_key'))) {
				exit_form_double_submit();
			}
			$fa = new ForumAdmin($group_id);
			$feedback .= $fa->ExecuteAction("add_forum");
		} else if (getStringFromRequest('change_status')) {
			$fa = new ForumAdmin($group_id);
			$feedback .= $fa->ExecuteAction("change_status");
		}
	}

	if (getStringFromRequest('add_forum')) {
		/*
			Show the form for adding forums
		*/
		forum_header(array('title'=>_('Add forum')));

		echo '
			<br>
			<form method="post" action="'.getStringFromServer('PHP_SELF').'">
			<input type="hidden" name="post_changes" value="y" />
			<input type="hidden" name="add_forum" value="y" />
			<input type="hidden" name="group_id" value="'.$group_id.'" />
			<input type="hidden" name="form_key" value="' . form_generate_key() . '">
			<strong>'._('Forum Name').':</strong><br />
			<input type="text" name="forum_name" value="" size="20" maxlength="30" /><br />
			<strong>'._('Description').':</strong><br />
			<input type="text" name="description" value="" size="40" maxlength="80" /><br />
			<strong>'._('Is Public?').'</strong><br />
			<input type="radio" name="is_public" value="1" checked="checked" />'._('Yes').' <br />
			<input type="radio" name="is_public" value="0" />'._('No').'
			<br  /><br />
			<strong>'._('Allow Anonymous Posts?').'</strong><br />
			<input type="radio" name="allow_anonymous" value="1" />'._('Yes').'<br />
			<input type="radio" name="allow_anonymous" value="0" checked="checked" />'._('No').'
			<br /><br />' .
					html_build_select_box_from_assoc(array("0" => _('No Moderation') ,"1" => _('Moderated Level 1'),"2" => _('Moderated Level 2') ),"moderation_level",0) . '
				<br>' . _('Moderated Level 1') . ': ' . _('To moderate anonymous posts (if allowed in public forum) and posts from non-member users.') . '<br>' . _('Moderated Level 2') . ': ' . _('To moderate ALL posts.') . '<p>
				
			<strong>'._('Email All Posts To:').'</strong><br />
			<input type="text" name="send_all_posts_to" value="" size="30" maxlength="50" />
			<p>
			<input type="submit" name="submit" value="'._('Add This Forum').'" />
			</p>
			</form>';

		forum_footer(array());

	} else if (getStringFromRequest('change_status')) {
		/*
			Change a forum
		*/

		$f = new Forum ($g,$group_forum_id);
		if (!$f || !is_object($f)) {
			exit_error('Error','Could Not Get Forum Object');
		} elseif ($f->isError()) {
			exit_error('Error',$f->getErrorMessage());
		} elseif (!$f->userIsAdmin()) {
			exit_permission_denied();
		}

		forum_header(array('title'=>_('Change forum status')));
		echo '<p>'._('You can adjust forum features from here. Please note that private forums can still be viewed by members of your project, not the general public.').'</p>';
		$fa = new ForumAdmin();
		if ($fa->Authorized($group_id)) {
			if ($fa->isForumAdmin($group_forum_id)) {
				$fa->PrintAdminPendingOption($group_forum_id);
			}
		}
		
		echo '
			<form action="'.getStringFromServer('PHP_SELF').'" method="post">
				<input type="hidden" name="post_changes" value="y" />
				<input type="hidden" name="change_status" value="y" />
				<input type="hidden" name="group_forum_id" value="'. $f->getID() .'" />
				<input type="hidden" name="group_id" value="'.$group_id.'" />
				
				<strong>'._('Allow Anonymous Posts?').'</strong><br />
				<input type="radio" name="allow_anonymous" value="1"'.(($f->AllowAnonymous() == 1)?' checked="checked"':'').' /> '._('Yes').'<br />
				<input type="radio" name="allow_anonymous" value="0"'.(($f->AllowAnonymous() == 0)?' checked="checked"':'').'/> '._('No').'<br />
				
				
				<strong>'._('Is Public?').'</strong><br />
				<input type="radio" name="is_public" value="1"'.(($f->isPublic() == 1)?' checked="checked"':'').' /> '._('Yes').'<br />
				<input type="radio" name="is_public" value="0"'.(($f->isPublic() == 0)?' checked="checked"':'').' /> '._('No').'<br />
				<input type="radio" name="is_public" value="9"'.(($f->isPublic() == 9)?' checked="checked"':'').' />'._('Deleted').'<br />
				<p>' .
					html_build_select_box_from_assoc(array("0" => _('No Moderation') ,"1" => _('Moderated Level 1'),"2" => _('Moderated Level 2') ),"moderation_level",$f->getModerationLevel()) . '
				<br>' . _('Moderated Level 1') . ': ' . _('To moderate anonymous posts (if allowed in public forum) and posts from non-member users.') . '<br>' . _('Moderated Level 2') . ': ' . _('To moderate ALL posts.') . '<p>
				

				<strong>'._('Forum Name').':</strong><br />
				<input type="text" name="forum_name" value="'. $f->getName() .'" size="20" maxlength="30" />
				<p>
				<strong>'._('Email All Posts To:').'</strong><br />
				<input type="text" name="send_all_posts_to" value="'. $f->getSendAllPostsTo() .'" size="30" maxlength="50" />
				<p>
				<strong>'._('Description').':</strong><br />
				<input type="text" name="description" value="'. $f->getDescription() .'" size="40" maxlength="80" /><br />
				<p>
				<input type="submit" name="submit" value="'._('Update').'" />
			</form><p>';
			//echo '<a href="'.getStringFromServer('PHP_SELF').'?group_id='.$group_id.'&amp;group_forum_id='.$group_forum_id.'&amp;delete=1">'._('Delete Message').'</a><br />';
			echo '<a href="'.getStringFromServer('PHP_SELF').'?group_id='.$group_id.'&amp;group_forum_id='.$group_forum_id.'&amp;deleteforum=1">'._('Delete entire forum and all content').'</a><br />';
		forum_footer(array());

	} elseif ($deleteforum && $group_forum_id) {

		$f = new Forum ($g,$group_forum_id);
		if (!$f || !is_object($f)) {
			exit_error('Error','Could Not Get Forum Object');
		} elseif ($f->isError()) {
			exit_error('Error',$f->getErrorMessage());
		} elseif (!$f->userIsAdmin()) {
			exit_permission_denied();
		}
		forum_header(array('title'=>_('Delete')));
		echo '<p>
			<strong>'._('You are about to permanently and irretrievably delete this entire forum and all its contents!').'</strong><br />
			<form method="post" action="'.getStringFromServer('PHP_SELF').'">
			<input type="hidden" name="post_changes" value="y" />
			<input type="hidden" name="deleteforum" value="y" />
			<input type="hidden" name="group_id" value="'.$group_id.'" />
			<input type="hidden" name="group_forum_id" value="'.$group_forum_id.'" />
			<input type="checkbox" name="sure" value="1" />'._('I\'m Sure').'<br />
			<input type="checkbox" name="really_sure" value="1" />'._('I\'m Really Sure').'<br />
			<input type="submit" name="submit" value="'._('Delete').'" />
			</form>';
		forum_footer(array());

	} elseif ( getStringFromRequest("deletemsg") ) {
		// delete message handling
		
		$fa = new ForumAdmin();
		if ($fa->Authorized($group_id)) {
			$forum_id = getStringFromRequest("forum_id");
			$thread_id = getStringFromRequest("thread_id");
			$msg_id = getStringFromRequest("deletemsg");
			if ($fa->isForumAdmin($forum_id)) {
				if (getStringFromRequest("ok")) {
					//actually delete the message
					$feedback .= $fa->ExecuteAction("delete");
					forum_header(array('title'=>_('Delete a Message')));
					echo '<p>'.util_make_link ('/forum/forum.php?forum_id=' . $forum_id, _("Return to the forum")) ;
					forum_footer(array());
				} elseif (getStringFromRequest("cancel")) {
					// the user cancelled the request, go back to forum
					echo "<script>";
					//if thread_id is 0, then we came from message.php. else, we came from forum.php
					if (!$thread_id) {
						echo "window.location='/forum/message.php?msg_id=$msg_id';";
					} else {
						echo "window.location='/forum/forum.php?thread_id=$thread_id&forum_id=$forum_id';";
					}
					echo "</script>";
				} else {
					//print the delete message confirmation
					forum_header(array('title'=>_('Delete a Message')));
					echo '<p><center>
							<form action="'.getStringFromServer('PHP_SELF').'" method="post">
							<h3>' . _('WARNING! You are about to permanently delete a message and all of its followups!!') . '</h3><p>
							<input type="submit" name="ok" value="' . _('Yes') . '">    
							<input type="submit" name="cancel" value="' . _('No') . '">    
							<input type="hidden" name="deletemsg" value="'.$msg_id.'">
							<input type="hidden" name="group_id" value="'.$group_id.'">
							<input type="hidden" name="forum_id" value="'.$forum_id.'">
							<input type="hidden" name="thread_id" value="'.$thread_id.'">
							</center>
							</form>';
					forum_footer(array());
				}
			} else {
				exit_permission_denied();
			}
		} else {
			//manage auth errors
			if ($fa->isGroupIdError()) {
				exit_no_group();
			}	elseif ($fa->isPermissionDeniedError()) {
				exit_permission_denied();
			}
		}
	} elseif (getStringFromRequest("editmsg")) {
		// edit message handling
		$forum_id = getStringFromRequest("forum_id");
		$thread_id = getStringFromRequest("thread_id");
		$msg_id = getStringFromRequest("editmsg");
		$fa = new ForumAdmin();
		if ($fa->Authorized($group_id)) {
			if ($fa->isForumAdmin($forum_id)) {
				if (getStringFromRequest("ok")) {
					//actually finish editing the message and save the contents
					$f = new Forum ($fa->GetGroupObject(),$forum_id);
					if (!$f || !is_object($f)) {
						exit_error('Error','Error Getting Forum');
					} elseif ($f->isError()) {
						exit_error('Error',$f->getErrorMessage());
					}
					$fm=new ForumMessage($f,$msg_id,false,false);
					if (!$fm || !is_object($fm)) {
						exit_error(_('Error'),_('Error getting new forum message'));
					} elseif ($fm->isError()) {
						exit_error(_('Error'),$fm->getErrorMessage());
					}
					$subject = getStringFromRequest('subject');
					$body = getStringFromRequest('body');
					
					$sanitizer = new TextSanitizer();
					$body = $sanitizer->SanitizeHtml($body);
					
					$is_followup_to = getStringFromRequest('is_followup_to');
					$form_key = getStringFromRequest('form_key');
					$posted_by = getStringFromRequest('posted_by');
					$post_date = getStringFromRequest('post_date');
					$is_followup_to = getStringFromRequest('is_followup_to');
					$has_followups = getStringFromRequest('has_followups');
					$most_recent_date = getStringFromRequest('most_recent_date');
					if ($fm->updatemsg($forum_id,$posted_by,$subject,$body,$post_date,$is_followup_to,$thread_id,$has_followups,$most_recent_date)) {
						$feedback .= _('Message Edited Successfully');
					} else {
						$feedback .= $fm->getErrorMessage();
					}
					forum_header(array('title'=>_('Edit a Message')));
					echo '<p>'.util_make_link ('/forum/forum.php?forum_id=' . $forum_id, _("Return to the forum")) ;
					forum_footer(array());
				} elseif (getStringFromRequest("cancel")) {
					// the user cancelled the request, go back to forum
					echo "<script>";
					echo "window.location='/forum/message.php?msg_id=$msg_id';";
					echo "</script>";
				} else { 
					//print the edit message confirmation
					
					$f = new Forum ($fa->GetGroupObject(),$forum_id);
					if (!$f || !is_object($f)) {
						exit_error('Error','Error Getting Forum');
					} elseif ($f->isError()) {
						exit_error('Error',$f->getErrorMessage());
					}
					
					$fm=new ForumMessage($f,$msg_id,false,false);
					if (!$fm || !is_object($fm)) {
						exit_error(_('Error'),_('Error Getting ForumMessage'));
					} elseif ($fm->isError()) {
						exit_error(_('Error'),$fm->getErrorMessage());
					}
					
					$fh = new ForumHTML($f);
					if (!$fh || !is_object($fh)) {
						exit_error(_('Error'),_('Error Getting ForumHTML'));
					} elseif ($fh->isError()) {
						exit_error(_('Error'),$fh->getErrorMessage());
					}
					
					forum_header(array('title'=>_('Edit a Message')));
					$fh->showEditForm($fm);
					forum_footer(array());
				}
			} else {
				exit_permission_denied();
			}
		} else {
			//manage auth errors
			if ($fa->isGroupIdError()) {
				exit_no_group();
			}	elseif ($fa->isPermissionDeniedError()) {
				exit_permission_denied();
			}
		}
	} else {
		/*
			Show main page for choosing
			either moderator or delete
		*/
		forum_header(array('title'=>_('Forums: Administration')));

		//
		//	Add new forum
		//
		if ($p->isForumAdmin()) {
			$fa = new ForumAdmin();
			$fa->PrintAdminOptions();
		}
		//
		//	Get existing forums
		//
		$ff=new ForumFactory($g);
		if (!$ff || !is_object($ff) || $ff->isError()) {
			exit_error(_('Error'),$ff->getErrorMessage());
		}

		$farr =& $ff->getForums();

		if ($ff->isError()) {
			echo '<h1>'.sprintf(_('No Forums Found For %1$s'), $g->getPublicName()) .'</h1>';
			echo $ff->getErrorMessage();
			forum_footer(array());
			exit;
		}

		/*
			List the existing forums so they can be edited.
		*/

		for ($j = 0; $j < count($farr); $j++) {
			if (!is_object($farr[$j])) {
				//just skip it - this object should never have been placed here
			} elseif ($farr[$j]->isError()) {
				echo $farr[$j]->getErrorMessage();
			} else {
				echo '<a href="'.getStringFromServer('PHP_SELF').'?group_id='.$group_id.'&amp;change_status=1&amp;group_forum_id='. $farr[$j]->getID() .'">'.
					$farr[$j]->getName() .'</a><br />'.$farr[$j]->getDescription().'<br /><a href="monitor.php?group_id='.$group_id.'&amp;group_forum_id='. $farr[$j]->getID() .'">Monitoring Users</a><p>';
			}
		}

		forum_footer(array());
	}

} else {
	/*
		Not logged in or insufficient privileges
	*/
	if (!$group_id) {
		exit_no_group();
	} else {
		exit_permission_denied();
	}
}
?>
