<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

// Answers status
define('_SF_AN_STATUS_NOTSET', -1);
define('_SF_AN_STATUS_PROPOSED', 0);
define('_SF_AN_STATUS_APPROVED', 1);
define('_SF_AN_STATUS_REJECTED', 2);

// Notification Events
define('_SF_NOT_ANSWER_APPROVED', 3);
define('_SF_NOT_ANSWER_REJECTED', 4);

/**
 * Class sfAnswer
 */
class sfAnswer extends XoopsObject
{
    public $attachment_array = array();

    /**
     * constructor
     * @param null $id
     */
    public function __construct($id = null)
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('answerid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('status', XOBJ_DTYPE_INT, -1, false);
        $this->initVar('faqid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('answer', XOBJ_DTYPE_TXTAREA, null, true);
        $this->initVar('uid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('datesub', XOBJ_DTYPE_INT, null, false);
        $this->initVar('notifypub', XOBJ_DTYPE_INT, 0, false);

        $this->initVar('attachment', XOBJ_DTYPE_TXTAREA, '');

        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('doxcode', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('dosmiley', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('doimage', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('dobr', XOBJ_DTYPE_INT, 1, false);

        // for backward compatibility
        if (isset($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                $answerHandler = new sfAnswerHandler($this->db);
                $answer        = $answerHandler->get($id);
                foreach ($answer->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
            }
        }
    }

    // ////////////////////////////////////////////////////////////////////////////////////
    // attachment functions    TODO: there should be a file/attachment management class
    /**
     * @return array|mixed|null
     */
    public function getAttachment()
    {
        if (count($this->attachment_array)) {
            return $this->attachment_array;
        }
        $attachment = $this->getVar('attachment');
        if (empty($attachment)) {
            $this->attachment_array = null;
        } else {
            $this->attachment_array = @unserialize(base64_decode($attachment));
        }

        return $this->attachment_array;
    }

    /**
     * @param $attach_key
     * @return bool
     */
    public function incrementDownload($attach_key)
    {
        if (!$attach_key) {
            return false;
        }
        $this->attachment_array[(string)$attach_key]['num_download']++;

        return $this->attachment_array[(string)$attach_key]['num_download'];
    }

    /**
     * @return bool
     */
    public function saveAttachment()
    {
        $attachment_save = '';
        if (is_array($this->attachment_array) && count($this->attachment_array) > 0) {
            $attachment_save = base64_encode(serialize($this->attachment_array));
        }
        $this->setVar('attachment', $attachment_save);
        $sql = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smartfaq_answers') . ' SET attachment=' . $GLOBALS['xoopsDB']->quoteString($attachment_save) . ' WHERE post_id = ' . $this->getVar('answerid');
        if (!$result = $GLOBALS['xoopsDB']->queryF($sql)) {
            //xoops_error($GLOBALS["xoopsDB"]->error());
            return false;
        }

        return true;
    }

    /**
     * @param  null $attach_array
     * @return bool
     */
    public function deleteAttachment($attach_array = null)
    {
        global $xoopsModuleConfig;

        $attach_old = $this->getAttachment();
        if (!is_array($attach_old) || count($attach_old) < 1) {
            return true;
        }
        $this->attachment_array = array();

        if ($attach_array === null) {
            $attach_array = array_keys($attach_old);
        } // to delete all!
        if (!is_array($attach_array)) {
            $attach_array = array($attach_array);
        }

        foreach ($attach_old as $key => $attach) {
            if (in_array($key, $attach_array)) {
                @unlink(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['dir_attachments'] . '/' . $attach['name_saved']);
                @unlink(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['dir_attachments'] . '/thumbs/' . $attach['name_saved']); // delete thumbnails
                continue;
            }
            $this->attachment_array[$key] = $attach;
        }
        $attachment_save = '';
        if (is_array($this->attachment_array) && count($this->attachment_array) > 0) {
            $attachment_save = base64_encode(serialize($this->attachment_array));
        }
        $this->setVar('attachment', $attachment_save);

        return true;
    }

    /**
     * @param  string $name_saved
     * @param  string $name_display
     * @param  string $mimetype
     * @param  int    $num_download
     * @return bool
     */
    public function setAttachment($name_saved = '', $name_display = '', $mimetype = '', $num_download = 0)
    {
        static $counter = 0;
        $this->attachment_array = $this->getAttachment();
        if ($name_saved) {
            $key                          = (string)(time() + ($counter++));
            $this->attachment_array[$key] = array(
                'name_saved'   => $name_saved,
                'name_display' => isset($name_display) ? $name_display : $name_saved,
                'mimetype'     => $mimetype,
                'num_download' => isset($num_download) ? (int)$num_download : 0
            );
        }
        $attachment_save = null;
        if (is_array($this->attachment_array)) {
            $attachment_save = base64_encode(serialize($this->attachment_array));
        }
        $this->setVar('attachment', $attachment_save);

        return true;
    }

    /**
     * TODO: refactor
     * @param  bool $asSource
     * @return string
     */
    public function displayAttachment($asSource = false)
    {
        global $xoopsModule, $xoopsModuleConfig;

        $post_attachment = '';
        $attachments     = $this->getAttachment();
        if (is_array($attachments) && count($attachments) > 0) {
            $iconHandler = sf_getIconHandler();
            $mime_path   = $iconHandler->getPath('mime');
            include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname', 'n') . '/include/functions.image.php';
            $image_extensions = array('jpg', 'jpeg', 'gif', 'png', 'bmp'); // need improve !!!
            $post_attachment .= '<br /><strong>' . _MD_ATTACHMENT . '</strong>:';
            $post_attachment .= '<br /><hr size="1" noshade="noshade" /><br />';
            foreach ($attachments as $key => $att) {
                $file_extension = ltrim(strrchr($att['name_saved'], '.'), '.');
                $filetype       = $file_extension;
                if (file_exists(XOOPS_ROOT_PATH . '/' . $mime_path . '/' . $filetype . '.gif')) {
                    $icon_filetype = XOOPS_URL . '/' . $mime_path . '/' . $filetype . '.gif';
                } else {
                    $icon_filetype = XOOPS_URL . '/' . $mime_path . '/unknown.gif';
                }
                $file_size = @filesize(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['dir_attachments'] . '/' . $att['name_saved']);
                $file_size = number_format($file_size / 1024, 2) . ' KB';
                if ($xoopsModuleConfig['media_allowed'] && in_array(strtolower($file_extension), $image_extensions)) {
                    $post_attachment .= '<br /><img src="' . $icon_filetype . '" alt="' . $filetype . '" /><strong>&nbsp; ' . $att['name_display'] . '</strong> <small>(' . $file_size . ')</small>';
                    $post_attachment .= '<br />' . sf_attachmentImage($att['name_saved']);
                    $isDisplayed = true;
                } else {
                    global $xoopsUser;
                    if (empty($xoopsModuleConfig['show_userattach'])) {
                        $post_attachment .= '<a href="' . XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname', 'n') . '/dl_attachment.php?attachid=' . $key . '&amp;post_id=' . $this->getVar('post_id') . '"> <img src="' . $icon_filetype . '" alt="' . $filetype . '" /> ' . $att['name_display'] . '</a> ' . _MD_FILESIZE . ': ' . $file_size . '; ' . _MD_HITS . ': ' . $att['num_download'];
                    } elseif ($xoopsUser && $xoopsUser->uid() > 0 && $xoopsUser->isactive()) {
                        $post_attachment .= '<a href="' . XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname', 'n') . '/dl_attachment.php?attachid=' . $key . '&amp;post_id=' . $this->getVar('post_id') . '"> <img src="' . $icon_filetype . '" alt="' . $filetype . '" /> ' . $att['name_display'] . '</a> ' . _MD_FILESIZE . ': ' . $file_size . '; ' . _MD_HITS . ': ' . $att['num_download'];
                    } else {
                        $post_attachment .= _MD_NEWBB_SEENOTGUEST;
                    }
                }
                $post_attachment .= '<br />';
            }
        }

        return $post_attachment;
    }
    // attachment functions
    // ////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param  bool $force
     * @return bool
     */
    public function store($force = true)
    {
        $answerHandler = new sfAnswerHandler($this->db);

        if ($this->status() == _SF_AN_STATUS_APPROVED) {
            $criteria = new CriteriaCompo(new criteria('faqid', $this->faqid()));
            $answerHandler->updateAll('status', _SF_AN_STATUS_REJECTED, $criteria);
        }

        return $answerHandler->insert($this, $force);
    }

    /**
     * @return mixed
     */
    public function answerid()
    {
        return $this->getVar('answerid');
    }

    /**
     * @return mixed
     */
    public function faqid()
    {
        return $this->getVar('faqid');
    }

    /**
     * @param  string $format
     * @return mixed
     */
    public function answer($format = 'S')
    {
        return $this->getVar('answer', $format);
    }

    /**
     * @return mixed
     */
    public function uid()
    {
        return $this->getVar('uid');
    }

    /**
     * @param  string $dateFormat
     * @param  string $format
     * @return string
     */
    public function datesub($dateFormat = 'none', $format = 'S')
    {
        if ($dateFormat === 'none') {
            $smartModuleConfig = sf_getModuleConfig();
            $dateFormat        = $smartModuleConfig['dateformat'];
        }

        return formatTimestamp($this->getVar('datesub', $format), $dateFormat);
    }

    /**
     * @return mixed
     */
    public function status()
    {
        return $this->getVar('status');
    }

    /**
     * @return bool
     */
    public function notLoaded()
    {
        return ($this->getVar('answerid') == -1);
    }

    /**
     * @param array $notifications
     */
    public function sendNotifications($notifications = array())
    {
        $smartModule = sf_getModuleInfo();

        $myts                = MyTextSanitizer::getInstance();
        $notificationHandler = xoops_getHandler('notification');

        $faqObj = new sfFaq($this->faqid());

        $tags                  = array();
        $tags['MODULE_NAME']   = $myts->displayTarea($smartModule->getVar('name'));
        $tags['FAQ_NAME']      = $faqObj->question();
        $tags['FAQ_URL']       = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/faq.php?faqid=' . $faqObj->faqid();
        $tags['CATEGORY_NAME'] = $faqObj->getCategoryName();
        $tags['CATEGORY_URL']  = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/category.php?categoryid=' . $faqObj->categoryid();
        $tags['FAQ_QUESTION']  = $faqObj->question();

        // TODO : Not sure about the 'formpreview' ...
        $tags['FAQ_ANSWER'] = $this->answer('formpreview');
        $tags['DATESUB']    = $this->datesub();

        foreach ($notifications as $notification) {
            switch ($notification) {
                case _SF_NOT_ANSWER_APPROVED:
                    // This notification is not working for PM, but is for email... and I don't understand why???
                    $notificationHandler->triggerEvent('faq', $this->answerid(), 'answer_approved', $tags);

                    break;

                case -1:
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
 * @author  marcan <marcan@smartfactory.ca>
 * @package SmartFAQ
 */
class sfAnswerHandler extends XoopsPersistableObjectHandler
{

    /**
     * create a new answer
     *
     * @param  bool $isNew flag the new objects as "new"?
     * @return object sfAnswer
     */
    public function create($isNew = true)
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
     * @param  int $id answerid of the answer
     * @param null $fields
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
     * @param  XoopsObject $answerObj reference to the <a href='psi_element://sfAnswer'>sfAnswer</a> object
     * @param  bool        $force
     * @return bool        FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(XoopsObject $answerObj, $force = false)
    {
        if (strtolower(get_class($answerObj)) !== 'sfanswer') {
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
            $sql = sprintf('INSERT INTO %s (answerid, `status`, faqid, answer, uid, datesub, notifypub) VALUES (NULL, %u, %u, %s, %u, %u, %u)', $this->db->prefix('smartfaq_answers'), $status, $faqid, $this->db->quoteString($answer), $uid, time(), $notifypub);
        } else {
            $sql = sprintf('UPDATE %s SET status = %u, faqid = %s, answer = %s, uid = %u, datesub = %u, notifypub = %u WHERE answerid = %u', $this->db->prefix('smartfaq_answers'), $status, $faqid, $this->db->quoteString($answer), $uid, $datesub, $notifypub, $answerid);
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
        } else {
            $answerObj->assignVar('answerid', $answerid);
        }

        return true;
    }

    /**
     * delete an answer from the database
     *
     * @param  XoopsObject $answer reference to the answer to delete
     * @param  bool        $force
     * @return bool        FALSE if failed.
     */
    public function delete(XoopsObject $answer, $force = false)
    {
        if (strtolower(get_class($answer)) !== 'sfanswer') {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE answerid = %u', $this->db->prefix('smartfaq_answers'), $answer->getVar('answerid'));

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
     * @param object $faqObj reference to the answer to delete
     * @return bool FALSE if failed.
     * @internal param bool $force
     */
    public function deleteFaqAnswers($faqObj)
    {
        if (strtolower(get_class($faqObj)) !== 'sffaq') {
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
     * @param  CriteriaElement $criteria  {@link CriteriaElement} conditions to be met
     * @param  bool            $id_as_key use the answerid as key for the array?
     * @param bool             $as_object
     * @return array array of <a href='psi_element://sfAnswer'>sfAnswer</a> objects
     */
    public function &getObjects(CriteriaElement $criteria = null, $id_as_key = false, $as_object = true)
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('smartfaq_answers');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
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
     * @param  int $faqid
     * @return mixed reference to the <a href='psi_element://sfAnswer'>sfAnswer</a> object, FALSE if failed
     */
    public function getOfficialAnswer($faqid = 0)
    {
        $theaAnswers = $this->getAllAnswers($faqid, _SF_AN_STATUS_APPROVED, 1, 0);
        $ret         = false;
        if (count($theaAnswers) == 1) {
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
    public function getAllAnswers($faqid = 0, $status = -1, $limit = 0, $start = 0, $sort = 'datesub', $order = 'DESC')
    {
        $hasStatusCriteria = false;
        $criteriaStatus    = new CriteriaCompo();
        if (is_array($status)) {
            $hasStatusCriteria = true;
            foreach ($status as $v) {
                $criteriaStatus->add(new Criteria('status', $v), 'OR');
            }
        } elseif ($status != -1) {
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
        $ret =& $this->getObjects($criteria);

        return $ret;
    }

    /**
     * count answers matching a condition
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} to match
     * @return int             count of answers
     */
    public function getCount(CriteriaElement $criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('smartfaq_answers');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
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
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $sql .= ' ' . $criteria->getGroupby();
        }

        //echo "<br />$sql<br />";

        $result = $this->db->query($sql);
        if (!$result) {
            return array();
        }
        $ret = array();
        while (list($id, $count) = $this->db->fetchRow($result)) {
            $ret[$id] = $count;
        }

        return $ret;
    }

    /**
     * delete answers matching a set of conditions
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement}
     * @param bool             $force
     * @param bool             $asObject
     * @return bool FALSE if deletion failed
     */
    public function deleteAll(CriteriaElement $criteria = null, $force = true, $asObject = false)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix('smartfaq_answers');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
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
     * @param string          $fieldname  Name of the field
     * @param string          $fieldvalue Value to write
     * @param CriteriaElement $criteria   {@link CriteriaElement}
     * @param bool            $force
     * @return bool
     */
    public function updateAll($fieldname, $fieldvalue, CriteriaElement $criteria = null, $force = false)
    {
        $set_clause = is_numeric($fieldvalue) ? $fieldname . ' = ' . $fieldvalue : $fieldname . ' = ' . $this->db->quoteString($fieldvalue);
        $sql        = 'UPDATE ' . $this->db->prefix('smartfaq_answers') . ' SET ' . $set_clause;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        //echo "<br />" . $sql . "<br />";
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
        $ret    = array();
        $sql    = 'SELECT faqid, answer, uid, datesub FROM ' . $this->db->prefix('smartfaq_answers') . '
               WHERE faqid IN (' . implode(',', $faqids) . ') AND status = ' . _SF_AN_STATUS_APPROVED . ' GROUP BY faqid';
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
