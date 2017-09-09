<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

$moduleDirName = basename(dirname(__DIR__));

if (false !== ($moduleHelper = Xmf\Module\Helper::getHelper($moduleDirName))) {
} else {
    $moduleHelper = Xmf\Module\Helper::getHelper('system');
}


$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
//$pathModIcon32 = $moduleHelper->getModule()->getInfo('modicons32');

$moduleHelper->loadLanguage('modinfo');

$adminmenu              = [];
$i                      = 0;
'title' =>  _AM_MODULEADMIN_HOME,
'link' =>  'admin/index.php',
'icon' =>  $pathIcon32 . '/home.png',
++$i;
'title' =>  _MI_SF_ADMENU1,
'link' =>  'admin/main.php',
'icon' =>  $pathIcon32 . '/manage.png',

++$i;
'title' =>  _MI_SF_ADMENU2,
'link' =>  'admin/category.php',
'icon' =>  $pathIcon32 . '/category.png',
++$i;
'title' =>  _MI_SF_ADMENU3,
'link' =>  'admin/faq.php',
'icon' =>  $pathIcon32 . '/search.png',
++$i;
'title' =>  _MI_SF_ADMENU4,
'link' =>  'admin/question.php',
'icon' =>  $pathIcon32 . '/faq.png',
++$i;
'title' =>  _MI_SF_ADMENU5,
'link' =>  'admin/permissions.php',
'icon' =>  $pathIcon32 . '/permissions.png',
++$i;
'title' =>  _MI_SF_ADMENU8,
'link' =>  'admin/import.php',
'icon' =>  $pathIcon32 . '/download.png',
++$i;
'title' =>  _AM_MODULEADMIN_ABOUT,
'link' =>  'admin/about.php',
'icon' =>  $pathIcon32 . '/about.png',
//++$i;
//'title' =>  _AM_MODULEADMIN_ABOUT,
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
