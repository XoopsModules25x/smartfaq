<?php

/**
* $Id: submit.inc.php,v 1.12 2005/08/16 15:39:46 fx2024 Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/
if (!defined("XOOPS_ROOT_PATH")) { 
 	die("XOOPS root path not defined");
}

global $_POST, $xoopsDB;

include_once XOOPS_ROOT_PATH . "/class/xoopstree.php";
include_once XOOPS_ROOT_PATH . "/class/xoopslists.php";
include_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";

include_once 'functions.php';

$mytree = new XoopsTree($xoopsDB->prefix("smartfaq_categories"), "categoryid", "parentid");
$form = new XoopsThemeForm(_MD_SF_SUB_SMNAME, "form", xoops_getenv('PHP_SELF'));

// Category
ob_start();
$form->addElement(new XoopsFormHidden('categoryid', ''));
$mytree->makeMySelBox("name", "weight", $categoryObj->categoryid());
$category_label = new XoopsFormLabel(_MD_SF_CATEGORY_FAQ, ob_get_contents());
$category_label->setDescription(_MD_SF_CATEGORY_FAQ_DSC);
$form->addElement($category_label);
ob_end_clean();

// FAQ QUESTION
$form->addElement(new XoopsFormTextArea(_MD_SF_QUESTION, 'question', $faqObj->question(), 7, 60), true);

// ANSWER
//$answer_text = new XoopsFormDhtmlTextArea(_MD_SF_ANSWER_FAQ, 'answer', $answerObj->answer(), 15, 60);
//$answer_text->setDescription(_MD_SF_ANSWER_FAQ_DSC);
//$form->addElement($answer_text, true);

$editorTray = new XoopsFormElementTray(_MD_SF_ANSWER_FAQ, '<br />');
   if (class_exists('XoopsFormEditor')) {
           $options['name'] = 'answer';
           $options['value'] = $answerObj->answer();
           $options['rows'] = 5;
           $options['cols'] = '100%';
           $options['width'] = '100%';
           $options['height'] = '200px';
       $answerEditor  = new XoopsFormEditor('', $xoopsModuleConfig['form_editorOptionsUser'], $options, $nohtml = false, $onfailure = 'textarea');
           $editorTray->addElement($answerEditor);
       } else {
       $answerEditor  = new XoopsFormDhtmlTextArea('', 'answer', $faqObj->question(), '100%', '100%');
       $answerEditor->setDescription(_MD_SF_ANSWER_FAQ_DSC);
       $editorTray->addElement($answerEditor);
   }

$form->addElement($editorTray);


// HOW DO I
$howdoi_text = new XoopsFormText(_MD_SF_HOWDOI_FAQ, 'howdoi', 50, 255, $faqObj->howdoi());
$howdoi_text->setDescription(_MD_SF_HOWDOI_FAQ_DSC);
$form->addElement($howdoi_text, false);

// DIDUNO
$diduno_text = new XoopsFormTextArea(_MD_SF_DIDUNO_FAQ, 'diduno', $faqObj->diduno(), 3, 60);
$diduno_text->setDescription(_MD_SF_DIDUNO_FAQ_DSC);
$form->addElement($diduno_text);

// CONTEXT MODULE LINK
// Retreive the list of module currently installed. The key value is the dirname
/*$module_handler = &xoops_gethandler('module');
$modules_array = $module_handler->getList(null, true);
$modulelink_select_array = array("url" => _MD_SF_SPECIFIC_URL_SELECT);
$modulelink_select_array = array_merge($modules_array, $modulelink_select_array);
$modulelink_select_array = array_merge(array("None" => _MD_SF_NONE, "All" => _MD_SF_ALL), $modulelink_select_array);

$modulelink_select = new XoopsFormSelect('', 'modulelink', '');
$modulelink_select->addOptionArray($modulelink_select_array);
$modulelink_tray = new XoopsFormElementTray(_MD_SF_CONTEXTMODULELINK_FAQ , '&nbsp;');
$modulelink_tray->addElement($modulelink_select);
$form->addElement($modulelink_tray);
*/
// CONTEXTPAGE
//$form->addElement(new XoopsFormText(_MD_SF_SPECIFIC_URL, 'contextpage', 50, 60, ''), false);

// EXACT URL?
/*$excaturl_radio = new XoopsFormRadioYN(_MD_SF_EXACTURL, 'exacturl', 0, ' ' . _MD_SF_YES . '', ' ' . _MD_SF_NO . '');
$form->addElement($excaturl_radio);
*/
// NOTIFY ON PUBLISH
if (is_object($xoopsUser)) {
	$notify_checkbox = new XoopsFormCheckBox('', 'notifypub', $notifypub);
	$notify_checkbox->addOption(1, _MD_SF_NOTIFY);
	$form->addElement($notify_checkbox);
}

$button_tray = new XoopsFormElementTray('', '');
	
$butt_create = new XoopsFormButton('', 'post', _MD_SF_CREATE, 'submit');
$button_tray->addElement($butt_create);

$butt_preview = new XoopsFormButton('', 'preview', _MD_SF_PREVIEW, 'submit');
$button_tray->addElement($butt_preview);

$form->addElement($button_tray);
$form->assign($xoopsTpl);

unset($hidden);
unset($hidden2);

?>