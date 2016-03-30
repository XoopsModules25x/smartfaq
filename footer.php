<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

global $xoopsTpl, $xoopsModule, $xoopsModuleConfig;

$uid     = $xoopsUser ? $xoopsUser->getVar('uid') : 0;
$isAdmin = (sf_userIsAdmin() || sf_moderator());

$xoopsTpl->assign('sf_adminpage', "<a href='" . XOOPS_URL . "/modules/smartfaq/admin/index.php'>" . _MD_SF_ADMIN_PAGE . '</a>');
$xoopsTpl->assign('isAdmin', $isAdmin);

$xoopsTpl->assign(array('lang_on' => _MD_SF_ON, 'lang_postedby' => _MD_SF_POSTEDBY, 'lang_faq' => _MD_SF_QUESTION, 'lang_datesub' => _MD_SF_DATESUB, 'lang_hits' => _MD_SF_HITS));
$xoopsTpl->assign('sectionname', $myts->displayTarea($xoopsModule->getVar('name')));

$xoopsTpl->assign('modulename', $xoopsModule->dirname());
$xoopsTpl->assign('displaylastfaq', $xoopsModuleConfig['displaylastfaq']);
$xoopsTpl->assign('displaysubcatdsc', $xoopsModuleConfig['displaysubcatdsc']);
$xoopsTpl->assign('displaycollaps', $xoopsModuleConfig['displaycollaps']);
$xoopsTpl->assign('display_date_col', $xoopsModuleConfig['display_date_col']);
$xoopsTpl->assign('display_hits_col', $xoopsModuleConfig['display_hits_col']);

$xoopsTpl->assign('displaytopcatdsc', $xoopsModuleConfig['displaytopcatdsc']);

$xoopsTpl->assign('ref_smartfaq', 'SmartFAQ is developed by The SmartFactory (http://www.smartfactory.ca), a division of InBox Solutions (http://www.inboxsolutions.net)');

$xoopsTpl->assign('xoops_module_header', "<link rel='stylesheet' type='text/css' href='" . XOOPS_URL . "/modules/smartfaq/assets/css/smartfaq.css'/>");
