<?php namespace XoopsModules\Smartfaq;

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use \XoopsModules\Smartfaq;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');


/**
 * Categories handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of Category class objects.
 *
 * @author  marcan <marcan@smartfactory.ca>
 * @package SmartFAQ
 */
class CategoryHandler extends \XoopsObjectHandler
{
    /**
     * create a new category
     *
     * @param  bool $isNew flag the new objects as "new"?
     * @return object Smartfaq\Category
     */
    public function create($isNew = true)
    {
        $category = new Smartfaq\Category();
        if ($isNew) {
            $category->setNew();
        }

        return $category;
    }

    /**
     * retrieve a category
     *
     * @param  int $id categoryid of the category
     * @return mixed reference to the {@link Smartfaq\Category} object, FALSE if failed
     */
    public function get($id)
    {
        $false = false;
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('smartfaq_categories') . ' WHERE categoryid=' . $id;
            if (!$result = $this->db->query($sql)) {
                return $false;
            }

            $numrows = $this->db->getRowsNum($result);
            if (1 == $numrows) {
                $category = new Smartfaq\Category();
                $category->assignVars($this->db->fetchArray($result));

                return $category;
            }
        }

        return $false;
    }

    /**
     * insert a new category in the database
     *
     * @param \XoopsObject $category reference to the {@link Smartfaq\Category}
     *                               object
     * @param  bool        $force
     * @return bool        FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(\XoopsObject $category, $force = false)
    {
        if ('xoopsmodules\smartfaq\category' !== strtolower(get_class($category))) {
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
            $sql = sprintf(
                'INSERT INTO `%s` (parentid, name, description, total, weight, created) VALUES (%u, %s, %s, %u, %u, %u)',
                $this->db->prefix('smartfaq_categories'),
                           $parentid,
                $this->db->quoteString($name),
                $this->db->quoteString($description),
                $total,
                $weight,
                time()
            );
        } else {
            $sql = sprintf(
                'UPDATE `%s` SET parentid = %u, name = %s, description = %s, total = %s, weight = %u, created = %u WHERE categoryid = %u',
                $this->db->prefix('smartfaq_categories'),
                $parentid,
                           $this->db->quoteString($name),
                $this->db->quoteString($description),
                $total,
                $weight,
                $created,
                $categoryid
            );
        }
        if (false !== $force) {
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
     * @param \XoopsObject $category reference to the category to delete
     * @param  bool        $force
     * @return bool        FALSE if failed.
     */
    public function delete(\XoopsObject $category, $force = false)
    {
        if ('xoopsmodules\smartfaq\category' !== strtolower(get_class($category))) {
            return false;
        }

        // Deleting the FAQs
        $faqHandler = new Smartfaq\FaqHandler($this->db);
        if (!$faqHandler->deleteAll(new \Criteria('categoryid', $category->categoryid()))) {
            return false;
        }

        // Deleteing the sub categories
        $subcats =& $this->getCategories(0, 0, $category->categoryid());
        foreach ($subcats as $subcat) {
            $this->delete($subcat);
        }

        $sql = sprintf('DELETE FROM `%s` WHERE categoryid = %u', $this->db->prefix('smartfaq_categories'), $category->getVar('categoryid'));

        $smartModule = Smartfaq\Utility::getModuleInfo();
        $module_id   = $smartModule->getVar('mid');

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        xoops_groupperm_deletebymoditem($module_id, 'category_read', $category->categoryid());
        //xoops_groupperm_deletebymoditem ($module_id, "category_admin", $categoryObj->categoryid());

        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     * retrieve categories from the database
     *
     * @param  object $criteria  {@link CriteriaElement} conditions to be met
     * @param  bool   $id_as_key use the categoryid as key for the array?
     * @return array  array of {@link XoopsFaq} objects
     */
    public function getObjects($criteria = null, $id_as_key = false)
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('smartfaq_categories');
        if (null !== $criteria && is_subclass_of($criteria, 'CriteriaElement')) {
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
            $category = new Smartfaq\Category();
            $category->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $category;
            } else {
                $ret[$myrow['categoryid']] = $category;
            }
            unset($category);
        }

        return $ret;
    }

    /**
     * @param  int    $limit
     * @param  int    $start
     * @param  int    $parentid
     * @param  string $sort
     * @param  string $order
     * @param  bool   $id_as_key
     * @return array
     */
    public function &getCategories(
        $limit = 0,
        $start = 0,
        $parentid = 0,
        $sort = 'weight',
        $order = 'ASC',
        $id_as_key = true
    ) {
//        require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

        $criteria = new \CriteriaCompo();

        $criteria->setSort($sort);
        $criteria->setOrder($order);

        if (-1 != $parentid) {
            $criteria->add(new \Criteria('parentid', $parentid));
        }
        if (!Smartfaq\Utility::userIsAdmin()) {
            /** @var Smartfaq\PermissionHandler $smartPermHandler */
            $smartPermHandler = Smartfaq\Helper::getInstance()->getHandler('Permission');

            $categoriesGranted = $smartPermHandler->getPermissions('category');
            $criteria->add(new \Criteria('categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN'));
        }
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $ret = $this->getObjects($criteria, $id_as_key);

        return $ret;
    }

    /**
     * @param  int    $limit
     * @param  int    $start
     * @param  int    $parentid
     * @param  string $sort
     * @param  string $order
     * @return array
     */
    public function &getCategoriesWithOpenQuestion(
        $limit = 0,
        $start = 0,
        $parentid = 0,
        $sort = 'weight',
        $order = 'ASC'
    ) {
//        require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

        $criteria = new \CriteriaCompo();

        $criteria->setSort($sort);
        $criteria->setOrder($order);

        if (-1 != $parentid) {
            $criteria->add(new \Criteria('c.parentid', $parentid));
        }
        if (!Smartfaq\Utility::userIsAdmin()) {
            /** @var Smartfaq\PermissionHandler $smartPermHandler */
            $smartPermHandler = Smartfaq\Helper::getInstance()->getHandler('Permission');

            $categoriesGranted = $smartPermHandler->getPermissions('category');
            $criteria->add(new \Criteria('categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN'));
        }

        $criteria->add(new \Criteria('f.status', Constants::SF_STATUS_OPENED));
        $criteria->setStart($start);
        $criteria->setLimit($limit);

        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT DISTINCT c.categoryid, c.parentid, c.name, c.description, c.total, c.weight, c.created FROM ' . $this->db->prefix('smartfaq_categories') . ' AS c INNER JOIN ' . $this->db->prefix('smartfaq_faq') . ' AS f ON c.categoryid = f.categoryid';
        if (null !== $criteria && is_subclass_of($criteria, 'CriteriaElement')) {
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
            $category = new Smartfaq\Category();
            $category->assignVars($myrow);
            $ret[] = $category;
            unset($category);
        }

        return $ret;
    }

    /**
     * count Categories matching a condition
     *
     * @param  object $criteria {@link CriteriaElement} to match
     * @return int    count of categories
     */
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('smartfaq_categories');
        if (null !== $criteria && is_subclass_of($criteria, 'CriteriaElement')) {
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
     * @param  int $parentid
     * @return int
     */
    public function getCategoriesCount($parentid = 0)
    {
        if (-1 == $parentid) {
            return $this->getCount();
        }
        $criteria = new \CriteriaCompo();
        if (isset($parentid) && (-1 != $parentid)) {
            $criteria->add(new \Criteria('parentid', $parentid));
            if (!Smartfaq\Utility::userIsAdmin()) {
                /** @var Smartfaq\PermissionHandler $smartPermHandler */
                $smartPermHandler = Smartfaq\Helper::getInstance()->getHandler('Permission');

                $categoriesGranted = $smartPermHandler->getPermissions('category');
                $criteria->add(new \Criteria('categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN'));
            }
        }

        return $this->getCount($criteria);
    }

    /**
     * @param  int $parentid
     * @return int
     */
    public function getCategoriesWithOpenQuestionsCount($parentid = 0)
    {
        if (-1 == $parentid) {
            return $this->getCount();
        }
        $criteria = new \CriteriaCompo();
        if (isset($parentid) && (-1 != $parentid)) {
            $criteria->add(new \Criteria('parentid', $parentid));
            if (!Smartfaq\Utility::userIsAdmin()) {
                /** @var Smartfaq\PermissionHandler $smartPermHandler */
                $smartPermHandler = Smartfaq\Helper::getInstance()->getHandler('Permission');

                $categoriesGranted = $smartPermHandler->getPermissions('category');
                $criteria->add(new \Criteria('categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN'));
            }
        }

        $criteria->add(new \Criteria('f.status', Constants::SF_STATUS_OPENED));

        $sql = 'SELECT COUNT(c.categoryid) FROM ' . $this->db->prefix('smartfaq_categories') . ' AS c INNER JOIN ' . $this->db->prefix('smartfaq_faq') . ' AS f ON c.categoryid = f.categoryid';

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
     * @param $categories
     * @return array
     */
    public function getSubCats($categories)
    {
        $criteria = new \CriteriaCompo(new \Criteria('parentid', '(' . implode(',', array_keys($categories)) . ')'), 'IN');
        $ret      = [];
        if (!Smartfaq\Utility::userIsAdmin()) {
            /** @var Smartfaq\PermissionHandler $smartPermHandler */
            $smartPermHandler = Smartfaq\Helper::getInstance()->getHandler('Permission');

            $categoriesGranted = $smartPermHandler->getPermissions('category');
            $criteria->add(new \Criteria('categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN'));
        }
        $subcats =& $this->getObjects($criteria, true);
        foreach ($subcats as $subcat_id => $subcat) {
            $ret[$subcat->getVar('parentid')][$subcat->getVar('categoryid')] = $subcat;
        }

        return $ret;
    }

    /**
     * delete categories matching a set of conditions
     *
     * @param  object $criteria {@link CriteriaElement}
     * @return bool   FALSE if deletion failed
     */
    public function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix('smartfaq_categories');
        if (null !== $criteria && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
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
     * @param string          $fieldname  Name of the field
     * @param string          $fieldvalue Value to write
     * @param \CriteriaElement $criteria   {@link CriteriaElement}
     *
     * @return bool
     **/
    public function updateAll($fieldname, $fieldvalue, \CriteriaElement $criteria = null)
    {
        $set_clause = is_numeric($fieldvalue) ? $fieldname . ' = ' . $fieldvalue : $fieldname . ' = ' . $this->db->quoteString($fieldvalue);
        $sql        = 'UPDATE ' . $this->db->prefix('smartfaq_categories') . ' SET ' . $set_clause;
        if (null !== $criteria && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * @param  int $cat_id
     * @return mixed
     */
    public function publishedFaqsCount($cat_id = 0)
    {
        return $this->faqsCount($cat_id, $status = [Constants::SF_STATUS_PUBLISHED, Constants::SF_STATUS_NEW_ANSWER]);
    }

    /**
     * @param  int    $cat_id
     * @param  string $status
     * @return mixed
     */
    public function faqsCount($cat_id = 0, $status = '')
    {
        global $xoopsUser;
//        require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

        /** @var Smartfaq\FaqHandler $faqHandler */
        $faqHandler = Smartfaq\Helper::getInstance()->getHandler('Faq');

        return $faqHandler->getCountsByCat($cat_id, $status);
    }
}
