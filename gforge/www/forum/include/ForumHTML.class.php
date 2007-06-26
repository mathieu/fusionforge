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
*/

require_once('pre.php');
require_once('note.php');
require_once('www/news/news_utils.php');
require_once('www/forum/admin/ForumAdmin.class.php');
require_once('www/forum/include/AttachManager.class.php');

function forum_header($params) {
	global $HTML,$group_id,$forum_name,$forum_id,$sys_datefmt,$sys_news_group,$Language,$f,$sys_use_forum,$group_forum_id;

	if ($group_forum_id) {
		$forum_id=$group_forum_id;
	}
	if (!$sys_use_forum) {
		exit_disabled();
	}

	$params['group']=$group_id;
	$params['toptab']='forums';

	/*
		bastardization for news
		Show icon bar unless it's a news forum
	*/
	if ($group_id == $sys_news_group) {
		//this is a news item, not a regular forum
		if ($forum_id) {
			// Show this news item at the top of the page
			$sql="SELECT submitted_by, post_date, group_id, forum_id, summary, details FROM news_bytes WHERE forum_id='$forum_id'";
			$result=db_query($sql);

			// checks which group the news item belongs to
			$params['group']=db_result($result,0,'group_id');
			$params['toptab']='news';
			$HTML->header($params);


			echo '<table><tr><td valign="top">';
			if (!$result || db_numrows($result) < 1) {
				echo '<h3>'._('Error - this news item was not found').'</h3>';
			} else {
				$user = user_get_object(db_result($result,0,'submitted_by'));
				$group =& group_get_object($params['group']);
				if (!$group || !is_object($group) || $group->isError()) {
					exit_no_group();
				}
				echo '
				<strong>'._('Posted by').':</strong> '.$user->getRealName().'<br />
				<strong>'._('Date').':</strong> '. date($sys_datefmt,db_result($result,0,'post_date')).'<br />
				<strong>'._('Summary').':</strong> <a href="'.$GLOBALS['sys_urlprefix'].'/forum/forum.php?forum_id='.db_result($result,0,'forum_id').'&group_id='.$group_id.'">'. db_result($result,0,'summary').'</a><br/>
				<strong>'._('Project').':</strong> <a href="'.$GLOBALS['sys_urlprefix'].'/projects/'.$group->getUnixName().'">'.$group->getPublicName().'</a> <br />
				<p>
				'. (util_make_links(nl2br(db_result($result,0,'details'))));

				echo '</p>';
			}
			echo '</td><td valign="top" width="35%">';
			echo $HTML->boxTop(_('Latest News'));
			echo news_show_latest($params['group'],5,false);
			echo $HTML->boxBottom();
			echo '</td></tr></table>';
		} else {
			site_project_header($params);
		}
	} else {
		site_project_header($params);
	}

	$menu_text=array();
	$menu_links=array();
	if ($f && $forum_id) {
		$menu_text[]=_('Discussion Forums:') .' '. $f->getName();
		$menu_links[]='/forum/forum.php?forum_id='.$forum_id;
	}
	if ($f && $f->userIsAdmin()) {
		$menu_text[]=_('Admin');
		$menu_links[]='/forum/admin/?group_id='.$group_id;
	}
	if (count($menu_text) > 0) {
		echo $HTML->subMenu(
			$menu_text,
			$menu_links
		);
	}

	if (session_loggedin() ) {
		if ($f) {
			if ($f->isMonitoring()) {
				echo '<a href="'.$GLOBALS['sys_urlprefix'].'/forum/monitor.php?forum_id='.$forum_id.'&amp;group_id='.$group_id.'&amp;stop=1">' .
				html_image('ic/xmail16w.png','20','20',array()).' '._('Stop Monitoring').'</a> | ';
			} else {
				echo '<a href="'.$GLOBALS['sys_urlprefix'].'/forum/monitor.php?forum_id='.$forum_id.'&amp;group_id='.$group_id.'&amp;start=1">' .
				html_image('ic/mail16w.png','20','20',array()).' '._('Monitor Forum').'</a> | ';
			}
			echo '<a href="'.$GLOBALS['sys_urlprefix'].'/forum/save.php?forum_id='.$forum_id.'&amp;group_id='.$group_id.'">' .
			html_image('ic/save.png','24','24',array()) .' '._('Save Place').'</a> | ';
		}
	}

	if ($f && $forum_id) {
		echo '<a href="'.$GLOBALS['sys_urlprefix'].'/forum/new.php?forum_id='.$forum_id.'&amp;group_id='.$group_id.'">' .
			html_image('ic/write16w.png','20','20',array('alt'=>_('Start New Thread'))) .' '.
			_('Start New Thread').'</a>';
	}
}

