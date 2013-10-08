<?php

/**
* $Id: answer.php,v 1.16 2006/09/29 18:49:10 malanciault Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

if (!defined("XOOPS_ROOT_PATH")) {
 	die("XOOPS root path not defined");
}


// Answers status
define("_SF_AN_STATUS_NOTSET", -1);
define("_SF_AN_STATUS_PROPOSED", 0);
define("_SF_AN_STATUS_APPROVED", 1);
define("_SF_AN_STATUS_REJECTED", 2);

// Notification Events
define("_SF_NOT_ANSWER_APPROVED", 3);
define("_SF_NOT_ANSWER_REJECTED", 4);


class sfAnswer extends XoopsObject
{
	/**
	* constructor
	*/
	function sfAnswer($id = null)
	{
		$this->db =& XoopsDatabaseFactory::getDatabaseConnection();
		$this->initVar("answerid", XOBJ_DTYPE_INT, null, false);
		$this->initVar("status", XOBJ_DTYPE_INT, -1, false);
		$this->initVar("faqid", XOBJ_DTYPE_INT, null, false);
		$this->initVar("answer", XOBJ_DTYPE_TXTAREA, null, true);
		$this->initVar("uid", XOBJ_DTYPE_INT, 0, false);
		$this->initVar("datesub", XOBJ_DTYPE_INT, null, false);
		$this->initVar("notifypub", XOBJ_DTYPE_INT, 0, false);

		$this->initVar("dohtml", XOBJ_DTYPE_INT, 1, false);
		$this->initVar("doxcode", XOBJ_DTYPE_INT, 1, false);
		$this->initVar("dosmiley", XOBJ_DTYPE_INT, 1, false);
		$this->initVar("doimage", XOBJ_DTYPE_INT, 0, false);
		$this->initVar("dobr", XOBJ_DTYPE_INT, 1, false);

		// for backward compatibility
		if (isset($id)) {
			if (is_array($id)) {
				$this->assignVars($id);
			} else {
				$answer_handler = new sfAnswerHandler($this->db);
				$answer =& $answer_handler->get($id);
				foreach ($answer->vars as $k => $v) {
					$this->assignVar($k, $v['value']);
				}
			}
		}
	}

	function store($force = true)
	{
		$answer_handler = new sfAnswerHandler($this->db);

		if ($this->status() == _SF_AN_STATUS_APPROVED) {
			$criteria = new CriteriaCompo(new criteria('faqid', $this->faqid()));
			$answer_handler->updateAll('status', _SF_AN_STATUS_REJECTED, $criteria);
		}
		return $answer_handler->insert($this, $force);
	}

	function answerid()
    {
        return $this->getVar("answerid");
    }

	function faqid()
    {
        return $this->getVar("faqid");
    }

	function answer($format="S")
    {
        return $this->getVar("answer", $format);
    }

	function uid()
    {
        return $this->getVar("uid");
    }

	function datesub($dateFormat='none', $format="S")
	{
		if ($dateFormat == 'none') {
		    $smartModuleConfig =& sf_getModuleConfig();
			$dateFormat = $smartModuleConfig['dateformat'];
		}

		return formatTimestamp($this->getVar('datesub', $format), $dateFormat);
	}

    function status()
    {
        return $this->getVar("status");
    }


	function notLoaded()
	{
	   return ($this->getVar('answerid')== -1);
	}

	function sendNotifications($notifications=array())
	{
	    $smartModule =& sf_getModuleInfo();

		$myts =& MyTextSanitizer::getInstance();
		$notification_handler = &xoops_gethandler('notification');

		$faqObj = new sfFaq($this->faqid());

		$tags = array();
		$tags['MODULE_NAME'] = $myts->displayTarea($smartModule->getVar('name'));
		$tags['FAQ_NAME'] = $faqObj->question();
		$tags['FAQ_URL'] = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/faq.php?faqid=' . $faqObj->faqid();
		$tags['CATEGORY_NAME'] = $faqObj->getCategoryName();
		$tags['CATEGORY_URL'] = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/category.php?categoryid=' . $faqObj->categoryid();
		$tags['FAQ_QUESTION'] = $faqObj->question();

		// TODO : Not sure about the 'formpreview' ...
		$tags['FAQ_ANSWER'] = $this->answer('formpreview');
		$tags['DATESUB'] = $this->datesub();

		foreach ( $notifications as $notification ) {
			switch ($notification) {
				case _SF_NOT_ANSWER_APPROVED :
				// This notification is not working for PM, but is for email... and I don't understand why???
				$notification_handler->triggerEvent('faq', $this->answerid(), 'answer_approved', $tags);

				break;

				case -1 :
				default:
				break;
			}
		}
	}


}

/**
* Answers handler class.
* This class is responsible for providing data access mechanisms to the data source
* of Answer class objects.
*
* @author marcan <marcan@smartfactory.ca>
* @package SmartFAQ
*/

