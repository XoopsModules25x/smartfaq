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
 * Q&A handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of Q&A class objects.
 *
 * @author  marcan <marcan@smartfactory.ca>
 * @package SmartFAQ
 */
class FaqHandler extends \XoopsObjectHandler
{
    /**
     * @param  bool $isNew
     * @return Smartfaq\Faq
     */
    public function create($isNew = true)
    {
        $faq = new Smartfaq\Faq();
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
     * @return mixed reference to the {@link Smartfaq\Faq} object, FALSE if failed
     */
    public function get($id)
    {
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('smartfaq_faq') . ' WHERE faqid=' . $id;
            if (!$result = $this->db->query($sql)) {
                return false;
            }

            $numrows = $this->db->getRowsNum($result);
            if (1 == $numrows) {
                $faq = new Smartfaq\Faq();
                $faq->assignVars($this->db->fetchArray($result));

                return $faq;
            }
        }

        return false;
    }

    /**
     * insert a new faq in the database
     *
     * @param \XoopsObject $faq reference to the {@link Smartfaq\Faq}
     *                          object
     * @param  bool        $force
     * @return bool        FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(\XoopsObject $faq, $force = false)
    {
        if ('xoopsmodules\smartfaq\faq' !== strtolower(get_class($faq))) {
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
            $sql = sprintf(
                'INSERT INTO `%s` (faqid, categoryid, question, howdoi, diduno, uid, datesub, status, counter, weight, html, smiley, xcodes, cancomment, comments, notifypub, modulelink, contextpage, exacturl, partialview) VALUES (NULL, %u, %s, %s, %s, %u, %u, %u, %u, %u, %u, %u, %u, %u, %u, %u, %s, %s, %u, %u)',
                           $this->db->prefix('smartfaq_faq'),
                $categoryid,
                $this->db->quoteString($question),
                $this->db->quoteString($howdoi),
                $this->db->quoteString($diduno),
                $uid,
                time(),
                $status,
                $counter,
                $weight,
                $html,
                $smiley,
                $xcodes,
                $cancomment,
                $comments,
                $notifypub,
                $this->db->quoteString($modulelink),
                $this->db->quoteString($contextpage),
                $exacturl,
                $partialview
            );
        } else {
            $sql = sprintf(
                'UPDATE `%s` SET categoryid = %u, question = %s, howdoi = %s, diduno = %s, uid = %u, datesub = %u, status = %u, counter = %u, weight = %u, html = %u, smiley = %u, xcodes = %u, cancomment = %u, comments = %u, notifypub = %u, modulelink = %s, contextpage = %s, exacturl = %u, partialview = %u  WHERE faqid = %u',

                $this->db->prefix('smartfaq_faq'),
                $categoryid,
                $this->db->quoteString($question),
                $this->db->quoteString($howdoi),
                $this->db->quoteString($diduno),
                $uid,
                $datesub,
                $status,
                $counter,
                $weight,
                $html,
                $smiley,
                $xcodes,
                $cancomment,
                $comments,
                $notifypub,
                $this->db->quoteString($modulelink),
                $this->db->quoteString($contextpage),
                $exacturl,
                $partialview,
                $faqid
            );
        }
        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            $faq->setErrors('Could not store data in the database.<br />'.$this->db->error().' ('.$this->db->errno().')<br />'.$sql);

            $logger = \XoopsLogger::getInstance();
            $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
            $logger->addExtra('Token Validation', 'No valid token found in request/session');


            /** @var Smartfaq\Helper $helper */
            $helper = Smartfaq\Helper::getInstance();
            $helper->addLog($this->db->error());

            /** @var \XoopsObject $faq */
//            $faq->setError($this->db->error());


            trigger_error('Class ' . $faq . ' could not be saved ' . __FILE__ . ' at line ' . __LINE__, E_USER_WARNING);

