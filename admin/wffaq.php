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

include_once __DIR__ . '/admin_header.php';

$importFromModuleName = 'WF-FAQ';
$scriptname           = 'wffaq.php';

$op = 'start';

if (isset($_POST['op']) && ($_POST['op'] === 'go')) {
    $op = $_POST['op'];
}

if ($op === 'start') {
    include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    xoops_cp_header();
    $result = $xoopsDB->query('select count(*) from ' . $xoopsDB->prefix('faqcategories'));
    list($totalCat) = $xoopsDB->fetchRow($result);

    sf_collapsableBar('bottomtable', 'bottomtableicon');
    echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt='' /></a>&nbsp;" . sprintf(_AM_SF_IMPORT_FROM, $importFromModuleName) . '</h3>';
    echo "<div id='bottomtable'>";

    if ($totalCat == 0) {
        echo "<span style=\"color: #567; margin: 3px 0 12px 0; font-size: small; display: block; \">" . _AM_SF_IMPORT_NO_CATEGORY . '</span>';
    } else {
        include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

        $result = $xoopsDB->query('select count(*) from ' . $xoopsDB->prefix('faqtopics'));
        list($totalFAQ) = $xoopsDB->fetchRow($result);

        if ($totalFAQ == 0) {
            echo "<span style=\"color: #567; margin: 3px 0 12px 0; font-size: small; display: block; \">" . sprintf(_AM_SF_IMPORT_MODULE_FOUND_NO_FAQ, $importFromModuleName, $totalCat) . '</span>';
        } else {
            echo "<span style=\"color: #567; margin: 3px 0 12px 0; font-size: small; display: block; \">" . sprintf(_AM_SF_IMPORT_MODULE_FOUND, $importFromModuleName, $totalCat, $totalFAQ) . '</span>';

            $form = new XoopsThemeForm(_AM_SF_IMPORT_SETTINGS, 'import_form', XOOPS_URL . '/modules/smartfaq/admin/' . $scriptname);

            // Categories to be imported
            $cat_cbox = new XoopsFormCheckBox(sprintf(_AM_SF_IMPORT_CATEGORIES, $importFromModuleName), 'import_category', -1);
            $result   = $xoopsDB->query('select c.catID, c.name, count(t.topicID) from ' . $xoopsDB->prefix('faqcategories') . ' as c, ' . $xoopsDB->prefix('faqtopics') . ' as t where c.catID=t.catID group by t.catID order by c.weight');
            while (list($cid, $cat_title, $count) = $xoopsDB->fetchRow($result)) {
                $cat_cbox->addOption($cid, "$cat_title ($count)<br\>");
            }
            $form->addElement($cat_cbox);

            // SmartFAQ parent category
            $mytree = new XoopsTree($xoopsDB->prefix('smartfaq_categories'), 'categoryid', 'parentid');
            ob_start();
            $mytree->makeMySelBox('name', 'weight', $preset_id = 0, $none = 1, $sel_name = 'parent_category');
            $form->addElement(new XoopsFormLabel(_AM_SF_IMPORT_PARENT_CATEGORY, ob_get_contents()));
            ob_end_clean();

            // Auto-Approve
            $form->addElement(new XoopsFormRadioYN(_AM_SF_IMPORT_AUTOAPPROVE, 'autoaprove', 1, ' ' . _AM_SF_YES . '', ' ' . _AM_SF_NO . ''));

            // Submitted and answered by
            $memberHandler = xoops_getHandler('member');
            $user_select   = new XoopsFormSelect(_AM_SF_IMPORTED_USER, 'uid', 0);
            $user_select->addOption(0, '----');
            //$criteria = new CriteriaCompo ();
            //$criteria->setSort ('uname');
            //$criteria->setOrder ('ASC');
            $user_select->addOptionArray($memberHandler->getUserList());
            $form->addElement($user_select);

            // Q&As can be commented?
            $form->addElement(new XoopsFormRadioYN(_AM_SF_IMPORT_ALLOWCOMMENTS, 'cancomment', 1, ' ' . _AM_SF_YES . '', ' ' . _AM_SF_NO . ''));
            $group_list      = $memberHandler->getGroupList();
            $groups_selected = array();
            $groups_checkbox = new XoopsFormCheckBox(_AM_SF_IMPORT_PERMISSIONS, 'groups_read');
            foreach ($group_list as $group_id => $group_name) {
                if ($group_id != XOOPS_GROUP_ADMIN) {
                    $groups_selected [] = $group_id;
                    $groups_checkbox->addOption($group_id, $group_name);
                }
            }
            $groups_checkbox->setValue($groups_selected);
            $form->addElement($groups_checkbox);

            $form->addElement(new XoopsFormHidden('op', 'go'));
            $form->addElement(new XoopsFormButton('', 'import', _AM_SF_IMPORT, 'submit'));

            $form->display();
        }
    }

    exit();
}

