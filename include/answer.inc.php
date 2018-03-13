<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq;
use XoopsModules\Smartfaq\Constants;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

global $_POST;

require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
//require_once __DIR__ . '/functions.php';

$mytree = new Smartfaq\Tree($xoopsDB->prefix('smartfaq_categories'), 'categoryid', 'parentid');
//$form = new \XoopsThemeForm(_MD_SF_SUBMITANSWER, "form", xoops_getenv('PHP_SELF'), 'post', true);
$form = new \XoopsThemeForm(_MD_SF_SUBMITANSWER, 'form', '');
// faq QUESTION
$form->addElement(new \XoopsFormLabel(_MD_SF_QUESTION, $faqObj->question()), false);
// ANSWER
//$form->addElement(new \XoopsFormDhtmlTextArea(_MD_SF_ANSWER_FAQ, 'answer', '', 15, 60), true);

$editorTray = new \XoopsFormElementTray(_MD_SF_ANSWER_FAQ, '<br>');
if (class_exists('XoopsFormEditor')) {
    $options['name']   = 'answer';
    $options['value']  = '';
    $options['rows']   = 5;
    $options['cols']   = '100%';
    $options['width']  = '100%';
    $options['height'] = '200px';
    $answerEditor      = new \XoopsFormEditor('', $xoopsModuleConfig['form_editorOptionsUser'], $options, $nohtml = false, $onfailure = 'textarea');
    $editorTray->addElement($answerEditor, true);
} else {
    $answerEditor = new \XoopsFormDhtmlTextArea(_MD_SF_ANSWER_FAQ, 'answer', '', '100%', '100%');
    $editorTray->addElement($answerEditor, true);
}
$form->addElement($editorTray);

// NOTIFY ON PUBLISH
if (is_object($xoopsUser)) {
    $notify_checkbox = new \XoopsFormCheckBox('', 'notifypub', 1);
    $notify_checkbox->addOption(1, _MD_SF_NOTIFY);
    $form->addElement($notify_checkbox);
}

if ((Constants::SF_STATUS_PUBLISHED == $faqObj->status()) || (Constants::SF_STATUS_NEW_ANSWER == $faqObj->status())) {
    $answerObj = $faqObj->answer();
    $form->addElement(new \XoopsFormLabel(_MD_SF_ORIGINAL_ANSWER, $answerObj->answer()), false);
}

$form->addElement(new \XoopsFormHidden('faqid', $faqObj->faqid()));

$button_tray = new \XoopsFormElementTray('', '');
$hidden      = new \XoopsFormHidden('op', 'post');
$button_tray->addElement($hidden);

$button_tray = new \XoopsFormElementTray('', '');
$hidden      = new \XoopsFormHidden('op', 'post');
$button_tray->addElement($hidden);
$button_tray->addElement(new \XoopsFormButton('', 'post', _MD_SF_SUBMITANSWER, 'submit'));
$form->addElement($button_tray);

$form->assign($xoopsTpl);

unset($hidden);
