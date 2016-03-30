<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

require_once XOOPS_ROOT_PATH . '/modules/smartfaq/class/category.php';

// FAQ status
define('_SF_STATUS_NOTSET', -1);
define('_SF_STATUS_ALL', 0);
define('_SF_STATUS_ASKED', 1);
define('_SF_STATUS_OPENED', 2);
define('_SF_STATUS_ANSWERED', 3);
define('_SF_STATUS_SUBMITTED', 4);
define('_SF_STATUS_PUBLISHED', 5);
define('_SF_STATUS_NEW_ANSWER', 6);
define('_SF_STATUS_OFFLINE', 7);
define('_SF_STATUS_REJECTED_QUESTION', 8);
define('_SF_STATUS_REJECTED_SMARTFAQ', 9);

// Notification Events
define('_SF_NOT_CATEGORY_CREATED', 1);
define('_SF_NOT_FAQ_SUBMITTED', 2);
define('_SF_NOT_FAQ_PUBLISHED', 3);
define('_SF_NOT_FAQ_REJECTED', 4);
define('_SF_NOT_QUESTION_SUBMITTED', 5);
define('_SF_NOT_QUESTION_PUBLISHED', 6);
define('_SF_NOT_NEW_ANSWER_PROPOSED', 7);
define('_SF_NOT_NEW_ANSWER_PUBLISHED', 8);

/**
 * Class sfFaq
 */
class sfFaq extends XoopsObject
{

    /**
     * @var sfCategory
     * @access private
     */
    private $category = null;

    /**
     * @var sfAnswer
     * @access private
     */
    private $answer = null;

    /**
     * @var array
     * @access private
     */
    private $_notifications = null;
    // TODO : Create a seperated class for notifications

    /**
     * @var array
     * @access private
     */
    private $groups_read = null;

    /**
     * @var object
     * @access private
     */
    // Is this still usefull??
    private $_smartModule = null;
    private $_smartModuleConfig;

