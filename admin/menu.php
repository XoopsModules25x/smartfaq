<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */



if (!isset($moduleDirName)) {
    $moduleDirName = basename(dirname(__DIR__));
}

if (false !== ($moduleHelper = Xmf\Module\Helper::getHelper($moduleDirName))) {
} else {
    $moduleHelper = Xmf\Module\Helper::getHelper('system');
}
$adminObject = \Xmf\Module\Admin::getInstance();

$pathIcon32    = \Xmf\Module\Admin::menuIconPath('');
//$pathModIcon32 = $moduleHelper->getModule()->getInfo('modicons32');

$moduleHelper->loadLanguage('modinfo');

$adminObject              = array();
$i                      = 0;
$adminmenu[$i]['title'] = _AM_MODULEADMIN_HOME;
$adminmenu[$i]['link']  = 'admin/index.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/home.png';
++$i;
$adminmenu[$i]['title'] = _MI_SF_ADMENU1;
$adminmenu[$i]['link']  = 'admin/main.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/manage.png';

++$i;
$adminmenu[$i]['title'] = _MI_SF_ADMENU2;
$adminmenu[$i]['link']  = 'admin/category.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/category.png';
++$i;
$adminmenu[$i]['title'] = _MI_SF_ADMENU3;
$adminmenu[$i]['link']  = 'admin/faq.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/search.png';
++$i;
$adminmenu[$i]['title'] = _MI_SF_ADMENU4;
$adminmenu[$i]['link']  = 'admin/question.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/faq.png';
++$i;
$adminmenu[$i]['title'] = _MI_SF_ADMENU5;
$adminmenu[$i]['link']  = 'admin/permissions.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/permissions.png';
++$i;
$adminmenu[$i]['title'] = _MI_SF_ADMENU8;
$adminmenu[$i]['link']  = 'admin/import.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/download.png';
++$i;
$adminmenu[$i]['title'] = _AM_MODULEADMIN_ABOUT;
$adminmenu[$i]['link']  = 'admin/about.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/about.png';
//++$i;
//$adminmenu[$i]['title'] = _AM_MODULEADMIN_ABOUT;
//$adminmenu[$i]["link"]  = "admin/about2.php";
//$adminmenu[$i]["icon"]  = $pathIcon32 . '/about.png';
//-------------------------------
// Index
//$adminmenu[0]['title'] = _MI_SF_ADMENU1;
//$adminmenu[0]['link'] = "admin/index.php";
// Category
//$adminmenu[1]['title'] = _MI_SF_ADMENU2;
//$adminmenu[1]['link'] = "admin/category.php";
// faqs
//$adminmenu[2]['title'] = _MI_SF_ADMENU3;
//$adminmenu[2]['link'] = "admin/faq.php";
// Questions
//$adminmenu[3]['title'] = _MI_SF_ADMENU4;
//$adminmenu[3]['link'] = "admin/question.php";
//// Permissions
//$adminmenu[4]['title'] = _MI_SF_ADMENU5;
//$adminmenu[4]['link'] = "admin/permissions.php";
//// Blocks and Groups
//$adminmenu[5]['title'] = _MI_SF_ADMENU6;
//$adminmenu[5]['link'] = "admin/myblocksadmin.php";
// Goto Module
//$adminmenu[6]['title'] = _MI_SF_ADMENU7;
//$adminmenu[6]['link'] = "index.php";
