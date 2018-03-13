<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

require_once __DIR__ . '/../../../mainfile.php';
require_once __DIR__ . '/../../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/kernel/module.php';
require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

//require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';
//require_once XOOPS_ROOT_PATH . '/modules/smartfaq/class/category.php';
//require_once XOOPS_ROOT_PATH . '/modules/smartfaq/class/faq.php';
//require_once XOOPS_ROOT_PATH . '/modules/smartfaq/class/answer.php';

$myts = \MyTextSanitizer::getInstance();
