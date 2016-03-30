<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

include_once __DIR__ . '/header.php';

$categoryid = isset($_GET['categoryid']) ? (int)$_GET['categoryid'] : 0;

// Creating the category handler object
$categoryHandler = sf_gethandler('category');

// Creating the category object for the selected category
$categoryObj = new sfCategory($categoryid);

// If the selected category was not found, exit
if ($categoryObj->notLoaded()) {
    redirect_header('javascript:history.go(-1)', 1, _MD_SF_NOCATEGORYSELECTED);
}

// Check user permissions to access this category
if (!$categoryObj->checkPermission()) {
    redirect_header('javascript:history.go(-1)', 1, _NOPERM);
}
$totalQnas = $categoryHandler->publishedFaqsCount($categoryid);
// If there is no FAQ under this categories or the sub-categories, exit
if (!isset($totalQnas[$categoryid]) || $totalQnas[$categoryid] == 0) {
    //redirect_header("index.php", 1, _MD_SF_MAINNOFAQS);
}
$xoopsOption['template_main'] = 'smartfaq_category.tpl';

include_once(XOOPS_ROOT_PATH . '/header.php');
include_once __DIR__ . '/footer.php';

// At which record shall we start
$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;

// Creating the faq handler object
$faqHandler = sf_gethandler('faq');

// creating the FAQ objects that belong to the selected category
if ($xoopsModuleConfig['orderbydate'] == 1) {
    $sort  = 'datesub';
    $order = 'DESC';
} else {
    $sort  = 'weight';
    $order = 'ASC';
}
$faqsObj = $faqHandler->getAllPublished($xoopsModuleConfig['indexperpage'], $start, $categoryid, $sort, $order);

$totalQnasOnPage = 0;
if ($faqsObj) {
    $totalQnasOnPage = count($faqsObj);
}

// Arrays that will hold the informations passed on to smarty variables
$category = array();
$qnas     = array();

// Populating the smarty variables with informations related to the selected category
$category  = $categoryObj->toArray(null, true);
$totalQnas = $categoryHandler->publishedFaqsCount();

$category['categoryPath'] = $categoryObj->getCategoryPath();

if ($xoopsModuleConfig['displaylastfaq'] == 1) {
    // Get the last smartfaq
    $last_qnaObj = $faqHandler->getLastPublishedByCat();
}
$lastfaqsize = (int)$xoopsModuleConfig['lastfaqsize'];
// Creating the sub-categories objects that belong to the selected category
$subcatsObj    = $categoryHandler->getCategories(0, 0, $categoryid);
$total_subcats = count($subcatsObj);
$total_faqs    = 0;
if ($total_subcats != 0) {
    $subcat_keys = array_keys($subcatsObj);
    foreach ($subcat_keys as $i) {
        $subcat_id = $subcatsObj[$i]->getVar('categoryid');
        if (isset($totalQnas[$subcat_id]) && $totalQnas[$subcat_id] > 0) {
            if (isset($last_qnaObj[$subcat_id])) {
                $subcatsObj[$i]->setVar('last_faqid', $last_qnaObj[$subcat_id]->getVar('faqid'));
                $subcatsObj[$i]->setVar('last_question_link', "<a href='faq.php?faqid=" . $last_qnaObj[$subcat_id]->getVar('faqid') . "'>" . $last_qnaObj[$subcat_id]->question($lastfaqsize) . '</a>');
            }
        }
        $subcatsObj[$i]->setVar('faqcount', $totalQnas[$subcat_id]);
        $subcats[$subcat_id] = $subcatsObj[$i]->toArray();
        $total_faqs += $totalQnas[$subcat_id];
        //}replacé ligne 92
    }
    $xoopsTpl->assign('subcats', $subcats);
}
$thiscategory_faqcount = isset($totalQnas[$categoryid]) ? $totalQnas[$categoryid] : 0;
$category['total']     = $thiscategory_faqcount + $total_faqs;

