<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq;
use XoopsModules\Smartfaq\Constants;

require_once __DIR__ . '/header.php';

global $xoopsUser, $xoopsConfig, $xoopsModuleConfig, $xoopsModule;

// Creating the category handler object
/** @var \XoopsModules\Smartfaq\CategoryHandler $categoryHandler */
$categoryHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Category');

// Creating the FAQ handler object
/** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
$faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');

// Creating the answer handler object
/** @var \XoopsModules\Smartfaq\AnswerHandler $answerHandler */
$answerHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Answer');

// Get the total number of categories
$totalCategories = count($categoryHandler->getCategories());

if (0 == $totalCategories) {
    redirect_header('index.php', 1, _AM_SF_NOCOLEXISTS);
}

// Find if the user is admin of the module
$isAdmin = Smartfaq\Utility::userIsAdmin();
// If the user is not admin AND we don't allow user submission, exit
if (!($isAdmin
      || (isset($xoopsModuleConfig['allowsubmit']) && 1 == $xoopsModuleConfig['allowsubmit']
          && (is_object($xoopsUser)
              || (isset($xoopsModuleConfig['anonpost'])
                  && 1 == $xoopsModuleConfig['anonpost']))))) {
    redirect_header('index.php', 1, _NOPERM);
}

$op = 'form';

if (isset($_POST['post'])) {
    $op = 'post';
} elseif (isset($_POST['preview'])) {
    $op = 'preview';
}

