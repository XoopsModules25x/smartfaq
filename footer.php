<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq;

global $xoopsTpl, $xoopsModule;

$uid     = $xoopsUser ? $xoopsUser->getVar('uid') : 0;
$isAdmin = (Smartfaq\Utility::userIsAdmin() || Smartfaq\Utility::hasModerator());

/** @var Smartfaq\Helper $helper */
$helper = Smartfaq\Helper::getInstance();

$xoopsTpl->assign('sf_adminpage', "<a href='" . XOOPS_URL . "/modules/smartfaq/admin/index.php'>" . _MD_SF_ADMIN_PAGE . '</a>');
$xoopsTpl->assign('isAdmin', $isAdmin);

$xoopsTpl->assign(
    [
        'lang_on'       => _MD_SF_ON,
        'lang_postedby' => _MD_SF_POSTEDBY,
        'lang_faq'      => _MD_SF_QUESTION,
        'lang_datesub'  => _MD_SF_DATESUB,
        'lang_hits'     => _MD_SF_HITS,
    ]
);
$xoopsTpl->assign('sectionname', $myts->displayTarea($xoopsModule->getVar('name')));

$xoopsTpl->assign('modulename', $xoopsModule->dirname());
$xoopsTpl->assign('displaylastfaq', $helper->getConfig('displaylastfaq'));
$xoopsTpl->assign('displaysubcatdsc', $helper->getConfig('displaysubcatdsc'));
$xoopsTpl->assign('displaycollaps', $helper->getConfig('displaycollaps'));
$xoopsTpl->assign('display_date_col', $helper->getConfig('display_date_col'));
$xoopsTpl->assign('display_hits_col', $helper->getConfig('display_hits_col'));

$xoopsTpl->assign('displaytopcatdsc', $helper->getConfig('displaytopcatdsc'));

$xoopsTpl->assign('ref_smartfaq', 'SmartFAQ is developed by The SmartFactory (http://www.smartfactory.ca), a division of InBox Solutions (http://www.inboxsolutions.net)');

$xoopsTpl->assign('xoops_module_header', "<link rel='stylesheet' type='text/css' href='" . XOOPS_URL . "/modules/smartfaq/assets/css/smartfaq.css'>");