function forum_footer($params) {
	site_project_footer($params);
}


/**

	Wrap many forum functions in this class

**/
class ForumHTML extends Error {
	/**
	 * The Forum object.
	 *
	 * @var  object  $Forum
	 */
	var $Forum;

	function ForumHTML(&$Forum) {
		$this->Error();
		if (!$Forum || !is_object($Forum)) {
			$this->setError('ForumMessage:: No Valid Forum Object');
			return false;
		}
		if ($Forum->isError()) {
			$this->setError('ForumMessage:: '.$Forum->getErrorMessage());
			return false;
		}
		$this->Forum =& $Forum;
		return true;
	}
	
	
	/**
	 * Function showPendingMessage
	 *
	 * @param 	object	The message.
	 *
	 * @return 	returns the html output
	 */
	function showPendingMessage ( &$msg) {
		global $sys_datefmt,$Language,$HTML,$group_id;
		
		$am = new AttachManager();
		$ret_val = $am->PrintHelperFunctions();
		html_feedback_top(_('This is the content of the pending message'));
		$ret_val .= '
		<table border="0">
			<tr>
				<td class="tablecontent" nowrap="nowrap">'._('By:').
					$msg->getPosterRealName().
					'<br />
					';
					$msgforum =& $msg->getForum();
					$ret_val .= $am->PrintAttachLink($msg,$group_id,$msgforum->getID()) . '
					<br />
					'.
					html_image('ic/msg.png',"10","12",array("border"=>"0")) .
					$bold_begin. $msg->getSubject() . $bold_end .'&nbsp; '.
					'<br />'. date($sys_datefmt,$msg->getPostDate()) .'
				</td>
			</tr>
			<tr>
				<td>
					'.  $msg->getBody() .'
				</td>
			</tr>
		</table>';
		return $ret_val;
		
	}
	
	function showNestedMessage ( &$msg ) {
		/*

		accepts a database result handle to display a single message
		in the format appropriate for the nested messages

		*/
		global $sys_datefmt,$Language,$HTML,$group_id;
		/*
			See if this message is new or not
			If so, highlite it in bold
		*/
		if ($this->Forum->getSavedDate() < $msg->getPostDate()) {
			$bold_begin='<strong>';
			$bold_end='</strong>';
		}
		$am = new AttachManager();
		$fa = new ForumAdmin();
		$msgforum =& $msg->getForum(); 
		$ret_val = 		
		'
		<table border="0">
			<tr>
				<td class="tablecontent" nowrap="nowrap">'; if ($msgforum->userIsAdmin()) {$ret_val .= $fa->PrintAdminMessageOptions($msg->getID(),$group_id,$msg->getThreadID(),$msgforum->getID());} $ret_val .= _('By:').' <a href="'.$GLOBALS['sys_urlprefix'].'/users/'.
					$msg->getPosterName() .'/">'.
					$msg->getPosterRealName() .'</a>'.
					'<br />
					';
					$ret_val .= $am->PrintAttachLink($msg,$group_id,$msgforum->getID()) . '
					<br />
					<a href="'.$GLOBALS['sys_urlprefix'].'/forum/message.php?msg_id='.
					$msg->getID() .'&group_id='.$group_id.'">'.
					html_image('ic/msg.png',"10","12",array("border"=>"0")) .
					$bold_begin. $msg->getSubject() .' [ '._('reply').' ]'. $bold_end .'</a> &nbsp; '.
					'<br />'. date($sys_datefmt,$msg->getPostDate()) .'
				</td>
			</tr>
			<tr>
				<td>
					'; 
					if (!strstr($msg->getBody(),'<')) {
						$ret_val .= nl2br($msg->getBody()); //backwards compatibility for non html messages
					} else {
						$ret_val .= $msg->getBody();
					}
					$ret_val .= '
				</td>
			</tr>
		</table>';
		return $ret_val;
	}
	