    /**
     * constructor
     * @param null $id
     */
    public function __construct($id = null)
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('faqid', XOBJ_DTYPE_INT, -1, false);
        $this->initVar('categoryid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('question', XOBJ_DTYPE_TXTBOX, null, true, 100000);
        $this->initVar('howdoi', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('diduno', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('uid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('datesub', XOBJ_DTYPE_INT, null, false);
        $this->initVar('status', XOBJ_DTYPE_INT, -1, false);
        $this->initVar('counter', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('html', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('smiley', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('image', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('linebreak', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('xcodes', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('cancomment', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('comments', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('notifypub', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('modulelink', XOBJ_DTYPE_TXTBOX, 'None', false, 50);
        $this->initVar('contextpage', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('exacturl', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('partialview', XOBJ_DTYPE_INT, 0, false);

        if (isset($id)) {
            $faqHandler = new sfFaqHandler($this->db);
            $faq        = &$faqHandler->get($id);
            foreach ($faq->vars as $k => $v) {
                $this->assignVar($k, $v['value']);
            }
            $this->assignOtherProperties();
        }
    }

    public function assignOtherProperties()
    {
        $smartModule = sf_getModuleInfo();
        $module_id   = $smartModule->getVar('mid');

        $gpermHandler = xoops_getHandler('groupperm');

        $this->category    = new sfCategory($this->getVar('categoryid'));
        $this->groups_read = $gpermHandler->getGroupIds('item_read', $this->faqid(), $module_id);
    }

    /**
     * @return bool
     */
    public function checkPermission()
    {
        include_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

        $userIsAdmin = sf_userIsAdmin();
        if ($userIsAdmin) {
            return true;
        }

        $smartPermHandler = xoops_getModuleHandler('permission', 'smartfaq');

        $faqsGranted = $smartPermHandler->getPermissions('item');
        if (in_array($this->categoryid(), $faqsGranted)) {
            $ret = true;
        }

        return $ret;
    }

    /**
     * @return array
     */
    public function getGroups_read()
    {
        if (count($this->groups_read) < 1) {
            $this->assignOtherProperties();
        }

        return $this->groups_read;
    }

    /**
     * @param array $groups_read
     */
    public function setGroups_read($groups_read = array('0'))
    {
        $this->groups_read = $groups_read;
    }

    /**
     * @return mixed
     */
    public function faqid()
    {
        return $this->getVar('faqid');
    }

    /**
     * @return mixed
     */
    public function categoryid()
    {
        return $this->getVar('categoryid');
    }

    /**
     * @return sfCategory
     */
    public function category()
    {
        return $this->category;
    }

    /**
     * @param  int    $maxLength
     * @param  string $format
     * @return mixed|string
     */
    public function question($maxLength = 0, $format = 'S')
    {
        $ret = $this->getVar('question', $format);
        if (($format === 's') || ($format === 'S') || ($format === 'show')) {
            $myts = MyTextSanitizer:: getInstance();
            $ret  = $myts->displayTarea($ret);
        }
        if ($maxLength != 0) {
            if (!XOOPS_USE_MULTIBYTES) {
                if (strlen($ret) >= $maxLength) {
                    $ret = substr($ret, 0, $maxLength - 1) . '...';
                }
            }
        }

        return $ret;
    }

    /**
     * @param  string $format
     * @return mixed
     */
    public function howdoi($format = 'S')
    {
        $ret = $this->getVar('howdoi', $format);
        if (($format === 's') || ($format === 'S') || ($format === 'show')) {
            $myts = MyTextSanitizer:: getInstance();
            $ret  = $myts->displayTarea($ret);
        }

        return $ret;
    }

    /**
     * @param  string $format
     * @return mixed
     */
    public function diduno($format = 'S')
    {
        $ret = $this->getVar('diduno', $format);
        if (($format === 's') || ($format === 'S') || ($format === 'show')) {
            $myts = MyTextSanitizer:: getInstance();
            $ret  = $myts->displayTarea($ret);
        }

        return $ret;
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
            $smartConfig = sf_getModuleConfig();
            $dateFormat  = $smartConfig['dateformat'];
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
     * @return mixed
     */
    public function counter()
    {
        return $this->getVar('counter');
    }

    /**
     * @return mixed
     */
    public function weight()
    {
        return $this->getVar('weight');
    }

    /**
     * @return mixed
     */
    public function html()
    {
        return $this->getVar('html');
    }

    /**
     * @return mixed
     */
    public function smiley()
    {
        return $this->getVar('smiley');
    }

    /**
     * @return mixed
     */
    public function xcodes()
    {
        return $this->getVar('xcodes');
    }

    /**
     * @return mixed
     */
    public function cancomment()
    {
        return $this->getVar('cancomment');
    }

    /**
     * @return mixed
     */
    public function comments()
    {
        return $this->getVar('comments');
    }

    /**
     * @return mixed
     */
    public function notifypub()
    {
        return $this->getVar('notifypub');
    }

    /**
     * @param  string $format
     * @return mixed
     */
    public function modulelink($format = 'S')
    {
        return $this->getVar('modulelink', $format);
    }

    /**
     * @param  string $format
     * @return mixed
     */
    public function contextpage($format = 'S')
    {
        return $this->getVar('contextpage', $format);
    }

    /**
     * @return mixed
     */
    public function exacturl()
    {
        return $this->getVar('exacturl');
    }

    /**
     * @return mixed
     */
    public function partialview()
    {
        return $this->getVar('partialview');
    }

    /**
     * @param  int $realName
     * @return string
     */
    public function posterName($realName = -1)
    {
        if ($realName == -1) {
            $smartConfig = sf_getModuleConfig();
            $realName    = $smartConfig['userealname'];
        }

        return sf_getLinkedUnameFromId($this->uid(), $realName);
    }

    /**
     * @return mixed|object|sfAnswer
     */
    public function answer()
    {
        $answerHandler = new sfAnswerHandler($this->db);
        switch ($this->status()) {
            case _SF_STATUS_SUBMITTED:
                $theAnswers = $answerHandler->getAllAnswers($this->faqid(), _SF_AN_STATUS_APPROVED, 1, 0);
                //echo "test";
                //exit;
                $this->answer = &$theAnswers[0];
                break;

            case _SF_STATUS_ANSWERED:
                $theAnswers = $answerHandler->getAllAnswers($this->faqid(), _SF_AN_STATUS_PROPOSED, 1, 0);
                //echo "test";
                //exit;
                $this->answer = &$theAnswers[0];
                break;

            case _SF_STATUS_PUBLISHED:
            case _SF_STATUS_NEW_ANSWER:
            case _SF_STATUS_OFFLINE:
                $this->answer = $answerHandler->getOfficialAnswer($this->faqid());
                break;

            case _SF_STATUS_ASKED:
                $this->answer = $answerHandler->create();
                break;
            case _SF_STATUS_OPENED:
                $this->answer = $answerHandler->create();
                break;
        }

        if ($this->answer) {
            $this->answer->setVar('dohtml', $this->getVar('html'));
            $this->answer->setVar('doxcode', $this->getVar('xcodes'));
            $this->answer->setVar('dosmiley', $this->getVar('smiley'));
            $this->answer->setVar('doimage', $this->getVar('image'));
            $this->answer->setVar('dobr', $this->getVar('linebreak'));
        }

        return $this->answer;
    }

    /**
     * @return array
     */
    public function getAllAnswers()
    {
        $answerHandler = new sfAnswerHandler($this->db);

        return $answerHandler->getAllAnswers($this->faqid());
    }

    /**
     * @return bool
     */
    public function updateCounter()
    {
        $faqHandler = new sfFaqHandler($this->db);

        return $faqHandler->updateCounter($this->faqid());
    }

    /**
     * @param  bool $force
     * @return bool
     */
    public function store($force = true)
    {
        $faqHandler = new sfFaqHandler($this->db);

        return $faqHandler->insert($this, $force);
    }

    /**
     * @return mixed
     */
    public function getCategoryName()
    {
        if (!isset($this->category)) {
            $this->category = new sfCategory($this->getVar('categoryid'));
        }

        return $this->category->name();
    }

    /**
     * @param array $notifications
     */
    public function sendNotifications($notifications = array())
    {
        $smartModule = sf_getModuleInfo();

        $myts                = MyTextSanitizer:: getInstance();
        $notificationHandler = xoops_getHandler('notification');
        //$categoryObj = $this->category();

        $tags                  = array();
        $tags['MODULE_NAME']   = $myts->displayTarea($smartModule->getVar('name'));
        $tags['FAQ_NAME']      = $this->question();
        $tags['CATEGORY_NAME'] = $this->getCategoryName();
        $tags['CATEGORY_URL']  = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/category.php?categoryid=' . $this->categoryid();
        $tags['FAQ_QUESTION']  = $this->question();
        $answerObj             = $this->answer();
        if (is_object($answerObj)) {
            // TODO : Not sure about the 'formpreview' ...
            $tags['FAQ_ANSWER'] = $answerObj->answer('formpreview');
        }
        $tags['DATESUB'] = $this->datesub();

        foreach ($notifications as $notification) {
            switch ($notification) {
                case _SF_NOT_FAQ_PUBLISHED:
                    $tags['FAQ_URL'] = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/faq.php?faqid=' . $this->faqid();

                    $notificationHandler->triggerEvent('global_faq', 0, 'published', $tags);
                    $notificationHandler->triggerEvent('category_faq', $this->categoryid(), 'published', $tags);
                    $notificationHandler->triggerEvent('faq', $this->faqid(), 'approved', $tags);
                    break;

                case _SF_NOT_FAQ_SUBMITTED:
                    $tags['WAITINGFILES_URL'] = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/admin/faq.php?faqid=' . $this->faqid();
                    $notificationHandler->triggerEvent('global_faq', 0, 'submitted', $tags);
                    $notificationHandler->triggerEvent('category_faq', $this->categoryid(), 'submitted', $tags);
                    break;

                case _SF_NOT_QUESTION_PUBLISHED:
                    $tags['FAQ_URL'] = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/answer.php?faqid=' . $this->faqid();
                    $notificationHandler->triggerEvent('global_question', 0, 'published', $tags);
                    $notificationHandler->triggerEvent('category_question', $this->categoryid(), 'published', $tags);
                    $notificationHandler->triggerEvent('question', $this->faqid(), 'approved', $tags);
                    break;

                case _SF_NOT_QUESTION_SUBMITTED:
                    $tags['WAITINGFILES_URL'] = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/admin/question.php?op=mod&faqid=' . $this->faqid();
                    $notificationHandler->triggerEvent('global_question', 0, 'submitted', $tags);
                    $notificationHandler->triggerEvent('category_question', $this->categoryid(), 'submitted', $tags);
                    break;

                case _SF_NOT_FAQ_REJECTED:
                    $notificationHandler->triggerEvent('faq', $this->faqid(), 'rejected', $tags);
                    break;

                case _SF_NOT_NEW_ANSWER_PROPOSED:
                    $tags['WAITINGFILES_URL'] = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/admin/answer.php?op=mod&faqid=' . $this->faqid();
                    $notificationHandler->triggerEvent('global_faq', 0, 'answer_proposed', $tags);
                    $notificationHandler->triggerEvent('category_faq', $this->categoryid(), 'answer_proposed', $tags);
                    break;

                case _SF_NOT_NEW_ANSWER_PUBLISHED:
                    $notificationHandler->triggerEvent('global_faq', 0, 'answer_published', $tags);
                    $notificationHandler->triggerEvent('category_faq', $this->categoryid(), 'answer_published', $tags);
                    break;

                // TODO : I commented out this one because I'm not sure. The $this->faqid() should probably be the
                // answerid not the faqid....
                /*
                case _SF_NOT_ANSWER_APPROVED:
                $notificationHandler->triggerEvent('faq', $this->faqid(), 'answer_approved', $tags);
                break;
                */

                // TODO : I commented out this one because I'm not sure. The $this->faqid() should probably be the
                // answerid not the faqid....
                /*
                case _SF_NOT_ANSWER_REJECTED:
                $notificationHandler->triggerEvent('faq', $this->faqid(), 'answer_approved', $tags);
                break;
                */

                case -1:
                default:
                    break;
            }
        }
    }

    public function setDefaultPermissions()
    {
        $memberHandler = xoops_getHandler('member');
        $groups        = $memberHandler->getGroupList();

        $j         = 0;
        $group_ids = array();
        foreach (array_keys($groups) as $i) {
            $group_ids[$j] = $i;
            ++$j;
        }
        $this->groups_read = $group_ids;
    }

    /**
     * @param $group_ids
     */
    public function setPermissions($group_ids)
    {
        if (!isset($group_ids)) {
            $memberHandler = xoops_getHandler('member');
            $groups        = $memberHandler->getGroupList();

            $j         = 0;
            $group_ids = array();
            foreach (array_keys($groups) as $i) {
                $group_ids[$j] = $i;
                ++$j;
            }
        }
    }

    /**
     * @return bool
     */
    public function notLoaded()
    {
        return ($this->getVar('faqid') == -1);
    }

    /**
     * @param  null  $answerObj
     * @param  array $users
     * @return string
     */
    public function getWhoAndWhen($answerObj = null, $users = array())
    {
        $smartModuleConfig = sf_getModuleConfig();

        $requester   = sf_getLinkedUnameFromId($this->uid(), $smartModuleConfig['userealname'], $users);
        $requestdate = $this->datesub();

        if (($this->status() == _SF_STATUS_PUBLISHED) || $this->status() == _SF_STATUS_NEW_ANSWER) {
            if ($answerObj == null) {
                $answerObj = $this->answer();
            }
            $submitdate = $answerObj->datesub();
            if ($this->uid() == $answerObj->uid()) {
                $result = sprintf(_MD_SF_REQUESTEDANDANSWERED, $requester, $submitdate);
            } else {
                $submitter = sf_getLinkedUnameFromId($answerObj->uid(), $smartModuleConfig['userealname'], $users);
                $result    = sprintf(_MD_SF_REQUESTEDBYANDANSWEREDBY, $requester, $submitter, $submitdate);
            }
        } else {
            $result = sprintf(_MD_SF_REQUESTEDBY, $requester, $requestdate);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getComeFrom()
    {
        global $xoopsConfig;
        $text = _MD_SF_QUESTIONCOMEFROM;
        if (($this->status() == _SF_STATUS_PUBLISHED) || $this->status() == _SF_STATUS_NEW_ANSWER) {
            $text = _MD_SF_FAQCOMEFROM;
        }

        return $text . $xoopsConfig['sitename'] . ' : <a href=' . XOOPS_URL . '/modules/smartfaq/faq.php?faqid=' . $this->faqid() . '>' . XOOPS_URL . '/modules/smartfaq/faq.php?faqid=' . $this->faqid() . '</a>';
    }

    /**
     * @param  array $faq
     * @param  null  $category
     * @param  bool  $linkInQuestion
     * @return array
     */
    public function toArray($faq = array(), $category = null, $linkInQuestion = true)
    {
        global $xoopsModuleConfig;
        $lastfaqsize = (int)$xoopsModuleConfig['lastfaqsize'];

        $faq['id']         = $this->faqid();
        $faq['categoryid'] = $this->categoryid();
        $faq['question']   = $this->question();
        $page              = ($this->status() == _SF_STATUS_OPENED) ? 'answer.php' : 'faq.php';

        $faq['questionlink'] = "<a href='$page?faqid=" . $this->faqid() . "'>" . $this->question($lastfaqsize) . '</a>';
        if ($linkInQuestion) {
            $faq['fullquestionlink'] = "<a href='$page?faqid=" . $this->faqid() . "'>" . $this->question() . '</a>';
        } else {
            $faq['fullquestionlink'] = $this->question();
        }
        $faq['faqid']      = $this->faqid();
        $faq['counter']    = $this->counter();
        $faq['cancomment'] = $this->cancomment();
        $faq['comments']   = $this->comments();
        $faq['datesub']    = $this->datesub();
        if (isset($category)) {
            if (is_object($category) && strtolower(get_class($category)) === 'sfcategory') {
                $categoryObj = $category;
            } elseif (is_array($category)) {
                $categoryObj = $category[$this->categoryid()];
            }
            $faq['categoryname'] = $categoryObj->getVar('name');
            $faq['categorylink'] = "<a href='" . XOOPS_URL . '/modules/smartfaq/category.php?categoryid=' . $this->categoryid() . "'>" . $categoryObj->getVar('name') . '</a>';
        }

        return $faq;
    }
}

/**
 * Q&A handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of Q&A class objects.
 *
 * @author  marcan <marcan@smartfactory.ca>
 * @package SmartFAQ
 */
class sfFaqHandler extends XoopsObjectHandler
{
    /**
     * @param  bool $isNew
     * @return sfFaq
     */
    public function & create($isNew = true)
    {
        $faq = new sfFaq();
        if ($isNew) {
            $faq->setDefaultPermissions();
            $faq->setNew();
        }

        return $faq;
    }

    /**
     * retrieve an FAQ
     *
     * @param  int $id faqid of the user
     * @return mixed reference to the {@link sfFaq} object, FALSE if failed
     */
    public function & get($id)
    {
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('smartfaq_faq') . ' WHERE faqid=' . $id;
            if (!$result = $this->db->query($sql)) {
                return false;
            }

            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $faq = new sfFaq();
                $faq->assignVars($this->db->fetchArray($result));

                return $faq;
            }
        }

        return false;
    }

    /**
     * insert a new faq in the database
     *
     * @param  XoopsObject $faq reference to the {@link sfFaq} object
     * @param  bool        $force
     * @return bool        FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(XoopsObject $faq, $force = false)
    {
        if (strtolower(get_class($faq)) !== 'sffaq') {
            return false;
        }

        if (!$faq->isDirty()) {
            return true;
        }

        if (!$faq->cleanVars()) {
            return false;
        }

        foreach ($faq->cleanVars as $k => $v) {
            ${$k} = $v;
        }

        if ($faq->isNew()) {
            $sql = sprintf('INSERT INTO %s (faqid, categoryid, question, howdoi, diduno, uid, datesub, `status`, counter, weight, html, smiley, xcodes, cancomment, comments, notifypub, modulelink, contextpage, exacturl, partialview) VALUES (NULL, %u, %s, %s, %s, %u, %u, %u, %u, %u, %u, %u, %u, %u, %u, %u, %s, %s, %u, %u)', $this->db->prefix('smartfaq_faq'), $categoryid, $this->db->quoteString($question), $this->db->quoteString($howdoi), $this->db->quoteString($diduno), $uid, time(), $status, $counter, $weight, $html, $smiley, $xcodes, $cancomment, $comments, $notifypub, $this->db->quoteString($modulelink), $this->db->quoteString($contextpage), $exacturl, $partialview);
        } else {
            $sql = sprintf('UPDATE %s SET categoryid = %u, question = %s, howdoi = %s, diduno = %s, uid = %u, datesub = %u, `status` = %u, counter = %u, weight = %u, html = %u, smiley = %u, xcodes = %u, cancomment = %u, comments = %u, notifypub = %u, modulelink = %s, contextpage = %s, exacturl = %u, partialview = %u  WHERE faqid = %u', $this->db->prefix('smartfaq_faq'), $categoryid, $this->db->quoteString($question), $this->db->quoteString($howdoi), $this->db->quoteString($diduno), $uid, $datesub, $status, $counter, $weight, $html, $smiley, $xcodes, $cancomment, $comments, $notifypub, $this->db->quoteString($modulelink), $this->db->quoteString($contextpage), $exacturl, $partialview, $faqid);
        }
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            return false;
        }
        if ($faq->isNew()) {
            $faq->assignVar('faqid', $this->db->getInsertId());
        }

        // Saving permissions
        sf_saveItemPermissions($faq->getGroups_read(), $faq->faqid());

        return true;
    }

    /**
     * delete an FAQ from the database
     *
     * @param  XoopsObject $faq reference to the FAQ to delete
     * @param  bool        $force
     * @return bool        FALSE if failed.
     */
    public function delete(XoopsObject $faq, $force = false)
    {
        $smartModule = sf_getModuleInfo();
        $module_id   = $smartModule->getVar('mid');

        if (strtolower(get_class($faq)) !== 'sffaq') {
            return false;
        }

        // Deleting the answers
        $answerHandler = new sfAnswerHandler($this->db);
        if (!$answerHandler->deleteFaqAnswers($faq)) {
            // error msg...
            echo 'error while deleteing an answer';
        }

        $sql = sprintf('DELETE FROM %s WHERE faqid = %u', $this->db->prefix('smartfaq_faq'), $faq->getVar('faqid'));

        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }

        xoops_groupperm_deletebymoditem($module_id, 'item_read', $faq->faqid());

        return true;
    }

    /**
     * retrieve FAQs from the database
     *
     * @param  object $criteria  {@link CriteriaElement} conditions to be met
     * @param  bool   $id_as_key use the faqid as key for the array?
     * @param  string $notNullFields
     * @return array  array of <a href='psi_element://sfFaq'>sfFaq</a> objects
     */
    public function &getObjects($criteria = null, $id_as_key = false, $notNullFields = '')
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('smartfaq_faq');

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $whereClause = $criteria->renderWhere();

            if ($whereClause !== 'WHERE ()') {
                $sql .= ' ' . $criteria->renderWhere();
                if (!empty($notNullFields)) {
                    $sql .= $this->NotNullFieldClause($notNullFields, true);
                }
            } elseif (!empty($notNullFields)) {
                $sql .= ' WHERE ' . $this->NotNullFieldClause($notNullFields);
            }
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        } elseif (!empty($notNullFields)) {
            $sql .= $sql .= ' WHERE ' . $this->NotNullFieldClause($notNullFields);
        }

        //echo "<br />" . $sql . "<br />";
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return false;
        }

        if (count($result) == 0) {
            return false;
        }

        while ($myrow = $this->db->fetchArray($result)) {
            $faq = new sfFaq();
            $faq->assignVars($myrow);

            if (!$id_as_key) {
                $ret[] = &$faq;
            } else {
                $ret[$myrow['faqid']] = &$faq;
            }
            unset($faq);
        }

        return $ret;
    }

    /**
     * @param  null   $criteria
     * @param  bool   $id_as_key
     * @param  string $notNullFields
     * @return array|bool
     */
    public function &getObjectsAdminSide($criteria = null, $id_as_key = false, $notNullFields = '')
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT
                            faq.faqid AS faqid,
                            faq.categoryid AS categoryid,
                            faq.question AS question,
                            faq.howdoi AS howdoi,
                            faq.diduno AS diduno,
                            faq.uid AS uid,
                            faq.datesub AS datesub,
                            faq.status AS status,
                            faq.counter AS counter,
                            faq.weight AS weight,
                            faq.html AS html,
                            faq.smiley AS smiley,
                            faq.image AS image,
                            faq.linebreak AS linebreak,
                            faq.xcodes AS xcodes,
                            faq.cancomment AS cancomment,
                            faq.comments AS comments,
                            faq.notifypub AS notifypub,
                            faq.modulelink AS modulelink,
                            faq.contextpage AS contextpage,
                            faq.exacturl AS exacturl
                FROM ' . $this->db->prefix('smartfaq_faq') . ' AS faq INNER JOIN ' . $this->db->prefix('smartfaq_categories') . ' AS category ON faq.categoryid = category.categoryid ';

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $whereClause = $criteria->renderWhere();

            if ($whereClause !== 'WHERE ()') {
                $sql .= ' ' . $criteria->renderWhere();
                if (!empty($notNullFields)) {
                    $sql .= $this->NotNullFieldClause($notNullFields, true);
                }
            } elseif (!empty($notNullFields)) {
                $sql .= ' WHERE ' . $this->NotNullFieldClause($notNullFields);
            }
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        } elseif (!empty($notNullFields)) {
            $sql .= $sql .= ' WHERE ' . $this->NotNullFieldClause($notNullFields);
        }

        //echo "<br />" . $sql . "<br />";
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return false;
        }

        if (count($result) == 0) {
            return false;
        }

        while ($myrow = $this->db->fetchArray($result)) {
            $faq = new sfFaq();
            $faq->assignVars($myrow);

            if (!$id_as_key) {
                $ret[] = &$faq;
            } else {
                $ret[$myrow['faqid']] = &$faq;
            }
            unset($faq);
        }

        return $ret;

        /*while ($myrow = $this->db->fetchArray($result)) {
            $faq = new sfFaq($myrow['faqid']);

            if (!$id_as_key) {
                $ret[] =& $faq;
            } else {
                $ret[$myrow['faqid']] =& $faq;
            }
            unset($faq);
        }

        return $ret;*/
    }

    /**
     * count FAQs matching a condition
     *
     * @param  object $criteria {@link CriteriaElement} to match
     * @param  string $notNullFields
     * @return int    count of FAQs
     */
    public function getCount($criteria = null, $notNullFields = '')
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('smartfaq_faq');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $whereClause = $criteria->renderWhere();
            if ($whereClause !== 'WHERE ()') {
                $sql .= ' ' . $criteria->renderWhere();
                if (!empty($notNullFields)) {
                    $sql .= $this->NotNullFieldClause($notNullFields, true);
                }
            } elseif (!empty($notNullFields)) {
                $sql .= ' WHERE ' . $this->NotNullFieldClause($notNullFields);
            }
        } elseif (!empty($notNullFields)) {
            $sql .= ' WHERE ' . $this->NotNullFieldClause($notNullFields);
        }

        //echo "<br />" . $sql . "<br />";
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * @param  int    $categoryid
     * @param  string $status
     * @param  string $notNullFields
     * @return int
     */
    public function getFaqsCount($categoryid = -1, $status = '', $notNullFields = '')
    {
        global $xoopsUser;

        //  if ( ($categoryid = -1) && (empty($status) || ($status == -1)) ) {
        //return $this->getCount();
        //}

        $userIsAdmin = sf_userIsAdmin();
        // Categories for which user has access
        if (!$userIsAdmin) {
            $smartPermHandler = xoops_getModuleHandler('permission', 'smartfaq');

            $categoriesGranted = $smartPermHandler->getPermissions('category');
            $grantedCategories = new Criteria('categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN');

            $faqsGranted = $smartPermHandler->getPermissions('item');
            $grantedFaq  = new CriteriaCompo();
            $grantedFaq->add(new Criteria('faqid', '(' . implode(',', $faqsGranted) . ')', 'IN'), 'OR');
            // If user is anonymous, check if the FAQ allow partialview
            if (!is_object($xoopsUser)) {
                $grantedFaq->add(new Criteria('partialview', '1'), 'OR');
            }
        }

        if (isset($categoryid) && ($categoryid != -1)) {
            $criteriaCategory = new criteria('categoryid', $categoryid);
        }

        $criteriaStatus = new CriteriaCompo();
        if (!empty($status) && is_array($status)) {
            foreach ($status as $v) {
                $criteriaStatus->add(new Criteria('status', $v), 'OR');
            }
        } elseif (!empty($status) && ($status != -1)) {
            $criteriaStatus->add(new Criteria('status', $status), 'OR');
        }

        $criteriaPermissions = new CriteriaCompo();
        if (!$userIsAdmin) {
            $criteriaPermissions->add($grantedCategories, 'AND');
            $criteriaPermissions->add($grantedFaq, 'AND');
        }

        $criteria = new CriteriaCompo();
        if (!empty($criteriaCategory)) {
            $criteria->add($criteriaCategory);
        }

        if (!empty($criteriaPermissions) && (!$userIsAdmin)) {
            $criteria->add($criteriaPermissions);
        }

        if (!empty($criteriaStatus)) {
            $criteria->add($criteriaStatus);
        }

        return $this->getCount($criteria, $notNullFields);
    }

    /**
     * @return array
     */
    public function getFaqsCountByStatus()
    {
        $sql    = 'SELECT status, COUNT(*) FROM ' . $this->db->prefix('smartfaq_faq') . ' GROUP BY status';
        $result = $this->db->query($sql);
        if (!$result) {
            return array();
        }
        $ret = array();
        while (list($status, $count) = $this->db->fetchRow($result)) {
            $ret[$status] = $count;
        }

        return $ret;
    }

    /**
     * @param  int    $limit
     * @param  int    $start
     * @param  int    $categoryid
     * @param  string $sort
     * @param  string $order
     * @param  bool   $asobject
     * @return array
     */
    public function getAllPublished($limit = 0, $start = 0, $categoryid = -1, $sort = 'datesub', $order = 'DESC', $asobject = true)
    {
        return $this->getFaqs($limit, $start, array(_SF_STATUS_PUBLISHED, _SF_STATUS_NEW_ANSWER), $categoryid, $sort, $order, null, $asobject, null);
    }

    /**
     * @param  int    $limit
     * @param  int    $start
     * @param  string $status
     * @param  int    $categoryid
     * @param  string $sort
     * @param  string $order
     * @param  string $notNullFields
     * @param  bool   $asobject
     * @param  null   $otherCriteria
     * @return array
     */
    public function getFaqs($limit = 0, $start = 0, $status = '', $categoryid = -1, $sort = 'datesub', $order = 'DESC', $notNullFields = '', $asobject = true, $otherCriteria = null)
    {
        global $xoopsUser;
        include_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

        //if ( ($categoryid == -1) && (empty($status) || ($status == -1)) && ($limit == 0) && ($start ==0) ) {
        //  return $this->getObjects();
        //}
        $ret         = array();
        $userIsAdmin = sf_userIsAdmin();
        // Categories for which user has access
        if (!$userIsAdmin) {
            $smartPermHandler = xoops_getModuleHandler('permission', 'smartfaq');

            $categoriesGranted = $smartPermHandler->getPermissions('category');
            $grantedCategories = new Criteria('categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN');

            $faqsGranted = $smartPermHandler->getPermissions('item');
            $grantedFaq  = new CriteriaCompo();
            $grantedFaq->add(new Criteria('faqid', '(' . implode(',', $faqsGranted) . ')', 'IN'), 'OR');
            // If user is anonymous, check if the FAQ allow partialview
            if (!is_object($xoopsUser)) {
                $grantedFaq->add(new Criteria('partialview', '1'), 'OR');
            }
        }

        if (isset($categoryid) && ($categoryid != -1)) {
            if (is_array($categoryid)) {
                $criteriaCategory = new Criteria('categoryid', '(' . implode(',', $categoryid) . ')', 'IN');
            } else {
                $criteriaCategory = new Criteria('categoryid', (int)$categoryid);
            }
        }

        if (!empty($status) && is_array($status)) {
            $criteriaStatus = new CriteriaCompo();
            foreach ($status as $v) {
                $criteriaStatus->add(new Criteria('status', $v), 'OR');
            }
        } elseif (!empty($status) && ($status != -1)) {
            $criteriaStatus = new CriteriaCompo();
            $criteriaStatus->add(new Criteria('status', $status), 'OR');
        }

        $criteriaPermissions = new CriteriaCompo();
        if (!$userIsAdmin) {
            $criteriaPermissions->add($grantedCategories, 'AND');
            $criteriaPermissions->add($grantedFaq, 'AND');
        }

        $criteria = new CriteriaCompo();
        if (!empty($criteriaCategory)) {
            $criteria->add($criteriaCategory);
        }

        if (!empty($criteriaPermissions) && (!$userIsAdmin)) {
            $criteria->add($criteriaPermissions);
        }

        if (!empty($criteriaStatus)) {
            $criteria->add($criteriaStatus);
        }

        if (!empty($otherCriteria)) {
            $criteria->add($otherCriteria);
        }

        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $ret = &$this->getObjects($criteria, false, $notNullFields);

        return $ret;
    }

    /**
     * @param  int    $limit
     * @param  int    $start
     * @param  string $status
     * @param  int    $categoryid
     * @param  string $sort
     * @param  string $order
     * @param  bool   $asobject
     * @param  null   $otherCriteria
     * @return array|bool
     */
    public function getFaqsAdminSide($limit = 0, $start = 0, $status = '', $categoryid = -1, $sort = 'datesub', $order = 'DESC', $asobject = true, $otherCriteria = null)
    {
        include_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

        $smartModule = sf_getModuleInfo();

        $ret = array();

        if (isset($categoryid) && ($categoryid != -1)) {
            $criteriaCategory = new criteria('faq.categoryid', $categoryid);
        }

        if (!empty($status) && is_array($status)) {
            $criteriaStatus = new CriteriaCompo();
            foreach ($status as $v) {
                $criteriaStatus->add(new Criteria('faq.status', $v), 'OR');
            }
        } elseif (!empty($status) && ($status != -1)) {
            $criteriaStatus = new CriteriaCompo();
            $criteriaStatus->add(new Criteria('faq.status', $status), 'OR');
        }

        $criteria = new CriteriaCompo();
        if (!empty($criteriaCategory)) {
            $criteria->add($criteriaCategory);
        }

        if (!empty($criteriaStatus)) {
            $criteria->add($criteriaStatus);
        }

        if (!empty($otherCriteria)) {
            $criteria->add($otherCriteria);
        }

        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $ret = &$this->getObjectsAdminSide($criteria, false);

        return $ret;
    }

    /**
     * @param  string $field
     * @param  string $status
     * @param  int    $category
     * @return bool|mixed
     */
    public function getRandomFaq($field = '', $status = '', $category = -1)
    {
        $ret = false;

        $notNullFields = $field;

        // Getting the number of published FAQ
        $totalFaqs = $this->getFaqsCount(-1, $status, $notNullFields);

        if ($totalFaqs > 0) {
            --$totalFaqs;
            mt_srand((double)microtime() * 1000000);
            $entrynumber = mt_rand(0, $totalFaqs);
            $faq         = $this->getFaqs(1, $entrynumber, $status, -1, 'datesub', 'DESC', $notNullFields);
            if ($faq) {
                $ret = &$faq[0];
            }
        }

        return $ret;
    }

    /**
     * @param  int $limit
     * @return array|bool
     */
    public function getContextualFaqs($limit = 0)
    {
        $ret = false;

        $otherCriteria = new CriteriaCompo();
        $otherCriteria->add(new Criteria('modulelink', 'None', '<>'));

        $faqsObj = $this->getFaqs(0, 0, array(_SF_STATUS_PUBLISHED, _SF_STATUS_NEW_ANSWER), -1, 'datesub', 'DESC', '', true, $otherCriteria);

        $totalfaqs  = count($faqsObj);
        $randomFaqs = array();
        if ($faqsObj) {
            for ($i = 0; $i < $totalfaqs; ++$i) {
                $display = false;

                $http        = (strpos(XOOPS_URL, 'https://') === false) ? 'http://' : 'https://';
                $phpself     = $_SERVER['PHP_SELF'];
                $httphost    = $_SERVER['HTTP_HOST'];
                $querystring = $_SERVER['QUERY_STRING'];
                if ($querystring != '') {
                    $querystring = '?' . $querystring;
                }
                $currenturl     = $http . $httphost . $phpself . $querystring;
                $fullcontexturl = XOOPS_URL . '/' . $faqsObj[$i]->contextpage();
                switch ($faqsObj[$i]->modulelink()) {
                    case '':
                        $display = false;
                        break;
                    case 'None':
                        $display = false;
                        break;
                    case 'All':
                        $display = true;
                        break;
                    case 'url':
                        if ($faqsObj[$i]->exacturl()) {
                            $display = ($currenturl == $fullcontexturl);
                        } else {
                            $display = (strpos($currenturl, $fullcontexturl) === false);
                        }
                        break;
                    default:
                        if (strpos($currenturl, XOOPS_URL . '/modules/') === false) {
                            $display = false;
                        } else {
                            if (strpos($currenturl, $faqsObj[$i]->modulelink()) === false) {
                                $display = false;
                            } else {
                                $display = true;
                            }
                        }
                        break;
                }
                if ($display) {
                    $randomFaqs[] = &$faqsObj[$i];
                }
            }
        }

        if (count($randomFaqs) > $limit) {
            mt_srand((float)microtime() * 10000000);
            $rand_keys = array_rand($randomFaqs, $limit);
            for ($j = 0, $jMax = count($rand_keys); $j < $jMax; ++$j) {
                $ret[] = &$randomFaqs[$rand_keys[$j]];
            }
        } else {
            $ret = &$randomFaqs;
        }

        return $ret;
    }

    /**
     * @param  array $status
     * @return array
     */
    public function getLastPublishedByCat($status = array(_SF_STATUS_PUBLISHED, _SF_STATUS_NEW_ANSWER))
    {
        $ret       = array();
        $faqclause = '';
        if (!sf_userIsAdmin()) {
            $smartPermHandler = xoops_getModuleHandler('permission', 'smartfaq');
            $items            = $smartPermHandler->getPermissions('item');
            $faqclause        = ' AND faqid IN (' . implode(',', $items) . ')';
        }

        $sql  = "CREATE TEMPORARY TABLE tmp (categoryid INT(8) UNSIGNED NOT NULL,datesub int(11) DEFAULT '0' NOT NULL);";
        $sql2 = ' LOCK TABLES ' . $this->db->prefix('smartfaq_faq') . ' READ;';
        $sql3 = ' INSERT INTO tmp SELECT categoryid, MAX(datesub) FROM ' . $this->db->prefix('smartfaq_faq') . ' WHERE status IN (' . implode(',', $status) . ") $faqclause GROUP BY categoryid;";
        $sql4 = ' SELECT ' . $this->db->prefix('smartfaq_faq') . '.categoryid, faqid, question, uid, ' . $this->db->prefix('smartfaq_faq') . '.datesub FROM ' . $this->db->prefix('smartfaq_faq') . ', tmp
                              WHERE ' . $this->db->prefix('smartfaq_faq') . '.categoryid=tmp.categoryid AND ' . $this->db->prefix('smartfaq_faq') . '.datesub=tmp.datesub;';
        /*
        //Old implementation
        $sql = "SELECT categoryid, faqid, question, uid, MAX(datesub) AS datesub FROM ".$this->db->prefix("smartfaq_faq")."
               WHERE status IN (". implode(',', $status).")";
        $sql .= " GROUP BY categoryid";
        */
        $this->db->queryF($sql);
        $this->db->queryF($sql2);
        $this->db->queryF($sql3);
        $result = $this->db->query($sql4);
        $error  = $this->db->error();
        $this->db->queryF('UNLOCK TABLES;');
        $this->db->queryF('DROP TABLE tmp;');
        if (!$result) {
            trigger_error('Error in getLastPublishedByCat SQL: ' . $error);

            return $ret;
        }
        while ($row = $this->db->fetchArray($result)) {
            $faq = new sfFaq();
            $faq->assignVars($row);
            $ret[$row['categoryid']] = &$faq;
            unset($faq);
        }

        return $ret;
    }

    /**
     * delete FAQs matching a set of conditions
     *
     * @param  object $criteria {@link CriteriaElement}
     * @return bool   FALSE if deletion failed
     */
    public function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix('smartfaq_faq');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$this->db->query($sql)) {
            return false;
            // TODO : Also delete the permissions related to each FAQ
        }

        return true;
    }

    /**
     * Change a value for FAQ with a certain criteria
     *
     * @param string $fieldname  Name of the field
     * @param string $fieldvalue Value to write
     * @param object $criteria   {@link CriteriaElement}
     *
     * @return bool
     **/
    public function updateAll($fieldname, $fieldvalue, $criteria = null)
    {
        $set_clause = is_numeric($fieldvalue) ? $fieldname . ' = ' . $fieldvalue : $fieldname . ' = ' . $this->db->quoteString($fieldvalue);
        $sql        = 'UPDATE ' . $this->db->prefix('smartfaq_faq') . ' SET ' . $set_clause;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * @param $faqid
     * @return bool
     */
    public function updateCounter($faqid)
    {
        $sql = 'UPDATE ' . $this->db->prefix('smartfaq_faq') . ' SET counter=counter+1 WHERE faqid = ' . $faqid;
        if ($this->db->queryF($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param  string $notNullFields
     * @param  bool   $withAnd
     * @return string
     */
    public function NotNullFieldClause($notNullFields = '', $withAnd = false)
    {
        $ret = '';
        if ($withAnd) {
            $ret .= ' AND ';
        }
        if (!empty($notNullFields) && is_array($notNullFields)) {
            foreach ($notNullFields as $v) {
                $ret .= " ($v IS NOT NULL AND $v <> ' ' )";
            }
        } elseif (!empty($notNullFields)) {
            $ret .= " ($notNullFields IS NOT NULL AND $notNullFields <> ' ' )";
        }

        return $ret;
    }

    /**
     * @param  array  $queryarray
     * @param  string $andor
     * @param  int    $limit
     * @param  int    $offset
     * @param  int    $userid
     * @return array
     */
    public function getFaqsFromSearch($queryarray = array(), $andor = 'AND', $limit = 0, $offset = 0, $userid = 0)
    {
        global $xoopsUser;

        $ret = array();

        $userIsAdmin = sf_userIsAdmin();

        if ($userid != 0) {
            $criteriaUser = new CriteriaCompo();
            $criteriaUser->add(new Criteria('faq.uid', $userid), 'OR');
            $criteriaUser->add(new Criteria('answer.uid', $userid), 'OR');
        }

        if ($queryarray) {
            $criteriaKeywords = new CriteriaCompo();
            for ($i = 0, $iMax = count($queryarray); $i < $iMax; ++$i) {
                $criteriaKeyword = new CriteriaCompo();
                $criteriaKeyword->add(new Criteria('faq.question', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
                $criteriaKeyword->add(new Criteria('answer.answer', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
                $criteriaKeywords->add($criteriaKeyword, $andor);
                unset($criteriaKeyword);
            }
        }

        // Categories for which user has access
        if (!$userIsAdmin) {
            $smartPermHandler = xoops_getModuleHandler('permission', 'smartfaq');

            $categoriesGranted = $smartPermHandler->getPermissions('category');
            $faqsGranted       = $smartPermHandler->getPermissions('item');
            if (!$categoriesGranted) {
                return $ret;
            }
            if (!$faqsGranted) {
                return $ret;
            }
            $grantedCategories = new Criteria('faq.categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN');
            $grantedFaq        = new CriteriaCompo();
            $grantedFaq->add(new Criteria('faq.faqid', '(' . implode(',', $faqsGranted) . ')', 'IN'), 'OR');
            // If user is anonymous, check if the FAQ allow partialview
            if (!is_object($xoopsUser)) {
                $grantedFaq->add(new Criteria('partialview', '1'), 'OR');
            }
        }

        $criteriaPermissions = new CriteriaCompo();
        if (!$userIsAdmin) {
            $criteriaPermissions->add($grantedCategories, 'AND');
            $criteriaPermissions->add($grantedFaq, 'AND');
        }

        $criteriaAnswersStatus = new CriteriaCompo();
        $criteriaAnswersStatus->add(new Criteria('answer.status', _SF_AN_STATUS_APPROVED));

        $criteriaFasStatus = new CriteriaCompo();
        $criteriaFasStatus->add(new Criteria('faq.status', _SF_STATUS_OPENED), 'OR');
        $criteriaFasStatus->add(new Criteria('faq.status', _SF_STATUS_PUBLISHED), 'OR');

        $criteria = new CriteriaCompo();
        if (!empty($criteriaUser)) {
            $criteria->add($criteriaUser, 'AND');
        }

        if (!empty($criteriaKeywords)) {
            $criteria->add($criteriaKeywords, 'AND');
        }

        if (!empty($criteriaPermissions) && (!$userIsAdmin)) {
            $criteria->add($criteriaPermissions);
        }

        if (!empty($criteriaAnswersStatus)) {
            $criteria->add($criteriaAnswersStatus, 'AND');
        }

        if (!empty($criteriaFasStatus)) {
            $criteria->add($criteriaFasStatus, 'AND');
        }

        $criteria->setLimit($limit);
        $criteria->setStart($offset);
        $criteria->setSort('faq.datesub');
        $criteria->setOrder('DESC');

        $sql = 'SELECT faq.faqid, faq.question, faq.datesub, faq.uid FROM ' . $this->db->prefix('smartfaq_faq') . ' as faq INNER JOIN ' . $this->db->prefix('smartfaq_answers') . ' as answer ON faq.faqid = answer.faqid';

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $whereClause = $criteria->renderWhere();

            if ($whereClause !== 'WHERE ()') {
                $sql .= ' ' . $criteria->renderWhere();
                if ($criteria->getSort() != '') {
                    $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
                }
                $limit = $criteria->getLimit();
                $start = $criteria->getStart();
            }
        }

        //echo "<br />" . $sql . "<br />";

        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            trigger_error('Query did not work in smartfaq', E_USER_WARNING);

            return $ret;
        }

        if (count($result) == 0) {
            return $ret;
        }

        while ($myrow = $this->db->fetchArray($result)) {
            $faq = new sfFaq();
            $faq->assignVars($myrow);
            $ret[] = &$faq;
            unset($faq);
        }

        return $ret;
    }

    /**
     * @param int $cat_id
     * @param     $status
     * @return array
     */
    public function getCountsByCat($cat_id = 0, $status)
    {
        global $xoopsUser;
        $ret = array();
        $sql = 'SELECT categoryid, COUNT(*) AS count FROM ' . $this->db->prefix('smartfaq_faq');
        if ((int)$cat_id > 0) {
            $sql .= ' WHERE categoryid = ' . (int)$cat_id;
            $sql .= ' AND status IN (' . implode(',', $status) . ')';
        } else {
            $sql .= ' WHERE status IN (' . implode(',', $status) . ')';
            if (!sf_userIsAdmin()) {
                $smartPermHandler = xoops_getModuleHandler('permission', 'smartfaq');
                $items            = $smartPermHandler->getPermissions('item');
                if (is_object($xoopsUser)) {
                    $sql .= ' AND faqid IN (' . implode(',', $items) . ')';
                } else {
                    $sql .= ' AND (faqid IN (' . implode(',', $items) . ') OR partialview = 1)';
                }
            }
        }
        $sql .= ' GROUP BY categoryid';

        //echo "<br />" . $sql . "<br />";

        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while ($row = $this->db->fetchArray($result)) {
            $ret[$row['categoryid']] = (int)$row['count'];
        }

        return $ret;
    }
}
