<?php
/**
 * ArtifactMessage.class.php - Class to handle artifact messages.
 *
 * Copyright 2004 (c) GForge, LLC
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

class ArtifactMessage extends Error {

	/** 
	 * The artifact object.
	 *
	 * @var		object	$Artifact.
	 */
	var $Artifact; //object

	/**
	 * Array of artifact data.
	 *
	 * @var		array	$data_array.
	 */
	var $data_array;

	/**
	 *  ArtifactMessage - constructor.
	 *
	 *	@param	object	Artifact object.
	 *  @param	array	(all fields from artifact_history_user_vw) OR id from database.
	 *  @return	boolean	success.
	 */
	function ArtifactMessage(&$Artifact, $data=false) {
		$this->Error(); 

		//was Artifact legit?
		if (!$Artifact || !is_object($Artifact)) {
			$this->setError('ArtifactMessage: No Valid Artifact');
			return false;
		}
		//did Artifact have an error?
		if ($Artifact->isError()) {
			$this->setError('ArtifactMessage: '.$Artifact->getErrorMessage());
			return false;
		}
		$this->Artifact =& $Artifact;

		if ($data) {
			if (is_array($data)) {
				$this->data_array =& $data;
				return true;
			} else {
				if (!$this->fetchData($data)) {
					return false;
				} else {
					return true;
				}
			}
		}
	}

	/**
	 *	create - create a new item in the database.
	 *
	 *	@param	string	Body.
	 *	@param	string	email of submitter (obsolete?).
	 *  @return id on success / false on failure.
	 */
	function create($body,$by=false) {
		if (!$body) {
			$this->setMissingParamsError();
			return false;
		}

		if (session_loggedin()) {
			$user_id=user_getid();
			$user =& user_get_object($user_id);
			if (!$user || !is_object($user)) {
				$this->setError('ERROR - Logged In User Bug Could Not Get User Object');
				return false;
			}
			$body=_('Logged In: YES')." \nuser_id=$user_id\n\n".$body;

			//  we'll store this email even though it will likely never be used -
			//  since we have their correct user_id, we can join the USERS table to get email
			$by=$user->getEmail();
		} else {
			$body=_('Logged In: NO')." \n\n".$body;
			$user_id=100;
			if (!$by || !validate_email($by)) {
				$this->setMissingParamsError();
				return false;
			}
		}

		$sql="insert into artifact_message (artifact_id,submitted_by,from_email,adddate,body) 
			VALUES ('". $this->Artifact->getID() ."','$user_id','$by','". time() ."','". htmlspecialchars($body). "')";
		$res = db_query($sql);

		if (!$res) {
			$this->setError(db_error());
			return false;
		} else {
			$id=db_insertid($res,'artifact_message','id');
		}

		//
		//	Now set up our internal data structures
		//
		if (!$this->fetchData($id)) {
			return false;
		}
		return $id;
	}

	/**
	 *	fetchData - re-fetch the data for this ArtifactMessage from the database.
	 *
	 *	@param	int		ID of the category.
	 *	@return	boolean	success.
	 */
	function fetchData($id) {
		$res=db_query("SELECT * FROM artifact_message_user_vw WHERE id='$id'");
		if (!$res || db_numrows($res) < 1) {
			$this->setError('ArtifactMessage: Invalid ArtifactMessage ID');
			return false;
		}
		$this->data_array =& db_fetch_array($res);
		db_free_result($res);
		return true;
	}

	/**
	 *	getArtifact - get the Artifact Object this ArtifactMessage is associated with.
	 *
	 *	@return object	Artifact.
	 */
	function &getArtifact() {
		return $this->Artifact;
	}
	
	/**
	 *	getID - get this ArtifactMessage's ID.
	 *
	 *	@return	int	The id #.
	 */
	function getID() {
		return $this->data_array['id'];
	}

	/**
	 *	getBody - get the message body.
	 *
	 *	@return	string	The message body.
	 */
	function getBody() {
		return $this->data_array['body'];
	}

	/**
	 *	getAddDate - get the date this message was added.
	 *
	 *	@return int adddate.
	 */
	function getAddDate() {
		return $this->data_array['addate'];
	}

	/**
	 *	getUserID - get the ID of the person who posted this.
	 *
	 *	@return int user_id.
	 */
	function getUserID() {
		return $this->data_array['user_id'];
	}

}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