	/**
	*  LinkAttachEditForm - Returns the link to the attach form for editing
	*
	*	@param 		string	Filename
	*	@param 		int		group id
	*	@param 		int		forum id
	*	@param 		int		attach id
	*	@param 		int		msg id
	*
	*	@return		The HTML output
	*/
	
	function LinkAttachEditForm($filename,$group_id,$forum_id,$attachid,$msg_id) {
		global $Language;
		$return_val = '
			
			<p>
			<form action="' . getStringFromServer('PHP_SELF') . '" method="post" enctype="multipart/form-data">
			<table>
			<tr>
				<td>' . _('Current File') . ": <span class=\"selected\">" . $filename . '</span></td>
			</tr>
			</table>
			
			<fieldset class=\"fieldset\">
			<table>
					
					<tr>
						<td>' . _('Use the "Browse" button to find the file you want to attach') . '</td>
					</tr>
					<tr>
						<td>' . _('File to upload') . ':   <input type="file" name="attachment1"/></td>
					</tr>
					<tr>
						<td class="warning">' . _('Warning : Current file will be deleted permanently') . '</td>
					</tr>
			</table>
			<input type="submit" name="go" value="'._('Update').'">
			<input type="hidden" name="doedit" value="1"/>
			<input type="hidden" name="edit" value="yes"/>
			<input type="hidden" name="forum_id" value="'.$forum_id.'"/>
			<input type="hidden" name="group_id" value="'.$group_id.'"/>
			<input type="hidden" name="attachid" value="'.$attachid.'"/>
			<input type="hidden" name="msg_id" value="'.$msg_id.'"/>
			</fieldset></form><p>';
		return $return_val;
	}
	
	/**
	*  LinkAttachForm - echoes the link to the attach form
	*
	*	@return		The HTML output echoed
	*/

	function LinkAttachForm() {
		global $Language;
		
		$poststarttime = time();
		$posthash = md5($poststarttime . user_getid() );
		echo "
		<fieldset class=\"fieldset\">
		<table>
				<tr>
					<td>" . _('Use the "Browse" button to find the file you want to attach') . "</td>
				</tr>
				<tr>
					<td>" . _('File to upload') . ":   <input type=\"file\" name=\"attachment1\"/></td>
				</tr>
		</table>
		
		</fieldset>";	
	
	}	
	
	
	function showNestedMessages ( &$msg_arr, $msg_id ) {
		global $total_rows,$sys_datefmt;

		$rows=count($msg_arr["$msg_id"]);
		$ret_val='';

		if ($msg_arr["$msg_id"] && $rows > 0) {
			$ret_val .= '
			<ul><li style="list-style: none">';

			/*

			iterate and show the messages in this result

			for each message, recurse to show any submessages

			*/
			$am = new AttachManager();
			for ($i=($rows-1); $i >= 0; $i--) {
				//	  increment the global total count
				$total_rows++;

				//	  show the actual nested message
				$ret_val .= $this->showNestedMessage ($msg_arr["$msg_id"][$i]).'<p />';

				if ($msg_arr["$msg_id"][$i]->hasFollowups()) {
					//	  Call yourself if there are followups
					$ret_val .= $this->showNestedMessages ( $msg_arr,$msg_arr["$msg_id"][$i]->getID() );
				}
			}
			$ret_val .= '
			</li></ul>';
		} else {
			//$ret_val .= "<p><strong>no messages actually follow up to $msg_id</strong>";
		}

		return $ret_val;
	}

