<?php
/**
 * Module: SmartFAQ
 * Author: Marius Scurtescu <mariuss@romanians.bc.ca>
 * Licence: GNU
 *
 * Import script from WF-FAQ to SmartFAQ.
 *
 * It was tested with WF-FAQ version 1.0.5 and SmartFAQ version 1.0 beta
 *
 */

use XoopsModules\Smartfaq;
use XoopsModules\Smartfaq\Constants;

require_once __DIR__ . '/admin_header.php';

$importFromModuleName = 'WF-FAQ';
$scriptname           = 'wffaq.php';

$op = 'start';

if (isset($_POST['op']) && ('go' === $_POST['op'])) {
    $op = $_POST['op'];
}

if ('start' === $op) {
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    xoops_cp_header();
    $result = $xoopsDB->queryF('SELECT count(*) FROM ' . $xoopsDB->prefix('faqcategories'));
    list($totalCat) = $xoopsDB->fetchRow($result);

    Smartfaq\Utility::collapsableBar('bottomtable', 'bottomtableicon');
    echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . sprintf(_AM_SF_IMPORT_FROM, $importFromModuleName) . '</h3>';
    echo "<div id='bottomtable'>";

    if (0 == $totalCat) {
        echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . _AM_SF_IMPORT_NO_CATEGORY . '</span>';
    } else {
        require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

        $result = $xoopsDB->queryF('SELECT count(*) FROM ' . $xoopsDB->prefix('faqtopics'));
        list($totalFAQ) = $xoopsDB->fetchRow($result);

        if (0 == $totalFAQ) {
            echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . sprintf(_AM_SF_IMPORT_MODULE_FOUND_NO_FAQ, $importFromModuleName, $totalCat) . '</span>';
        } else {
            echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . sprintf(_AM_SF_IMPORT_MODULE_FOUND, $importFromModuleName, $totalCat, $totalFAQ) . '</span>';

            $form = new \XoopsThemeForm(_AM_SF_IMPORT_SETTINGS, 'import_form', XOOPS_URL . '/modules/smartfaq/admin/' . $scriptname);

            // Categories to be imported
            $cat_cbox = new \XoopsFormCheckBox(sprintf(_AM_SF_IMPORT_CATEGORIES, $importFromModuleName), 'import_category', -1);
            $result   = $xoopsDB->queryF('SELECT c.catID, c.name, count(t.topicID) FROM ' . $xoopsDB->prefix('faqcategories') . ' AS c, ' . $xoopsDB->prefix('faqtopics') . ' AS t WHERE c.catID=t.catID GROUP BY t.catID ORDER BY c.weight');
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
            //$criteria = new \CriteriaCompo ();
            //$criteria->setSort ('uname');
            //$criteria->setOrder ('ASC');
            $user_select->addOptionArray($memberHandler->getUserList());
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
    }

    exit();
}

if ('go' === $op) {
    $import_category = (isset($_POST['import_category']) ? $_POST['import_category'] : null);
    if (!$import_category) {
        redirect_header($scriptname, 2, _AM_SF_NOCATSELECTED);
    }

    require_once __DIR__ . '/admin_header.php';
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

    $resultCat = $xoopsDB->queryF('select * from ' . $xoopsDB->prefix('faqcategories') . " where catID in ($import_category_list) order by weight");

    while (false !== ($arrCat = $xoopsDB->fetchArray($resultCat))) {
        extract($arrCat, EXTR_PREFIX_ALL, 'wfc');

        // insert category into SmartFAQ
        $categoryObj = $categoryHandler->create();

        $categoryObj->setVar('parentid', $parentId);
        $categoryObj->setVar('weight', $wfc_weight);
        $categoryObj->setGroups_read(explode(' ', trim($wfc_groupid)));
        $categoryObj->setVar('name', $wfc_name);
        $categoryObj->setVar('description', $wfc_description);

        if (!$categoryObj->store(false)) {
            echo sprintf(_AM_SF_IMPORT_CATEGORY_ERROR, $xcat_name) . '<br>';
            continue;
        }

        Smartfaq\Utility::saveCategoryPermissions($categoryObj->getGroups_read(), $categoryObj->categoryid(), 'category_read');

        ++$cnt_imported_cat;

        echo sprintf(_AM_SF_IMPORT_CATEGORY_SUCCESS, $wfc_name) . "<br\>";

        $resultFAQ = $xoopsDB->queryF('select * from ' . $xoopsDB->prefix('faqtopics') . " where catID=$wfc_catID order by weight");
        while (false !== ($arrFAQ = $xoopsDB->fetchArray($resultFAQ))) {
            extract($arrFAQ, EXTR_PREFIX_ALL, 'wft');

            if ($autoaprove) {
                if ($wft_submit) {
                    $qstatus = Constants::SF_STATUS_PUBLISHED;
                } else {
                    $qstatus = Constants::SF_STATUS_SUBMITTED;
                }
            } else {
                $qstatus = Constants::SF_STATUS_SUBMITTED;
            }

            // insert question into SmartFAQ
            $faqObj    = $faqHandler->create();
            $answerObj = $answerHandler->create();

            $faqObj->setGroups_read(explode(' ', trim($wft_groupid)));
            $faqObj->setVar('categoryid', $categoryObj->categoryid());
            $faqObj->setVar('question', $wft_question);
            $faqObj->setVar('uid', $wft_uid);
            $faqObj->setVar('status', $qstatus);
            $faqObj->setVar('weight', $wft_weight);
            $faqObj->setVar('html', $wft_html);
            $faqObj->setVar('smiley', $wft_smiley);
            $faqObj->setVar('xcodes', $wft_xcodes);
            $faqObj->setVar('cancomment', $cancomment);
            $faqObj->setVar('diduno', $wft_summary);
            $faqObj->setVar('exacturl', 0);

            if (!$faqObj->store(false)) {
                echo sprintf('  ' . _AM_SF_IMPORT_FAQ_ERROR, $wft_question) . '<br>';
                continue;
            } else {
                $answerObj->setVar('faqid', $faqObj->faqid());
                $answerObj->setVar('answer', $wft_answer);
                $answerObj->setVar('uid', $wft_uid);
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
