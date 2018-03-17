<?php

/**
 * Module: SmartFAQ
 * Author: Marius Scurtescu <mariuss@romanians.bc.ca>
 * Licence: GNU
 *
 * Import script from XoopsFAQ to SmartFAQ.
 *
 * It was tested with XoopsFAQ version 1.1 and SmartFAQ version 1.0 beta
 *
 */

use XoopsModules\Smartfaq;
use XoopsModules\Smartfaq\Constants;

require_once __DIR__ . '/admin_header.php';

$importFromModuleName = 'XoopsFAQ';
$scriptname           = 'xoopsfaq.php';

$op = 'start';

if (isset($_POST['op']) && ('go' === $_POST['op'])) {
    $op = $_POST['op'];
}

if ('start' === $op) {
    xoops_cp_header();
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    $result = $xoopsDB->queryF('SELECT count(*) FROM ' . $xoopsDB->prefix('xoopsfaq_categories'));
    list($totalCat) = $xoopsDB->fetchRow($result);

    Smartfaq\Utility::collapsableBar('bottomtable', 'bottomtableicon');
    echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . sprintf(_AM_SF_IMPORT_FROM, $importFromModuleName) . '</h3>';
    echo "<div id='bottomtable'>";

    if (0 == $totalCat) {
        echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . _AM_SF_IMPORT_NO_CATEGORY . '</span>';
    } else {
        require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

        $result = $xoopsDB->queryF('SELECT count(*) FROM ' . $xoopsDB->prefix('xoopsfaq_contents'));
        list($totalFAQ) = $xoopsDB->fetchRow($result);

        if (0 == $totalFAQ) {
            echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . sprintf(_AM_SF_IMPORT_MODULE_FOUND_NO_FAQ, $importFromModuleName, $totalCat) . '</span>';
        } else {
            echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . sprintf(_AM_SF_IMPORT_MODULE_FOUND, $importFromModuleName, $totalCat, $totalFAQ) . '</span>';

            $form = new \XoopsThemeForm(_AM_SF_IMPORT_SETTINGS, 'import_form', XOOPS_URL . '/modules/smartfaq/admin/' . $scriptname);

            // Categories to be imported
            $cat_cbox = new \XoopsFormCheckBox(sprintf(_AM_SF_IMPORT_CATEGORIES, $importFromModuleName), 'import_category', -1);
            $result   = $xoopsDB->queryF('SELECT c.category_id, c.category_title, count(q.contents_id) FROM ' . $xoopsDB->prefix('xoopsfaq_categories') . ' AS c, ' . $xoopsDB->prefix('xoopsfaq_contents') . ' AS q WHERE c.category_id=q.category_id GROUP BY c.category_id ORDER BY category_order');

            while (false !== (list($cid, $cat_title, $count) = $xoopsDB->fetchRow($result))) {
                $cat_cbox->addOption($cid, "$cat_title ($count)<br\>");
            }
            $form->addElement($cat_cbox);

            // SmartFAQ parent category
            $mytree = new Smartfaq\Tree($xoopsDB->prefix('smartfaq_categories'), 'categoryid', 'parentid');
            ob_start();
            $mytree->makeMySelBox('name', 'weight', $preset_id = 0, $none = 1, $sel_name = 'parent_category');
            $form->addElement(new \XoopsFormLabel(_AM_SF_IMPORT_PARENT_CATEGORY, ob_get_contents()));
            ob_end_clean();

            // Auto-Approve
            $form->addElement(new \XoopsFormRadioYN(_AM_SF_IMPORT_AUTOAPPROVE, 'autoaprove', 1, ' ' . _AM_SF_YES . '', ' ' . _AM_SF_NO . ''));

            // Submitted and answered by
            $memberHandler = xoops_getHandler('member');
            $user_select   = new \XoopsFormSelect(_AM_SF_IMPORTED_USER, 'uid', 0);
            $user_select->addOption(0, '----');
            $criteria = new \CriteriaCompo();
            $criteria->setSort('uname');
            $criteria->setOrder('ASC');
            $user_select->addOptionArray($memberHandler->getUserList($criteria));
            $form->addElement($user_select);

            // Q&As can be commented?
            $form->addElement(new \XoopsFormRadioYN(_AM_SF_IMPORT_ALLOWCOMMENTS, 'cancomment', 1, ' ' . _AM_SF_YES . '', ' ' . _AM_SF_NO . ''));

            $group_list      = $memberHandler->getGroupList();
            $groups_selected = [];
            $groups_checkbox = new \XoopsFormCheckBox(_AM_SF_IMPORT_PERMISSIONS, 'groups_read');
            foreach ($group_list as $group_id => $group_name) {
                if (XOOPS_GROUP_ADMIN != $group_id) {
                    $groups_selected [] = $group_id;
                    $groups_checkbox->addOption($group_id, $group_name);
                }
            }
            $groups_checkbox->setValue($groups_selected);
            $form->addElement($groups_checkbox);

            $form->addElement(new \XoopsFormHidden('op', 'go'));
            $form->addElement(new \XoopsFormButton('', 'import', _AM_SF_IMPORT, 'submit'));
            $form->display();
        }

        exit();
    }
}