	function showSubmessages(&$msg_arr, $msg_id, $level) {
		/*
			Recursive. Selects this message's id in this thread,
			then checks if any messages are nested underneath it.
			If there are, it calls itself, incrementing $level
			$level is used for indentation of the threads.
		*/
		global $total_rows,$sys_datefmt,$forum_id,$current_message,$group_id;

		$rows=count($msg_arr["$msg_id"]);
		$ret_val = "";
//echo "<p>ShowSubmessages() $msg_id | $rows";
		if ($rows > 0) {
			for ($i=($rows-1); $i >= 0; $i--) {
				/*
					Is this row's background shaded or not?
				*/
				$total_rows++;

				$ret_val .= '
					<tr '. $GLOBALS['HTML']->boxGetAltRowStyle($total_rows) .'><td nowrap="nowrap">';
				/*
					How far should it indent?
				*/
				for ($i2=0; $i2<$level; $i2++) {
					$ret_val .= ' &nbsp; &nbsp; &nbsp; ';
				}

				/*
					If it this is the message being displayed, don't show a link to it
				*/
				if ($current_message != $msg_arr["$msg_id"][$i]->getID()) {
					$ah_begin='<a href="'.$GLOBALS['sys_urlprefix'].'/forum/message.php?msg_id='. $msg_arr["$msg_id"][$i]->getID() .
						'&group_id='.$group_id.'">';
					$ah_end='</a>';
				} else {
					$ah_begin='';
					$ah_end='';
				}

				$ret_val .= $ah_begin .
					html_image('ic/msg.png',"10","12",array("border"=>"0"));
				/*
					See if this message is new or not
				*/
				if ($this->Forum->getSavedDate() < $msg_arr["$msg_id"][$i]->getPostDate()) {
					$bold_begin='<strong>';
					$bold_end='</strong>';
				} else {
					$bold_begin='';
					$bold_end='';
				}

				$ret_val .= $bold_begin.$msg_arr["$msg_id"][$i]->getSubject() .$bold_end.$ah_end.'</td>'.
					'<td><a href="'.$GLOBALS['sys_urlprefix'].'/users/'.$msg_arr["$msg_id"][$i]->getPosterName().'/">'. $msg_arr["$msg_id"][$i]->getPosterRealName() .'</a></td>'.
					'<td>'.date($sys_datefmt, $msg_arr["$msg_id"][$i]->getPostDate() ).'</td></tr>';

				if ($msg_arr["$msg_id"][$i]->hasFollowups() > 0) {
					/*
						Call yourself, incrementing the level
					*/
					$ret_val .= $this->showSubmessages($msg_arr,$msg_arr["$msg_id"][$i]->getID(),($level+1));
				}
			}
		}
		return $ret_val;
	}

	/**
	*  showEditForm - Prints the form to edit a message
	*
	*	@param 		int		The Message
	*	@return		The HTML output echoed
	*/
	
