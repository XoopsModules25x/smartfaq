<?php namespace XoopsModules\Smartfaq;

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq;
use XoopsModules\Smartfaq\Constants;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');


/**
 * Answers handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of Answer class objects.
 *
 * @author  marcan <marcan@smartfactory.ca>
 * @package SmartFAQ
 */
class AnswerHandler extends \XoopsPersistableObjectHandler
{

    /**
     * create a new answer
     *
     * @param  bool $isNew flag the new objects as "new"?
     * @return object Answer
     */
    public function create($isNew = true)
    {
        $answer = new Smartfaq\Answer();
        if ($isNew) {
            $answer->setNew();
        }

        return $answer;
    }

    /**
     * retrieve an answer
     *
     * @param  int  $id answerid of the answer
     * @param  null $fields
     * @return mixed reference to the <a href='psi_element://sfAnswer'>sfAnswer</a> object, FALSE if failed
     */
    public function get($id = null, $fields = null)
    {
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('smartfaq_answers') . ' WHERE answerid=' . $id;
            if (!$result = $this->db->query($sql)) {
                return false;
            }

            $numrows = $this->db->getRowsNum($result);
            if (1 == $numrows) {
                $answer = new Smartfaq\Answer();
                $answer->assignVars($this->db->fetchArray($result));

                return $answer;
            }
        }

