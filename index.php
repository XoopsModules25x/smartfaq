<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq;
use XoopsModules\Smartfaq\Constants;

require_once __DIR__ . '/header.php';

// At which record shall we start for the Categories
$catstart = isset($_GET['catstart']) ? (int)$_GET['catstart'] : 0;

// At which record shall we start for the FAQ
$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;

// Creating the category handler object
/** @var \XoopsModules\Smartfaq\CategoryHandler $categoryHandler */
$categoryHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Category');

// Creating the faq handler object
/** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
$faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');

$totalCategories = $categoryHandler->getCategoriesCount(0);

// Total number of published FAQ in the module
$totalFaqs = $faqHandler->getFaqsCount(-1, [Constants::SF_STATUS_PUBLISHED, Constants::SF_STATUS_NEW_ANSWER]);

if (0 == $totalFaqs) {
    if (($totalCategories > 0)
        && ($xoopsModuleConfig['allowrequest'] && $xoopsModuleConfig['anonpost']
            || is_object($xoopsUser))) {
        redirect_header('request.php', 2, _AM_SF_NO_TOP_PERMISSIONS);
    } else {
        redirect_header('../../index.php', 2, _AM_SF_NO_TOP_PERMISSIONS);
    }
}

$GLOBALS['xoopsOption']['template_main'] = 'smartfaq_index.tpl';

require_once XOOPS_ROOT_PATH . '/header.php';
require_once __DIR__ . '/footer.php';

// Creating the categories objects
$categoriesObj =& $categoryHandler->getCategories($xoopsModuleConfig['catperpage'], $catstart);
// If no categories are found, exit
$totalCategoriesOnPage = count($categoriesObj);
if (0 == $totalCategoriesOnPage) {
    redirect_header('javascript:history.go(-1)', 2, _AM_SF_NO_CAT_EXISTS);
}
// Arrays that will hold the informations passed on to smarty variables

$qnas = [];

//if ($xoopsModuleConfig['displaysubcatonindex']) {
$subcats = $categoryHandler->getSubCats($categoriesObj);
//}
$totalQnas  = $categoryHandler->publishedFaqsCount();
$faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');

if (1 == $xoopsModuleConfig['displaylastfaq']) {
    // Get the last smartfaq in each category
    $last_qnaObj = $faqHandler->getLastPublishedByCat();
}
$lastfaqsize = (int)$xoopsModuleConfig['lastfaqsize'];
$categories  = [];
foreach ($categoriesObj as $cat_id => $category) {
    $total = 0;
    if (isset($subcats[$cat_id])) {
        foreach ($subcats[$cat_id] as $key => $subcat) {
            $subcat_id = $subcat->getVar('categoryid');
            if (isset($totalQnas[$subcat_id]) && $totalQnas[$subcat_id] > 0) {
                if (isset($last_qnaObj[$subcat_id])) {
                    $subcat->setVar('last_faqid', $last_qnaObj[$subcat_id]->getVar('faqid'));
                    $subcat->setVar('last_question_link', "<a href='faq.php?faqid=" . $last_qnaObj[$subcat_id]->getVar('faqid') . "'>" . $last_qnaObj[$subcat_id]->question($lastfaqsize) . '</a>');
                }
                $subcat->setVar('faqcount', $totalQnas[$subcat_id]);
                if ($xoopsModuleConfig['displaysubcatonindex']) {
                    $categories[$cat_id]['subcats'][$subcat_id] = $subcat->toArray();
                }
            }
            $total += $totalQnas[$subcat_id];
            //}replac� ligne 80
        }
    }

    if (isset($totalQnas[$cat_id]) && $totalQnas[$cat_id] > 0) {
        $total += $totalQnas[$cat_id];
    }
    if ($total > 0) {
        if (isset($last_qnaObj[$cat_id])) {
            $category->setVar('last_faqid', $last_qnaObj[$cat_id]->getVar('faqid'));
            $category->setVar('last_question_link', "<a href='faq.php?faqid=" . $last_qnaObj[$cat_id]->getVar('faqid') . "'>" . $last_qnaObj[$cat_id]->question($lastfaqsize) . '</a>');
        }
        $category->setVar('faqcount', $total);
        if (!isset($categories[$cat_id])) {
            $categories[$cat_id] = [];
        }
    }

    $categories[$cat_id]                 = $category->toArray(@$categories[$cat_id]);
    $categories[$cat_id]['categoryPath'] = $category->getCategoryPath();
    //}replac� ligne 97
}
/*echo count($categories);
echo "<br>";
var_dump($categories);
exit;*/
$xoopsTpl->assign('categories', $categories);

