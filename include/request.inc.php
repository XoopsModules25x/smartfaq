<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

global $_POST;

require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

$form = new \XoopsThemeForm(_MD_SF_REQUEST, 'form', xoops_getenv('PHP_SELF'), 'post', true);
// CATEGORY
$mytree = new Smartfaq\Tree($xoopsDB->prefix('smartfaq_categories'), 'categoryid', 'parentid');
ob_start();
$form->addElement(new \XoopsFormHidden('categoryid', ''));
$mytree->makeMySelBox('name', 'weight', '');
$category_label = new \XoopsFormLabel(_MD_SF_CATEGORY_QUESTION, ob_get_contents());
$category_label->setDescription(_MD_SF_CATEGORY_QUESTION_DSC);
$form->addElement($category_label);
ob_end_clean();
// QUESTION
$form->addElement(new \XoopsFormTextArea(_MD_SF_QUESTION, 'question', '', 10, 38), true);

$button_tray = new \XoopsFormElementTray('', '');
$hidden      = new \XoopsFormHidden('op', 'post');
$button_tray->addElement($hidden);
$button_tray->addElement(new \XoopsFormButton('', 'post', _SUBMIT, 'submit'));
$form->addElement($button_tray);
// NOTIFY ON PUBLISH
if (is_object($xoopsUser)) {
    $notify_checkbox = new \XoopsFormCheckBox('', 'notifypub', 1);
    $notify_checkbox->addOption(1, _MD_SF_NOTIFY);
    $form->addElement($notify_checkbox);
}

$form->assign($xoopsTpl);
unset($hidden);
