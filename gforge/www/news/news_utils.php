<?php
/**
 * GForge News Facility
 *
 * Copyright 1999-2001 (c) VA Linux Systems
 * The rest Copyright 2002-2004 (c) GForge Team
 * http://gforge.org/
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

/*
	News System
	By Tim Perdue, Sourceforge, 12/99
*/

function news_header($params) {
	global $HTML,$group_id,$news_name,$news_id,$sys_news_group,$sys_use_news;

	if (!$sys_use_news) {
		exit_disabled();
	}

	$params['toptab']='news';
	$params['group']=$group_id;
	/*
		Show horizontal links
	*/
	if ($group_id && ($group_id != $sys_news_group)) {
		site_project_header($params);
	} else {
		$HTML->header($params);
	}
	if ($group_id && ($group_id != $sys_news_group)) {
		$menu_texts=array();
		$menu_links=array();

		$menu_texts[]=_('Submit');
		$menu_links[]='/news/submit.php?group_id='.$group_id;
		if (session_loggedin()) {
			$project =& group_get_object($params['group']);
			if ($project && is_object($project) && !$project->isError()) {
				$perm =& $project->getPermission(session_get_user());
				if ($perm && is_object($perm) && !$perm->isError() && $perm->isAdmin()) {
					$menu_texts[]=_('Admin');
					$menu_links[]='/news/admin/?group_id='.$group_id;
				}
			}
		}
		echo $HTML->subMenu($menu_texts,$menu_links);
	}
}

function news_footer($params) {
	GLOBAL $HTML;
	$HTML->footer($params);
}

function news_show_latest($group_id='',$limit=10,$show_summaries=true,$allow_submit=true,$flat=false,$tail_headlines=0,$show_forum=true) {
	global $sys_news_group;
	if (!$group_id) {
		$group_id=$sys_news_group;
	}
	/*
		Show a simple list of the latest news items with a link to the forum
	*/

	if ($group_id != $sys_news_group) {
		$wclause="news_bytes.group_id='$group_id' AND news_bytes.is_approved <> '4'";
	} else {
		$wclause='news_bytes.is_approved=1';
	}

	$sql="SELECT groups.group_name,groups.unix_group_name,groups.group_id,
		groups.type_id,users.user_name,users.realname,
		news_bytes.forum_id,news_bytes.summary,news_bytes.post_date,news_bytes.details 
		FROM users,news_bytes,groups 
		WHERE $wclause 
		AND users.user_id=news_bytes.submitted_by 
		AND news_bytes.group_id=groups.group_id 
		AND groups.status='A'
		ORDER BY post_date DESC";

	$result=db_query($sql,$limit+$tail_headlines);
	$rows=db_numrows($result);
	
	$return = '';

	if (!$result || $rows < 1) {
		$return .= _('No News Items Found');
		$return .= db_error();
	} else {
		if (!$limit) $return .= '<ul>';
		for ($i=0; $i<$rows; $i++) {
			if ($show_summaries && $limit) {
				//get the first paragraph of the story
				if (strstr(db_result($result,$i,'details'),'<br/>')) {
					// the news is html, fckeditor made for example
					$arr=explode("<br/>",db_result($result,$i,'details'));
				} else {
					$arr=explode("\n",db_result($result,$i,'details'));
				}
				//if the first paragraph is short, and so are following paragraphs, add the next paragraph on
				if ((isset($arr[1]))&&(isset($arr[2]))&& (strlen($arr[0]) < 200) && (strlen($arr[1].$arr[2]) < 300) && (strlen($arr[2]) > 5)) {
					$summ_txt='<br />'. util_make_links( $arr[0].'<br />'.$arr[1].'<br />'.$arr[2] );
				} else {
					$summ_txt='<br />'. util_make_links( $arr[0] );
				}
				$proj_name=' &nbsp; - &nbsp; '.util_make_link_g (strtolower(db_result($result,$i,'unix_group_name')),db_result($result,$i,'group_id'),db_result($result,$i,'group_name'));
			} else {
				$proj_name='';
				$summ_txt='';
			}

			if (!$limit) {
				if ($show_forum) {
					$return .= '<li>'.util_make_link ('/forum/forum.php?forum_id='. db_result($result,$i,'forum_id'),'<strong>'. db_result($result,$i,'summary') . '</strong>');
				} else {
					$return .= '<li><strong>'. db_result($result,$i,'summary') . '</strong>';
				}
				$return .= ' &nbsp; <em>'. date(_('Y-m-d H:i'),db_result($result,$i,'post_date')).'</em><br /></li>';
			} else {
				if ($show_forum) {
					$return .= util_make_link ('/forum/forum.php?forum_id='. db_result($result,$i,'forum_id'),'<strong>'. db_result($result,$i,'summary').'</strong>');
				} else {
					$return .= '
					<strong>'. db_result($result,$i,'summary') . '</strong>';
				}
				if (!$flat) {
					$return .= '
					<br />&nbsp;';
				}
				$return .= '&nbsp;&nbsp;&nbsp;<em>'. db_result($result,$i,'realname') .' - '.
					date(_('Y-m-d H:i'),db_result($result,$i,'post_date')). '</em>' .
					$proj_name . $summ_txt;

				$sql="SELECT total FROM forum_group_list_vw WHERE group_forum_id='" . db_result($result,$i,'forum_id') . "'";
				$res2 = db_query($sql);
				$num_comments = db_result($res2,0,'total');

				if (!$num_comments) {
					$num_comments = '0';
				}

				if ($num_comments <= 1) {
					$comments_txt = _('Comment');
				} else {
					$comments_txt = _('Comments');
				}

				if ($show_forum){
					$return .= '<div align="center">(' . $num_comments .' '. $comments_txt . ') '
					.util_make_link ('/forum/forum.php?forum_id='. db_result($result,$i,'forum_id'),'[' . _('Read&nbsp;More/Comment') . ']').'</div><hr width="100%" size="1" />';
				} else {
					$return .= '<hr width="100%" size="1" />';
				}
			}

			if ($limit==1 && $tail_headlines) {
				$return .= "<ul>\n";
			}
			if ($limit) {
				$limit--;
			}
		}
		if ($tail_headlines){
		  if (!$limit) $return .= '</ul>';
			$return .= '<hr width="100%" size="1" />'."\n";
		}
		if ($group_id != $sys_news_group) {
			$archive_url=util_make_url ('/news/?group_id='.$group_id);
		} else {
			$archive_url=util_make_url ('/news/');
		}

		if ($show_forum) {
			$return .= '<div align="center">'
				.'<a href="'.$archive_url.'">[' . _('News archive') . ']</a></div>';
		} else {
			$return .= '<div align="center">...</div>';
		}
	}

	if ($allow_submit && $group_id != $sys_news_group) {
		if(!$result || $rows < 1) {
			$return .= '<hr width="100%" size="1" />';
		}
		//you can only submit news from a project now
		//you used to be able to submit general news
		$return .= '<div align="center">'
		.util_make_link ('/news/submit.php?group_id='.$group_id,'['._('Submit News').']').'</div>';
	}

	return $return;
}

