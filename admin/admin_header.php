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
 * @copyright    XOOPS Project (http://xoops.org)
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team
 */

include_once __DIR__ . '/../../../include/cp_header.php';
//include_once $GLOBALS['xoops']->path('www/class/xoopsformloader.php');

//require __DIR__ . '/../class/utility.php';
//require_once __DIR__ . '/../include/common.php';

if (!isset($moduleDirName)) {
    $moduleDirName = basename(dirname(__DIR__));
}

$path = dirname(dirname(dirname(__DIR__)));
include_once $path . '/kernel/module.php';
include_once $path . '/class/xoopstree.php';
include_once $path . '/class/xoopslists.php';
include_once $path . '/class/pagenav.php';
include_once $path . '/class/xoopsformloader.php';

include_once __DIR__ . '/../include/functions.php';
include_once __DIR__ . '/../class/category.php';
include_once __DIR__ . '/../class/faq.php';
include_once __DIR__ . '/../class/answer.php';
//require __DIR__ . '/../class/utility.php';
//require_once __DIR__ . '/../include/common.php';


if (false !== ($moduleHelper = Xmf\Module\Helper::getHelper($moduleDirName))) {
} else {
    $moduleHelper = Xmf\Module\Helper::getHelper('system');
}
$adminObject = \Xmf\Module\Admin::getInstance();

$pathIcon16      = \Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32      = \Xmf\Module\Admin::iconUrl('', 32);
$pathModIcon32 = $moduleHelper->getModule()->getInfo('modicons32');

// Load language files
$moduleHelper->loadLanguage('admin');
$moduleHelper->loadLanguage('modinfo');
$moduleHelper->loadLanguage('main');

$myts = MyTextSanitizer::getInstance();

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof XoopsTpl)) {
    include_once $GLOBALS['xoops']->path('class/template.php');
    $xoopsTpl = new XoopsTpl();
}
