<?php
/**
 * SOAP Tracker Include - this file contains wrapper functions for the SOAP interface
 *
 * Copyright 2004 (c) GForge, LLC
 * http://gforge.org
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

require_once('common/include/Error.class');
require_once('common/tracker/ArtifactType.class');
require_once('common/tracker/Artifact.class');
require_once('common/tracker/ArtifactFactory.class');
require_once('common/tracker/ArtifactTypeFactory.class');
require_once('common/tracker/Artifacts.class');
require_once('common/tracker/ArtifactResolution.class');
require_once('common/tracker/ArtifactCategory.class');
require_once('common/tracker/ArtifactGroup.class');
require_once('common/tracker/ArtifactFile.class');
require_once('common/tracker/ArtifactMessage.class');

//
//	ArtifactType
//
$server->wsdl->addComplexType(
	'ArtifactType',
	'complexType',
	'struct',
	'sequence',
	'',
	array(
	'group_artifact_id' => array('name'=>'group_artifact_id', 'type' => 'xsd:int'),
	'group_id' => array('name'=>'group_id', 'type' => 'xsd:int'),
	'name' => array('name'=>'name', 'type' => 'xsd:string'),
	'description' => array('name'=>'description', 'type' => 'xsd:string'),
	'is_public' => array('name'=>'is_public', 'type' => 'xsd:int'),
	'allow_anon' => array('name'=>'allow_anon', 'type' => 'xsd:int'),
	'due_period' => array('name'=>'due_period', 'type' => 'xsd:int'),
	'use_resolution' => array('name'=>'use_resolution', 'type' => 'xsd:int'),
	'datatype' => array('name'=>'datatype', 'type' => 'xsd:int'),
	'status_timeout' => array('name'=>'status_timeout', 'type' => 'xsd:int')
	)
);

$server->wsdl->addComplexType(
	'ArrayOfArtifactType',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:ArtifactType[]')),
	'tns:ArtifactType');

$server->register(
	'getArtifactTypes',
	array('session_ser'=>'xsd:string','group_id'=>'xsd:int'),
	array('getArtifactTypesResponse'=>'tns:ArrayOfArtifactType'),
	$uri,
	$uri.'#getArtifactTypes','rpc','encoded'
);
//
//	Artifacts
//
$server->wsdl->addComplexType(
	'Artifact',
	'complexType',
	'struct',
	'sequence',
	'',
	array(
	'artifact_id' => array('name'=>'artifact_id', 'type' => 'xsd:int'),
	'group_artifact_id' => array('name'=>'group_artifact_id', 'type' => 'xsd:int'),
	'status_id' => array('name'=>'status_id', 'type' => 'xsd:int'),
	'category_id' => array('name'=>'category_id', 'type' => 'xsd:int'),
	'artifact_group_id' => array('name'=>'artifact_group_id', 'type' => 'xsd:int'),
	'resolution_id' => array('name'=>'resolution_id', 'type' => 'xsd:int'),
	'priority' => array('name'=>'priority', 'type' => 'xsd:int'),
	'submitted_by' => array('name'=>'submitted_by', 'type' => 'xsd:int'),
	'assigned_to' => array('name'=>'assigned_to', 'type' => 'xsd:int'),
	'open_date' => array('name'=>'open_date', 'type' => 'xsd:int'),
	'close_date' => array('name'=>'close_date', 'type' => 'xsd:int'),
	'summary' => array('name'=>'summary', 'type' => 'xsd:string'),
	'details' => array('name'=>'details', 'type' => 'xsd:string')
	)
);

$server->wsdl->addComplexType(
	'ArrayOfArtifact',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Artifact[]')),
	'tns:Artifact'
);

//getArtifact
$server->register(
	'getArtifacts',
	array(
		'session_ser'=>'xsd:string',
		'group_id'=>'xsd:int',
		'group_artifact_id'=>'xsd:int',
		'assigned_to'=>'xsd:int',
		'status'=>'xsd:int',
		'category'=>'xsd:int',
		'group'=>'xsd:int'),
	array('getArtifactsResponse'=>'tns:ArrayOfArtifact'),
	$uri,$uri.'#getArtifacts','rpc','encoded');

//addArtifact
$server->register(
	'addArtifact',
	array(
		'session_ser'=>'xsd:string',
		'group_id'=>'xsd:int',
		'group_artifact_id'=>'xsd:int',
		'status_id'=>'xsd:int',
		'category_id'=>'xsd:int',
		'artifact_group_id'=>'xsd:int',
		'resolution_id'=>'xsd:int',
		'priority'=>'xsd:int',
		'assigned_to'=>'xsd:int',
		'summary'=>'xsd:string',
		'details'=>'xsd:string'
	),
	array('addArtifactResponse'=>'xsd:int'),
	$uri,$uri.'#addArtifact','rpc','encoded'
);

//updateArtifact
$server->register(
	'updateArtifact',
	array(
		'session_ser'=>'xsd:string',
		'group_id'=>'xsd:int',
		'group_artifact_id'=>'xsd:int',
		'artifact_id'=>'xsd:int',
		'status_id'=>'xsd:int',
		'category_id'=>'xsd:int',
		'artifact_group_id'=>'xsd:int',
		'resolution_id'=>'xsd:int',
		'priority'=>'xsd:int',
		'assigned_to'=>'xsd:int',
		'summary'=>'xsd:string',
		'details'=>'xsd:string'
	),
	array('addArtifactResponse'=>'xsd:int'),
	$uri,$uri.'#updateArtifact','rpc','encoded'
);
/*
$session_ser,$group_id,$group_artifact_id,$artifact_id,$status_id,$category_id,
    $artifact_group_id,$resolution_id,$priority,$assigned_to,$summary,$details
*/
//
//	ArtifactCategory
//
$server->wsdl->addComplexType(
	'ArtifactCategory',
	'complexType',
	'struct',
	'sequence',
	'',
	array(
	'id' => array('name'=>'id', 'type' => 'xsd:int'),
	'group_artifact_id' => array('name'=>'group_artifact_id', 'type' => 'xsd:int'),
	'category_name' => array('name'=>'category_name', 'type' => 'xsd:string'),
	'auto_assign_to' => array('name'=>'auto_assign_to', 'type' => 'xsd:int')
	)
);

