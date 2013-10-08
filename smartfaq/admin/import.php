<?php

/**
* $Id: import.php,v 1.16 2005/08/15 16:51:57 fx2024 Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

include_once("admin_header.php");

$op='none';

if (isset($_GET['op'])) $op = $_GET['op'];
if (isset($_POST['op'])) $op = $_POST['op'];

global $xoopsDB;

switch ($op) {
	
	case "importExecute":

	$importfile = (isset($_POST['importfile']))? $_POST['importfile'] : 'nonselected';
	$importfile_path = XOOPS_ROOT_PATH."/modules/smartfaq/admin/".$importfile.".php";
	if (!file_exists($importfile_path)) {
		$errs[] = sprintf(_AM_SF_IMPORT_FILE_NOT_FOUND, $importfile_path);
		$error = true;
	} else {
		include_once($importfile_path);
	}
	foreach ($msgs as $m) {
		echo $m.'<br />';
	}
	echo "<br />";
	if ($error == true) {
		$endMsg = _AM_SF_IMPORT_ERROR;
	} else {
		$endMsg = _AM_SF_IMPORT_SUCCESS;
	}
	
	echo $endMsg;
	echo "<br /><br />";
	echo "<a href='import.php'>" . _AM_SF_IMPORT_BACK . "</a>";
	echo "<br /><br />";
	break;
	
	case "default":
	default:
	
	$importfile = 'none';
	
	xoops_cp_header();
	//sf_adminMenu(-1, _AM_SF_IMPORT);
	
	sf_collapsableBar('bottomtable', 'bottomtableicon');
	echo "<img id='bottomtableicon' src=" . XOOPS_URL . "/modules/" . $xoopsModule->dirname() . "/images/icon/close12.gif alt='' /></a>&nbsp;" . _AM_SF_IMPORT_TITLE . "</h3>";
	echo "<div id='bottomtable'>";
	echo "<span style=\"color: #567; margin: 3px 0 12px 0; font-size: small; display: block; \">" . _AM_SF_IMPORT_INFO . "</span>";
	
	global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $modify, $xoopsModuleConfig, $xoopsModule, $XOOPS_URL, $myts;
	
	include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
	
	$module_handler =& xoops_gethandler ('module');
	if ($module_handler->getByDirname('xoopsfaq')) {
		$importfile_select_array["xoopsfaq"] = _AM_SF_IMPORT_XOOPSFAQ_110;
	}
	
	if ($module_handler->getByDirname('wffaq')) {
		$importfile_select_array["wffaq"] = _AM_SF_IMPORT_WFFAQ_105;
	}
	
	if (isset($importfile_select_array) && count($importfile_select_array) > 0 ) {
		
		$sform = new XoopsThemeForm(_AM_SF_IMPORT_SELECTION, "op", xoops_getenv('PHP_SELF'));
		$sform->setExtra('enctype="multipart/form-data"');
		
		// Q&A set to import		
		$importfile_select = new XoopsFormSelect('', 'importfile', $importfile);
		$importfile_select->addOptionArray($importfile_select_array);
		$importfile_tray = new XoopsFormElementTray(_AM_SF_IMPORT_SELECT_FILE , '&nbsp;');
		$importfile_tray->addElement($importfile_select);
		$sform->addElement($importfile_tray);
		
		// Buttons
		$button_tray = new XoopsFormElementTray('', '');
		$hidden = new XoopsFormHidden('op', 'importExecute');
		$button_tray->addElement($hidden);
		
		$butt_import = new XoopsFormButton('', '', _AM_SF_IMPORT, 'submit');
		$butt_import->setExtra('onclick="this.form.elements.op.value=\'importExecute\'"');
		$button_tray->addElement($butt_import);
		
		$butt_cancel = new XoopsFormButton('', '', _AM_SF_CANCEL, 'button');
		$butt_cancel->setExtra('onclick="history.go(-1)"');
		$button_tray->addElement($butt_cancel);
		
		$sform->addElement($button_tray);
		$sform->display();
		unset($hidden);
	} else {
	  echo "<span style=\"color: #567; margin: 3px 0 12px 0; font-weight: bold; font-size: small; display: block; \">" . _AM_SF_IMPORT_NO_MODULE . "</span>";
	}
	
	
	// End of collapsable bar
	echo "</div>";
	
	break;
}

include "admin_footer.php";