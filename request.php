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

require_once __DIR__ . '/header.php';
require_once XOOPS_ROOT_PATH . '/header.php';

global $xoopsUser, $xoopsConfig, $xoopsModule;
/** @var Smartfaq\Helper $helper */
$helper = Helper::getInstance();

// Creating the category handler object
/** @var \XoopsModules\Smartfaq\CategoryHandler $categoryHandler */
$categoryHandler = Helper::getInstance()->getHandler('Category');

// Creating the FAQ handler object
/** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
$faqHandler = Helper::getInstance()->getHandler('Faq');

// Get the total number of categories
$totalCategories = count($categoryHandler->getCategories());

if (0 == $totalCategories) {
    redirect_header('index.php', 1, _AM_SF_NOCOLEXISTS);
}

// Find if the user is admin of the module
$isAdmin = Smartfaq\Utility::userIsAdmin();
// If the user is not admin AND we don't allow user submission, exit
if (!($isAdmin
      || (null !== $helper->getConfig('allowrequest')
          && 1 == $helper->getConfig('allowrequest')
          && (is_object($xoopsUser) || (null !== $helper->getConfig('anonpost') && 1 == $helper->getConfig('anonpost')))))) {
    redirect_header('index.php', 1, _NOPERM);
}

$op = Request::getCmd('op', '');

switch ($op) {
    case 'post':
        global $xoopsUser, $xoopsConfig, $xoopsModule, $xoopsDB;

        $newFaqObj = $faqHandler->create();

        if ($xoopsUser) {
            $uid = $xoopsUser->uid();
        } else {
            if (1 == $helper->getConfig('anonpost')) {
                $uid = 0;
            } else {
                redirect_header('index.php', 3, _NOPERM);
            }
        }

        // Putting the values about the FAQ in the FAQ object
        $newFaqObj->setVar('categoryid', $_POST['categoryid']);
        $newFaqObj->setVar('uid', $uid);
        $newFaqObj->setVar('question', $_POST['question']);
        $notifypub = Request::getInt('notifypub', 0, 'POST');
        $newFaqObj->setVar('notifypub', $notifypub);

        // Setting the status of the FAQ
        if (1 == $helper->getConfig('autoapprove_request')) {
            $newFaqObj->setVar('status', Constants::SF_STATUS_OPENED);
        } else {
            $newFaqObj->setVar('status', Constants::SF_STATUS_ASKED);
        }

        // Storing the FAQ object in the database
        if (!$newFaqObj->store()) {
            redirect_header('<script>javascript:history.go(-1)</script>', 3, _MD_SF_REQUEST_ERROR . Smartfaq\Utility::formatErrors($newFaqObj->getErrors()));
        }

        // Get the cateopry object related to that FAQ
        // If autoapprove_requested
        if (1 == $helper->getConfig('autoapprove_request')) {
            // We do not subscribe user to notification on publish since we publish it right away

            // Send notifications
            $newFaqObj->sendNotifications([Constants::SF_NOT_QUESTION_PUBLISHED]);

            $redirect_msg = _MD_SF_REQUEST_RECEIVED_AND_PUBLISHED;
        } else {
            // Subscribe the user to On Published notification, if requested
            if (1 == $notifypub) {
                require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
                /** @var \XoopsNotificationHandler $notificationHandler */
                $notificationHandler = xoops_getHandler('notification');
                $notificationHandler->subscribe('question', $newFaqObj->faqid(), 'approved', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
            }
            // Send notifications
            $newFaqObj->sendNotifications([Constants::SF_NOT_QUESTION_SUBMITTED]);

            $redirect_msg = _MD_SF_REQUEST_RECEIVED_NEED_APPROVAL;
        }

        //redirect_header("javascript:history.go(-2)", 3, $redirect_msg);
        redirect_header('index.php', 2, $redirect_msg);
        break;
    case 'form':
    default:
        global $xoopsUser, $myts;

        $GLOBALS['xoopsOption']['template_main'] = 'smartfaq_submit.tpl';
        require_once XOOPS_ROOT_PATH . '/header.php';
        require_once __DIR__ . '/footer.php';

        $name = $xoopsUser ? ucwords($xoopsUser->getVar('uname')) : 'Anonymous';

        $moduleName = &$myts->displayTarea($xoopsModule->getVar('name'));
        $xoopsTpl->assign('whereInSection', $moduleName);
        $xoopsTpl->assign('lang_submit', _MD_SF_REQUEST);

        $xoopsTpl->assign('lang_intro_title', _MD_SF_REQUEST);
        $xoopsTpl->assign('lang_intro_text', _MD_SF_GOODDAY . "<b>$name</b>, " . $myts->displayTarea($helper->getConfig('requestintromsg')));

        require_once __DIR__ . '/include/request.inc.php';

        $name = $xoopsUser ? ucwords($xoopsUser->getVar('uname')) : 'Anonymous';

        $sectionname = htmlspecialchars($xoopsModule->getVar('name'), ENT_QUOTES | ENT_HTML5);

        require_once __DIR__ . '/include/request.inc.php';

        require_once XOOPS_ROOT_PATH . '/footer.php';

        break;
}