$server->wsdl->addComplexType(
	'ArrayOfArtifactCategory',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:ArtifactCategory[]')),
	'tns:ArtifactCategory'
);

$server->register(
	'getArtifactCategories',
	array('session_ser'=>'xsd:string','group_id'=>'xsd:int','group_artifact_id'=>'xsd:int'),
	array('getArtifactCategoriesResponse'=>'tns:ArrayOfArtifactCategory'),
	$uri,$uri.'#getArtifactCategories','rpc','encoded'
);

//
//	ArtifactGroup
//
$server->wsdl->addComplexType(
	'ArtifactGroup',
	'complexType',
	'struct',
	'sequence',
	'',
	array(
	'id' => array('name'=>'id', 'type' => 'xsd:int'),
	'group_artifact_id' => array('name'=>'group_artifact_id', 'type' => 'xsd:int'),
	'group_name' => array('name'=>'group_name', 'type' => 'xsd:string')
	)
);

$server->wsdl->addComplexType(
	'ArrayOfArtifactGroup',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:ArtifactGroup[]')),
	'tns:ArtifactGroup'
);

$server->register(
	'getArtifactGroups',
	array('session_ser'=>'xsd:string','group_id'=>'xsd:int','group_artifact_id'=>'xsd:int'),
	array('getArtifactGroupsResponse'=>'tns:ArrayOfArtifactGroup'),
	$uri,$uri.'#getArtifactGroups','rpc','encoded'
);

//
//	ArtifactResolution
//
$server->wsdl->addComplexType(
	'ArtifactResolution',
	'complexType',
	'struct',
	'sequence',
	'',
	array(
	'id' => array('name'=>'id', 'type' => 'xsd:int'),
	'group_artifact_id' => array('name'=>'group_artifact_id', 'type' => 'xsd:int'),
	'resolution_name' => array('name'=>'resolution_name', 'type' => 'xsd:string')
	)
);

