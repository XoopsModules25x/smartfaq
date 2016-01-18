<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author     XOOPS Development Team
 * @version    $Id $
 */

$path = dirname(dirname(dirname(__DIR__)));
include_once $path . '/mainfile.php';
include_once $path . '/include/cp_functions.php';
require_once $path . '/include/cp_header.php';

include_once $path . "/kernel/module.php";
include_once $path . "/class/xoopstree.php";
include_once $path . "/class/xoopslists.php";
include_once $path . '/class/pagenav.php';
include_once $path . "/class/xoopsformloader.php";

include_once $path .'/modules/smartfaq/include/functions.php';
include_once $path .'/modules/smartfaq/class/category.php';
include_once $path .'/modules/smartfaq/class/faq.php';
include_once $path .'/modules/smartfaq/class/answer.php';

$myts = &MyTextSanitizer::getInstance();

global $xoopsModule;

$thisModuleDir = $GLOBALS['xoopsModule']->getVar('dirname');

//if functions.php file exist
//require_once dirname(__DIR__) . '/include/functions.php';

// Load language files
xoops_loadLanguage('admin', $thisModuleDir);
xoops_loadLanguage('modinfo', $thisModuleDir);
xoops_loadLanguage('main', $thisModuleDir);

$pathIcon16 = '../'.$xoopsModule->getInfo('icons16');
$pathIcon32 = '../'.$xoopsModule->getInfo('icons32');
$pathModuleAdmin = $xoopsModule->getInfo('dirmoduleadmin');

if ( file_exists($GLOBALS['xoops']->path($pathModuleAdmin.'/moduleadmin.php'))) {
        include_once $GLOBALS['xoops']->path($pathModuleAdmin.'/moduleadmin.php');
    } else {
        redirect_header("../../../admin.php", 5, _AM_BIRTHDAY_MODULEADMIN_MISSING, false);
    }