if (count($faqsObj) > 0) {
    $userids = array();
    foreach ($faqsObj as $key => $thisfaq) {
        $faqids[]                 = $thisfaq->getVar('faqid');
        $userids[$thisfaq->uid()] = 1;
    }
    $answerHandler = sf_gethandler('answer');
    $allanswers    = $answerHandler->getLastPublishedByFaq($faqids);

    foreach ($allanswers as $key => $thisanswer) {
        $userids[$thisanswer->uid()] = 1;
    }

    $memberHandler = xoops_getHandler('member');
    $users         = $memberHandler->getUsers(new Criteria('uid', '(' . implode(',', array_keys($userids)) . ')', 'IN'), true);
    // Adding the Q&As of the selected category
    for ($i = 0; $i < $totalQnasOnPage; ++$i) {
        $faq = $faqsObj[$i]->toArray(null, $categoryObj);

        // Creating the answer object
        $answerObj =& $allanswers[$faqsObj[$i]->faqid()];

        $answerObj->setVar('dohtml', $faqsObj[$i]->getVar('html'));
        $answerObj->setVar('doxcode', $faqsObj[$i]->getVar('xcodes'));
        $answerObj->setVar('dosmiley', $faqsObj[$i]->getVar('smiley'));
        $answerObj->setVar('doimage', $faqsObj[$i]->getVar('image'));
        $answerObj->setVar('dobr', $faqsObj[$i]->getVar('linebreak'));

        $faq['answer']    = $answerObj->answer();
        $faq['answerid']  = $answerObj->answerid();
        $faq['datesub']   = $faqsObj[$i]->datesub();
        $faq['adminlink'] = sf_getAdminLinks($faqsObj[$i]->faqid());

        $faq['who_when'] = $faqsObj[$i]->getWhoAndWhen($answerObj, $users);

        $xoopsTpl->append('faqs', $faq);
    }

    if (isset($last_qnaObj) && $last_qnaObj) {
        $category['last_faqid']         = $last_qnaObj[$categoryObj->getVar('categoryid')]->getVar('faqid');
        $category['last_question_link'] = "<a href='faq.php?faqid=" . $last_qnaObj[$categoryObj->getVar('categoryid')]->getVar('faqid') . "'>" . $last_qnaObj[$categoryObj->getVar('categoryid')]->question($lastfaqsize) . '</a>';
    }
}

$xoopsTpl->assign('whereInSection', $myts->displayTarea($xoopsModule->getVar('name')));
$xoopsTpl->assign('displaylastfaqs', true);
$xoopsTpl->assign('display_categoryname', true);
$xoopsTpl->assign('displayFull', $xoopsModuleConfig['displaytype'] === 'full');

// Language constants
$xoopsTpl->assign('lang_index_faqs', _MD_SF_SMARTFAQS);
$xoopsTpl->assign('lang_index_faqs_info', _MD_SF_SMARTFAQS_INFO);

$xoopsTpl->assign('lang_category', $totalQnasOnPage);
$xoopsTpl->assign('lang_reads', _MD_SF_READS);
$xoopsTpl->assign('lang_home', _MD_SF_HOME);
$xoopsTpl->assign('lang_smartfaqs', _MD_SF_SMARTFAQS);
$xoopsTpl->assign('lang_last_smartfaq', _MD_SF_LAST_SMARTFAQ);
$xoopsTpl->assign('lang_category_summary', _MD_SF_CATEGORY_SUMMARY);
$xoopsTpl->assign('lang_category_summary_info', _MD_SF_CATEGORY_SUMMARY_INFO);

$xoopsTpl->assign('lang_category', _MD_SF_CATEGORY);
$xoopsTpl->assign('lang_comments', _MD_SF_COMMENTS);

// The Navigation Bar
include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
$pagenav = new XoopsPageNav($thiscategory_faqcount, $xoopsModuleConfig['indexperpage'], $start, 'start', 'categoryid=' . $categoryObj->getVar('categoryid'));
if ($xoopsModuleConfig['useimagenavpage'] == 1) {
    $xoopsTpl->assign('navbar', '<div style="text-align:right;">' . $pagenav->renderImageNav() . '</div>');
} else {
    $xoopsTpl->assign('navbar', '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>');
}

$xoopsTpl->assign('category', $category);

// Page Title Hack by marcan
$module_name = $myts->htmlSpecialChars($xoopsModule->getVar('name'));
$xoopsTpl->assign('xoops_pagetitle', $module_name . ' - ' . $category['name']);
// End Page Title Hack by marcan

//code to include smartie
if (file_exists(XOOPS_ROOT_PATH . '/modules/smarttie/smarttie_links.php')) {
    include_once XOOPS_ROOT_PATH . '/modules/smarttie/smarttie_links.php';
    $xoopsTpl->assign('smarttie', 1);
}
//end code for smarttie

include_once(XOOPS_ROOT_PATH . '/footer.php');