$server->wsdl->addComplexType(
	'ArrayOfArtifactResolution',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:ArtifactResolution[]')),
	'tns:ArtifactResolution');

$server->register(
	'getArtifactResolutions',
	array('session_ser'=>'xsd:string','group_id'=>'xsd:int','group_artifact_id'=>'xsd:int'),
	array('getArtifactResolutionsResponse'=>'tns:ArrayOfArtifactResolution'),
	$uri,$uri.'#getArtifactResolutions','rpc','encoded'
);

//
//	ArtifactFile
//
$server->wsdl->addComplexType(
	'ArtifactFile',
	'complexType',
	'struct',
	'sequence',
	'',
	array(
	'id' => array('name'=>'id', 'type' => 'xsd:int'),
	'artifact_id' => array('name'=>'artifact_id', 'type' => 'xsd:int'),
	'description' => array('name'=>'description', 'type' => 'xsd:string'),
	'filesize' => array('name'=>'filesize', 'type' => 'xsd:int'),
	'filetype' => array('name'=>'filetype', 'type' => 'xsd:string'),
	'adddate' => array('name'=>'adddate', 'type' => 'xsd:int'),
	'submitted_by' => array('name'=>'submitted_by', 'type' => 'xsd:int')
	)
);

$server->wsdl->addComplexType(
	'ArrayOfArtifactFile',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:ArtifactFile[]')),
	'tns:ArtifactFile'
);

$server->register(
	'getArtifactFiles',
	array('session_ser'=>'xsd:string','group_id'=>'xsd:int','group_artifact_id'=>'xsd:int','artifact_id'=>'xsd:int'),
	array('getArtifactFilesResponse'=>'tns:ArrayOfArtifactFile'),
	$uri,$uri.'#getArtifactFiles','rpc','encoded'
);

//TODO - FINISH ADD FILE
$server->register(
	'addArtifactFile',
	array('session_ser'=>'xsd:string','group_id'=>'xsd:int','group_artifact_id'=>'xsd:int','artifact_id'=>'xsd:int','file_path'=>'xsd:string','description'=>'xsd:string','filename'=>'xsd:string','filetype'=>'xsd:string'),
	array('addArtifactFileResponse'=>'xsd:int'),
	$uri,$uri.'#addArtifactFile','rpc','encoded'
);


//
//	ArtifactMessage
//
$server->wsdl->addComplexType(
	'ArtifactMessage',
	'complexType',
	'struct',
	'sequence',
	'',
	array(
	'id' => array('name'=>'id', 'type' => 'xsd:int'),
	'artifact_id' => array('name'=>'artifact_id', 'type' => 'xsd:int'),
	'body' => array('name'=>'body', 'type' => 'xsd:string'),
	'adddate' => array('name'=>'adddate', 'type' => 'xsd:int'),
	'user_id' => array('name'=>'user_id', 'type' => 'xsd:int')
	)
);

$server->wsdl->addComplexType(
	'ArrayOfArtifactMessage',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:ArtifactMessage[]')),
	'tns:ArtifactMessage'
);

$server->register(
	'getArtifactMessages',
	array('session_ser'=>'xsd:string','group_id'=>'xsd:int','group_artifact_id'=>'xsd:int','artifact_id'=>'xsd:int'),
	array('getArtifactMessagesResponse'=>'tns:ArrayOfArtifactMessage'),
	$uri,$uri.'#getArtifactMessages','rpc','encoded'
);

//add
$server->register(
	'addArtifactMessage',
	array('session_ser'=>'xsd:string','group_id'=>'xsd:int','group_artifact_id'=>'xsd:int','artifact_id'=>'xsd:int','body'=>'xsd:string'),
	array('addArtifactMessageResponse'=>'xsd:int'),
	$uri,$uri.'#addArtifactMessage','rpc','encoded'
);

//
//	ArtifactTechnician
//
//	Array of Users
//
$server->register(
	'getArtifactTechnicians',
	array('session_ser'=>'xsd:string','group_id'=>'xsd:int','group_artifact_id'=>'xsd:int'),
	array('getArtifactTechniciansResponse'=>'tns:ArrayOfUser'),
	$uri,$uri.'#getArtifactTechnicians','rpc','encoded'
);


