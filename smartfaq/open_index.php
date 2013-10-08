<?php

/**
* $Id: open_index.php,v 1.23 2005/08/15 16:51:58 fx2024 Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

include_once("header.php");

global $xoopsConfig, $xoopsModuleConfig, $xoopsModule;


// At which record shall we start for the Categories
$catstart = isset($_GET['catstart'])? intval($_GET['catstart']) : 0;

// At which record shall we start for the FAQs
$start = isset($_GET['start'])? intval($_GET['start']) : 0;

// Creating the category handler object
$category_handler =& sf_gethandler('category');

// Get the total number of categories
$totalCategories = count($category_handler->getCategories());

// Creating the faq handler object
$faq_handler =& sf_gethandler('faq');

// Total number of published FAQ in the module
$totalFaqs = $faq_handler->getFaqsCount(-1, _SF_STATUS_OPENED);
if ($totalFaqs  == 0 ) {
	redirect_header("request.php", 2, _MD_SF_NO_OPEN_QUESTION);
	exit;
}

// Creating the categories objects
$categoriesObj = $category_handler->getCategories($xoopsModuleConfig['catperpage'], $catstart);

// If no categories are found, exit
$totalCategoriesOnPage = count($categoriesObj);
if ($totalCategoriesOnPage  == 0 ) {
	redirect_header("javascript:history.go(-1)", 2, _AM_SF_NO_CAT_EXISTS);
	exit;
}

$xoopsOption['template_main'] = 'smartfaq_index.html';

include_once(XOOPS_ROOT_PATH . "/header.php");
include_once("footer.php");

//get all categories for future reference
$allcategories = $category_handler->getObjects(null, true);

// Arrays that will hold the informations passed on to smarty variables
$qnas = array();
$categories = array();
$subcats = $category_handler->getSubCats($categoriesObj);
$totalQnas = $category_handler->faqsCount(0, array(_SF_STATUS_OPENED));

$faq_handler =& sf_gethandler('faq');
$last_qnaObj = $faq_handler->getLastPublishedByCat(array(_SF_STATUS_OPENED));

foreach ($categoriesObj as $cat_id => $category) {
	$total = 0;
	if (isset($subcats[$cat_id]) && count($subcats[$cat_id])>0) {
		foreach ($subcats[$cat_id] as $key => $subcat) {
			$subcat_id = $subcat->getVar('categoryid');
			if (isset($totalQnas[$subcat_id]) && $totalQnas[$subcat_id] > 0) {
				if (isset($last_qnaObj[$subcat_id])) {
					$subcat->setVar('last_faqid', $last_qnaObj[$subcat_id]->getVar('faqid'));
					$subcat->setVar('last_question_link', "<a href='faq.php?faqid=" . $last_qnaObj[$subcat_id]->getVar('faqid') . "'>" . $last_qnaObj[$subcat_id]->question(50) . "</a>");
				}
				$subcat->setVar('faqcount', $totalQnas[$subcat_id]);
				$categories[$cat_id]['subcats'][$subcat_id] = $subcat->toArray(null, true);
				$total += $totalQnas[$subcat_id];
			}
		}
	}
	if (isset($totalQnas[$cat_id]) && $totalQnas[$cat_id] > 0) {
		$total += $totalQnas[$cat_id];
	}
	if ($total > 0) {
		$category->setVar('faqcount', $total);
		if (!isset($categories[$cat_id])) {
			$categories[$cat_id] = array();
		}
		$categories[$cat_id] = $category->toArray($categories[$cat_id], true);
		$categories[$cat_id]['categoryPath'] = $category->getCategoryPath();
	}
	
}
$xoopsTpl->assign('categories', $categories);

$displaylastfaqs = $xoopsModuleConfig['displaylastfaqs'];
if ($displaylastfaqs) {
	// Creating the last FAQs
	$faqsObj = $faq_handler->getFaqs($xoopsModuleConfig['indexperpage'], $start, _SF_STATUS_OPENED);
	$totalQnasOnPage = count($faqsObj);
	
	if ($faqsObj) {
		$userids = array();
		foreach ($faqsObj as $key => $thisfaq) {
			$faqids[] = $thisfaq->getVar('faqid');
			$userids[$thisfaq->uid()] = 1;
		}
		
		$member_handler = &xoops_gethandler('member');
		$users = $member_handler->getUsers(new Criteria('uid', "(".implode(',', array_keys($userids)).")", "IN"), true);
		for ( $i = 0; $i < $totalQnasOnPage; $i++ ) {
			$faq = $faqsObj[$i]->toArray(null, $allcategories);
			
			$faq['adminlink'] = sf_getAdminLinks($faqsObj[$i]->faqid(), true);
			
			$faq['who_when'] = $faqsObj[$i]->getWhoAndWhen(null, $users);
			
			$xoopsTpl->append('faqs', $faq);
		}
	}
}
// Language constants
$moduleName = $myts->displayTarea($xoopsModule->getVar('name'));
$xoopsTpl->assign(array('lang_on' => _MD_SF_ON, 'lang_postedby' => _MD_SF_POSTEDBY, 'lang_total' => $totalQnasOnPage, 'lang_faq' => _MD_SF_FAQ, 'lang_datesub' => _MD_SF_DATESUB, 'lang_hits' => _MD_SF_HITS));

$moduleName = $myts->displayTarea($xoopsModule->getVar('name'));
$xoopsTpl->assign('lang_mainhead', sprintf(_MD_SF_OPEN_WELCOME, $xoopsConfig['sitename']));
$xoopsTpl->assign('lang_mainintro', $myts->displayTarea($xoopsModuleConfig['openquestionintromsg'], 1));
$xoopsTpl->assign('lang_total', _MD_SF_TOTAL_QUESTIONS);
$xoopsTpl->assign('lang_home', _MD_SF_HOME);
$xoopsTpl->assign('lang_description', _MD_SF_DESCRIPTION);
$xoopsTpl->assign('lang_category', _MD_SF_CATEGORY);
$xoopsTpl->assign('sectionname', $moduleName);
$xoopsTpl->assign('whereInSection', "<a href='index.php'>" . $moduleName . "</a> > " . _MD_SF_OPEN_SECTION);

$xoopsTpl->assign('displayFull', false);
$xoopsTpl->assign('displaylastfaqs', $xoopsModuleConfig['displaylastfaqs']);
$xoopsTpl->assign('display_categoryname', true);

$xoopsTpl->assign('lang_reads', _MD_SF_READS);
$xoopsTpl->assign('lang_smartfaqs', _MD_SF_SMARTFAQS);
$xoopsTpl->assign('lang_last_smartfaq', _MD_SF_LAST_SMARTFAQ);
$xoopsTpl->assign('lang_categories_summary', _MD_SF_INDEX_CATEGORIES_SUMMARY);
$xoopsTpl->assign('lang_categories_summary_info', _MD_SF_INDEX_CATEGORIES_QUESTIONS_SUMMARY_INFO);
$xoopsTpl->assign('lang_index_faqs', _MD_SF_INDEX_QUESTIONS);
$xoopsTpl->assign('lang_index_faqs_info', _MD_SF_INDEX_QUESTIONS_INFO);
$xoopsTpl->assign('lang_category', _MD_SF_CATEGORY);

// Category Navigation Bar
include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
$pagenav = new XoopsPageNav($totalCategories, $xoopsModuleConfig['catperpage'], $catstart, 'catstart', '');
if ($xoopsModuleConfig['useimagenavpage'] == 1) {
	$xoopsTpl->assign('catnavbar', '<div style="text-align:right;">' . $pagenav->renderImageNav() . '</div>');
} else {
	$xoopsTpl->assign('catnavbar', '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>');
}

// FAQ Navigation Bar
$pagenav = new XoopsPageNav($totalFaqs, $xoopsModuleConfig['indexperpage'], $start, 'start', '');
if ($xoopsModuleConfig['useimagenavpage'] == 1) {
	$xoopsTpl->assign('navbar', '<div style="text-align:right;">' . $pagenav->renderImageNav() . '</div>');
} else {
	$xoopsTpl->assign('navbar', '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>');
}

// Page Title Hack by marcan
$module_name = $myts->htmlSpecialChars($xoopsModule->getVar('name'));
$xoopsTpl->assign('xoops_pagetitle', $module_name . ' - ' . $category->getVar('name'));
// End Page Title Hack by marcan

include_once(XOOPS_ROOT_PATH . "/footer.php");

?>