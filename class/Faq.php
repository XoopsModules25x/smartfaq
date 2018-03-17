<?php namespace XoopsModules\Smartfaq;

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq;
use XoopsModules\Smartfaq\Constants;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

//require_once XOOPS_ROOT_PATH . '/modules/smartfaq/class/category.php';



/**
 * Class Faq
 */
class Faq extends \XoopsObject
{

    /**
     * @var Smartfaq\Category
     * @access private
     */
    private $category = null;

    /**
     * @var Answer
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
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
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

        if (null !== $id) {
            $faqHandler = new Smartfaq\FaqHandler($this->db);
            $faq        = $faqHandler->get($id);
            foreach ($faq->vars as $k => $v) {
                $this->assignVar($k, $v['value']);
            }
            $this->assignOtherProperties();
        }
    }

    public function assignOtherProperties()
    {
        $smartModule = Smartfaq\Utility::getModuleInfo();
        $module_id   = $smartModule->getVar('mid');

        $gpermHandler = xoops_getHandler('groupperm');

        $this->category    = new Smartfaq\Category($this->getVar('categoryid'));
        $this->groups_read = $gpermHandler->getGroupIds('item_read', $this->faqid(), $module_id);
    }

    /**
     * @return bool
     */
    public function checkPermission()
    {
//        require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

        $userIsAdmin = Smartfaq\Utility::userIsAdmin();
        if ($userIsAdmin) {
            return true;
        }
        /** @var Smartfaq\PermissionHandler $smartPermHandler */
        $smartPermHandler = Smartfaq\Helper::getInstance()->getHandler('Permission');
//        $smartPermHandler = xoops_getModuleHandler('permission', 'smartfaq');

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
    public function setGroups_read($groups_read = ['0'])
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
     * @return Smartfaq\Category
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
        if (('s' === $format) || ('S' === $format) || ('show' === $format)) {
            $myts = \MyTextSanitizer::getInstance();
            $ret  = $myts->displayTarea($ret);
        }
        if (0 != $maxLength) {
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
        if (('s' === $format) || ('S' === $format) || ('show' === $format)) {
            $myts = \MyTextSanitizer::getInstance();
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
        if (('s' === $format) || ('S' === $format) || ('show' === $format)) {
            $myts = \MyTextSanitizer::getInstance();
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
        if ('none' === $dateFormat) {
            $smartConfig = Smartfaq\Utility::getModuleConfig();
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
        if (-1 == $realName) {
            $smartConfig = Smartfaq\Utility::getModuleConfig();
            $realName    = $smartConfig['userealname'];
        }

        return Smartfaq\Utility::getLinkedUnameFromId($this->uid(), $realName);
    }

    /**
     * @return mixed|object|Smartfaq\Answer
     */
    public function answer()
    {
        $answerHandler = new Smartfaq\AnswerHandler($this->db);
        switch ($this->status()) {
            case Constants::SF_STATUS_SUBMITTED:
                $theAnswers = $answerHandler->getAllAnswers($this->faqid(), Constants::SF_AN_STATUS_APPROVED, 1, 0);
                //echo "test";
                //exit;
                $this->answer =& $theAnswers[0];
                break;

            case Constants::SF_STATUS_ANSWERED:
                $theAnswers = $answerHandler->getAllAnswers($this->faqid(), Constants::SF_AN_STATUS_PROPOSED, 1, 0);
                //echo "test";
                //exit;
                $this->answer =& $theAnswers[0];
                break;

            case Constants::SF_STATUS_PUBLISHED:
            case Constants::SF_STATUS_NEW_ANSWER:
            case Constants::SF_STATUS_OFFLINE:
                $this->answer = $answerHandler->getOfficialAnswer($this->faqid());
                break;

            case Constants::SF_STATUS_ASKED:
                $this->answer = $answerHandler->create();
                break;
            case Constants::SF_STATUS_OPENED:
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
        $answerHandler = new Smartfaq\AnswerHandler($this->db);

        return $answerHandler->getAllAnswers($this->faqid());
    }

    /**
     * @return bool
     */
    public function updateCounter()
    {
        $faqHandler = new Smartfaq\FaqHandler($this->db);

        return $faqHandler->updateCounter($this->faqid());
    }

    /**
     * @param  bool $force
     * @return bool
     */
    public function store($force = true)
    {
        $faqHandler = new Smartfaq\FaqHandler($this->db);

        return $faqHandler->insert($this, $force);
    }

    /**
     * @return mixed
     */
    public function getCategoryName()
    {
        if (!isset($this->category)) {
            $this->category = new Smartfaq\Category($this->getVar('categoryid'));
        }

        return $this->category->name();
    }

    /**
     * @param array $notifications
     */
    public function sendNotifications($notifications = [])
    {
        $smartModule = Smartfaq\Utility::getModuleInfo();

        $myts                = \MyTextSanitizer::getInstance();
        $notificationHandler = xoops_getHandler('notification');
        //$categoryObj = $this->category();

        $tags                  = [];
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
                case Constants::SF_NOT_FAQ_PUBLISHED:
                    $tags['FAQ_URL'] = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/faq.php?faqid=' . $this->faqid();

                    $notificationHandler->triggerEvent('global_faq', 0, 'published', $tags);
                    $notificationHandler->triggerEvent('category_faq', $this->categoryid(), 'published', $tags);
                    $notificationHandler->triggerEvent('faq', $this->faqid(), 'approved', $tags);
                    break;

                case Constants::SF_NOT_FAQ_SUBMITTED:
                    $tags['WAITINGFILES_URL'] = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/admin/faq.php?faqid=' . $this->faqid();
                    $notificationHandler->triggerEvent('global_faq', 0, 'submitted', $tags);
                    $notificationHandler->triggerEvent('category_faq', $this->categoryid(), 'submitted', $tags);
                    break;

                case Constants::SF_NOT_QUESTION_PUBLISHED:
                    $tags['FAQ_URL'] = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/answer.php?faqid=' . $this->faqid();
                    $notificationHandler->triggerEvent('global_question', 0, 'published', $tags);
                    $notificationHandler->triggerEvent('category_question', $this->categoryid(), 'published', $tags);
                    $notificationHandler->triggerEvent('question', $this->faqid(), 'approved', $tags);
                    break;

                case Constants::SF_NOT_QUESTION_SUBMITTED:
                    $tags['WAITINGFILES_URL'] = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/admin/question.php?op=mod&faqid=' . $this->faqid();
                    $notificationHandler->triggerEvent('global_question', 0, 'submitted', $tags);
                    $notificationHandler->triggerEvent('category_question', $this->categoryid(), 'submitted', $tags);
                    break;

                case Constants::SF_NOT_FAQ_REJECTED:
                    $notificationHandler->triggerEvent('faq', $this->faqid(), 'rejected', $tags);
                    break;

                case Constants::SF_NOT_NEW_ANSWER_PROPOSED:
                    $tags['WAITINGFILES_URL'] = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/admin/answer.php?op=mod&faqid=' . $this->faqid();
                    $notificationHandler->triggerEvent('global_faq', 0, 'answer_proposed', $tags);
                    $notificationHandler->triggerEvent('category_faq', $this->categoryid(), 'answer_proposed', $tags);
                    break;

                case Constants::SF_NOT_NEW_ANSWER_PUBLISHED:
                    $notificationHandler->triggerEvent('global_faq', 0, 'answer_published', $tags);
                    $notificationHandler->triggerEvent('category_faq', $this->categoryid(), 'answer_published', $tags);
                    break;

                // TODO : I commented out this one because I'm not sure. The $this->faqid() should probably be the
                // answerid not the faqid....
                /*
                case Constants::SF_NOT_ANSWER_APPROVED:
                $notificationHandler->triggerEvent('faq', $this->faqid(), 'answer_approved', $tags);
                break;
                */

                // TODO : I commented out this one because I'm not sure. The $this->faqid() should probably be the
                // answerid not the faqid....
                /*
                case Constants::SF_NOT_ANSWER_REJECTED:
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
        $group_ids = [];
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
            $group_ids = [];
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
        return (-1 == $this->getVar('faqid'));
    }

    /**
     * @param  null  $answerObj
     * @param  array $users
     * @return string
     */
    public function getWhoAndWhen($answerObj = null, $users = [])
    {
        $smartModuleConfig = Smartfaq\Utility::getModuleConfig();

        $requester   = Smartfaq\Utility::getLinkedUnameFromId($this->uid(), $smartModuleConfig['userealname'], $users);
        $requestdate = $this->datesub();

        if ((Constants::SF_STATUS_PUBLISHED == $this->status()) || Constants::SF_STATUS_NEW_ANSWER == $this->status()) {
            if (null === $answerObj) {
                $answerObj = $this->answer();
            }
            $submitdate = $answerObj->datesub();
            if ($this->uid() == $answerObj->uid()) {
                $result = sprintf(_MD_SF_REQUESTEDANDANSWERED, $requester, $submitdate);
            } else {
                $submitter = Smartfaq\Utility::getLinkedUnameFromId($answerObj->uid(), $smartModuleConfig['userealname'], $users);
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
        if ((Constants::SF_STATUS_PUBLISHED == $this->status()) || Constants::SF_STATUS_NEW_ANSWER == $this->status()) {
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
    public function toArray($faq = [], $category = null, $linkInQuestion = true)
    {
        global $xoopsModuleConfig;
        $lastfaqsize = (int)$xoopsModuleConfig['lastfaqsize'];

        $faq['id']         = $this->faqid();
        $faq['categoryid'] = $this->categoryid();
        $faq['question']   = $this->question();
        $page              = (Constants::SF_STATUS_OPENED == $this->status()) ? 'answer.php' : 'faq.php';

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
        if (null !== $category) {
            if (is_object($category) && 'xoopsmodules\smartfaq\category' === strtolower(get_class($category))) {
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