if ('go' === $op) {
    require_once __DIR__ . '/admin_header.php';

    $import_category = (isset($_POST['import_category']) ? $_POST['import_category'] : null);
    if (!$import_category) {
        redirect_header($scriptname, 2, _AM_SF_NOCATSELECTED);
    }

    xoops_cp_header();

    Smartfaq\Utility::collapsableBar('bottomtable', 'bottomtableicon');
    echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . sprintf(_AM_SF_IMPORT_FROM, $importFromModuleName) . '</h3>';
    echo "<div id='bottomtable'>";
    echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . _AM_SF_IMPORT_RESULT . '</span>';

    $cnt_imported_cat = 0;
    $cnt_imported_faq = 0;

    $parentId    = $_POST['parent_category'];
    $groups_read = isset($_POST['groups_read']) ? $_POST['groups_read'] : [];
    $uid         = !empty($_POST['uid']) ? $_POST['uid'] : 0;
    $cancomment  = $_POST['cancomment'];
    $autoaprove  = $_POST['autoaprove'];

    if (is_array($_POST['import_category'])) {
        $import_category_list = implode(',', $_POST['import_category']);
    } else {
        $import_category_list = $_POST['import_category'];
    }

    /** @var \XoopsModules\Smartfaq\CategoryHandler $categoryHandler */
    $categoryHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Category');
    $faqHandler      = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');
    $answerHandler   = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Answer');

    /*echo "Parent Category ID: $parentId<br>";
    echo "Groups Read: " . implode (",", $groups_read) . "<br>";
    echo "Import Categories: $import_category_list<br>";
    echo "User ID: $uid<br>";
    echo "Can Comment: $cancomment<br>";
    echo "Auto aprove: $autoaprove<br>";*/

    $resultCat = $xoopsDB->queryF('select * from ' . $xoopsDB->prefix('xoopsfaq_categories') . " where category_id in ($import_category_list) order by category_order");

    while (false !== ($arrCat = $xoopsDB->fetchArray($resultCat))) {
        extract($arrCat, EXTR_PREFIX_ALL, 'xcat');

        // insert category into SmartFAQ
        $categoryObj = $categoryHandler->create();

        $categoryObj->setVar('parentid', $parentId);
        $categoryObj->setVar('weight', $xcat_category_order);
        $categoryObj->setGroups_read($groups_read);
        $categoryObj->setVar('name', $xcat_category_title);

        if (!$categoryObj->store(false)) {
            echo sprintf(_AM_SF_IMPORT_CATEGORY_ERROR, $xcat_name) . '<br>';
            continue;
        }

        Smartfaq\Utility::saveCategoryPermissions($categoryObj->getGroups_read(), $categoryObj->categoryid(), 'category_read');

        ++$cnt_imported_cat;

        echo sprintf(_AM_SF_IMPORT_CATEGORY_SUCCESS, $xcat_category_title) . "<br\>";

        $resultFAQ = $xoopsDB->queryF('select * from ' . $xoopsDB->prefix('xoopsfaq_contents') . " where category_id=$xcat_category_id order by contents_order");
        while (false !== ($arrFAQ = $xoopsDB->fetchArray($resultFAQ))) {
            extract($arrFAQ, EXTR_PREFIX_ALL, 'xfaq');

            if (1 != $xfaq_contents_visible) {
                $qstatus = Constants::SF_STATUS_OFFLINE;
            } elseif ($autoaprove) {
                $qstatus = Constants::SF_STATUS_PUBLISHED;
            } else {
                $qstatus = Constants::SF_STATUS_SUBMITTED;
            }

            // insert question into SmartFAQ
            $faqObj    = $faqHandler->create();
            $answerObj = $answerHandler->create();

            $faqObj->setGroups_read($groups_read);
            $faqObj->setVar('categoryid', $categoryObj->categoryid());
            $faqObj->setVar('question', $xfaq_contents_title);
            $faqObj->setVar('uid', $uid);
            $faqObj->setVar('status', $qstatus);
            $faqObj->setVar('weight', $xfaq_contents_order);
            $faqObj->setVar('html', 1 == $xfaq_contents_nohtml ? 0 : 1);
            $faqObj->setVar('smiley', 1 == $xfaq_contents_nosmiley ? 0 : 1);
            $faqObj->setVar('xcodes', 1 == $xfaq_contents_noxcode ? 0 : 1);
            $faqObj->setVar('cancomment', $cancomment);

            if (!$faqObj->store(false)) {
                echo sprintf('  ' . _AM_SF_IMPORT_FAQ_ERROR, $xfaq_contents_title) . '<br>';
                continue;
            } else {
                $answerObj->setVar('faqid', $faqObj->faqid());
                $answerObj->setVar('answer', $xfaq_contents_contents);
                $answerObj->setVar('uid', $uid);
                $answerObj->setVar('status', Constants::SF_AN_STATUS_APPROVED);

                if (!$answerObj->store()) {
                    echo sprintf('  ' . _AM_SF_IMPORT_FAQ_ERROR) . '<br>';
                    continue;
                } else {
                    echo '&nbsp;&nbsp;' . sprintf(_AM_SF_IMPORTED_QUESTION, $faqObj->question(50)) . '<br>';
                    ++$cnt_imported_faq;
                }
            }
        }

        echo '<br>';
    }

    echo 'Done.<br>';
    echo sprintf(_AM_SF_IMPORTED_CATEGORIES, $cnt_imported_cat) . '<br>';
    echo sprintf(_AM_SF_IMPORTED_QUESTIONS, $cnt_imported_faq) . '<br>';

    exit();
}