            return false;
        }

        if ($faq->isNew()) {
            $faq->assignVar('faqid', $this->db->getInsertId());
        }

        // Saving permissions
        Smartfaq\Utility::saveItemPermissions($faq->getGroups_read(), $faq->faqid());

        return true;
    }

    /**
     * delete an FAQ from the database
     *
     * @param \XoopsObject $faq reference to the FAQ to delete
     * @param  bool        $force
     * @return bool        FALSE if failed.
     */
    public function delete(\XoopsObject $faq, $force = false)
    {
        $smartModule = Smartfaq\Utility::getModuleInfo();
        $module_id   = $smartModule->getVar('mid');

        if ('xoopsmodules\smartfaq\faq' !== strtolower(get_class($faq))) {
            return false;
        }

        // Deleting the answers
        $answerHandler = new Smartfaq\AnswerHandler($this->db);
        if (!$answerHandler->deleteFaqAnswers($faq)) {
            // error msg...
            echo 'error while deleteing an answer';
        }

        $sql = sprintf('DELETE FROM `%s` WHERE faqid = `%u`', $this->db->prefix('smartfaq_faq'), $faq->getVar('faqid'));

        if (false !== $force) {
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
     * @param  \CriteriaElement $criteria  {@link CriteriaElement} conditions to be met
     * @param  bool            $id_as_key use the faqid as key for the array?
     * @param  string          $notNullFields
     * @return false|array  array of <a href='psi_element://Smartfaq\Faq'>Smartfaq\Faq</a> objects
     */
    public function &getObjects(\CriteriaElement $criteria = null, $id_as_key = false, $notNullFields = '')
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('smartfaq_faq');

        if (null !== $criteria && is_subclass_of($criteria, 'CriteriaElement')) {
            $whereClause = $criteria->renderWhere();

            if ('WHERE ()' !== $whereClause) {
                $sql .= ' ' . $criteria->renderWhere();
                if (!empty($notNullFields)) {
                    $sql .= $this->NotNullFieldClause($notNullFields, true);
                }
            } elseif (!empty($notNullFields)) {
                $sql .= ' WHERE ' . $this->NotNullFieldClause($notNullFields);
            }
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        } elseif (!empty($notNullFields)) {
            $sql .= $sql .= ' WHERE ' . $this->NotNullFieldClause($notNullFields);
        }

        //echo "<br>" . $sql . "<br>";
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return false;
        }

        if (0 == $GLOBALS['xoopsDB']->getRowsNum($result)) {
            $temp = false;
            return $temp;
        }

        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $faq = new Smartfaq\Faq();
            $faq->assignVars($myrow);

            if (!$id_as_key) {
                $ret[] =& $faq;
            } else {
                $ret[$myrow['faqid']] =& $faq;
            }
            unset($faq);
        }

        return $ret;
    }

    /**
     * @param  null|\CriteriaElement $criteria
     * @param  bool                 $id_as_key
     * @param  string               $notNullFields
     * @return array|bool
     */
    public function getObjectsAdminSide(\CriteriaElement $criteria = null, $id_as_key = false, $notNullFields = '')
    {
        $ret   = [];
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

        if (null !== $criteria && is_subclass_of($criteria, 'CriteriaElement')) {
            $whereClause = $criteria->renderWhere();

            if ('WHERE ()' !== $whereClause) {
                $sql .= ' ' . $criteria->renderWhere();
                if (!empty($notNullFields)) {
                    $sql .= $this->NotNullFieldClause($notNullFields, true);
                }
            } elseif (!empty($notNullFields)) {
                $sql .= ' WHERE ' . $this->NotNullFieldClause($notNullFields);
            }
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        } elseif (!empty($notNullFields)) {
            $sql .= $sql .= ' WHERE ' . $this->NotNullFieldClause($notNullFields);
        }

        //echo "<br>" . $sql . "<br>";
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return false;
        }

        if (0 == $GLOBALS['xoopsDB']->getRowsNum($result)) {
            return false;
        }

        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $faq = new Smartfaq\Faq();
            $faq->assignVars($myrow);

            if (!$id_as_key) {
                $ret[] =& $faq;
            } else {
                $ret[$myrow['faqid']] =& $faq;
            }
            unset($faq);
        }

        return $ret;

        /*while (false !== ($myrow = $this->db->fetchArray($result))) {
            $faq = new Smartfaq\Faq($myrow['faqid']);

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
        if (null !== $criteria && is_subclass_of($criteria, 'CriteriaElement')) {
            $whereClause = $criteria->renderWhere();
            if ('WHERE ()' !== $whereClause) {
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

        //echo "<br>" . $sql . "<br>";
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

        $userIsAdmin = Smartfaq\Utility::userIsAdmin();
        // Categories for which user has access
        if (!$userIsAdmin) {
            /** @var Smartfaq\PermissionHandler $smartPermHandler */
            $smartPermHandler = Smartfaq\Helper::getInstance()->getHandler('Permission');

            $categoriesGranted = $smartPermHandler->getPermissions('category');
            $grantedCategories = new \Criteria('categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN');

            $faqsGranted = $smartPermHandler->getPermissions('item');
            $grantedFaq  = new \CriteriaCompo();
            $grantedFaq->add(new \Criteria('faqid', '(' . implode(',', $faqsGranted) . ')', 'IN'), 'OR');
            // If user is anonymous, check if the FAQ allow partialview
            if (!is_object($xoopsUser)) {
                $grantedFaq->add(new \Criteria('partialview', '1'), 'OR');
            }
        }

        if (isset($categoryid) && (-1 != $categoryid)) {
            $criteriaCategory = new \Criteria('categoryid', $categoryid);
        }

        $criteriaStatus = new \CriteriaCompo();
        if (!empty($status) && is_array($status)) {
            foreach ($status as $v) {
                $criteriaStatus->add(new \Criteria('status', $v), 'OR');
            }
        } elseif (!empty($status) && (-1 != $status)) {
            $criteriaStatus->add(new \Criteria('status', $status), 'OR');
        }

        $criteriaPermissions = new \CriteriaCompo();
        if (!$userIsAdmin) {
            $criteriaPermissions->add($grantedCategories, 'AND');
            $criteriaPermissions->add($grantedFaq, 'AND');
        }

        $criteria = new \CriteriaCompo();
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
            return [];
        }
        $ret = [];
        while (false !== (list($status, $count) = $this->db->fetchRow($result))) {
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
    public function getAllPublished(
        $limit = 0,
        $start = 0,
        $categoryid = -1,
        $sort = 'datesub',
        $order = 'DESC',
        $asobject = true
    ) {
        return $this->getFaqs($limit, $start, [Constants::SF_STATUS_PUBLISHED, Constants::SF_STATUS_NEW_ANSWER], $categoryid, $sort, $order, null, $asobject, null);
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
    public function getFaqs(
        $limit = 0,
        $start = 0,
        $status = '',
        $categoryid = -1,
        $sort = 'datesub',
        $order = 'DESC',
        $notNullFields = '',
        $asobject = true,
        $otherCriteria = null
    ) {
        global $xoopsUser;
//        require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

        //if ( ($categoryid == -1) && (empty($status) || ($status == -1)) && ($limit == 0) && ($start ==0) ) {
        //  return $this->getObjects();
        //}
        $ret         = [];
        $userIsAdmin = Smartfaq\Utility::userIsAdmin();
        // Categories for which user has access
        if (!$userIsAdmin) {
            /** @var Smartfaq\PermissionHandler $smartPermHandler */
            $smartPermHandler = Smartfaq\Helper::getInstance()->getHandler('Permission');

            $categoriesGranted = $smartPermHandler->getPermissions('category');
            $grantedCategories = new \Criteria('categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN');

            $faqsGranted = $smartPermHandler->getPermissions('item');
            $grantedFaq  = new \CriteriaCompo();
            $grantedFaq->add(new \Criteria('faqid', '(' . implode(',', $faqsGranted) . ')', 'IN'), 'OR');
            // If user is anonymous, check if the FAQ allow partialview
            if (!is_object($xoopsUser)) {
                $grantedFaq->add(new \Criteria('partialview', '1'), 'OR');
            }
        }

        if (isset($categoryid) && (-1 != $categoryid)) {
            if (is_array($categoryid)) {
                $criteriaCategory = new \Criteria('categoryid', '(' . implode(',', $categoryid) . ')', 'IN');
            } else {
                $criteriaCategory = new \Criteria('categoryid', (int)$categoryid);
            }
        }

        if (!empty($status) && is_array($status)) {
            $criteriaStatus = new \CriteriaCompo();
            foreach ($status as $v) {
                $criteriaStatus->add(new \Criteria('status', $v), 'OR');
            }
        } elseif (!empty($status) && (-1 != $status)) {
            $criteriaStatus = new \CriteriaCompo();
            $criteriaStatus->add(new \Criteria('status', $status), 'OR');
        }

        $criteriaPermissions = new \CriteriaCompo();
        if (!$userIsAdmin) {
            $criteriaPermissions->add($grantedCategories, 'AND');
            $criteriaPermissions->add($grantedFaq, 'AND');
        }

        $criteria = new \CriteriaCompo();
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
        $ret =& $this->getObjects($criteria, false, $notNullFields);

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
    public function getFaqsAdminSide(
        $limit = 0,
        $start = 0,
        $status = '',
        $categoryid = -1,
        $sort = 'datesub',
        $order = 'DESC',
        $asobject = true,
        $otherCriteria = null
    ) {
//        require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

//        $smartModule = Smartfaq\Utility::getModuleInfo();

        $ret = [];

        if (isset($categoryid) && (-1 != $categoryid)) {
            $criteriaCategory = new \Criteria('faq.categoryid', $categoryid);
        }

        if (!empty($status) && is_array($status)) {
            $criteriaStatus = new \CriteriaCompo();
            foreach ($status as $v) {
                $criteriaStatus->add(new \Criteria('faq.status', $v), 'OR');
            }
        } elseif (!empty($status) && (-1 != $status)) {
            $criteriaStatus = new \CriteriaCompo();
            $criteriaStatus->add(new \Criteria('faq.status', $status), 'OR');
        }

        $criteria = new \CriteriaCompo();
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
        $ret = $this->getObjectsAdminSide($criteria, false);

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
                $ret =& $faq[0];
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

        $otherCriteria = new \CriteriaCompo();
        $otherCriteria->add(new \Criteria('modulelink', 'None', '<>'));

        $faqsObj = $this->getFaqs(0, 0, [Constants::SF_STATUS_PUBLISHED, Constants::SF_STATUS_NEW_ANSWER], -1, 'datesub', 'DESC', '', true, $otherCriteria);

        $totalfaqs  = count($faqsObj);
        $randomFaqs = [];
        if ($faqsObj) {
            foreach ($faqsObj as $i => $iValue) {
                $display = false;

                $http        = (false === strpos(XOOPS_URL, 'https://')) ? 'http://' : 'https://';
                $phpself     = $_SERVER['PHP_SELF'];
                $httphost    = $_SERVER['HTTP_HOST'];
                $querystring = $_SERVER['QUERY_STRING'];
                if ('' != $querystring) {
                    $querystring = '?' . $querystring;
                }
                $currenturl     = $http . $httphost . $phpself . $querystring;
                $fullcontexturl = XOOPS_URL . '/' . $iValue->contextpage();
                switch ($iValue->modulelink()) {
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
                        if ($iValue->exacturl()) {
                            $display = ($currenturl == $fullcontexturl);
                        } else {
                            $display = (false === strpos($currenturl, $fullcontexturl));
                        }
                        break;
                    default:
                        if (false === strpos($currenturl, XOOPS_URL . '/modules/')) {
                            $display = false;
                        } else {
                            if (false === strpos($currenturl, $iValue->modulelink())) {
                                $display = false;
                            } else {
                                $display = true;
                            }
                        }
                        break;
                }
                if ($display) {
                    $randomFaqs[] =& $faqsObj[$i];
                }
            }
        }

        if (count($randomFaqs) > $limit) {
            mt_srand((float)microtime() * 10000000);
            $rand_keys = array_rand($randomFaqs, $limit);
            for ($j = 0, $jMax = count($rand_keys); $j < $jMax; ++$j) {
                $ret[] =& $randomFaqs[$rand_keys[$j]];
            }
        } else {
            $ret =& $randomFaqs;
        }

        return $ret;
    }

    /**
     * @param  array $status
     * @return array
     */
    public function getLastPublishedByCat($status = [Constants::SF_STATUS_PUBLISHED, Constants::SF_STATUS_NEW_ANSWER])
    {
        $ret       = [];
        $faqclause = '';
        if (!Smartfaq\Utility::userIsAdmin()) {
            /** @var Smartfaq\PermissionHandler $smartPermHandler */
            $smartPermHandler = Smartfaq\Helper::getInstance()->getHandler('Permission');
            $items            = $smartPermHandler->getPermissions('item');
            $faqclause        = ' AND faqid IN (' . implode(',', $items) . ')';
        }

        $sql  = "CREATE TEMPORARY TABLE tmp (categoryid INT(8) UNSIGNED NOT NULL,datesub INT(11) DEFAULT '0' NOT NULL);";
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
        while (false !== ($row = $this->db->fetchArray($result))) {
            $faq = new Smartfaq\Faq();
            $faq->assignVars($row);
            $ret[$row['categoryid']] =& $faq;
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
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
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
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
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
    public function getFaqsFromSearch($queryarray = [], $andor = 'AND', $limit = 0, $offset = 0, $userid = 0)
    {
        global $xoopsUser;

        $ret = [];

        $userIsAdmin = Smartfaq\Utility::userIsAdmin();

        if (0 != $userid) {
            $criteriaUser = new \CriteriaCompo();
            $criteriaUser->add(new \Criteria('faq.uid', $userid), 'OR');
            $criteriaUser->add(new \Criteria('answer.uid', $userid), 'OR');
        }

        if (! empty($queryarray)) {
            $criteriaKeywords = new \CriteriaCompo();
            foreach ($queryarray as $iValue) {
                $criteriaKeyword = new \CriteriaCompo();
                $criteriaKeyword->add(new \Criteria('faq.question', '%' . $iValue . '%', 'LIKE'), 'OR');
                $criteriaKeyword->add(new \Criteria('answer.answer', '%' . $iValue . '%', 'LIKE'), 'OR');
                $criteriaKeywords->add($criteriaKeyword, $andor);
                unset($criteriaKeyword);
            }
        }

        // Categories for which user has access
        if (!$userIsAdmin) {
            /** @var Smartfaq\PermissionHandler $smartPermHandler */
            $smartPermHandler = Smartfaq\Helper::getInstance()->getHandler('Permission');

            $categoriesGranted = $smartPermHandler->getPermissions('category');
            $faqsGranted       = $smartPermHandler->getPermissions('item');
            if (empty($categoriesGranted)) {
                return $ret;
            }
            if (empty($faqsGranted)) {
                return $ret;
            }
            $grantedCategories = new \Criteria('faq.categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN');
            $grantedFaq        = new \CriteriaCompo();
            $grantedFaq->add(new \Criteria('faq.faqid', '(' . implode(',', $faqsGranted) . ')', 'IN'), 'OR');
            // If user is anonymous, check if the FAQ allow partialview
            if (!is_object($xoopsUser)) {
                $grantedFaq->add(new \Criteria('partialview', '1'), 'OR');
            }
        }

        $criteriaPermissions = new \CriteriaCompo();
        if (!$userIsAdmin) {
            $criteriaPermissions->add($grantedCategories, 'AND');
            $criteriaPermissions->add($grantedFaq, 'AND');
        }

        $criteriaAnswersStatus = new \CriteriaCompo();
        $criteriaAnswersStatus->add(new \Criteria('answer.status', Constants::SF_AN_STATUS_APPROVED));

        $criteriaFasStatus = new \CriteriaCompo();
        $criteriaFasStatus->add(new \Criteria('faq.status', Constants::SF_STATUS_OPENED), 'OR');
        $criteriaFasStatus->add(new \Criteria('faq.status', Constants::SF_STATUS_PUBLISHED), 'OR');

        $criteria = new \CriteriaCompo();
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

        $sql = 'SELECT faq.faqid, faq.question, faq.datesub, faq.uid FROM ' . $this->db->prefix('smartfaq_faq') . ' AS faq INNER JOIN ' . $this->db->prefix('smartfaq_answers') . ' AS answer ON faq.faqid = answer.faqid';

        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $whereClause = $criteria->renderWhere();

            if ('WHERE ()' !== $whereClause) {
                $sql .= ' ' . $criteria->renderWhere();
                if ('' != $criteria->getSort()) {
                    $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
                }
                $limit = $criteria->getLimit();
                $start = $criteria->getStart();
            }
        }

        //echo "<br>" . $sql . "<br>";

        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            trigger_error('Query did not work in smartfaq', E_USER_WARNING);

            return $ret;
        }

        if (0 == $GLOBALS['xoopsDB']->getRowsNum($result)) {
            return $ret;
        }

        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $faq = new Smartfaq\Faq();
            $faq->assignVars($myrow);
            $ret[] =& $faq;
            unset($faq);
        }

        return $ret;
    }

    /**
     * @param  int   $cat_id
     * @param        $status
     * @return array
     */
    public function getCountsByCat($cat_id = 0, $status)
    {
        global $xoopsUser;
        $ret = [];
        $sql = 'SELECT categoryid, COUNT(*) AS count FROM ' . $this->db->prefix('smartfaq_faq');
        if ((int)$cat_id > 0) {
            $sql .= ' WHERE categoryid = ' . (int)$cat_id;
            $sql .= ' AND status IN (' . implode(',', $status) . ')';
        } else {
            $sql .= ' WHERE status IN (' . implode(',', $status) . ')';
            if (!Smartfaq\Utility::userIsAdmin()) {
                /** @var Smartfaq\PermissionHandler $smartPermHandler */
                $smartPermHandler = Smartfaq\Helper::getInstance()->getHandler('Permission');
                $items            = $smartPermHandler->getPermissions('item');
                if (is_object($xoopsUser)) {
                    $sql .= ' AND faqid IN (' . implode(',', $items) . ')';
                } else {
                    $sql .= ' AND (faqid IN (' . implode(',', $items) . ') OR partialview = 1)';
                }
            }
        }
        $sql .= ' GROUP BY categoryid';

        //echo "<br>" . $sql . "<br>";

        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while (false !== ($row = $this->db->fetchArray($result))) {
            $ret[$row['categoryid']] = (int)$row['count'];
        }

        return $ret;
    }
}