class sfAnswerHandler extends XoopsPersistableObjectHandler
{

	/**
	* create a new answer
	*
	* @param bool $isNew flag the new objects as "new"?
	* @return object sfAnswer
	*/
	function &create($isNew = true)
	{
		$answer = new sfAnswer();
		if ($isNew) {
			$answer->setNew();
		}
		return $answer;
	}

	/**
	* retrieve an answer
	*
	* @param int $id answerid of the answer
	* @return mixed reference to the {@link sfAnswer} object, FALSE if failed
	*/
	function &get($id)
	{
		if (intval($id) > 0) {
			$sql = 'SELECT * FROM '.$this->db->prefix('smartfaq_answers').' WHERE answerid='.$id;
			if (!$result = $this->db->query($sql)) {
				return false;
			}

			$numrows = $this->db->getRowsNum($result);
			if ($numrows == 1) {
				$answer = new sfAnswer();
				$answer->assignVars($this->db->fetchArray($result));
				return $answer;
			}
		}
		return false;
	}

	/**
	* insert a new answer in the database
	*
	* @param object $answer reference to the {@link sfAnswer} object
	* @param bool $force
	* @return bool FALSE if failed, TRUE if already present and unchanged or successful
	*/
	function insert(&$answerObj, $force = false)
	{
		if (strtolower(get_class($answerObj)) != 'sfanswer') {
            return false;
        }
        if (!$answerObj->isDirty()) {
            return true;
        }
        if (!$answerObj->cleanVars()) {
            return false;
        }

        foreach ($answerObj->cleanVars as $k => $v) {
            ${$k} = $v;
        }

		if ($answerObj->isNew()) {
			$sql = sprintf("INSERT INTO %s (answerid, `status`, faqid, answer, uid, datesub, notifypub) VALUES (NULL, %u, %u, %s, %u, %u, %u)", $this->db->prefix('smartfaq_answers'), $status, $faqid, $this->db->quoteString($answer), $uid, time(), $notifypub);
		} else {
			$sql = sprintf("UPDATE %s SET status = %u, faqid = %s, answer = %s, uid = %u, datesub = %u, notifypub = %u WHERE answerid = %u", $this->db->prefix('smartfaq_answers'), $status, $faqid, $this->db->quoteString($answer), $uid, $datesub, $notifypub, $answerid);
		}

		if (false != $force) {
			$result = $this->db->queryF($sql);
		} else {
			$result = $this->db->query($sql);
		}

		if (!$result) {
			return false;
		}

		if ($answerObj->isNew()) {
			$answerObj->assignVar('answerid', $this->db->getInsertId());
		}
		else {
		    $answerObj->assignVar('answerid', $answerid);
		}
		return true;
	}

	/**
	* delete an answer from the database
	*
	* @param object $answer reference to the answer to delete
	* @param bool $force
	* @return bool FALSE if failed.
	*/
	function delete(&$answer, $force = false)
	{
		if (strtolower(get_class($answer)) != 'sfanswer') {
			return false;
		}
		$sql = sprintf("DELETE FROM %s WHERE answerid = %u", $this->db->prefix("smartfaq_answers"), $answer->getVar('answerid'));

		//echo "<br />" . $sql . "<br />";

		if (false != $force) {
			$result = $this->db->queryF($sql);
		} else {
			$result = $this->db->query($sql);
		}
		if (!$result) {
			return false;
		}
		return true;
	}

	/**
	* delete an answer from the database
	*
	* @param object $answer reference to the answer to delete
	* @param bool $force
	* @return bool FALSE if failed.
	*/
	function deleteFaqAnswers(&$faqObj)
	{
		if (strtolower(get_class($faqObj)) != 'sffaq') {
			return false;
		}
		$answers =& $this->getAllAnswers($faqObj->faqid());
		$result = true;
		foreach ($answers as $answer) {
			if (!$this->delete($answer)) {
				$result = false;
			}
		}
		return $result;
	}


