<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq;

require_once __DIR__ . '/header.php';

$faqid = isset($_GET['faqid']) ? (int)$_GET['faqid'] : 0;

if (0 == $faqid) {
    redirect_header('javascript:history.go(-1)', 1, _MD_SF_NOFAQSELECTED);
}

// Creating the FAQ handler object
/** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
$faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');

// Creating the FAQ object for the selected FAQ
$faqObj = new Smartfaq\Faq($faqid);

// If the selected FAQ was not found, exit
if ($faqObj->notLoaded()) {
    redirect_header('javascript:history.go(-1)', 1, _MD_SF_NOFAQSELECTED);
}

// Creating the category object that holds the selected FAQ
$categoryObj = $faqObj->category();

// Creating the answer object
$answerObj = $faqObj->answer();

// Check user permissions to access that category of the selected FAQ
$faqAccessGrantedResult = Smartfaq\Utility::faqAccessGranted($faqObj);
if ($faqAccessGrantedResult < 0) {
    redirect_header('javascript:history.go(-1)', 1, _NOPERM);
}

// Update the read counter of the selected FAQ
if (!$xoopsUser || ($xoopsUser->isAdmin($xoopsModule->mid()) && 1 == $xoopsModuleConfig['adminhits'])
    || ($xoopsUser
        && !$xoopsUser->isAdmin($xoopsModule->mid()))) {
    $faqObj->updateCounter();
}
$GLOBALS['xoopsOption']['template_main'] = 'smartfaq_faq.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once __DIR__ . '/footer.php';

$faq = $faqObj->toArray(null, $categoryObj, false);

// Populating the smarty variables with informations related to the selected FAQ
/*$faq['questionlink'] = $faqObj->question($xoopsModuleConfig['questionsize']);
$faq['question'] = $faqObj->question();

$faq['categoryid'] = $categoryObj->categoryid();
$faq['categoryname'] = $categoryObj->name();

$faq['categorydescription'] = $categoryObj->description();
$faq['counter'] = $faqObj->counter();
$faq['comments'] = $faqObj->comments();
$faq['cancomment'] = $faqObj->cancomment();
*/
$faq['categoryPath'] = $categoryObj->getCategoryPath(true);
$faq['answer']       = $answerObj->answer();

// Check to see if we need to display partial answer. This should probably be in a the FAQ class...
if (0 == $faqAccessGrantedResult) {
    $faq['answer'] = xoops_substr($faq['answer'], 0, 150);
}

$faq['who_when'] = $faqObj->getWhoAndWhen();

$faq['adminlink'] = Smartfaq\Utility::getAdminLinks($faqObj->faqid());

$faq['comments'] = $faqObj->comments();

// Language constants
$xoopsTpl->assign('faq', $faq);
$xoopsTpl->assign('display_categoryname', false);

$xoopsTpl->assign('xcodes', $faqObj->getVar('xcodes'));
$xoopsTpl->assign('mail_link', 'mailto:?subject=' . sprintf(_MD_SF_INTARTICLE, $xoopsConfig['sitename']) . '&amp;body=' . sprintf(_MD_SF_INTARTFOUND, $xoopsConfig['sitename']) . ':  ' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/faq.php?faqid=' . $faqObj->getVar('faqid'));
$xoopsTpl->assign('lang_printerpage', _MD_SF_PRINTERFRIENDLY);
$xoopsTpl->assign('lang_sendstory', _MD_SF_SENDSTORY);
$xoopsTpl->assign('faqid', $faqObj->getVar('faqid'));
$xoopsTpl->assign('lang_reads', _MD_SF_READS);
$xoopsTpl->assign('lang_home', _MD_SF_HOME);
$xoopsTpl->assign('lang_faq', _MD_SF_FAQ);
$xoopsTpl->assign('lang_postedby', _MD_SF_POSTEDBY);
$xoopsTpl->assign('lang_on', _MD_SF_ON);
$xoopsTpl->assign('lang_datesub', _MD_SF_DATESUB);
$xoopsTpl->assign('lang_hitsdetail', _MD_SF_HITSDETAIL);
$xoopsTpl->assign('lang_hits', _MD_SF_READS);
$xoopsTpl->assign('lang_comments', _MD_SF_COMMENTS);

// Page Title Hack by marcan
$module_name = $myts->htmlSpecialChars($xoopsModule->getVar('name'));
$xoopsTpl->assign('xoops_pagetitle', $module_name . ' - ' . $categoryObj->name() . ' - ' . $faq['question']);
// End Page Title Hack by marcan

// Include the comments if the selected FAQ supports comments
if (1 == $faqObj->cancomment()) {
    require_once XOOPS_ROOT_PATH . '/include/comment_view.php';
}

//code to include smartie
if (file_exists(XOOPS_ROOT_PATH . '/modules/smarttie/smarttie_links.php')) {
    require_once XOOPS_ROOT_PATH . '/modules/smarttie/smarttie_links.php';
    $xoopsTpl->assign('smarttie', 1);
}
//end code for smarttie

require_once XOOPS_ROOT_PATH . '/footer.php';
