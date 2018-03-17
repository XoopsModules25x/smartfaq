<?php namespace XoopsModules\Smartfaq;

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use \XoopsModules\Smartfaq;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Class Category
 * @package XoopsModules\Smartfaq
 */
class Category extends \XoopsObject
{
    /**
     * @var array
     * @access private
     */
    private $groups_read = null;

    /**
     * @var array
     * @access private
     */
    private $groups_admin = null;

    /**
     * constructor
     * @param null $id
     */
    public function __construct($id = null)
    {
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('categoryid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('parentid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA, null, false, 255);
        $this->initVar('total', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('created', XOBJ_DTYPE_INT, null, false);
        $this->initVar('last_faq', XOBJ_DTYPE_INT);

        //not persistent values
        $this->initVar('faqcount', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('last_faqid', XOBJ_DTYPE_INT);
        $this->initVar('last_question_link', XOBJ_DTYPE_TXTBOX);

        if (null !== $id) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                /** @var Smartfaq\CategoryHandler $categoryHandler */
                $categoryHandler = Smartfaq\Helper::getInstance()->getHandler('Category');
                $category        = $categoryHandler->get($id);
                foreach ($category->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
                $this->assignOtherProperties();
            }
        }
    }

    /**
     * @return bool
     */
    public function notLoaded()
    {
        return (-1 == $this->getVar('categoryid'));
    }

    public function assignOtherProperties()
    {
        global $xoopsUser;
        $smartModule = Smartfaq\Utility::getModuleInfo();
        $module_id   = $smartModule->getVar('mid');

        $gpermHandler = xoops_getHandler('groupperm');

        $this->groups_read = $gpermHandler->getGroupIds('category_read', $this->categoryid(), $module_id);
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

        $categoriesGranted = $smartPermHandler->getPermissions('category');
        if (in_array($this->categoryid(), $categoriesGranted)) {
            $ret = true;
        }

        return $ret;
    }

    /**
     * @return mixed
     */
    public function categoryid()
    {
        return $this->getVar('categoryid');
    }

    /**
     * @return mixed
     */
    public function parentid()
    {
        return $this->getVar('parentid');
    }

    /**
     * @param  string $format
     * @return mixed
     */
    public function name($format = 'S')
    {
        $ret = $this->getVar('name', $format);
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
    public function description($format = 'S')
    {
        return $this->getVar('description', $format);
    }

    /**
     * @return mixed
     */
    public function weight()
    {
        return $this->getVar('weight');
    }

    /**
     * @param  bool $withAllLink
     * @param  bool $open
     * @return mixed|string
     */
    public function getCategoryPath($withAllLink = false, $open = false)
    {
        $filename = 'category.php';
        if (false !== $open) {
            $filename = 'open_category.php';
        }
        if ($withAllLink) {
            $ret = "<a href='" . XOOPS_URL . '/modules/smartfaq/' . $filename . '?categoryid=' . $this->categoryid() . "'>" . $this->name() . '</a>';
        } else {
            $ret = $this->name();
        }
        $parentid        = $this->parentid();
        /** @var Smartfaq\CategoryHandler $categoryHandler */
        $categoryHandler = Smartfaq\Helper::getInstance()->getHandler('Category');
        if (0 != $parentid) {
            $parentObj = $categoryHandler->get($parentid);
            if ($parentObj->notLoaded()) {
                exit;
            }
            $parentid = $parentObj->parentid();
            $ret      = $parentObj->getCategoryPath(true, $open) . ' > ' . $ret;
        }

        return $ret;
    }

    /**
     * @return array
     */
    public function getGroups_read()
    {
//        if(count($this->groups_read) < 1) {
        if (!is_array($this->groups_read)) {
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
     * @param  bool $sendNotifications
     * @param  bool $force
     * @return bool
     */
    public function store($sendNotifications = true, $force = true)
    {
//        $categoryHandler = new sfCategoryHandler($this->db);
        /** @var Smartfaq\CategoryHandler $categoryHandler */
        $categoryHandler = Smartfaq\Helper::getInstance()->getHandler('Category');

        $ret = $categoryHandler->insert($this, $force);
        if ($sendNotifications && $ret && $this->isNew()) {
            $this->sendNotifications();
        }
        $this->unsetNew();

        return $ret;
    }

    public function sendNotifications()
    {
        $smartModule = Smartfaq\Utility::getModuleInfo();

        $myts                = \MyTextSanitizer::getInstance();
        $notificationHandler = xoops_getHandler('notification');

        $tags                  = [];
        $tags['MODULE_NAME']   = $myts->htmlSpecialChars($smartModule->getVar('name'));
        $tags['CATEGORY_NAME'] = $this->name();
        $tags['CATEGORY_URL']  = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/category.php?categoryid=' . $this->categoryid();

        $notificationHandler = xoops_getHandler('notification');
        $notificationHandler->triggerEvent('global_faq', 0, 'category_created', $tags);
    }

    /**
     * @param  array $category
     * @param  bool  $open
     * @return array
     */
    public function toArray($category = [], $open = false)
    {
        $category['categoryid'] = $this->categoryid();
        $category['name']       = $this->name();
        if (false !== $open) {
            $category['categorylink'] = "<a href='" . XOOPS_URL . '/modules/smartfaq/open_category.php?categoryid=' . $this->categoryid() . "'>" . $this->name() . '</a>';
        } else {
            $category['categorylink'] = "<a href='" . XOOPS_URL . '/modules/smartfaq/category.php?categoryid=' . $this->categoryid() . "'>" . $this->name() . '</a>';
        }
        $category['total']       = $this->getVar('faqcount');
        $category['description'] = $this->description();

        if ($this->getVar('last_faqid') > 0) {
            $category['last_faqid']         = $this->getVar('last_faqid', 'n');
            $category['last_question_link'] = $this->getVar('last_question_link', 'n');
        }

        return $category;
    }
}
