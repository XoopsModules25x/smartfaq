<?php

/**
* $Id: submit.php,v 1.16 2005/08/15 16:51:58 fx2024 Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

include_once("header.php");

Global $xoopsUser, $xoopsConfig, $xoopsModuleConfig, $xoopsModule;

// Creating the category handler object
$category_handler =& sf_gethandler('category');

// Creating the FAQ handler object
$faq_handler =& sf_gethandler('faq');

// Creating the answer handler object
$answer_handler =& sf_gethandler('answer');

// Get the total number of categories
$totalCategories = count($category_handler->getCategories());

if ($totalCategories == 0) {
	redirect_header("index.php", 1, _AM_SF_NOCOLEXISTS);
	exit();
}

// Find if the user is admin of the module
$isAdmin = sf_userIsAdmin();
// If the user is not admin AND we don't allow user submission, exit
if (!($isAdmin || (isset($xoopsModuleConfig['allowsubmit']) && $xoopsModuleConfig['allowsubmit'] == 1 && (is_object($xoopsUser) || (isset($xoopsModuleConfig['anonpost']) && $xoopsModuleConfig['anonpost'] == 1))))) {
	redirect_header("index.php", 1, _NOPERM);
	exit();
}

$op = '';

if (isset($_POST['post'])) {
	$op = 'post';
} elseif (isset($_POST['preview'])) {
	$op = 'preview';	
} else {
	$op = 'form';	
}

switch ($op) {
	case 'preview':
	
	Global $xoopsUser, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $xoopsDB;
	
	$faqObj = $faq_handler->create();
	$answerObj = $answer_handler->create();
	$categoryObj = $category_handler->get($_POST['categoryid']);
	
	if (!$xoopsUser) {
		if ($xoopsModuleConfig['anonpost'] == 1) {
			$uid = 0;
		} else {
			redirect_header("index.php", 3, _NOPERM);
			exit();
		}
	} else {
		$uid = $xoopsUser->uid();
	}
	
	$notifypub = isset($_POST['notifypub'])? $_POST['notifypub'] : 0;
	
	// Putting the values about the FAQ in the FAQ object
	$faqObj->setVar('categoryid', $_POST['categoryid']);
	$faqObj->setVar('uid', $uid);
	$faqObj->setVar('question', $_POST['question']);
	$faqObj->setVar('howdoi', $_POST['howdoi']);
	$faqObj->setVar('diduno', $_POST['diduno']);
	$faqObj->setVar('datesub', time());
	
	// Putting the values in the answer object
	$answerObj->setVar('status', _SF_AN_STATUS_APPROVED);
	$answerObj->setVar('faqid', $faqObj->faqid());
	$answerObj->setVar('answer', $_POST['answer']);
	$answerObj->setVar('uid', $uid);
	
	global $xoopsUser, $myts;
	
	$xoopsOption['template_main'] = 'smartfaq_submit.html';
	include_once(XOOPS_ROOT_PATH . "/header.php");
	include_once("footer.php");
	
	$name = ($xoopsUser)? (ucwords($xoopsUser->getVar("uname"))) : 'Anonymous';
	
	$moduleName = $myts->displayTarea($xoopsModule->getVar('name'));
	$faq = $faqObj->toArray(null, $categoryObj, false);
	$faq['categoryPath'] = $categoryObj->getCategoryPath(true);
	$faq['answer'] = $answerObj->answer();
	$faq['who_when'] = $faqObj->getWhoAndWhen();
	
	
	$faq['comments'] = -1;
	$xoopsTpl->assign('faq', $faq);
	$xoopsTpl->assign('op', 'preview');
	$xoopsTpl->assign('whereInSection', $moduleName);
	$xoopsTpl->assign('lang_submit', _MD_SF_SUB_SNEWNAME);
	
	$xoopsTpl->assign('lang_intro_title', sprintf(_MD_SF_SUB_SNEWNAME, ucwords($xoopsModule->name())));
	$xoopsTpl->assign('lang_intro_text', _MD_SF_GOODDAY . "<b>$name</b>, " . _MD_SF_SUB_INTRO);
	
	include_once 'include/submit.inc.php';
	
	include_once XOOPS_ROOT_PATH . '/footer.php';
	
	
	exit();
	break;
	
	case 'post':
	
	Global $xoopsUser, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $xoopsDB;
	
	$newFaqObj = $faq_handler->create();
	$newAnswerObj = $answer_handler->create();
	
	if (!$xoopsUser) {
		if ($xoopsModuleConfig['anonpost'] == 1) {
			$uid = 0;
		} else {
			redirect_header("index.php", 3, _NOPERM);
			exit();
		}
	} else {
		$uid = $xoopsUser->uid();
	}
	
	$notifypub = isset($_POST['notifypub'])? $_POST['notifypub'] : 0;
	
	// Putting the values about the FAQ in the FAQ object
	$newFaqObj->setVar('categoryid', $_POST['categoryid']);
	$newFaqObj->setVar('uid', $uid);
	$newFaqObj->setVar('question', $_POST['question']);
	$newFaqObj->setVar('howdoi', $_POST['howdoi']);
	$newFaqObj->setVar('diduno', $_POST['diduno']);
	$newFaqObj->setVar('notifypub', $notifypub);
	//$newFaqObj->setVar('modulelink', $_POST['modulelink']);
	//$newFaqObj->setVar('contextpage', $_POST['contextpage']);
	
	// Setting the status of the FAQ
	
	// if user is admin, FAQ are automatically published
	$isAdmin = sf_userIsAdmin();
	if ($isAdmin) {
		$newFaqObj->setVar('status', _SF_STATUS_PUBLISHED);
	} elseif ( $xoopsModuleConfig['autoapprove_submitted_faq'] ==  1) {
		$newFaqObj->setVar('status', _SF_STATUS_PUBLISHED);
	} else {
		$newFaqObj->setVar('status', _SF_STATUS_SUBMITTED);
	}
	
	// Storing the FAQ object in the database
	if ( !$newFaqObj->store() ) {
		redirect_header("javascript:history.go(-1)", 2, _MD_SF_SUBMIT_ERROR);
		exit();
	}
	
	// Putting the values in the answer object
	$newAnswerObj->setVar('status', _SF_AN_STATUS_APPROVED);
	$newAnswerObj->setVar('faqid', $newFaqObj->faqid());
	$newAnswerObj->setVar('answer', $_POST['answer']);
	$newAnswerObj->setVar('uid', $uid);
	
	// Storing the answer object in the database
	if ( !$newAnswerObj->store() ) {
		redirect_header("javascript:history.go(-1)", 2, _MD_SF_SUBMIT_ERROR);
		exit();
	}
	
	// Get the cateopry object related to that FAQ
	$categoryObj =& $newFaqObj->category();
	
	// If autoapprove_submitted_faq
	if ($isAdmin) {
		// We do not not subscribe user to notification on publish since we publish it right away
		
		// Send notifications
		$newFaqObj->sendNotifications(array(_SF_NOT_FAQ_PUBLISHED));

		$redirect_msg = _MD_SF_SUBMIT_FROM_ADMIN;	
	} elseif ( $xoopsModuleConfig['autoapprove_submitted_faq'] ==  1) {
		// We do not not subscribe user to notification on publish since we publish it right away
		
		// Send notifications
		$newFaqObj->sendNotifications(array(_SF_NOT_FAQ_PUBLISHED));
		
		$redirect_msg = _MD_SF_QNA_RECEIVED_AND_PUBLISHED;
	} else {
		// Subscribe the user to On Published notification, if requested
		if ($notifypub == 1) {
			include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
			$notification_handler = &xoops_gethandler('notification');
			$notification_handler->subscribe('faq', $newFaqObj->faqid(), 'approved', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
		}
		// Send notifications
		$newFaqObj->sendNotifications(array(_SF_NOT_FAQ_SUBMITTED));
		
		$redirect_msg = _MD_SF_QNA_RECEIVED_NEED_APPROVAL;
	}
	
	redirect_header("index.php", 2, $redirect_msg);
	
	
	exit();
	break;
	
	case 'form':
	default:
	
	global $xoopsUser, $myts;
	
	$faqObj = $faq_handler->create();
	$answerObj = $answer_handler->create();
	$categoryObj = $category_handler->create();
	
	$xoopsOption['template_main'] = 'smartfaq_submit.html';
	include_once(XOOPS_ROOT_PATH . "/header.php");
	include_once("footer.php");
	
	$name = ($xoopsUser)? (ucwords($xoopsUser->getVar("uname"))) : 'Anonymous';
	$notifypub = 1;
	$moduleName = $myts->displayTarea($xoopsModule->getVar('name'));
	$xoopsTpl->assign('whereInSection', $moduleName);
	$xoopsTpl->assign('lang_submit', _MD_SF_SUB_SNEWNAME);
	
	$xoopsTpl->assign('lang_intro_title', sprintf(_MD_SF_SUB_SNEWNAME, ucwords($xoopsModule->name())));
	$xoopsTpl->assign('lang_intro_text', _MD_SF_GOODDAY . "<b>$name</b>, " . _MD_SF_SUB_INTRO);
	
	include_once 'include/submit.inc.php';
	
	include_once XOOPS_ROOT_PATH . '/footer.php';
	break;
}

?>