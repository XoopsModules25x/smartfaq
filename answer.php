<?php declare(strict_types=1);

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use Xmf\Request;
use XoopsModules\Smartfaq;
use XoopsModules\Smartfaq\Constants;
use XoopsModules\Smartfaq\Helper;

$GLOBALS['xoopsOption']['template_main'] = 'smartfaq_submit.tpl';

require_once __DIR__ . '/header.php';

/** @var Smartfaq\Helper $helper */
$helper = Helper::getInstance();

global $xoopsUser, $xoopsConfig, $xoopsModule;

// If user is anonymous, and we don't allow anonymous posting, exit; else, get the uid
if (!$xoopsUser && (1 != $helper->getConfig('anonpost'))) {
    redirect_header('index.php', 3, _NOPERM);
}

// Getting the operation we are doing
$op = Request::getCmd('op', 'form');

// Getting the faqid
$faqid = Request::getInt('faqid', 0, 'GET');
$faqid = Request::getInt('faqid', $faqid, 'POST');

// If no FAQ is selected, exit
if (0 == $faqid) {
    redirect_header('<script>javascript:history.go(-1)</script>', 1, _MD_SF_NOFAQSELECTED);
}

// Creating the FAQ handler object
/** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
$faqHandler = Helper::getInstance()->getHandler('Faq');

// Creating the answer handler object
/** @var \XoopsModules\Smartfaq\AnswerHandler $answerHandler */
$answerHandler = Helper::getInstance()->getHandler('Answer');