	function showEditForm(&$msg) {
		global $Language,$sys_default_domain;
		
		$thread_id = $msg->getThreadID();
		$msg_id = $msg->getID();
		$posted_by = $msg->getPosterID();
		$subject = $msg->getSubject();
		$body = $msg->getBody();
		$post_date = $msg->getPostDate();
		$is_followup_to = $msg->getParentID();
		$has_followups = $msg->hasFollowups();
		$most_recent_date = $msg->getMostRecentDate();
		$g =& $this->Forum->getGroup();
		$group_id = $g->getID();
		
		if (strtoupper(getStringFromServer('HTTPS')) == 'ON') {
			$http = "https://";
		} else {
			$http = "http://";
		}
		
		if ($this->Forum->userCanPost()) { // minor control, but anyways it should be an admin at this point
			echo notepad_func();
			?>
			<div align="center">
			<form "enctype="multipart/form-data" action="/forum/admin/index.php" method="post">
			<?php $objid = $this->Forum->getID();?>
			<input type="hidden" name="thread_id" value="<?php echo $thread_id; ?>" />
			<input type="hidden" name="forum_id" value="<?php echo $objid; ?>" />
			<input type="hidden" name="editmsg" value="<?php echo $msg_id; ?>" />
			<input type="hidden" name="is_followup_to" value="<?php echo $is_followup_to; ?>" />
			<input type="hidden" name="form_key" value="<?php echo form_generate_key();?>">
			<input type="hidden" name="posted_by" value="<?php echo $posted_by;?>">
			<input type="hidden" name="post_date" value="<?php echo $post_date;?>">
			<input type="hidden" name="has_followups" value="<?php echo $has_followups;?>">
			<input type="hidden" name="most_recent_date" value="<?php echo $most_recent_date;?>">
			<input type="hidden" name="group_id" value="<?php echo $group_id;?>">
			<fieldset class="fieldset"><table><tr><td valign="top">
			</td><td valign="top">
			<br>
			<strong><?php echo _('Subject:'); ?></strong><?php echo utils_requiredField(); ?><br />
				<input type="text" name="subject" value="<?php echo $subject; ?>" size="45" maxlength="45" />
			<br><br>
			<strong><?php echo _('Message:'); ?></strong><?php echo notepad_button('document.forms[1].body') ?><?php echo utils_requiredField(); ?><br />
				<?php
				$params['body'] = $body;
				$params['width'] = "800";
				$params['height'] = "500";
				$params['group'] = $group_id;
				plugin_hook("text_editor",$params);
				if (!$GLOBALS['editor_was_set_up']) {
					//if we don�t have any plugin for text editor, display a simple textarea edit box
					echo '<textarea name="body"  rows="10" cols="50" wrap="soft">' . $body . '</textarea>';
				}
				unset($GLOBALS['editor_was_set_up']);
				?>
			<br><br>		
			
				<p>
				<?php //$this->LinkAttachForm();?>
				<p>
			

			<?php
			?>
			<br />
			<center><input type="submit" name="ok" value="<?php echo _('Update'); ?>" />
			   <input type="submit" name="cancel" value="<?php echo _('Cancel'); ?>" />
			</center>
				</p>
			</td></tr></table></fieldset>
			</form>
			</div>
			<?php
		}
	}
	
