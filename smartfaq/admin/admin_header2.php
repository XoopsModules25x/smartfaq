<?php

/**
* $Id: admin_header.php,v 1.5 2004/11/20 16:52:32 malanciault Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

include_once "../../../mainfile.php";
include_once '../../../include/cp_header.php';
include_once XOOPS_ROOT_PATH . "/kernel/module.php";
include_once XOOPS_ROOT_PATH . "/class/xoopstree.php";
include_once XOOPS_ROOT_PATH . "/class/xoopslists.php";
include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
include_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";

include_once XOOPS_ROOT_PATH.'/modules/smartfaq/include/functions.php';
include_once XOOPS_ROOT_PATH.'/modules/smartfaq/class/category.php';
include_once XOOPS_ROOT_PATH.'/modules/smartfaq/class/faq.php';
include_once XOOPS_ROOT_PATH.'/modules/smartfaq/class/answer.php';

$myts = &MyTextSanitizer::getInstance();

?>