switch ($op) {
    // The answer is posted
    case 'post':
        global $faqObj, $xoopsUser, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $xoopsDB;

        // If user is anonymous,and we don't allow anonymous posting, exit; else, get the uid
        if ($xoopsUser) {
            $uid = $xoopsUser->uid();
        } else {
            if (1 == $helper->getConfig('anonpost')) {
                $uid = 0;
            } else {
                redirect_header('index.php', 3, _NOPERM);
            }
        }

        // Creating the FAQ object for the selected FAQ
        $faqObj = new Smartfaq\Faq($faqid);

        // If the selected FAQ was not found, exit
        if ($faqObj->notLoaded()) {
            redirect_header('javascript:history.go(-2)', 1, _MD_SF_NOFAQSELECTED);
        }

        // Get the category object related to that FAQ
        $categoryObj = $faqObj->category();

        // Create the answer object
        $newAnswerObj = $answerHandler->create();

        // Putting the values in the answer object
        $newAnswerObj->setVar('faqid', $faqObj->faqid());
        $newAnswerObj->setVar('answer', $_POST['answer']);
        $newAnswerObj->setVar('uid', $uid);

        // Depending on the status of the FAQ, some values need to be set
        $original_status = $faqObj->status();
        switch ($original_status) {
            // This is an Open Question
            case Constants::SF_STATUS_OPENED:
                if (1 == $helper->getConfig('autoapprove_answer')) {
                    // We automatically approve submitted answer for Open Question, so the question become a Submitted Q&A
                    if (1 == $helper->getConfig('autoapprove_submitted_faq')) {
                        // We automatically approve Submitted Q&A
                        $redirect_msg = _MD_SF_QNA_RECEIVED_AND_PUBLISHED;
                        $faqObj->setVar('status', Constants::SF_STATUS_PUBLISHED);
                        $newAnswerObj->setVar('status', Constants::SF_AN_STATUS_APPROVED);
                        $notifCase = 1;
                    } else {
                        // Submitted Q&A need approbation
                        $redirect_msg = _MD_SF_QNA_RECEIVED_NEED_APPROVAL;
                        $faqObj->setVar('status', Constants::SF_STATUS_SUBMITTED);
                        $newAnswerObj->setVar('status', Constants::SF_AN_STATUS_PROPOSED);
                        $notifCase = 2;
                    }
                } else {
                    // Submitted answer need approbation
                    $redirect_msg = _MD_SF_OPEN_ANSWER_NEED_APPROBATION;
                    $faqObj->setVar('status', Constants::SF_STATUS_ANSWERED);
                    $newAnswerObj->setVar('status', Constants::SF_AN_STATUS_PROPOSED);

                    $notifCase = 3;
                }
                break;
            // This is a published FAQ for which a user submitted a new answer
            case Constants::SF_STATUS_PUBLISHED:
            case Constants::SF_STATUS_NEW_ANSWER:
                if (1 == $helper->getConfig('autoapprove_answer_new')) {
                    // We automatically approve new submitted answer for already published FAQ
                    $redirect_msg = '4';
                    $faqObj->setVar('status', Constants::SF_STATUS_SUBMITTED);
                    $newAnswerObj->setVar('status', Constants::SF_AN_STATUS_APPROVED);
                    $notifCase = 4;
                } else {
                    // New submitted answer need approbation
                    $redirect_msg = _MD_SF_FAQ_NEW_ANSWER_NEED_APPROBATION;
                    $faqObj->setVar('status', Constants::SF_STATUS_NEW_ANSWER);
                    $newAnswerObj->setVar('status', Constants::SF_AN_STATUS_PROPOSED);
                    $notifCase = 5;
                }
                break;
        }

        // Storing the FAQ object in the database
        if (!$faqObj->store()) {
            redirect_header('<script>javascript:history.go(-1)</script>', 3, _MD_SF_SUBMIT_ERROR . Smartfaq\Utility::formatErrors($faqObj->getErrors()));
        }

        // Storing the answer object in the database
        if (!$newAnswerObj->store()) {
            redirect_header('<script>javascript:history.go(-1)</script>', 3, _MD_SF_SUBMIT_ERROR . Smartfaq\Utility::formatErrors($newAnswerObj->getErrors()));
        }

        /** @var \XoopsNotificationHandler $notificationHandler */
        $notificationHandler = xoops_getHandler('notification');
        switch ($notifCase) {
            case 1:
                // Question submitted, auto-approved; became Q&A, auto-approved
                // We do not subscribe user to notification on publish since we publish it right away

                // Send notifications
                $faqObj->sendNotifications([Constants::SF_NOT_FAQ_PUBLISHED]);
                break;
            case 2:
                // Answer for an open question submitted, auto-approved; became Q&A, need approbation
                if (Request::hasVar('notifypub', 'POST') && 1 == $_POST['notifypub']) {
                    require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
                    $notificationHandler->subscribe('faq', $faqObj->faqid(), 'approved', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
                }
                // Send notifications
                $faqObj->sendNotifications([Constants::SF_NOT_FAQ_SUBMITTED]);
                break;
            case 3:
                // Answer submitted, needs approbation
                if (Request::hasVar('notifypub', 'POST') && 1 == $_POST['notifypub']) {
                    require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
                    $notificationHandler->subscribe('question', $newAnswerObj->answerid(), 'approved', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
                }
                // Send notifications
                $faqObj->sendNotifications([Constants::SF_NOT_QUESTION_SUBMITTED]);
                break;
            case 4:
                // New answer submitted for a published Q&A, auto-approved
                // TODO...
                break;
            case 5:
                // New answer submitted for a published Q&A, need approbation
                // Send notifications
                if (Request::hasVar('notifypub', 'POST') && 1 == $_POST['notifypub']) {
                    require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
                    $notificationHandler->subscribe('faq', $newAnswerObj->answerid(), 'answer_approved', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
                }

                $faqObj->sendNotifications([Constants::SF_NOT_NEW_ANSWER_PROPOSED]);
                break;
        }

        redirect_header('index.php', 3, $redirect_msg);
        break;
    case 'form':
    default:
        global $xoopsUser, $xoopsModule, $_SERVER;

        // Creating the FAQ object for the selected FAQ
        $faqObj = new Smartfaq\Faq($faqid);

        // If the selected FAQ was not found, exit
        if ($faqObj->notLoaded()) {
            redirect_header('<script>javascript:history.go(-1)</script>', 1, _MD_SF_NOFAQSELECTED);
        }

        // Creating the category object that holds the selected FAQ
        $categoryObj = $faqObj->category();

        // Creating the answer object
        $answerObj = $faqObj->answer();

        // Check user permissions to access that category of the selected FAQ
        if (Smartfaq\Utility::faqAccessGranted($faqObj) < 0) {
            redirect_header('<script>javascript:history.go(-1)</script>', 1, _NOPERM);
        }

        require_once XOOPS_ROOT_PATH . '/header.php';
        require_once __DIR__ . '/footer.php';

        $name = $xoopsUser ? ucwords($xoopsUser->getVar('uname')) : 'Anonymous';

        $moduleName = &$myts->displayTarea($xoopsModule->getVar('name'));
        $xoopsTpl->assign('whereInSection', $moduleName);
        $xoopsTpl->assign('lang_submit', _MD_SF_SUBMITANSWER);

        $xoopsTpl->assign('lang_intro_title', sprintf(_MD_SF_SUBMITANSWERTO, ucwords($xoopsModule->name())));
        $xoopsTpl->assign('lang_intro_text', _MD_SF_GOODDAY . "<b>$name</b>, " . _MD_SF_SUBMITANSWER_INTRO);

        require_once __DIR__ . '/include/answer.inc.php';

        require_once XOOPS_ROOT_PATH . '/footer.php';
        break;
}