	/**
	* retrieve answers from the database
	*
	* @param object $criteria {@link CriteriaElement} conditions to be met
	* @param bool $id_as_key use the answerid as key for the array?
	* @return array array of {@link sfAnswer} objects
	*/
	function &getObjects($criteria = null, $id_as_key = false)
	{
		$ret = array();
		$limit = $start = 0;
		$sql = 'SELECT * FROM '.$this->db->prefix('smartfaq_answers');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
			if ($criteria->getSort() != '') {
				$sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
			}
			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		}
		//echo "<br />" . $sql . "<br />";
		$result = $this->db->query($sql, $limit, $start);
		if (!$result) {
			return $ret;
		}
		while ($myrow = $this->db->fetchArray($result)) {
			$answer = new sfAnswer();
			$answer->assignVars($myrow);
			if (!$id_as_key) {
				$ret[] =& $answer;
			} else {
				$ret[$myrow['answerid']] =& $answer;
			}
			unset($answer);
		}
		return $ret;
	}

	/**
	* retrieve 1 official answer (for now SmartFAQ only allow 1 official answer...)
	*
	* @param object $criteria {@link CriteriaElement} conditions to be met
	* @param int $faqid
	* @return mixed reference to the {@link sfAnswer} object, FALSE if failed
	*/
	function &getOfficialAnswer($faqid=0)
	{
		$theaAnswers =& $this->getAllAnswers($faqid, _SF_AN_STATUS_APPROVED, 1, 0);
		if (count($theaAnswers) == 1) {
            $ret = $theaAnswers[0];
		} else {
            $ret = false;
		}
        return $ret;
	}

	/**
	* retrieve all answers
	*
	* @param object $criteria {@link CriteriaElement} conditions to be met
	* @param int $faqid
	* @return array array of {@link sfAnswer} objects
	*/
	function &getAllAnswers($faqid=0, $status = -1, $limit = 0, $start = 0, $sort = 'datesub', $order = 'DESC')
	{
		$hasStatusCriteria = false;
		$criteriaStatus = new CriteriaCompo();
		if ( is_array($status)) {
			$hasStatusCriteria = true;
			foreach ($status as $v) {
				$criteriaStatus->add(new Criteria('status', $v), 'OR');
			}
		} elseif ( $status != -1) {
			$hasStatusCriteria = true;
			$criteriaStatus->add(new Criteria('status', $status), 'OR');
		}
		$criteriaFaqid = new Criteria('faqid', $faqid);

		$criteria = new CriteriaCompo();
		$criteria->add($criteriaFaqid);

		if ($hasStatusCriteria) {
			$criteria->add($criteriaStatus);
		}

		$criteria->setSort($sort);
		$criteria->setOrder($order);
		$criteria->setLimit($limit);
		$criteria->setStart($start);
		$ret = $this->getObjects($criteria);
		return $ret;
	}

	/**
	* count answers matching a condition
	*
	* @param object $criteria {@link CriteriaElement} to match
	* @return int count of answers
	*/
	function getCount($criteria = null)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('smartfaq_answers');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		}
		$result = $this->db->query($sql);
		if (!$result) {
			return 0;
		}
		list($count) = $this->db->fetchRow($result);
		return $count;
	}

	/**
	* count answers matching a condition and group by faq ID
	*
	* @param object $criteria {@link CriteriaElement} to match
	* @return array
	*/
	function getCountByFAQ($criteria = null)
	{
		$sql = 'SELECT faqid, COUNT(*) FROM '.$this->db->prefix('smartfaq_answers');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
			$sql .= ' '.$criteria->getGroupby();
		}

		//echo "<br />$sql<br />";

		$result = $this->db->query($sql);
		if (!$result) {
			return array();
		}
		$ret = array();
		while(list($id, $count) = $this->db->fetchRow($result)) {
		    $ret[$id] = $count;
		}
		return $ret;
	}

	/**
	* delete answers matching a set of conditions
	*
	* @param object $criteria {@link CriteriaElement}
	* @return bool FALSE if deletion failed
	*/
	function deleteAll($criteria = null)
	{
		$sql = 'DELETE FROM '.$this->db->prefix('smartfaq_answers');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		}
		if (!$this->db->query($sql)) {
			return false;
		}
		return true;
	}

	/**
	* Change a value for answers with a certain criteria
	*
	* @param   string  $fieldname  Name of the field
	* @param   string  $fieldvalue Value to write
	* @param   object  $criteria   {@link CriteriaElement}
	*
	* @return  bool
	**/
	function updateAll($fieldname, $fieldvalue, $criteria = null)
	{
		$set_clause = is_numeric($fieldvalue)? $fieldname.' = '.$fieldvalue : $fieldname.' = '.$this->db->quoteString($fieldvalue);
		$sql = 'UPDATE '.$this->db->prefix('smartfaq_answers').' SET '.$set_clause;
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		}
		//echo "<br />" . $sql . "<br />";
		if (!$this->db->queryF($sql)) {
			return false;
		}
		return true;
	}

	function getLastPublishedByFaq($faqids) {
	    $ret = array();
	    $sql = "SELECT faqid, answer, uid, datesub FROM ".$this->db->prefix("smartfaq_answers")."
	           WHERE faqid IN (".implode(',', $faqids).") AND status = ". _SF_AN_STATUS_APPROVED." GROUP BY faqid";
	    $result = $this->db->query($sql);
	    if (!$result) {
	        return $ret;
	    }
		while ($row = $this->db->fetchArray($result)) {
		    $answer = new sfAnswer();
			$answer->assignVars($row);
			$ret[$row['faqid']] =& $answer;
			unset($answer);
		}
		return $ret;
	}
}
?>