//
//	getArtifactTypes
//
function &getArtifactTypes($session_ser,$group_id) {
	continue_session($session_ser);
	$grp =& group_get_object($group_id);
	if (!$grp || !is_object($grp)) {
		return new soap_fault ('','getArtifactTypes','Could Not Get Group','Could Not Get Group');
	} elseif ($grp->isError()) {
		return new soap_fault ('','getArtifactTypes','$grp->getErrorMessage()',$grp->getErrorMessage());
	}

	$atf = new ArtifactTypeFactory($grp);
	if (!$atf || !is_object($atf)) {
		return new soap_fault ('','getArtifactTypes','Could Not Get ArtifactTypeFactory','Could Not Get ArtifactTypeFactory');
	} elseif ($atf->isError()) {
		return new soap_fault ('','getArtifactTypes',$atf->getErrorMessage(),$atf->getErrorMessage());
	}

	return artifacttype_to_soap($atf->getArtifactTypes());
}

//
//	convert array of artifact types to soap data structure
//
function artifacttype_to_soap($at_arr) {
	for ($i=0; $i<count($at_arr); $i++) {
		if ($at_arr[$i]->isError()) {
			//skip if error
		} else {
			$return[]=array(
				'group_artifact_id'=>$at_arr->data_array['group_artifact_id'],
				'group_id'=>$at_arr->data_array['group_id'],
				'name'=>$at_arr->data_array['name'],
				'description'=>$at_arr->data_array['description'],
				'is_public'=>$at_arr->data_array['is_public'],
				'allow_anon'=>$at_arr->data_array['allow_anon'],
				'due_period'=>$at_arr->data_array['due_period'],
				'use_resolution'=>$at_arr->data_array['use_resolution'],
				'datatype'=>$at_arr->data_array['datatype'],
				'status_timeout'=>$at_arr->data_array['status_timeout']
			);
		}
	}
	return $return;
}

//
//	addArtifact
//
function &addArtifact($session_ser,$group_id,$group_artifact_id,$status_id,$category_id,
	$artifact_group_id,$resolution_id,$priority,$assigned_to,$summary,$details) {
	continue_session($session_ser);
	$grp =& group_get_object($group_id);
	if (!$grp || !is_object($grp)) {
		return new soap_fault ('','addArtifact','Could Not Get Group','Could Not Get Group');
	} elseif ($grp->isError()) {
		return new soap_fault ('','addArtifact',$grp->getErrorMessage(),$grp->getErrorMessage());
	}

	$at = new ArtifactType($grp,$group_artifact_id);
	if (!$at || !is_object($at)) {
		return new soap_fault ('','addArtifact','Could Not Get ArtifactType','Could Not Get ArtifactType');
	} elseif ($at->isError()) {
		return new soap_fault ('','addArtifact',$at->getErrorMessage(),$at->getErrorMessage());
	}

	$a = new Artifact($at);
	if (!$a || !is_object($a)) {
		return new soap_fault ('','addArtifact','Could Not Get Artifact','Could Not Get Artifact');
	} elseif ($a->isError()) {
		return new soap_fault ('','addArtifact',$a->getErrorMessage(),$a->getErrorMessage());
	}

	if (!$a->create($category_id,$artifact_group_id,$summary,$details,$assigned_to,$priority)) {
		return new soap_fault ('','addArtifact',$a->getErrorMessage(),$a->getErrorMessage());
	} else {
		return $a->getID();
	}
}