switch ($op) {
    case 'preview':

        global $xoopsUser, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $xoopsDB;

        $faqObj      = $faqHandler->create();
        $answerObj   = $answerHandler->create();
        $categoryObj = $categoryHandler->get($_POST['categoryid']);

        if (!$xoopsUser) {
            if (1 == $xoopsModuleConfig['anonpost']) {
                $uid = 0;
            } else {
                redirect_header('index.php', 3, _NOPERM);
            }
        } else {
            $uid = $xoopsUser->uid();
        }

        $notifypub = isset($_POST['notifypub']) ? $_POST['notifypub'] : 0;

        // Putting the values about the FAQ in the FAQ object
        $faqObj->setVar('categoryid', $_POST['categoryid']);
        $faqObj->setVar('uid', $uid);
        $faqObj->setVar('question', $_POST['question']);
        $faqObj->setVar('howdoi', $_POST['howdoi']);
        $faqObj->setVar('diduno', $_POST['diduno']);
        $faqObj->setVar('datesub', time());

        // Putting the values in the answer object
        $answerObj->setVar('status', Constants::SF_AN_STATUS_APPROVED);
        $answerObj->setVar('faqid', $faqObj->faqid());
        $answerObj->setVar('answer', $_POST['answer']);
        $answerObj->setVar('uid', $uid);

        global $xoopsUser, $myts;

        $GLOBALS['xoopsOption']['template_main'] = 'smartfaq_submit.tpl';
        require_once XOOPS_ROOT_PATH . '/header.php';
        require_once __DIR__ . '/footer.php';

        $name = $xoopsUser ? ucwords($xoopsUser->getVar('uname')) : 'Anonymous';

        $moduleName          =& $myts->displayTarea($xoopsModule->getVar('name'));
        $faq                 = $faqObj->toArray(null, $categoryObj, false);
        $faq['categoryPath'] = $categoryObj->getCategoryPath(true);
        $faq['answer']       = $answerObj->answer();
        $faq['who_when']     = $faqObj->getWhoAndWhen();

        $faq['comments'] = -1;
        $xoopsTpl->assign('faq', $faq);
        $xoopsTpl->assign('op', 'preview');
        $xoopsTpl->assign('whereInSection', $moduleName);
        $xoopsTpl->assign('lang_submit', _MD_SF_SUB_SNEWNAME);

        $xoopsTpl->assign('lang_intro_title', sprintf(_MD_SF_SUB_SNEWNAME, ucwords($xoopsModule->name())));
        $xoopsTpl->assign('lang_intro_text', _MD_SF_GOODDAY . "<b>$name</b>, " . _MD_SF_SUB_INTRO);

        require_once __DIR__ . '/include/submit.inc.php';

        require_once XOOPS_ROOT_PATH . '/footer.php';

        exit();
        break;

    case 'post':

        global $xoopsUser, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $xoopsDB;

        $newFaqObj    = $faqHandler->create();
        $newAnswerObj = $answerHandler->create();

        if (!$xoopsUser) {
            if (1 == $xoopsModuleConfig['anonpost']) {
                $uid = 0;
            } else {
                redirect_header('index.php', 3, _NOPERM);
            }
        } else {
            $uid = $xoopsUser->uid();
        }

        $notifypub = isset($_POST['notifypub']) ? $_POST['notifypub'] : 0;

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
        $isAdmin = Smartfaq\Utility::userIsAdmin();
        if ($isAdmin) {
            $newFaqObj->setVar('status', Constants::SF_STATUS_PUBLISHED);
        } elseif (1 == $xoopsModuleConfig['autoapprove_submitted_faq']) {
            $newFaqObj->setVar('status', Constants::SF_STATUS_PUBLISHED);
        } else {
            $newFaqObj->setVar('status', Constants::SF_STATUS_SUBMITTED);
        }

        // Storing the FAQ object in the database
        if (!$newFaqObj->store()) {
            redirect_header('javascript:history.go(-1)', 2, _MD_SF_SUBMIT_ERROR);
        }

        // Putting the values in the answer object
        $newAnswerObj->setVar('status', Constants::SF_AN_STATUS_APPROVED);
        $newAnswerObj->setVar('faqid', $newFaqObj->faqid());
        $newAnswerObj->setVar('answer', $_POST['answer']);
        $newAnswerObj->setVar('uid', $uid);

        //====================================================================================
        //TODO post Attachment
        $attachments_tmp = [];
        if (!empty($_POST['attachments_tmp'])) {
            $attachments_tmp = unserialize(base64_decode($_POST['attachments_tmp']));
            if (isset($_POST['delete_tmp']) && count($_POST['delete_tmp'])) {
                foreach ($_POST['delete_tmp'] as $key) {
                    unlink(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['dir_attachments'] . '/' . $attachments_tmp[$key][0]);
                    unset($attachments_tmp[$key]);
                }
            }
        }
        if (count($attachments_tmp)) {
            foreach ($attachments_tmp as $key => $attach) {
                if (rename(XOOPS_CACHE_PATH . '/' . $attachments_tmp[$key][0], XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['dir_attachments'] . '/' . $attachments_tmp[$key][0])) {
                    $post_obj->setAttachment($attach[0], $attach[1], $attach[2]);
                }
            }
        }
        $error_upload = '';

        if (isset($_FILES['userfile']['name']) && '' != $_FILES['userfile']['name']
            && $topicHandler->getPermission($forum_obj, $topic_status, 'attach')) {
            require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname', 'n') . '/class/uploader.php';
            $maxfilesize = $forum_obj->getVar('attach_maxkb') * 1024;
            $uploaddir   = XOOPS_CACHE_PATH;

            $uploader = new Smartfaq\Uploader($uploaddir, $newAnswerObj->getVar('attach_ext'), (int)$maxfilesize, (int)$xoopsModuleConfig['max_img_width'], (int)$xoopsModuleConfig['max_img_height']);

            if ($_FILES['userfile']['error'] > 0) {
                switch ($_FILES['userfile']['error']) {
                    case 1:
                        $error_message[] = _MD_NEWBB_MAXUPLOADFILEINI;
                        break;
                    case 2:
                        $error_message[] = sprintf(_MD_NEWBB_MAXKB, $forum_obj->getVar('attach_maxkb'));
                        break;
                    default:
                        $error_message[] = _MD_NEWBB_UPLOAD_ERRNODEF;
                        break;
                }
            } else {
                $uploader->setCheckMediaTypeByExt();

                if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
                    $prefix = is_object($xoopsUser) ? (string)$xoopsUser->uid() . '_' : 'newbb_';
                    $uploader->setPrefix($prefix);
                    if (!$uploader->upload()) {
                        $error_message[] = $error_upload =& $uploader->getErrors();
                    } else {
                        if (is_file($uploader->getSavedDestination())) {
                            if (rename(XOOPS_CACHE_PATH . '/' . $uploader->getSavedFileName(), XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['dir_attachments'] . '/' . $uploader->getSavedFileName())) {
                                $post_obj->setAttachment($uploader->getSavedFileName(), $uploader->getMediaName(), $uploader->getMediaType());
                            }
                        }
                    }
                } else {
                    $error_message[] = $error_upload =& $uploader->getErrors();
                }
            }
        }

        //====================================================

        // Storing the answer object in the database
        if (!$newAnswerObj->store()) {
            redirect_header('javascript:history.go(-1)', 2, _MD_SF_SUBMIT_ERROR);
        }

        // Get the cateopry object related to that FAQ
        $categoryObj = $newFaqObj->category();

        // If autoapprove_submitted_faq
        if ($isAdmin) {
            // We do not not subscribe user to notification on publish since we publish it right away

            // Send notifications
            $newFaqObj->sendNotifications([Constants::SF_NOT_FAQ_PUBLISHED]);

            $redirect_msg = _MD_SF_SUBMIT_FROM_ADMIN;
        } elseif (1 == $xoopsModuleConfig['autoapprove_submitted_faq']) {
            // We do not not subscribe user to notification on publish since we publish it right away

            // Send notifications
            $newFaqObj->sendNotifications([Constants::SF_NOT_FAQ_PUBLISHED]);

            $redirect_msg = _MD_SF_QNA_RECEIVED_AND_PUBLISHED;
        } else {
            // Subscribe the user to On Published notification, if requested
            if (1 == $notifypub) {
                require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
                $notificationHandler = xoops_getHandler('notification');
                $notificationHandler->subscribe('faq', $newFaqObj->faqid(), 'approved', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
            }
            // Send notifications
            $newFaqObj->sendNotifications([Constants::SF_NOT_FAQ_SUBMITTED]);

            $redirect_msg = _MD_SF_QNA_RECEIVED_NEED_APPROVAL;
        }

        redirect_header('index.php', 2, $redirect_msg);
        break;

    case 'form':
    default:

        global $xoopsUser, $myts;

        $faqObj      = $faqHandler->create();
        $answerObj   = $answerHandler->create();
        $categoryObj = $categoryHandler->create();

        $GLOBALS['xoopsOption']['template_main'] = 'smartfaq_submit.tpl';
        require_once XOOPS_ROOT_PATH . '/header.php';
        require_once __DIR__ . '/footer.php';

        $name       = $xoopsUser ? ucwords($xoopsUser->getVar('uname')) : 'Anonymous';
        $notifypub  = 1;
        $moduleName =& $myts->displayTarea($xoopsModule->getVar('name'));
        $xoopsTpl->assign('whereInSection', $moduleName);
        $xoopsTpl->assign('lang_submit', _MD_SF_SUB_SNEWNAME);

        $xoopsTpl->assign('lang_intro_title', sprintf(_MD_SF_SUB_SNEWNAME, ucwords($xoopsModule->name())));
        $xoopsTpl->assign('lang_intro_text', _MD_SF_GOODDAY . "<b>$name</b>, " . _MD_SF_SUB_INTRO);

        require_once __DIR__ . '/include/submit.inc.php';

        require_once XOOPS_ROOT_PATH . '/footer.php';
        break;
}