if ($op === 'go') {
    $import_category = (isset($_POST['import_category']) ? $_POST['import_category'] : null);
    if (!$import_category) {
        redirect_header($scriptname, 2, _AM_SF_NOCATSELECTED);
    }

    include_once __DIR__ . '/admin_header.php';
    xoops_cp_header();

    sf_collapsableBar('bottomtable', 'bottomtableicon');
    echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt='' /></a>&nbsp;" . sprintf(_AM_SF_IMPORT_FROM, $importFromModuleName) . '</h3>';
    echo "<div id='bottomtable'>";
    echo "<span style=\"color: #567; margin: 3px 0 12px 0; font-size: small; display: block; \">" . _AM_SF_IMPORT_RESULT . '</span>';

    $cnt_imported_cat = 0;
    $cnt_imported_faq = 0;

    $parentId    = $_POST['parent_category'];
    $groups_read = isset($_POST['groups_read']) ? $_POST['groups_read'] : array();
    $uid         = !empty($_POST['uid']) ? $_POST['uid'] : 0;
    $cancomment  = $_POST['cancomment'];
    $autoaprove  = $_POST['autoaprove'];

    if (is_array($_POST['import_category'])) {
        $import_category_list = implode(',', $_POST['import_category']);
    } else {
        $import_category_list = $_POST['import_category'];
    }

    $categoryHandler = sf_gethandler('category');
    $faqHandler      = sf_gethandler('faq');
    $answerHandler   = sf_gethandler('answer');

    /*echo "Parent Category ID: $parentId<br/>";
    echo "Groups Read: " . implode (",", $groups_read) . "<br/>";
    echo "Import Categories: $import_category_list<br/>";
    echo "User ID: $uid<br/>";
    echo "Can Comment: $cancomment<br/>";
    echo "Auto aprove: $autoaprove<br/>";*/

    $resultCat = $xoopsDB->query('select * from ' . $xoopsDB->prefix('faqcategories') . " where catID in ($import_category_list) order by weight");

    while ($arrCat = $xoopsDB->fetchArray($resultCat)) {
        extract($arrCat, EXTR_PREFIX_ALL, 'wfc');

        // insert category into SmartFAQ
        $categoryObj = $categoryHandler->create();

        $categoryObj->setVar('parentid', $parentId);
        $categoryObj->setVar('weight', $wfc_weight);
        $categoryObj->setGroups_read(explode(' ', trim($wfc_groupid)));
        $categoryObj->setVar('name', $wfc_name);
        $categoryObj->setVar('description', $wfc_description);

        if (!$categoryObj->store(false)) {
            echo sprintf(_AM_SF_IMPORT_CATEGORY_ERROR, $xcat_name) . '<br/>';
            continue;
        }

        sf_saveCategory_Permissions($categoryObj->getGroups_read(), $categoryObj->categoryid(), 'category_read');

        ++$cnt_imported_cat;

        echo sprintf(_AM_SF_IMPORT_CATEGORY_SUCCESS, $wfc_name) . "<br\>";

        $resultFAQ = $xoopsDB->query('select * from ' . $xoopsDB->prefix('faqtopics') . " where catID=$wfc_catID order by weight");
        while ($arrFAQ = $xoopsDB->fetchArray($resultFAQ)) {
            extract($arrFAQ, EXTR_PREFIX_ALL, 'wft');

            if ($autoaprove) {
                if ($wft_submit) {
                    $qstatus = _SF_STATUS_PUBLISHED;
                } else {
                    $qstatus = _SF_STATUS_SUBMITTED;
                }
            } else {
                $qstatus = _SF_STATUS_SUBMITTED;
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
                echo sprintf('  ' . _AM_SF_IMPORT_FAQ_ERROR, $wft_question) . '<br/>';
                continue;
            } else {
                $answerObj->setVar('faqid', $faqObj->faqid());
                $answerObj->setVar('answer', $wft_answer);
                $answerObj->setVar('uid', $wft_uid);
                $answerObj->setVar('status', _SF_AN_STATUS_APPROVED);

                if (!$answerObj->store()) {
                    echo sprintf('  ' . _AM_SF_IMPORT_FAQ_ERROR) . '<br/>';
                    continue;
                } else {
                    echo '&nbsp;&nbsp;' . sprintf(_AM_SF_IMPORTED_QUESTION, $faqObj->question(50)) . '<br />';
                    ++$cnt_imported_faq;
                }
            }
        }

        echo '<br/>';
    }

    echo 'Done.<br/>';
    echo sprintf(_AM_SF_IMPORTED_CATEGORIES, $cnt_imported_cat) . '<br/>';
    echo sprintf(_AM_SF_IMPORTED_QUESTIONS, $cnt_imported_faq) . '<br/>';

    exit();
}