	function showPostForm($thread_id=0, $is_followup_to=0, $subject="") {
		global $Language,$sys_default_domain,$group_id;

			if (strtoupper(getStringFromServer('HTTPS')) == 'ON') {
				$http = "https://";
			} else {
				$http = "http://";
			}
		
		if ($this->Forum->userCanPost()) {
			if ($subject) {
				//if this is a followup, put a RE: before it if needed
				if (!eregi('RE:',$subject,$test)) {
					$subject ='RE: '.$subject;
				}
			}
			echo notepad_func();
			?>
			<div align="center">
			<form "enctype="multipart/form-data" action="/forum/forum.php?forum_id=<?php echo $this->Forum->getID(); ?>&group_id=<?php echo $group_id; ?>" method="post">
			<?php $objid = $this->Forum->getID();?>
			<input type="hidden" name="post_message" value="y" />
			<input type="hidden" name="thread_id" value="<?php echo $thread_id; ?>" />
			<input type="hidden" name="msg_id" value="<?php echo $is_followup_to; ?>" />
			<input type="hidden" name="is_followup_to" value="<?php echo $is_followup_to; ?>" />
			<input type="hidden" name="form_key" value="<?php echo form_generate_key();?>">
			<fieldset class="fieldset"><table><tr><td valign="top">
			</td><td valign="top">
			<br>
			<strong><?php echo _('Subject:'); ?></strong><?php echo utils_requiredField(); ?><br />
				<input type="text" name="subject" value="<?php echo $subject; ?>" size="45" maxlength="45" />
			<br><br>
			<strong><?php echo _('Message:'); ?></strong><?php echo notepad_button('document.forms[1].body') ?><?php echo utils_requiredField(); ?><br />

			<?php 
				$params['body'] = $body;
				$params['width'] = "800";
				$params['height'] = "500";
				$params['group'] = $group_id;
				plugin_hook("text_editor",$params);
				if (!$GLOBALS['editor_was_set_up']) {
					//if we don�t have any plugin for text editor, display a simple textarea edit box
					echo '<textarea name="body"  rows="10" cols="50" wrap="soft">' . $body . '</textarea>';
				}
				unset($GLOBALS['editor_was_set_up']);
			?>
				 <?php //$text_support->displayTextField('body'); ?>
			<br><br>		
<!--		<span class="selected"><?php echo _('HTML tags will display in your post as text'); ?></span> -->
				<p>
				<?php $this->LinkAttachForm();?>
				<p>
			

			<?php
			if (!session_loggedin()) {
				echo '<span class="highlight">'._('You are posting anonymously because you are not').' <a href="'.$GLOBALS['sys_urlprefix'].'/account/login.php?return_to='. urlencode(getStringFromServer('REQUEST_URI')) .'">['._('logged in').']</a></span>';
			}
			?>
			<br />
			<input type="submit" name="submit" value="<?php echo _('Post Comment'); echo ((!session_loggedin())?' '._('Anonymously'):''); ?>" /><?php
				echo ((session_loggedin()) ? '&nbsp;&nbsp;&nbsp;<input type="checkbox" value="1" name="monitor" />&nbsp;'._('Receive followups via email').'.' : ''); ?>
				</p>
			</td></tr></table></fieldset>
			</form>
			</div>
			<?php

		} elseif ($this->Forum->allowAnonymous()) {
			echo '<span class="error">';
			printf(_('You could post if you were %1$s logged in %2$s'), '<a href="'.$GLOBALS['sys_urlprefix'].'/account/login.php?return_to='.urlencode(getStringFromServer('REQUEST_URI')).' ">', '</a>');
			echo '<>';
		} elseif (!session_loggedin()) {
			echo '
			<span class="error">'.sprintf(_('Please %1$s login %2$s'), '<a href="'.$GLOBALS['sys_urlprefix'].'/account/login.php?return_to='.urlencode($REQUEST_URI).'">', '</a>').'</span><br/></p>';
		} else {
			//do nothing
		}

	}

}
/*
			$messagelink='http://'.$GLOBALS[sys_default_domain].'/forum/message.php?msg_id='.$msg_id;
			$messagesender=db_result($result,0, 'user_name');
			$messagebody=util_line_wrap(util_unconvert_htmlspecialchars(db_result($result,0, 'body')));
			$messagesys=$GLOBALS['sys_name'];
			$messagemonitor='http://'.$GLOBALS[sys_default_domain].'/forum/monitor.php?forum_id='.$forum_id;
			$body = stripcslashes(sprintf(_('
Read and respond to this message at: 
%1$s
By: %2$s 

 %3$s

______________________________________________________________________
You are receiving this email because you elected to monitor this forum.
To stop monitoring this forum, login to %4$s 
and visit: %5$s'), $messagelink, $messagesender, $messagebody, $messagesys, $messagemonitor));
			/*
			$body = "\nRead and respond to this message at: ".
				"\nhttp://$GLOBALS[sys_default_domain]/forum/message.php?msg_id=".$msg_id.
				"\nBy: " . db_result($result,0, 'user_name') .
				"\n\n" . util_line_wrap(util_unconvert_htmlspecialchars(db_result($result,0, 'body'))).
				"\n\n______________________________________________________________________".
				"\nYou are receiving this email because you elected to monitor this forum.".
				"\nTo stop monitoring this forum, login to ".$GLOBALS['sys_name']." and visit: ".
				"\nhttp://$GLOBALS[sys_default_domain]/forum/monitor.php?forum_id=$forum_id";
			* /

			$subject="[" .db_result($result,0,'unix_group_name'). " - " . db_result($result,0,'forum_name')."] ".util_unconvert_htmlspecialchars(db_result($result,0,'subject'));
			util_handle_message(array_unique($tolist),$subject,$body,$send_all_posts_to);
*/

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>