//
//	Update Artifact
//
function &updateArtifact($session_ser,$group_id,$group_artifact_id,$artifact_id,$status_id,$category_id,
	$artifact_group_id,$resolution_id,$priority,$assigned_to,$summary,$details) {
	continue_session($session_ser);
	$grp =& group_get_object($group_id);
	if (!$grp || !is_object($grp)) {
		return new soap_fault ('','addArtifact','Could Not Get Group','Could Not Get Group');
	} elseif ($grp->isError()) {
		return new soap_fault ('','addArtifact',$grp->getErrorMessage(),$grp->getErrorMessage());
	}

	$at = new ArtifactType($grp,$group_artifact_id);
	if (!$at || !is_object($at)) {
		return new soap_fault ('','addArtifact','Could Not Get ArtifactType','Could Not Get ArtifactType');
	} elseif ($at->isError()) {
		return new soap_fault ('','addArtifact',$at->getErrorMessage(),$at->getErrorMessage());
	}

	$a = new Artifact($at,$artifact_id);
	if (!$a || !is_object($a)) {
		return new soap_fault ('','addArtifact','Could Not Get Artifact','Could Not Get Artifact');
	} elseif ($a->isError()) {
		return new soap_fault ('','addArtifact',$a->getErrorMessage(),$a->getErrorMessage());
	}

	if (!$a->update($priority,$status_id,$category_id,$artifact_group_id,$resolution_id,$assigned_to,
		$summary,$canned_response,$details,$new_artifact_type_id)) {
		return new soap_fault ('','addArtifact',$a->getErrorMessage(),$a->getErrorMessage());
	} else {
		return $a->getID();
	}
}

//
//	getArtifactCategories
//
function &getArtifactCategories($session_ser,$group_id,$group_artifact_id) {
	continue_session($session_ser);
	$grp =& group_get_object($group_id);
	if (!$grp || !is_object($grp)) {
		return new soap_fault ('','getArtifactCategories','Could Not Get Group','Could Not Get Group');
	} elseif ($grp->isError()) {
		return new soap_fault ('','getArtifactCategories',$grp->getErrorMessage(),$grp->getErrorMessage());
	}

	$at = new ArtifactType($grp,$group_artifact_id);
	if (!$at || !is_object($at)) {
		return new soap_fault ('','getArtifactCategories','Could Not Get ArtifactType','Could Not Get ArtifactType');
	} elseif ($at->isError()) {
		return new soap_fault ('','getArtifactCategories',$at->getErrorMessage(),$at->getErrorMessage());
	}

	return artifactcategories_to_soap($at->getCategoryObjects());
}

//
//	convert array of artifact categories to soap data structure
//
function artifactcategories_to_soap($at_arr) {
	for ($i=0; $i<count($at_arr); $i++) {
		if ($at_arr[$i]->isError()) {
			//skip if error
		} else {
			$return[]=array(
				'id'=>$at_arr->data_array['id'],
				'group_artifact_id'=>$at_arr->data_array['group_artifact_id'],
				'category_name'=>$at_arr->data_array['category_name'],
				'auto_assign_to'=>$at_arr->data_array['auto_assign_to']
			);
		}
	}
	return $return;
}

//
//	getArtifactGroups
//
function &getArtifactGroups($session_ser,$group_id,$group_artifact_id) {
	continue_session($session_ser);
	$grp =& group_get_object($group_id);
	if (!$grp || !is_object($grp)) {
		return new soap_fault ('','getArtifactGroups','Could Not Get Group','Could Not Get Group');
	} elseif ($grp->isError()) {
		return new soap_fault ('','getArtifactGroups',$grp->getErrorMessage(),$grp->getErrorMessage());
	}

	$at = new ArtifactType($grp,$group_artifact_id);
	if (!$at || !is_object($at)) {
		return new soap_fault ('','getArtifactGroups','Could Not Get ArtifactType','Could Not Get ArtifactType');
	} elseif ($at->isError()) {
		return new soap_fault ('','getArtifactGroups',$at->getErrorMessage(),$at->getErrorMessage());
	}

	return artifactgroups_to_soap($at->getGroupObjects());
}

//
//	convert array of artifact groups to soap data structure
//
function artifactgroups_to_soap($at_arr) {
	for ($i=0; $i<count($at_arr); $i++) {
		if ($at_arr[$i]->isError()) {
			//skip if error
		} else {
			$return[]=array(
				'id'=>$at_arr->data_array['id'],
				'group_artifact_id'=>$at_arr->data_array['group_artifact_id'],
				'group_name'=>$at_arr->data_array['group_name']
			);
		}
	}
	return $return;
}

