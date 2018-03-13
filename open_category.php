<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq;
use XoopsModules\Smartfaq\Constants;

require_once __DIR__ . '/header.php';

global $xoopsConfig, $xoopsModuleConfig, $xoopsModule;

$GLOBALS['xoopsOption']['template_main'] = 'smartfaq_category.tpl';

require_once XOOPS_ROOT_PATH . '/header.php';
require_once __DIR__ . '/footer.php';

$categoryid = isset($_GET['categoryid']) ? (int)$_GET['categoryid'] : 0;

// Creating the category object for the selected category
$categoryObj = new Smartfaq\Category($categoryid);

// If the selected category was not found, exit
if ($categoryObj->notLoaded()) {
    redirect_header('javascript:history.go(-1)', 1, _MD_SF_NOCATEGORYSELECTED);
}

// Check user permissions to access this category
if (!$categoryObj->checkPermission()) {
    redirect_header('javascript:history.go(-1)', 1, _NOPERM);
}

// Creating the category handler object
/** @var \XoopsModules\Smartfaq\CategoryHandler $categoryHandler */
$categoryHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Category');

// At which record shall we start
$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;

// Creating the faq handler object
/** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
$faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');

// creating the FAQ objects that belong to the selected category
$faqsObj = $faqHandler->getFaqs($xoopsModuleConfig['indexperpage'], $start, Constants::SF_STATUS_OPENED, $categoryid);

$totalQnasOnPage = 0;
if ($faqsObj) {
    $totalQnasOnPage = count($faqsObj);
}

// If there is no Q&As to display, exit
/*if ($totalQnasOnPage == 0) {
    redirect_header("javascript:history.go(-1)", 2, _AM_SF_NO_TOP_PERMISSIONS);
}*/

// Arrays that will hold the informations passed on to smarty variables
$category    = [];
$qnas        = [];
$last_qnaObj = $faqHandler->getLastPublishedByCat([Constants::SF_STATUS_OPENED]);
if (isset($last_qnaObj[$categoryid])) {
    $categoryObj->setVar('last_faqid', $last_qnaObj[$categoryid]->getVar('faqid'));
    $categoryObj->setVar('last_question_link', "<a href='faq.php?faqid=" . $last_qnaObj[$categoryid]->getVar('faqid') . "'>" . $last_qnaObj[$categoryid]->question(50) . '</a>');
}
// Populating the smarty variables with informations related to the selected category
$category                 = $categoryObj->toArray(null, true);
$totalQnas                = $categoryHandler->faqsCount(0, [Constants::SF_STATUS_OPENED]);
$category['categoryPath'] = $categoryObj->getCategoryPath(false, true);

// Creating the sub-categories objects that belong to the selected category
$subcatsObj     =& $categoryHandler->getCategories(0, 0, $categoryid);
$total_subcats  = count($subcatsObj);
$catQnasWithSub = 0;
if (0 != $total_subcats) {
    $faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');
    // Arrays that will hold the informations passed on to smarty variables
    foreach ($subcatsObj as $key => $subcat) {
        $subcat_id = $subcat->getVar('categoryid');
        if (isset($totalQnas[$subcat_id]) && $totalQnas[$subcat_id] > 0) {
            if (isset($last_qnaObj[$subcat_id])) {
                $subcat->setVar('last_faqid', $last_qnaObj[$subcat_id]->getVar('faqid'));
                $subcat->setVar('last_question_link', "<a href='faq.php?faqid=" . $last_qnaObj[$subcat_id]->getVar('faqid') . "'>" . $last_qnaObj[$subcat_id]->question(50) . '</a>');
            }
            $subcat->setVar('faqcount', $totalQnas[$subcat_id]);
            $subcats[$subcat_id] = $subcat->toArray(null, true);
            $catQnasWithSub      += $subcats[$subcat_id]['total'];
        }
    }
    $xoopsTpl->assign('subcats', $subcats);
}
$category['total'] = $catQnasWithSub + $totalQnas[$categoryid];
if ($faqsObj) {
    $userids = [];
    foreach ($faqsObj as $key => $thisfaq) {
        $faqids[]                 = $thisfaq->getVar('faqid');
        $userids[$thisfaq->uid()] = 1;
    }

    $memberHandler = xoops_getHandler('member');
    $users         = $memberHandler->getUsers(new \Criteria('uid', '(' . implode(',', array_keys($userids)) . ')', 'IN'), true);
    foreach ($faqsObj as $iValue) {
        $faq = $iValue->toArray(null, $allcategories);

        $faq['adminlink'] = Smartfaq\Utility::getAdminLinks($iValue->faqid(), true);

        $faq['who_when'] = $iValue->getWhoAndWhen(null, $users);

        $xoopsTpl->append('faqs', $faq);
    }
}
// Language constants
$xoopsTpl->assign('whereInSection', $myts->htmlSpecialChars($xoopsModule->getVar('name')) . " > <a href='open_index.php'>" . _MD_SF_OPEN_SECTION . '</a>');
$xoopsTpl->assign('modulename', $xoopsModule->dirname());

$xoopsTpl->assign('displaylastfaqs', true);
$xoopsTpl->assign('display_categoryname', false);

$xoopsTpl->assign('lang_reads', _MD_SF_READS);
$xoopsTpl->assign('lang_home', _MD_SF_HOME);
$xoopsTpl->assign('lang_smartfaqs_info', _MD_SF_OPENED_INFO);
$xoopsTpl->assign('lang_smartfaqs', _MD_SF_QUESTIONS);
$xoopsTpl->assign('lang_cat_title', _MD_SF_OPENED_QUESTIONS);
$xoopsTpl->assign('lang_subcat_title', _MD_SF_CATEGORY_SUMMARY);
$xoopsTpl->assign('lang_category_summary', _MD_SF_CATEGORY_SUMMARY);
$xoopsTpl->assign('lang_category_summary_info', _MD_SF_CATEGORY_SUMMARY_INFO);
$xoopsTpl->assign('lang_category', _MD_SF_CATEGORY);

// The Navigation Bar
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
$pagenav = new \XoopsPageNav($totalQnas[$categoryid], $xoopsModuleConfig['indexperpage'], $start, 'start', 'categoryid=' . $categoryObj->getVar('categoryid'));
if (1 == $xoopsModuleConfig['useimagenavpage']) {
    $category['navbar'] = '<div style="text-align:right;">' . $pagenav->renderImageNav() . '</div>';
} else {
    $category['navbar'] = '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';
}

$xoopsTpl->assign('category', $category);

// Page Title Hack by marcan
$module_name = $myts->htmlSpecialChars($xoopsModule->getVar('name'));
$xoopsTpl->assign('xoops_pagetitle', $module_name . ' - ' . $category['name']);
// End Page Title Hack by marcan

require_once XOOPS_ROOT_PATH . '/footer.php';
