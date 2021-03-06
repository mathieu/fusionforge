<?php
/**
 * FusionForge permissions
 *
 * Copyright 1999-2001, VA Linux Systems, Inc.
 * Copyright 2002-2004, GForge, LLC
 *
 * This file is part of FusionForge.
 *
 * FusionForge is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published
 * by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 * 
 * FusionForge is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with FusionForge; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA
 */

require_once $gfcommon.'include/Error.class.php';

$PERMISSION_OBJ=array();

/**
 * permission_get_object() - Get permission objects
 *
 * permission_get_object is useful so you can pool Permission objects/save database queries
 * You should always use this instead of instantiating the object directly 
 *
 * @param		object	The Group in question
 * @param		object	The User needing Permission
 * @return a Permission or false on failure
 *
 */
function &permission_get_object(&$_Group, &$_User) {
	//create a common set of Permission objects
	//saves a little wear on the database
	
	global $PERMISSION_OBJ;

	if (is_object($_Group)) {
		$group_id = $_Group->getID();
	} else {
		$group_id = 0;
	}

	if (is_object($_User)) {
		$user_id = $_User->getID();
	} else {
		//invalid object, probably from user not being logged in
		$user_id = 0;
	}

	if (!isset($PERMISSION_OBJ["_".$group_id."_".$user_id])) {
		$PERMISSION_OBJ["_".$group_id."_".$user_id]= new Permission($_Group, $_User);
	}
	return $PERMISSION_OBJ["_".$group_id."_".$user_id];
}

class Permission extends Error {
	/**
	 * Associative array of data from db.
	 *
	 * @var array $data_array.
	 */
	var $data_array;

	/**
	 * The Group object.
	 *
	 * @var object $Group.
	 */
	var $Group;

	/**
	 * The User object.
	 *
	 * @var object $User.
	 */
	var $User;

	/**
	 * Whether the user is an admin/super user of this project.
	 *
	 * @var bool $is_admin.
	 */
	var $is_admin=false;

	/**
	 * Whether the user is an admin/super user of the entire site.
	 *
	 * @var bool $is_site_admin.
	 */
	var $is_site_admin;

	/**
	 *	Constructor for this object.
	 *
	 *	@param	object	Group Object required.
	 *	@param	object	User Object required.
	 *	
	 */
	function Permission (&$_Group, &$_User) {
		if (!$_Group || !is_object($_Group)) {
			$this->setError('No Valid Group Object');
			return false;
		}
		if ($_Group->isError()) {
			$this->setError('Permission: '.$_Group->getErrorMessage());
			return false;
		}
		$this->Group =& $_Group;

		if (!$_User || !is_object($_User)) {
			$this->setError('No Valid User Object');
			return false;
		}   
		if ($_User->isError()) {
			$this->setError('Permission: '.$_User->getErrorMessage());
			return false;
		}   
		$this->User =& $_User;

		if (!$this->fetchData()) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 *  fetchData - fetch the data for this Permission from the database.
	 *
	 *  @return	boolean success.
	 *	@access private.
	 */
	function fetchData() {
		$res=db_query("SELECT * FROM user_group 
			WHERE user_id='". $this->User->getID() ."' 
			AND group_id='". $this->Group->getID() ."'");
		if (!$res || db_numrows($res) < 1) {
			$this->setError('Permission: User Not Found');

			if ($this->setUpSuperUser()) {
				return true;
			}
		} else {
			$this->data_array = db_fetch_array($res);
			if (trim($this->data_array['admin_flags']) == 'A') {
				$this->is_admin=true;
			} else {
				$this->setUpSuperUser();
			}
			db_free_result($res);
			return true;
		}
	}

	/**
	 *	setUpSuperUser - check to see if this User is a site super-user.
	 *
	 *	@return	boolean	is_super_user.
	 *	@access private
	 */
	function setUpSuperUser() {
		//
		//  see if they are a site super-user
		//  if not a member of this group
		//
		if ($this->isSuperUser()) {
			$this->clearError();
			$this->is_admin = true;
			return true;
		}

		return false;
	}

	/**
	 *	getUser - get the User object this Permission is associated with.
	 *
	 *	@return	object	The User object.
	 */
	function &getUser() {
		return $this->User;
	}

	/**
	 *	getGroup - get the Group object this Permission is associated with.
	 *
	 *	@return the Group object.
	 */
	function &getGroup() {
		return $this->Group;
	}

	/**
	 *  isSuperUser - whether the current user has site admin privilege.
	 *
	 *  @return	boolean	is_super_user.
	 */
	function isSuperUser() {
		if (isset($this->is_site_admin)) {
			return $this->is_site_admin;
		}

		$res = db_query("SELECT count(*) AS count FROM user_group
			WHERE user_id='". $this->User->getID() ."'
			AND group_id='1'
			AND admin_flags='A'");
		$row_count = db_fetch_array($res);
		$this->is_site_admin = $res && $row_count['count'] > 0;
		db_free_result($res);

		return $this->is_site_admin;
	}

	/**
	 *  isForumAdmin - whether the current user has form admin perms.
	 *
	 *  @return	boolean	is_forum_admin.
	 */
	function isForumAdmin() {
		return $this->isMember('forum_flags',2);
	}

	/**
	 *  isDocEditor - whether the current user has form doc editor perms.
	 *
	 *  @return	boolean	is_doc_editor.
	 */
	function isDocEditor() {
		return $this->isMember('doc_flags',1);
	}

	/**
	 *  isReleaseTechnician - whether the current user has FRS admin perms.
	 *
	 *  @return	boolean	is_release_technician.
	 */
	function isReleaseTechnician() {
		return $this->isMember('release_flags',1);
	}

	/**
	 *  isArtifactAdmin - whether the current user has artifact admin perms.
	 *
	 *  @return	boolean	is_artifact_admin.
	 */
	function isArtifactAdmin() {
		return $this->isMember('artifact_flags',2);
	}

	/**
	 *  isPMAdmin - whether the current user has Task Manager admin perms.
	 *
	 *  @return	boolean	is_projman_admin.
	 */
	function isPMAdmin() {
		return $this->isMember('project_flags',2);
	}

	/**
	 *  isMember - Simple test to see if the current user is a member of this project.
	 *
	 *  Can optionally pass in vars to test other permissions.
	 *
	 *  @param string	The field to check.
	 *  @param int		The value that $field should have.
	 *  @return	boolean	is_member.
	 */
	function isMember($field='user_id',$value='-1') {
		if ($this->isAdmin()) {
			//admins are tested first so that super-users can return true
			//and admins of a project should always have full privileges 
			//on their project
			return true;
		} else {
			$arr =& $this->getPermData();
			if ($arr[$field] >= $value) {
				return true; 
			} else {
				return false;
			}
		}
	}

	/**
	 *  isAdmin - User is an admin of the project or admin of the entire site.
	 *
	 *  @return	boolean	is_admin.
	 */
	function isAdmin() {
		return $this->is_admin;
	}

	/**
	 *	getPermData - returns the assocative array from the db.
	 *
	 *	@return array The array of data.
	 *	@access private.
	 */
	function &getPermData() {
		return $this->data_array;
	}

	/**
	 *	isCVSReader - checks the cvs_flags field in user_group table.
	 *
	 *	@return	boolean	cvs_flags
	 */
	function isCVSReader() {
		return $this->isMember('cvs_flags',0);
	}
	
	/**
	 *      isCVSWriter - checks if the user has CVS write access.
	 *
	 *      @return boolean cvs_flags
	 */
	function isCVSWriter() {
		return $this->isMember('cvs_flags',1);
	}
}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