//
//	getArtifactResolutions
//
function &getArtifactResolutions($session_ser,$group_id,$group_artifact_id) {
	continue_session($session_ser);
	$grp =& group_get_object($group_id);
	if (!$grp || !is_object($grp)) {
		return new soap_fault ('','getArtifactResolutions','Could Not Get Group','Could Not Get Group');
	} elseif ($grp->isError()) {
		return new soap_fault ('','getArtifactResolutions',$grp->getErrorMessage(),$grp->getErrorMessage());
	}

	$at = new ArtifactType($grp,$group_artifact_id);
	if (!$at || !is_object($at)) {
		return new soap_fault ('','getArtifactResolutions','Could Not Get ArtifactType','Could Not Get ArtifactType');
	} elseif ($at->isError()) {
		return new soap_fault ('','getArtifactResolutions',$at->getErrorMessage(),$at->getErrorMessage());
	}

	return artifactresolutions_to_soap($at->getResolutionObjects());
}

//
//	convert array of artifact resolutions to soap data structure
//
function artifactresolutions_to_soap($at_arr) {
	for ($i=0; $i<count($at_arr); $i++) {
		if ($at_arr[$i]->isError()) {
			//skip if error
		} else {
			$return[]=array(
				'id'=>$at_arr->data_array['id'],
				'group_artifact_id'=>$at_arr->data_array['group_artifact_id'],
				'resolution_name'=>$at_arr->data_array['resolution_name']
			);
		}
	}
	return $return;
}

//
//	getArtifactTechnicians
//
function &getArtifactTechnicians($session_ser,$group_id,$group_artifact_id) {
	continue_session($session_ser);
	$grp =& group_get_object($group_id);
	if (!$grp || !is_object($grp)) {
		return new soap_fault ('','getArtifactTechnicians','Could Not Get Group','Could Not Get Group');
	} elseif ($grp->isError()) {
		return new soap_fault ('','getArtifactTechnicians',$grp->getErrorMessage(),$grp->getErrorMessage());
	}

	$at = new ArtifactType($grp,$group_artifact_id);
	if (!$at || !is_object($at)) {
		return new soap_fault ('','getArtifactTechnicians','Could Not Get ArtifactType','Could Not Get ArtifactType');
	} elseif ($at->isError()) {
		return new soap_fault ('','getArtifactTechnicians',$at->getErrorMessage(),$at->getErrorMessage());
	}

	return users_to_soap($at->getTechnicianObjects());
}

//
//	getArtifacts
//
function &getArtifacts($session_ser,$group_id,$group_artifact_id,$assigned_to,$status,$category,$group) {
	continue_session($session_ser);
	$grp =& group_get_object($group_id);
	if (!$grp || !is_object($grp)) {
		return new soap_fault ('','getArtifacts','Could Not Get Group','Could Not Get Group');
	} elseif ($grp->isError()) {
		return new soap_fault ('','getArtifacts',$grp->getErrorMessage(),$grp->getErrorMessage());
	}

	$at = new ArtifactType($grp,$group_artifact_id);
	if (!$at || !is_object($at)) {
		return new soap_fault ('','getArtifacts','Could Not Get ArtifactType','Could Not Get ArtifactType');
	} elseif ($at->isError()) {
		return new soap_fault ('','getArtifacts',$at->getErrorMessage(),$at->getErrorMessage());
	}

	$af = new ArtifactFactory($at);
	if (!$af || !is_object($af)) {
		return new soap_fault ('','getArtifacts','Could Not Get ArtifactFactory','Could Not Get ArtifactFactory');
	} elseif ($af->isError()) {
		return new soap_fault ('','getArtifacts',$af->getErrorMessage(),$af->getErrorMessage());
	}

	$af->setup(0,0,0,0,false,$assigned_to,$status,$category,$group,0);
	return artifacts_to_soap($af->getArtifacts());

}

//
//	Get artifact by ID
//
function getArtifact($session_ser,$group_id,$group_artifact_id,$artifact_id) {

}