        return false;
    }

    /**
     * insert a new answer in the database
     *
     * @param \XoopsObject $answerObj reference to the <a href='psi_element://sfAnswer'>sfAnswer</a> object
     * @param  bool        $force
     * @return bool        FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(\XoopsObject $answerObj, $force = false)
    {
        if ('xoopsmodules\smartfaq\answer' !== strtolower(get_class($answerObj))) {
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
            $sql = sprintf('INSERT INTO `%s` (answerid, `status`, faqid, answer, uid, datesub, notifypub) VALUES (NULL, %u, %u, %s, %u, %u, %u)', $this->db->prefix('smartfaq_answers'), $status, $faqid, $this->db->quoteString($answer), $uid, time(), $notifypub);
        } else {
            $sql = sprintf('UPDATE `%s` SET STATUS = %u, faqid = %s, answer = %s, uid = %u, datesub = %u, notifypub = %u WHERE answerid = %u', $this->db->prefix('smartfaq_answers'), $status, $faqid, $this->db->quoteString($answer), $uid, $datesub, $notifypub, $answerid);
        }

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            return false;
        }

        if ($answerObj->isNew()) {
            $answerObj->assignVar('answerid', $this->db->getInsertId());
        } else {
            $answerObj->assignVar('answerid', $answerid);
        }

        return true;
    }

    /**
     * delete an answer from the database
     *
     * @param \XoopsObject $answer reference to the answer to delete
     * @param  bool        $force
     * @return bool        FALSE if failed.
     */
    public function delete(\XoopsObject $answer, $force = false)
    {
        if ('xoopsmodules\smartfaq\answer' !== strtolower(get_class($answer))) {
            return false;
        }
        $sql = sprintf('DELETE FROM `%s` WHERE answerid = %u', $this->db->prefix('smartfaq_answers'), $answer->getVar('answerid'));

        //echo "<br>" . $sql . "<br>";

        if (false !== $force) {
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
     * @param  object $faqObj reference to the answer to delete
     * @return bool   FALSE if failed.
     * @internal param bool $force
     */
    public function deleteFaqAnswers($faqObj)
    {
        if ('xoopsmodules\smartfaq\faq' !== strtolower(get_class($faqObj))) {
            return false;
        }
        $answers = $this->getAllAnswers($faqObj->faqid());
        $result  = true;
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
     * @param  \CriteriaElement $criteria  {@link CriteriaElement} conditions to be met
     * @param  bool            $id_as_key use the answerid as key for the array?
     * @param  bool            $as_object
     * @return array           array of <a href='psi_element://sfAnswer'>sfAnswer</a> objects
     */
    public function &getObjects(\CriteriaElement $criteria = null, $id_as_key = false, $as_object = true)
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('smartfaq_answers');
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        //echo "<br>" . $sql . "<br>";
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $answer = new Smartfaq\Answer();
            $answer->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $answer;
            } else {
                $ret[$myrow['answerid']] = $answer;
            }
            unset($answer);
        }

        return $ret;
    }

    /**
     * retrieve 1 official answer (for now SmartFAQ only allow 1 official answer...)
     *
     * @param  int $faqid
     * @return mixed reference to the <a href='psi_element://sfAnswer'>sfAnswer</a> object, FALSE if failed
     */
    public function getOfficialAnswer($faqid = 0)
    {
        $theaAnswers = $this->getAllAnswers($faqid, Constants::SF_AN_STATUS_APPROVED, 1, 0);
        $ret         = false;
        if (1 == count($theaAnswers)) {
            $ret = $theaAnswers[0];
        }

        return $ret;
    }

    /**
     * retrieve all answers
     *
     * @param  int    $faqid
     * @param  int    $status
     * @param  int    $limit
     * @param  int    $start
     * @param  string $sort
     * @param  string $order
     * @return array  array of <a href='psi_element://sfAnswer'>sfAnswer</a> objects
     */
    public function getAllAnswers(
        $faqid = 0,
        $status = -1,
        $limit = 0,
        $start = 0,
        $sort = 'datesub',
        $order = 'DESC'
    ) {
        $hasStatusCriteria = false;
        $criteriaStatus    = new \CriteriaCompo();
        if (is_array($status)) {
            $hasStatusCriteria = true;
            foreach ($status as $v) {
                $criteriaStatus->add(new \Criteria('status', $v), 'OR');
            }
        } elseif (-1 != $status) {
            $hasStatusCriteria = true;
            $criteriaStatus->add(new \Criteria('status', $status), 'OR');
        }
        $criteriaFaqid = new \Criteria('faqid', $faqid);

        $criteria = new \CriteriaCompo();
        $criteria->add($criteriaFaqid);

        if ($hasStatusCriteria) {
            $criteria->add($criteriaStatus);
        }

        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $ret =& $this->getObjects($criteria);

        return $ret;
    }

    /**
     * count answers matching a condition
     *
     * @param  \CriteriaElement $criteria {@link CriteriaElement} to match
     * @return int             count of answers
     */
    public function getCount(\CriteriaElement $criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('smartfaq_answers');
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
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
     * @param  object $criteria {@link CriteriaElement} to match
     * @return array
     */
    public function getCountByFAQ($criteria = null)
    {
        $sql = 'SELECT faqid, COUNT(*) FROM ' . $this->db->prefix('smartfaq_answers');
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $sql .= ' ' . $criteria->getGroupby();
        }

        //echo "<br>$sql<br>";

        $result = $this->db->query($sql);
        if (!$result) {
            return [];
        }
        $ret = [];
        while (false !== (list($id, $count) = $this->db->fetchRow($result))) {
            $ret[$id] = $count;
        }

        return $ret;
    }

    /**
     * delete answers matching a set of conditions
     *
     * @param  \CriteriaElement $criteria {@link CriteriaElement}
     * @param  bool            $force
     * @param  bool            $asObject
     * @return bool            FALSE if deletion failed
     */
    public function deleteAll(\CriteriaElement $criteria = null, $force = true, $asObject = false)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix('smartfaq_answers');
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Change a value for answers with a certain criteria
     *
     * @param  string          $fieldname  Name of the field
     * @param  string          $fieldvalue Value to write
     * @param  \CriteriaElement $criteria   {@link CriteriaElement}
     * @param  bool            $force
     * @return bool
     */
    public function updateAll($fieldname, $fieldvalue, \CriteriaElement $criteria = null, $force = false)
    {
        $set_clause = is_numeric($fieldvalue) ? $fieldname . ' = ' . $fieldvalue : $fieldname . ' = ' . $this->db->quoteString($fieldvalue);
        $sql        = 'UPDATE ' . $this->db->prefix('smartfaq_answers') . ' SET ' . $set_clause;
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        //echo "<br>" . $sql . "<br>";
        if (!$this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * @param $faqids
     * @return array
     */
    public function getLastPublishedByFaq($faqids)
    {
        $ret    = [];
        $sql    = 'SELECT faqid, answer, uid, datesub FROM ' . $this->db->prefix('smartfaq_answers') . '
               WHERE faqid IN (' . implode(',', $faqids) . ') AND status = ' . Constants::SF_AN_STATUS_APPROVED . ' GROUP BY faqid';
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while (false !== ($row = $this->db->fetchArray($result))) {
            $answer = new Smartfaq\Answer();
            $answer->assignVars($row);
            $ret[$row['faqid']] =& $answer;
            unset($answer);
        }

        return $ret;
    }
}
