<?php

/**
 * MediaWikiPlugin Class
 *
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
 */

class MediaWikiPlugin extends Plugin {
	function MediaWikiPlugin () {
		$this->Plugin() ;
		$this->name = "mediawiki" ;
		$this->text = "Mediawiki" ; // To show in the tabs, use...
		$this->hooks[] = "user_personal_links";//to make a link to the user's personal part of the plugin
		$this->hooks[] = "usermenu" ;
		// $this->hooks[] = "outermenu" ;
		$this->hooks[] = "groupmenu" ;	// To put into the project tabs
		$this->hooks[] = "groupisactivecheckbox" ; // The "use ..." checkbox in editgroupinfo
		$this->hooks[] = "groupisactivecheckboxpost" ; //
		$this->hooks[] = "userisactivecheckbox" ; // The "use ..." checkbox in user account
		$this->hooks[] = "userisactivecheckboxpost" ; //
		$this->hooks[] = "project_admin_plugins"; // to show up in the admin page for group
		$this->hooks[] = "session_before_login" ; // to register GF users in the MW database
	}

	function CallHook ($hookname, $params) {
		global $use_mediawikiplugin,$G_SESSION,$HTML;

		if (isset($params['group_id'])) {
			$group_id=$params['group_id'];
		} elseif (isset($params['group'])) {
			$group_id=$params['group'];
		} else {
			$group_id=null;
		}
		if ($hookname == "outermenu") {
			$params['TITLES'][] = 'MediaWiki';
			$params['DIRS'][] = '/mediawiki';
		} elseif ($hookname == "usermenu") {
			$text = $this->text; // this is what shows in the tab
			if ($G_SESSION->usesPlugin("mediawiki")) {
				echo ' | ' . $HTML->PrintSubMenu (array ($text),
						  array ('/mediawiki/index.php?title=User:' . $G_SESSION->getUnixName() ));				
			}
		} elseif ($hookname == "groupmenu") {
			$project = &group_get_object($group_id);
			if (!$project || !is_object($project)) {
				return;
			}
			if ($project->isError()) {
				return;
			}
			if (!$project->isProject()) {
				return;
			}
			if ( $project->usesPlugin ( $this->name ) ) {
				$params['TITLES'][]=$this->text;
				$params['DIRS'][]='/plugins/mediawiki/index.php?group_id=' . $project->getID(); 
			}
			(($params['toptab'] == $this->name) ? $params['selected']=(count($params['TITLES'])-1) : '' );
		} elseif ($hookname == "groupisactivecheckbox") {
			//Check if the group is active
			// this code creates the checkbox in the project edit public info page to activate/deactivate the plugin
			$group = &group_get_object($group_id);
			echo "<tr>";
			echo "<td>";
			echo ' <input type="CHECKBOX" name="use_mediawikiplugin" value="1" ';
			// CHECKED OR UNCHECKED?
			if ( $group->usesPlugin ( $this->name ) ) {
				echo "CHECKED";
			}
			echo "><br/>";
			echo "</td>";
			echo "<td>";
			echo "<strong>Use ".$this->text." Plugin</strong>";
			echo "</td>";
			echo "</tr>";
		} elseif ($hookname == "groupisactivecheckboxpost") {
			// this code actually activates/deactivates the plugin after the form was submitted in the project edit public info page
			$group = &group_get_object($group_id);
			$use_mediawikiplugin = getStringFromRequest('use_mediawikiplugin');
			if ( $use_mediawikiplugin == 1 ) {
				$group->setPluginUse ( $this->name );
			} else {
				$group->setPluginUse ( $this->name, false );
			}
		} elseif ($hookname == "userisactivecheckbox") {
			//check if user is active
			// this code creates the checkbox in the user account manteinance page to activate/deactivate the plugin
			$user = $params['user'];
			echo "<tr>";
			echo "<td>";
			echo ' <input type="CHECKBOX" name="use_mediawikiplugin" value="1" ';
			// CHECKED OR UNCHECKED?
			if ( $user->usesPlugin ( $this->name ) ) {
				echo "CHECKED";
 			}
			echo ">    Use ".$this->text." Plugin";
			echo "</td>";
			echo "</tr>";
		} elseif ($hookname == "userisactivecheckboxpost") {
			// this code actually activates/deactivates the plugin after the form was submitted in the user account manteinance page
			$user = $params['user'];
			$use_mediawikiplugin = getStringFromRequest('use_mediawikiplugin');
			if ( $use_mediawikiplugin == 1 ) {
				$user->setPluginUse ( $this->name );
			} else {
				$user->setPluginUse ( $this->name, false );
			}
			echo "<tr>";
			echo "<td>";
			echo ' <input type="CHECKBOX" name="use_mediawikiplugin" value="1" ';
			// CHECKED OR UNCHECKED?
			if ( $user->usesPlugin ( $this->name ) ) {
				echo "CHECKED";
			}
			echo ">    Use ".$this->text." Plugin";
			echo "</td>";
			echo "</tr>";
		} elseif ($hookname == "user_personal_links") {
			// this displays the link in the user's profile page to it's personal MediaWiki (if you want other sto access it, youll have to change the permissions in the index.php
			$userid = $params['user_id'];
			$user = user_get_object($userid);
			$text = $params['text'];
			//check if the user has the plugin activated
			if ($user->usesPlugin($this->name)) {
				echo '	<p>' ;
				echo util_make_link ("/plugins/helloworld/index.php?id=$userid&type=user&pluginname=".$this->name,
						     _('View Personal MediaWiki')
					);
				echo '</p>';
			}
		} elseif ($hookname == "project_admin_plugins") {
			// this displays the link in the project admin options page to it's  MediaWiki administration
			$group_id = $params['group_id'];
			$group = &group_get_object($group_id);
			if ( $group->usesPlugin ( $this->name ) ) {
				echo util_make_link ("/plugins/projects_hierarchy/index.php?id=".$group->getID().'&type=admin&pluginname='.$this->name,
						     _('View the MediaWiki Administration'));
				echo '</p>';
			}
		}												    
		elseif ($hookname == "session_before_login") {
			$loginname = $params['loginname'] ;
			$passwd = $params['passwd'] ;
			if (! session_login_valid_dbonly ($loginname, $passwd, false)) {
				return ;
			}
			$u = user_get_object_by_name ($loginname) ;
			
			define ('MEDIAWIKI', true);
			if (is_file('/var/lib/mediawiki/LocalSettings.php')){
                        	require_once ('/var/lib/mediawiki/LocalSettings.php');
			} elseif (is_file('/var/lib/mediawiki1.10/LocalSettings.php')){
                        	require_once ('/var/lib/mediawiki1.10/LocalSettings.php');
			} else {
				return 1;
			}
			if (is_dir('/usr/share/mediawiki')){
				$mw_share_path="/usr/share/mediawiki";
			} elseif (is_dir('/usr/share/mediawiki1.10')){
				$mw_share_path="/usr/share/mediawiki1.10";
			} else {
				return 1;
			}
                        require_once ($mw_share_path.'/includes/Defines.php');
                        require_once ($mw_share_path.'/includes/Exception.php');
                        require_once ($mw_share_path.'/includes/GlobalFunctions.php');
                        require_once ($mw_share_path.'/StartProfiler.php');
                        require_once ($mw_share_path.'/includes/Database.php');

                        $mwdb = new Database() ;
                        $mwdb->open($wgDBserver, $wgDBuser, $wgDBpassword, $wgDBname) ;
                        $sql = "select count(*) from user where user_name=?";
                        $res = $mwdb->safeQuery ($sql, ucfirst($loginname));
			$row = $mwdb->fetchRow ($res) ;
			if ($row[0] == 1) {
				$sql = "update user set user_password=?, user_email=?, user_real_name=? where user_name=?" ;
				$res = $mwdb->safeQuery ($sql, md5($passwd), $u->getEmail(), $u->getRealName(), array(ucfirst($loginname))) ;
			} else {
				$sql = "insert into user (user_name, user_real_name, user_password, user_email, user_options) values (?, ?, ?, ?, ?)" ;
				$res = $mwdb->safeQuery ($sql, array(ucfirst($loginname), $u->getRealName(), md5($passwd), $u->getEmail(), "skin=gforge\ncols=80\nrows=25")) ;
			}
		} 

		elseif ($hookname == "blahblahblah") {
			// ...
		} 
	}
}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
