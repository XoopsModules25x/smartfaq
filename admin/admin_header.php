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
 * @copyright    XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team
 */

use XoopsModules\Smartfaq;

require_once __DIR__ . '/../../../include/cp_header.php';
//require_once $GLOBALS['xoops']->path('www/class/xoopsformloader.php');

// require_once __DIR__ . '/../class/Utility.php';
//require_once __DIR__ . '/../include/common.php';

$moduleDirName = basename(dirname(__DIR__));

$path = dirname(dirname(dirname(__DIR__)));
require_once $path . '/kernel/module.php';
require_once $path . '/class/xoopstree.php';
require_once $path . '/class/xoopslists.php';
require_once $path . '/class/pagenav.php';
require_once $path . '/class/xoopsformloader.php';

//require_once __DIR__ . '/../include/functions.php';
// require_once __DIR__ . '/../class/category.php';
// require_once __DIR__ . '/../class/faq.php';
// require_once __DIR__ . '/../class/answer.php';
// require_once __DIR__ . '/../class/Utility.php';
require_once __DIR__ . '/../include/common.php';
/** @var Smartfaq\Helper $helper */
$helper      = Smartfaq\Helper::getInstance();
$adminObject = \Xmf\Module\Admin::getInstance();

$pathIcon16    = \Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32    = \Xmf\Module\Admin::iconUrl('', 32);
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('main');

$myts = \MyTextSanitizer::getInstance();

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof XoopsTpl)) {
    require_once $GLOBALS['xoops']->path('class/template.php');
    $xoopsTpl = new \XoopsTpl();
}