function news_foundry_latest($group_id=0,$limit=5,$show_summaries=true) {
	/*
		Show a the latest news for a portal
	*/

	$sql="SELECT groups.group_name,groups.unix_group_name,groups.group_id,
		users.user_name,users.realname,news_bytes.forum_id,
		news_bytes.summary,news_bytes.post_date,news_bytes.details 
		FROM users,news_bytes,groups,foundry_news 
		WHERE foundry_news.foundry_id='$group_id' 
		AND users.user_id=news_bytes.submitted_by 
		AND foundry_news.news_id=news_bytes.id 
		AND news_bytes.group_id=groups.group_id 
		AND foundry_news.is_approved=1 
		ORDER BY news_bytes.post_date DESC";

	$result=db_query($sql,$limit);
	$rows=db_numrows($result);

	if (!$result || $rows < 1) {
		$return .= '<h3>' . _('No News Items Found') . '</h3>';
		$return .= db_error();
	} else {
		for ($i=0; $i<$rows; $i++) {
			if ($show_summaries) {
				//get the first paragraph of the story
				$arr=explode("\n",db_result($result,$i,'details'));
				if ((isset($arr[1]))&&(isset($arr[2]))&&(strlen($arr[0]) < 200) && (strlen($arr[1].$arr[2]) < 300) && (strlen($arr[2]) > 5)) {
					$summ_txt=util_make_links( $arr[0].'<br />'.$arr[1].'<br />'.$arr[2] );
				} else {
					$summ_txt=util_make_links( $arr[0] );
				}

				//show the project name
				$proj_name=' &nbsp; - &nbsp; '.util_make_link_g (strtolower(db_result($result,$i,'unix_group_name')),db_result($result,$i,'group_id'),db_result($result,$i,'group_name'));
			} else {
				$proj_name='';
				$summ_txt='';
			}
			$return .= util_make_link ('/forum/forum.php?forum_id='. db_result($result,$i,'forum_id'),'<strong>'. db_result($result,$i,'summary') . '</strong>')
				.'<br /><em>'. db_result($result,$i,'realname') .' - '.
					date(_('Y-m-d H:i'),db_result($result,$i,'post_date')) . $proj_name . '</em>
				'. $summ_txt .'<hr width="100%" size="1" />';
		}
	}
	return $return;
}

function get_news_name($id) {
	/*
		Takes an ID and returns the corresponding forum name
	*/
	$sql="SELECT summary FROM news_bytes WHERE id='$id'";
	$result=db_query($sql);
	if (!$result || db_numrows($result) < 1) {
		return "Not Found";
	} else {
		return db_result($result, 0, 'summary');
	}
}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