$displaylastfaqs = $xoopsModuleConfig['displaylastfaqs'];
if ($displaylastfaqs) {
    // Creating the last FAQs
    $faqsObj         = $faqHandler->getAllPublished($xoopsModuleConfig['indexperpage'], $start);
    $totalQnasOnPage = count($faqsObj);
    $allcategories   = $categoryHandler->getObjects(null, true);
    if ($faqsObj) {
        $userids = [];
        foreach ($faqsObj as $key => $thisfaq) {
            $faqids[]                 = $thisfaq->getVar('faqid');
            $userids[$thisfaq->uid()] = 1;
        }
        /** @var \XoopsModules\Smartfaq\AnswerHandler $answerHandler */
        $answerHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Answer');
        $allanswers    = $answerHandler->getLastPublishedByFaq($faqids);

        foreach ($allanswers as $key => $thisanswer) {
            $userids[$thisanswer->uid()] = 1;
        }

        $memberHandler = xoops_getHandler('member');
        $users         = $memberHandler->getUsers(new \Criteria('uid', '(' . implode(',', array_keys($userids)) . ')', 'IN'), true);
        for ($i = 0; $i < $totalQnasOnPage; ++$i) {
            $faq = $faqsObj[$i]->toArray(null, $allcategories);

            // Creating the answer object
            $answerObj = $allanswers[$faqsObj[$i]->faqid()];

            $answerObj->setVar('dohtml', $faqsObj[$i]->getVar('html'));
            $answerObj->setVar('doxcode', $faqsObj[$i]->getVar('xcodes'));
            $answerObj->setVar('dosmiley', $faqsObj[$i]->getVar('smiley'));
            $answerObj->setVar('doimage', $faqsObj[$i]->getVar('image'));
            $answerObj->setVar('dobr', $faqsObj[$i]->getVar('linebreak'));

            $faq['answer']    = $answerObj->answer();
            $faq['answerid']  = $answerObj->answerid();
            $faq['datesub']   = $faqsObj[$i]->datesub();
            $faq['adminlink'] = Smartfaq\Utility::getAdminLinks($faqsObj[$i]->faqid());

            $faq['who_when'] = $faqsObj[$i]->getWhoAndWhen($answerObj, $users);

            $xoopsTpl->append('faqs', $faq);
        }
    }
}

// Language constants
$moduleName =& $myts->displayTarea($xoopsModule->getVar('name'));
$xoopsTpl->assign('whereInSection', $moduleName);
$xoopsTpl->assign('displaysubcatonindex', $xoopsModuleConfig['displaysubcatonindex']);
$xoopsTpl->assign('displaylastfaqs', $xoopsModuleConfig['displaylastfaqs']);
$xoopsTpl->assign('display_categoryname', true);
$xoopsTpl->assign('displayFull', 'full' === $xoopsModuleConfig['displaytype']);

$xoopsTpl->assign('lang_mainhead', _MD_SF_MAINHEAD . ' ' . $moduleName);
$xoopsTpl->assign('lang_mainintro', $myts->displayTarea($xoopsModuleConfig['indexwelcomemsg'], 1));
$xoopsTpl->assign('lang_total', _MD_SF_TOTAL_SMARTFAQS);
$xoopsTpl->assign('lang_home', _MD_SF_HOME);
$xoopsTpl->assign('lang_description', _MD_SF_DESCRIPTION);
$xoopsTpl->assign('lang_category', _MD_SF_CATEGORY);

$xoopsTpl->assign('lang_reads', _MD_SF_READS);
$xoopsTpl->assign('lang_smartfaqs', _MD_SF_SMARTFAQS);
$xoopsTpl->assign('lang_last_smartfaq', _MD_SF_LAST_SMARTFAQ);
$xoopsTpl->assign('lang_categories_summary', _MD_SF_INDEX_CATEGORIES_SUMMARY);
$xoopsTpl->assign('lang_categories_summary_info', _MD_SF_INDEX_CATEGORIES_SUMMARY_INFO);
$xoopsTpl->assign('lang_index_faqs', _MD_SF_INDEX_FAQS);
$xoopsTpl->assign('lang_index_faqs_info', _MD_SF_INDEX_FAQS_INFO);
$xoopsTpl->assign('lang_category', _MD_SF_CATEGORY);
$xoopsTpl->assign('lang_editcategory', _MD_SF_CATEGORY_EDIT);
$xoopsTpl->assign('lang_comments', _MD_SF_COMMENTS);

// Category Navigation Bar
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
$pagenav = new \XoopsPageNav($totalCategories, $xoopsModuleConfig['catperpage'], $catstart, 'catstart', '');
if (1 == $xoopsModuleConfig['useimagenavpage']) {
    $xoopsTpl->assign('catnavbar', '<div style="text-align:right;">' . $pagenav->renderImageNav() . '</div>');
} else {
    $xoopsTpl->assign('catnavbar', '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>');
}

// FAQ Navigation Bar
$pagenav = new \XoopsPageNav($totalFaqs, $xoopsModuleConfig['indexperpage'], $start, 'start', '');
if (1 == $xoopsModuleConfig['useimagenavpage']) {
    $xoopsTpl->assign('navbar', '<div style="text-align:right;">' . $pagenav->renderImageNav() . '</div>');
} else {
    $xoopsTpl->assign('navbar', '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>');
}

// Page Title Hack by marcan
$module_name = $myts->htmlSpecialChars($xoopsModule->getVar('name'));
$xoopsTpl->assign('xoops_pagetitle', $module_name);
// End Page Title Hack by marcan

require_once XOOPS_ROOT_PATH . '/footer.php';