//
//	convert array of artifacts to soap data structure
//
function artifacts_to_soap($at_arr) {
	for ($i=0; $i<count($at_arr); $i++) {
		if ($at_arr[$i]->isError()) {
			//skip if error
		} else {
			$return[]=array(
				'artifact_id'=>$at_arr[$i]->data_array['artifact_id'],
				'group_artifact_id'=>$at_arr[$i]->data_array['group_artifact_id'],
				'status_id'=>$at_arr[$i]->data_array['status_id'],
				'category_id'=>$at_arr[$i]->data_array['category_id'],
				'artifact_group_id'=>$at_arr[$i]->data_array['artifact_group_id'],
				'resolution_id'=>$at_arr[$i]->data_array['resolution_id'],
				'priority'=>$at_arr[$i]->data_array['priority'],
				'submitted_by'=>$at_arr[$i]->data_array['submitted_by'],
				'assigned_to'=>$at_arr[$i]->data_array['assigned_to'],
				'open_date'=>$at_arr[$i]->data_array['open_date'],
				'close_date'=>$at_arr[$i]->data_array['close_date'],
				'summary'=>$at_arr[$i]->data_array['summary'],
				'details'=>$at_arr[$i]->data_array['details']
			);
		}
	}
	return $return;
}

//
//	getArtifactFiles
//
function &getArtifactFiles($session_ser,$group_id,$group_artifact_id,$artifact_id) {
	continue_session($session_ser);
	$grp =& group_get_object($group_id);
	if (!$grp || !is_object($grp)) {
		return new soap_fault ('','getArtifactFiles','Could Not Get Group','Could Not Get Group');
	} elseif ($grp->isError()) {
		return new soap_fault ('','getArtifactFiles',$grp->getErrorMessage(),$grp->getErrorMessage());
	}

	$at = new ArtifactType($grp,$group_artifact_id);
	if (!$at || !is_object($at)) {
		return new soap_fault ('','getArtifactFiles','Could Not Get ArtifactType','Could Not Get ArtifactType');
	} elseif ($at->isError()) {
		return new soap_fault ('','getArtifactFiles',$at->getErrorMessage(),$at->getErrorMessage());
	}

	$a = new Artifact($at,$artifact_id);
	if (!$a || !is_object($a)) {
		return new soap_fault ('','getArtifactFiles','Could Not Get Artifact','Could Not Get Artifact');
	} elseif ($a->isError()) {
		return new soap_fault ('','getArtifactFiles',$a->getErrorMessage(),$a->getErrorMessage());
	}

	return $a->getFiles();
}

//
//	convert array of artifact files to soap data structure
//
function artifactfiles_to_soap($at_arr) {
	for ($i=0; $i<count($at_arr); $i++) {
		if ($at_arr[$i]->isError()) {
			//skip if error
		} else {
//TODO FINISH
			$return[]=array(
				'id'=>$at_arr->data_array['id'],
				'group_artifact_id'=>$at_arr->data_array['group_artifact_id'],
				'resolution_name'=>$at_arr->data_array['resolution_name']
			);
		}
	}
	return $return;
}

