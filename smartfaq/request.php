<?php

/**
* $Id: request.php,v 1.14 2005/08/15 16:51:58 fx2024 Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

include_once("header.php");
include_once(XOOPS_ROOT_PATH . "/header.php");

Global $xoopsUser, $xoopsConfig, $xoopsModuleConfig, $xoopsModule;

// Creating the category handler object
$category_handler =& sf_gethandler('category');

// Creating the FAQ handler object
$faq_handler =& sf_gethandler('faq');

// Get the total number of categories
$totalCategories = count($category_handler->getCategories());

if ($totalCategories == 0) {
	redirect_header("index.php", 1, _AM_SF_NOCOLEXISTS);
	exit();
}

// Find if the user is admin of the module
$isAdmin = sf_userIsAdmin();
// If the user is not admin AND we don't allow user submission, exit
if (!($isAdmin || (isset($xoopsModuleConfig['allowrequest']) && $xoopsModuleConfig['allowrequest'] == 1 && (is_object($xoopsUser) || (isset($xoopsModuleConfig['anonpost']) && $xoopsModuleConfig['anonpost'] == 1))))) {
	redirect_header("index.php", 1, _NOPERM);
	exit();
}

$op = '';

if (isset($_GET['op'])) $op = $_GET['op'];
if (isset($_POST['op'])) $op = $_POST['op'];


switch ($op) {
	case 'post':
	
	Global $xoopsUser, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $xoopsDB;
	
	$newFaqObj = $faq_handler->create();
	
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
	
	// Putting the values about the FAQ in the FAQ object
	$newFaqObj->setVar('categoryid', $_POST['categoryid']);
	$newFaqObj->setVar('uid', $uid);
	$newFaqObj->setVar('question', $_POST['question']);
	$notifypub = (isset($_POST['notifypub']))? $_POST['notifypub'] : 0;
	$newFaqObj->setVar('notifypub', $notifypub);
	
	// Setting the status of the FAQ
	if ( $xoopsModuleConfig['autoapprove_request'] ==  1) {
		$newFaqObj->setVar('status', _SF_STATUS_OPENED);
	} else {
		$newFaqObj->setVar('status', _SF_STATUS_ASKED);
	}
	
	// Storing the FAQ object in the database
	if ( !$newFaqObj->store() ) {
		redirect_header("javascript:history.go(-1)", 3, _MD_SF_REQUEST_ERROR . sf_formatErrors($newFaqObj->getErrors()));
		exit();
	}
	
	// Get the cateopry object related to that FAQ
	// If autoapprove_requested
	if ( $xoopsModuleConfig['autoapprove_request'] ==  1) {
		// We do not not subscribe user to notification on publish since we publish it right away
		
		// Send notifications
		$newFaqObj->sendNotifications(array(_SF_NOT_QUESTION_PUBLISHED));
		
		$redirect_msg = _MD_SF_REQUEST_RECEIVED_AND_PUBLISHED;
	} else {
		// Subscribe the user to On Published notification, if requested
		if ($notifypub == 1) {
			include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
			$notification_handler = &xoops_gethandler('notification');
			$notification_handler->subscribe('question', $newFaqObj->faqid(), 'approved', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
		}
		// Send notifications
		$newFaqObj->sendNotifications(array(_SF_NOT_QUESTION_SUBMITTED));
		
		$redirect_msg = _MD_SF_REQUEST_RECEIVED_NEED_APPROVAL;
	}
	
	//redirect_header("javascript:history.go(-2)", 3, $redirect_msg);
redirect_header("index.php", 2, $redirect_msg);
	
	exit();
	break;
	
	case 'form':
	default:
	
	global $xoopsUser, $myts;
	
	$xoopsOption['template_main'] = 'smartfaq_submit.html';
	include_once(XOOPS_ROOT_PATH . "/header.php");
	include_once("footer.php");
	
	$name = ($xoopsUser)? (ucwords($xoopsUser->getVar("uname"))) : 'Anonymous';
	
	$moduleName = $myts->displayTarea($xoopsModule->getVar('name'));
	$xoopsTpl->assign('whereInSection', $moduleName);
	$xoopsTpl->assign('lang_submit', _MD_SF_REQUEST);
	
	$xoopsTpl->assign('lang_intro_title', _MD_SF_REQUEST);
	$xoopsTpl->assign('lang_intro_text', _MD_SF_GOODDAY . "<b>$name</b>, " . $myts->displayTarea($xoopsModuleConfig['requestintromsg']));
	
	include_once 'include/request.inc.php';	
	
	$name = ($xoopsUser)? (ucwords($xoopsUser->getVar("uname"))) : 'Anonymous';
	
	$sectionname = $myts->htmlSpecialChars($xoopsModule->getVar('name'));
	
	include_once 'include/request.inc.php';
	
	include_once XOOPS_ROOT_PATH . '/footer.php';
	
	break;
}

?>