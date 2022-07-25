<?php declare(strict_types=1);

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use Xmf\Module\Admin;
use XoopsModules\Smartfaq;

/** @var Smartfaq\Helper $helper */
$moduleDirName      = \basename(\dirname(__DIR__));
$moduleDirNameUpper = \mb_strtoupper($moduleDirName);

$helper = Smartfaq\Helper::getInstance();
$helper->loadLanguage('common');
$helper->loadLanguage('feedback');

$pathIcon32    = Admin::menuIconPath('');
$pathModIcon32 = XOOPS_URL . '/modules/' . $moduleDirName . '/assets/images/icons/32/';
if (is_object($helper->getModule()) && false !== $helper->getModule()->getInfo('modicons32')) {
    $pathModIcon32 = $helper->url($helper->getModule()->getInfo('modicons32'));
}

$adminmenu[] = [
    'title' => _MI_SF_HOME,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png',
];

$adminmenu[] = [
    'title' => _MI_SF_ADMENU1,
    'link'  => 'admin/main.php',
    'icon'  => $pathIcon32 . '/manage.png',
];

$adminmenu[] = [
    'title' => _MI_SF_ADMENU2,
    'link'  => 'admin/category.php',
    'icon'  => $pathIcon32 . '/category.png',
];

$adminmenu[] = [
    'title' => _MI_SF_ADMENU3,
    'link'  => 'admin/faq.php',
    'icon'  => $pathIcon32 . '/search.png',
];

$adminmenu[] = [
    'title' => _MI_SF_ADMENU4,
    'link'  => 'admin/question.php',
    'icon'  => $pathIcon32 . '/faq.png',
];

$adminmenu[] = [
    'title' => _MI_SF_ADMENU5,
    'link'  => 'admin/permissions.php',
    'icon'  => $pathIcon32 . '/permissions.png',
];

// Blocks Admin
$adminmenu[] = [
    'title' => _MI_SF_BLOCKS,
    'link'  => 'admin/blocksadmin.php',
    'icon'  => $pathIcon32 . '/block.png',
];

$adminmenu[] = [
    'title' => _MI_SF_ADMENU8,
    'link'  => 'admin/import.php',
    'icon'  => $pathIcon32 . '/download.png',
];

$adminmenu[] = [
    'title' => _MI_SF_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png',
];
