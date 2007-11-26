<?php
/**
 * GForge Survey Facility: Get Survey Questions 
 *
 * Copyright 2004 GForge, LLC
 * http://gforge.org/
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
	Survery Question Factory
	By Sung Kim 2004/2/13
*/


require_once('common/include/Error.class.php');
require_once('common/survey/SurveyQuestion.class.php');

class SurveyQuestionFactory extends Error {

       /**
        * The Group object.
        *
        * @var	 object  $Group.
        */
	var $Group;

	/**
	 * The question array.
	 *
	 * @var	 array	questionss.
	 */
	var $questions;

	/**
	 *  Constructor. 
	 *
	 *	@param	object	The Group object to which this survey question is associated.
         *      @param  int     The survey_id
	 */
	function SurveyQuestionFactory(&$Group) { 
		$this->Error();
		if (!$Group || !is_object($Group)) {
			$this->setError(_('No valid Group Object'));
			return false;
		}
		if ($Group->isError()) {
			$this->setError(_('Survey').':: '.$Group->getErrorMessage());
			return false;
		}
		$this->Group =& $Group;

		return true;
	}

	/**
	 *	getGroup - get the Group object this SurveyQuestionFactory is associated with.
	 *
	 *	@return object	The Group object.
	 */
	function &getGroup() {
		return $this->Group;
	}

	/**
	 *	getSurveyQuestion - get an array of Survey Question objects 
         *                          for this Group and Survey id if survey_id is given.
	 *
 	 *	@return	array	The array of Survey Question objects.
	 */
	function &getSurveyQuestions() {
		/* We alread have it */
		if ($this->questions) {
			return $this->questions;
		}

		$group_id = $this->Group->GetID();		
		$sql="SELECT survey_questions.*,survey_question_types.type ".
			"FROM survey_questions,survey_question_types ".
			"WHERE survey_question_types.id=survey_questions.question_type ". 
                        "AND survey_questions.group_id='$group_id' ".
			"ORDER BY survey_questions.question_id DESC";

		$result = db_query ($sql);

		if (!$result) {
			$this->setError(_('No Survey Question is found').db_error());
			return false;
		} else {
			while ($arr = db_fetch_array($result)) {
				$this->questions[] = new SurveyQuestion($this->Group, $arr['question_id'], $arr);
			}
			db_free_result($result);
		}
		return $this->questions;
	}

}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
