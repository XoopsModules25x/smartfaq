<?php

/**
* $Id: category.php,v 1.24 2006/09/29 18:49:10 malanciault Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

// defined("XOOPS_ROOT_PATH") || exit("XOOPS root path not defined");

class sfCategory extends XoopsObject
{
    /**
     * @var array
     * @access private
     */
    var $_groups_read = null;

    /**
     * @var array
     * @access private
     */
    var $_groups_admin = null;

    /**
    * constructor
    */
    function __construct($id = null)
    {
        $this->db =& XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar("categoryid", XOBJ_DTYPE_INT, null, false);
        $this->initVar("parentid", XOBJ_DTYPE_INT, null, false);
        $this->initVar("name", XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar("description", XOBJ_DTYPE_TXTAREA, null, false, 255);
        $this->initVar("total", XOBJ_DTYPE_INT, 1, false);
        $this->initVar("weight", XOBJ_DTYPE_INT, 1, false);
        $this->initVar("created", XOBJ_DTYPE_INT, null, false);
        $this->initVar("last_faq", XOBJ_DTYPE_INT);

        //not persistent values
        $this->initVar("faqcount", XOBJ_DTYPE_INT, 0, false);
        $this->initVar('last_faqid', XOBJ_DTYPE_INT);
        $this->initVar('last_question_link', XOBJ_DTYPE_TXTBOX);

        if (isset($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                $category_handler = new sfCategoryHandler($this->db);
                $category =& $category_handler->get($id);
                foreach ($category->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
                $this->assignOtherProperties();
            }
        }
    }

    function notLoaded()
    {
       return ($this->getVar('categoryid')== -1);
    }

    function assignOtherProperties()
    {
        global $xoopsUser;
        $smartModule =& sf_getModuleInfo();
        $module_id = $smartModule->getVar('mid');

        $gperm_handler = &xoops_gethandler('groupperm');

        $this->_groups_read = $gperm_handler->getGroupIds('category_read', $this->categoryid(), $module_id);
    }

    function checkPermission()
    {
        include_once XOOPS_ROOT_PATH.'/modules/smartfaq/include/functions.php';

        $userIsAdmin = sf_userIsAdmin();
        if ($userIsAdmin) {
            return true;
        }

        $smartPermHandler =& xoops_getmodulehandler('permission', 'smartfaq');

        $categoriesGranted = $smartPermHandler->getPermissions('category');
        if ( in_array($this->categoryid(), $categoriesGranted) ) {
            $ret = true;
        }

        return $ret;
    }

    function categoryid()
    {
        return $this->getVar("categoryid");
    }

    function parentid()
    {
        return $this->getVar("parentid");
    }

    function name($format="S")
    {
        $ret = $this->getVar("name", $format);
        if (($format=='s') || ($format=='S') || ($format=='show')) {
            $myts =& MyTextSanitizer::getInstance();
            $ret = $myts->displayTarea($ret);
        }

        return $ret;
    }

    function description($format="S")
    {
        return $this->getVar("description", $format);
    }

    function weight()
    {
        return $this->getVar("weight");
    }

    function getCategoryPath($withAllLink = false, $open = false)
    {
        if ($open != false) {
            $filename = "open_category.php";
        } else {
            $filename = "category.php";
        }
        if ($withAllLink) {
            $ret = "<a href='" . XOOPS_URL . "/modules/smartfaq/".$filename."?categoryid=" . $this->categoryid() . "'>" . $this->name() . "</a>";
        } else {
            $ret = $this->name();
        }
        $parentid = $this->parentid();
        $category_handler =& sf_gethandler('category');
        if ($parentid != 0) {
            $parentObj =& $category_handler->get($parentid);
            if ($parentObj->notLoaded()) {
                exit;
            }
            $parentid = $parentObj->parentid();
            $ret = $parentObj->getCategoryPath(true, $open) . " > " .$ret;
        }

        return $ret;
    }

    function getGroups_read()
    {
        if (count($this->_groups_read) < 1) {
            $this->assignOtherProperties();
        }

        return $this->_groups_read;
    }

    function setGroups_read($groups_read = array('0'))
    {
      $this->_groups_read = $groups_read;
    }

    function store($sendNotifications = true, $force = true )
    {
        $category_handler = new sfCategoryHandler($this->db);

        $ret = $category_handler->insert($this, $force);
        if ( $sendNotifications && $ret && ($this->isNew()) ) {
            $this->sendNotifications();
        }
        $this->unsetNew();

        return $ret;
    }

    function sendNotifications()
    {
        $smartModule =& sf_getModuleInfo();

        $myts =& MyTextSanitizer::getInstance();
        $notification_handler = &xoops_gethandler('notification');

        $tags = array();
        $tags['MODULE_NAME'] = $myts->htmlSpecialChars($smartModule->getVar('name'));
        $tags['CATEGORY_NAME'] = $this->name();
        $tags['CATEGORY_URL'] = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/category.php?categoryid=' . $this->categoryid();

        $notification_handler = &xoops_gethandler('notification');
        $notification_handler->triggerEvent('global_faq', 0, 'category_created', $tags);
    }

    function toArray($category = array(), $open = false)
    {
        $category['categoryid'] = $this->categoryid();
        $category['name'] = $this->name();
        if ($open != false) {
            $category['categorylink'] = "<a href='" . XOOPS_URL . "/modules/smartfaq/open_category.php?categoryid=" . $this->categoryid() . "'>" . $this->name() . "</a>";
        } else {
            $category['categorylink'] = "<a href='" . XOOPS_URL . "/modules/smartfaq/category.php?categoryid=" . $this->categoryid() . "'>" . $this->name() . "</a>";
        }
        $category['total'] = $this->getVar('faqcount');
        $category['description'] = $this->description();

        if ($this->getVar('last_faqid') > 0) {
            $category['last_faqid'] = $this->getVar('last_faqid', 'n');
            $category['last_question_link'] = $this->getVar('last_question_link', 'n');
        }

        return $category;
    }
}

/**
* Categories handler class.
* This class is responsible for providing data access mechanisms to the data source
* of Category class objects.
*
* @author marcan <marcan@smartfactory.ca>
* @package SmartFAQ
*/

class sfCategoryHandler extends XoopsObjectHandler
{
    /**
    * create a new category
    *
    * @param bool $isNew flag the new objects as "new"?
    * @return object sfCategory
    */
    function &create($isNew = true)
    {
        $category = new sfCategory();
        if ($isNew) {
            $category->setNew();
        }

        return $category;
    }

    /**
    * retrieve a category
    *
    * @param int $id categoryid of the category
    * @return mixed reference to the {@link sfCategory} object, FALSE if failed
    */
    function &get($id)
    {
        $false = false;
        if (intval($id) > 0) {
            $sql = 'SELECT * FROM '.$this->db->prefix('smartfaq_categories').' WHERE categoryid='.$id;
            if (!$result = $this->db->query($sql)) {
                return $false;
            }

            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $category = new sfCategory();
                $category->assignVars($this->db->fetchArray($result));

                return $category;
            }
        }

        return $false;
    }

    /**
    * insert a new category in the database
    *
    * @param object $category reference to the {@link sfCategory} object
    * @param bool $force
    * @return bool FALSE if failed, TRUE if already present and unchanged or successful
    */
    function insert(&$category, $force = false)
    {
        if (strtolower(get_class($category)) != 'sfcategory') {
            return false;
        }
        if (!$category->isDirty()) {
            return true;
        }
        if (!$category->cleanVars()) {
            return false;
        }

        foreach ($category->cleanVars as $k => $v) {
            ${$k} = $v;
        }

        if ($category->isNew()) {
            $sql = sprintf("INSERT INTO %s (categoryid, parentid, name, description, total, weight, created) VALUES (NULL, %u, %s, %s, %u, %u, %u)", $this->db->prefix('smartfaq_categories'),  $parentid, $this->db->quoteString($name), $this->db->quoteString($description), $total, $weight, time());
        } else {
            $sql = sprintf("UPDATE %s SET parentid = %u, name = %s, description = %s, total = %s, weight = %u, created = %u WHERE categoryid = %u", $this->db->prefix('smartfaq_categories'), $parentid, $this->db->quoteString($name), $this->db->quoteString($description), $total, $weight, $created, $categoryid);
        }
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }
        if ($category->isNew()) {
            $category->assignVar('categoryid', $this->db->getInsertId());
        } else {
            $category->assignVar('categoryid', $categoryid);
        }

        return true;
    }

    /**
    * delete a category from the database
    *
    * @param object $category reference to the category to delete
    * @param bool $force
    * @return bool FALSE if failed.
    */
    function delete(&$category, $force = false)
    {

        if (strtolower(get_class($category)) != 'sfcategory') {
            return false;
        }

        // Deleting the FAQs
        $faq_handler = new sfFaqHandler($this->db);
        if (!$faq_handler->deleteAll(new Criteria('categoryid', $category->categoryid()))) {
            return false;
        }

        // Deleteing the sub categories
        $subcats =& $this->getCategories(0, 0, $category->categoryid());
        foreach ($subcats as $subcat) {
            $this->delete($subcat);
        }

        $sql = sprintf("DELETE FROM %s WHERE categoryid = %u", $this->db->prefix("smartfaq_categories"), $category->getVar('categoryid'));

        $smartModule =& sf_getModuleInfo();
        $module_id = $smartModule->getVar('mid');

        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        xoops_groupperm_deletebymoditem ($module_id, "category_read", $category->categoryid());
        //xoops_groupperm_deletebymoditem ($module_id, "category_admin", $categoryObj->categoryid());

        if (!$result) {
            return false;
        }

        return true;
    }

    /**
    * retrieve categories from the database
    *
    * @param object $criteria {@link CriteriaElement} conditions to be met
    * @param bool $id_as_key use the categoryid as key for the array?
    * @return array array of {@link XoopsFaq} objects
    */
    function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT * FROM '.$this->db->prefix('smartfaq_categories');
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
            $category = new sfCategory();
            $category->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $category;
            } else {
                $ret[$myrow['categoryid']] =& $category;
            }
            unset($category);
        }

        return $ret;
    }

    function &getCategories($limit=0, $start=0, $parentid=0, $sort='weight', $order='ASC', $id_as_key = true)
    {
        include_once XOOPS_ROOT_PATH.'/modules/smartfaq/include/functions.php';

        $criteria = new CriteriaCompo();

        $criteria->setSort($sort);
        $criteria->setOrder($order);

        if ($parentid != -1) {
            $criteria->add(new Criteria('parentid', $parentid));
        }
        if (!sf_userIsAdmin()) {
            $smartPermHandler =& xoops_getmodulehandler('permission', 'smartfaq');

            $categoriesGranted = $smartPermHandler->getPermissions('category');
            $criteria->add(new Criteria('categoryid', "(".implode(',', $categoriesGranted).")", 'IN'));
        }
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $ret = $this->getObjects($criteria, $id_as_key);

        return $ret;
    }

    function &getCategoriesWithOpenQuestion($limit=0, $start=0, $parentid=0, $sort='weight', $order='ASC')
    {
        include_once XOOPS_ROOT_PATH.'/modules/smartfaq/include/functions.php';

        $criteria = new CriteriaCompo();

        $criteria->setSort($sort);
        $criteria->setOrder($order);

        if ($parentid != -1) {
            $criteria->add(new Criteria('c.parentid', $parentid));
        }
        if (!sf_userIsAdmin()) {
            $smartPermHandler =& xoops_getmodulehandler('permission', 'smartfaq');

            $categoriesGranted = $smartPermHandler->getPermissions('category');
            $criteria->add(new Criteria('categoryid', "(".implode(',', $categoriesGranted).")", 'IN'));
        }

        $criteria->add(new Criteria('f.status', _SF_STATUS_OPENED));
        $criteria->setStart($start);
        $criteria->setLimit($limit);

        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT DISTINCT c.categoryid, c.parentid, c.name, c.description, c.total, c.weight, c.created FROM '.$this->db->prefix('smartfaq_categories') . ' AS c INNER JOIN '.$this->db->prefix('smartfaq_faq') . ' AS f ON c.categoryid = f.categoryid';
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
            $category = new sfCategory();
            $category->assignVars($myrow);
            $ret[] =& $category;
            unset($category);
        }

        return $ret;

    }

    /**
    * count Categories matching a condition
    *
    * @param object $criteria {@link CriteriaElement} to match
    * @return int count of categories
    */
    function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('smartfaq_categories');
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

    function getCategoriesCount($parentid=0)
    {

        if ($parentid == -1) {
            return $this->getCount();
        }
        $criteria = new CriteriaCompo();
        if (isset($parentid) && ($parentid != -1)) {
            $criteria->add(new criteria('parentid', $parentid));
            if (!sf_userIsAdmin()) {
                $smartPermHandler =& xoops_getmodulehandler('permission', 'smartfaq');

                $categoriesGranted = $smartPermHandler->getPermissions('category');
                $criteria->add(new Criteria('categoryid', "(".implode(',', $categoriesGranted).")", 'IN'));
            }
        }

        return $this->getCount($criteria);
    }

    function getCategoriesWithOpenQuestionsCount($parentid=0)
    {

        if ($parentid == -1) {
            return $this->getCount();
        }
        $criteria = new CriteriaCompo();
        if (isset($parentid) && ($parentid != -1)) {
            $criteria->add(new criteria('parentid', $parentid));
            if (!sf_userIsAdmin()) {
                $smartPermHandler =& xoops_getmodulehandler('permission', 'smartfaq');

                $categoriesGranted = $smartPermHandler->getPermissions('category');
                $criteria->add(new Criteria('categoryid', "(".implode(',', $categoriesGranted).")", 'IN'));
            }
        }

        $criteria->add(new Criteria('f.status', _SF_STATUS_OPENED));

        $sql = 'SELECT COUNT(c.categoryid) FROM '.$this->db->prefix('smartfaq_categories') . ' AS c INNER JOIN '.$this->db->prefix('smartfaq_faq') . ' AS f ON c.categoryid = f.categoryid';

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

    function getSubCats(&$categories)
    {
        $criteria = new CriteriaCompo('parentid', "(".implode(',', array_keys($categories)).")", 'IN');
        $ret = array();
        if (!sf_userIsAdmin()) {
            $smartPermHandler =& xoops_getmodulehandler('permission', 'smartfaq');

            $categoriesGranted = $smartPermHandler->getPermissions('category');
            $criteria->add(new Criteria('categoryid', "(".implode(',', $categoriesGranted).")", 'IN'));
        }
        $subcats = $this->getObjects($criteria, true);
        foreach ($subcats as $subcat_id => $subcat) {
            $ret[$subcat->getVar('parentid')][$subcat->getVar('categoryid')] = $subcat;
        }

        return $ret;
    }

    /**
    * delete categories matching a set of conditions
    *
    * @param object $criteria {@link CriteriaElement}
    * @return bool FALSE if deletion failed
    */
    function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM '.$this->db->prefix('smartfaq_categories');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
        }
        if (!$this->db->query($sql)) {
            return false;
            // TODO : Also delete the permissions related to each FAQ
            // TODO : What about sub-categories???
        }

        return true;
    }

    /**
    * Change a value for categories with a certain criteria
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
        $sql = 'UPDATE '.$this->db->prefix('smartfaq_categories').' SET '.$set_clause;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
        }
        if (!$this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    function publishedFaqsCount($cat_id = 0)
    {
        return $this->faqsCount($cat_id, $status=array(_SF_STATUS_PUBLISHED, _SF_STATUS_NEW_ANSWER));
    }

    function faqsCount($cat_id = 0, $status='')
    {

        Global $xoopsUser;
        include_once XOOPS_ROOT_PATH.'/modules/smartfaq/include/functions.php';

        $faq_handler =& sf_gethandler('faq');

        return $faq_handler->getCountsByCat($cat_id, $status);
    }
}