//
//
//	addArtifactFile
// 
/*
function &addArtifactFile($session_ser,$group_id,$group_artifact_id,$artifact_id) {
	continue_session($session_ser);
	$grp =& group_get_object($group_id);
	if (!$grp || !is_object($grp) {
		return new soap_fault ('','addArtifactFile','Could Not Get Group','Could Not Get Group');
	} elseif ($grp->isError()) {
		return new soap_fault ('','addArtifactFile',$grp->getErrorMessage(),$grp->getErrorMessage());
	}

	$at = new ArtifactType($grp,$group_artifact_id);
	if (!$at || !is_object($at) {
		return new soap_fault ('','addArtifactFile','Could Not Get ArtifactType','Could Not Get ArtifactType');
	} elseif ($at->isError()) {
		return new soap_fault ('','addArtifactFile',$at->getErrorMessage(),$at->getErrorMessage());
	}

	$a = new Artifact($at,$artifact_id);
	if (!$a || !is_object($a) {
		return new soap_fault ('','addArtifactFile','Could Not Get Artifact','Could Not Get Artifact');
	} elseif ($a->isError()) {
		return new soap_fault ('','addArtifactFile',$a->getErrorMessage(),$a->getErrorMessage());
	}

	return $a->getFiles();
}
*/
//
//
//	getArtifactMessages
//
function &getArtifactMessages($session_ser,$group_id,$group_artifact_id,$artifact_id) {
	continue_session($session_ser);
	$grp =& group_get_object($group_id);
	if (!$grp || !is_object($grp)) {
		return new soap_fault ('','getArtifactMessages','Could Not Get Group','Could Not Get Group');
	} elseif ($grp->isError()) {
		return new soap_fault ('','getArtifactMessages',$grp->getErrorMessage(),$grp->getErrorMessage());
	}

	$at = new ArtifactType($grp,$group_artifact_id);
	if (!$at || !is_object($at)) {
		return new soap_fault ('','getArtifactMessages','Could Not Get ArtifactType','Could Not Get ArtifactType');
	} elseif ($at->isError()) {
		return new soap_fault ('','getArtifactMessages',$at->getErrorMessage(),$at->getErrorMessage());
	}

	$a = new Artifact($at,$artifact_id);
	if (!$a || !is_object($a)) {
		return new soap_fault ('','getArtifactMessages','Could Not Get Artifact','Could Not Get Artifact');
	} elseif ($a->isError()) {
		return new soap_fault ('','getArtifactMessages',$a->getErrorMessage(),$a->getErrorMessage());
	}

	return artifactmessages_to_soap($a->getMessageObjects());
}

//
//	convert array of artifact messages to soap data structure
//
function artifactmessages_to_soap($at_arr) {
	for ($i=0; $i<count($at_arr); $i++) {
		if ($at_arr[$i]->isError()) {
			//skip if error
		} else {
			$return[]=array(
				'id'=>$at_arr->data_array['id'],
				'artifact_id'=>$at_arr->data_array['artifact_id'],
				'body'=>$at_arr->data_array['body'],
				'adddate'=>$at_arr->data_array['adddate'],
				'user_id'=>$at_arr->data_array['user_id']
			);
		}
	}
	return $return;
}

//
//	addArtifactMessage
//
function &addArtifactMessage($session_ser,$group_id,$group_artifact_id,$artifact_id,$body) {
	continue_session($session_ser);
	$grp =& group_get_object($group_id);
	if (!$grp || !is_object($grp)) {
		return new soap_fault ('','addArtifactMessage','Could Not Get Group','Could Not Get Group');
	} elseif ($grp->isError()) {
		return new soap_fault ('','addArtifactMessage',$grp->getErrorMessage(),$grp->getErrorMessage());
	}

	$at = new ArtifactType($grp,$group_artifact_id);
	if (!$at || !is_object($at)) {
		return new soap_fault ('','addArtifactMessage','Could Not Get ArtifactType','Could Not Get ArtifactType');
	} elseif ($at->isError()) {
		return new soap_fault ('','addArtifactMessage',$at->getErrorMessage(),$at->getErrorMessage());
	}

	$a = new Artifact($at,$artifact_id);
	if (!$a || !is_object($a)) {
		return new soap_fault ('','addArtifactMessage','Could Not Get Artifact','Could Not Get Artifact');
	} elseif ($a->isError()) {
		return new soap_fault ('','addArtifactMessage',$a->getErrorMessage(),$a->getErrorMessage());
	}

	$am = new ArtifactMessage($a);
	if (!$am || !is_object($am)) {
		return new soap_fault ('','addArtifactMessage','Could Not Get ArtifactMessage','Could Not Get ArtifactMessage');
	} elseif ($am->isError()) {
		return new soap_fault ('','addArtifactMessage',$am->getErrorMessage(),$am->getErrorMessage());
	}

	if (!$am->create($body)) {
		return new soap_fault ('','addArtifactMessage',$am->getErrorMessage(),$am->getErrorMessage());
	} else {
		return $am->getID();
	}
}

?>
