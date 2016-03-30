<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
include_once dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
include_once XOOPS_ROOT_PATH . '/kernel/module.php';
include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

include_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';
include_once XOOPS_ROOT_PATH . '/modules/smartfaq/class/category.php';
include_once XOOPS_ROOT_PATH . '/modules/smartfaq/class/faq.php';
include_once XOOPS_ROOT_PATH . '/modules/smartfaq/class/answer.php';

$myts = MyTextSanitizer::getInstance();
