<?php
/**
 * GForge Tracker Facility
 *
 * Copyright 2002 GForge, LLC
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  US
 */

require_once('common/include/Error.class.php');
require_once('common/tracker/ArtifactType.class.php');

class ArtifactTypeFactory extends Error {

	/**
	 * The Group object.
	 *
	 * @var	 object  $Group.
	 */
	var $Group;

	/**
	 * The ArtifactTypes array.
	 *
	 * @var	 array	ArtifactTypes.
	 */
	var $ArtifactTypes;

	/**
	 * The data type (DAO)
	 *
  	 * @var 	string dataType
	 */
	var $dataType;

	/**
	 *  Constructor.
	 *
	 *	@param	object	The Group object to which this ArtifactTypeFactory is associated
	 *	@return	boolean	success.
	 */
	function ArtifactTypeFactory(&$Group) {
		$this->Error();
		if (!$Group || !is_object($Group)) {
			$this->setError('ArtifactTypeFactory:: No Valid Group Object');
			return false;
		}
		if ($Group->isError()) {
			$this->setError('ArtifactTypeFactory:: '.$Group->getErrorMessage());
			return false;
		}
		$this->Group =& $Group;

		return true;
	}

	/**
	 *	getGroup - get the Group object this ArtifactType is associated with.
	 *
	 *	@return	object	The Group object.
	 */
	function &getGroup() {
		return $this->Group;
	}

	/**
	 *	getArtifactTypes - return an array of ArtifactType objects.
	 *
	 *	@return	array	The array of ArtifactType objects.
	 */
	function &getArtifactTypes() {
		if ($this->ArtifactTypes) {
			return $this->ArtifactTypes;
		}
		if (session_loggedin()) {
			$perm =& $this->Group->getPermission( session_get_user() );
			if (!$perm || !is_object($perm) || !$perm->isMember()) {
				$public_flag='=1';
			} else {
				$public_flag='<3';
				if ($perm->isArtifactAdmin()) {
					$exists='';
				} else {
					$exists=" AND group_artifact_ID IN (SELECT group_artifact_ID
					FROM artifact_perm
					WHERE perm_level >= 0 AND group_artifact_id=artifact_group_list_vw.group_artifact_id
					AND user_id='".user_getid()."') ";
				}
			}
		} else {
			$public_flag='=1';
		}

		$sql="SELECT * FROM artifact_group_list_vw
			WHERE group_id='". $this->Group->getID() ."'
			AND is_public $public_flag
			$exists
			ORDER BY group_artifact_id ASC";

		$result = db_query ($sql);

		$rows = db_numrows($result);

		if (!$result || $rows < 1) {
			$this->setError('None Found '.db_error());
			return false;
		} else {
			while ($arr =& db_fetch_array($result)) {
				$artifactType = new ArtifactType($this->Group, $arr['group_artifact_id'], $arr);
				if($artifactType->isError()) {
					$this->setError($artifactType->getErrorMessage());
				} else {
					$this->ArtifactTypes[] = $artifactType;
				}
			}
		}
		return $this->ArtifactTypes;
	}

	/**
	 * getPublicFlag - a utility method to load up the current user's permissions
 	 *
	 * @return 	string 	The public_flag field to plug into a SQL string
	 */	
	function &getPublicFlag() {
		return $public_flag;
	}

}

?>