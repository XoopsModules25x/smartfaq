<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq;

require_once __DIR__ . '/admin_header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

if (! Smartfaq\Utility::userIsAdmin()) {
    redirect_header('javascript:history.go(-1)', 1, _NOPERM);
}

$op = '';

foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

foreach ($_GET as $k => $v) {
    ${$k} = $v;
}

switch ($op) {
    case 'default':
    default:
        global $xoopsDB, $xoopsModule;

        $adminObject = \Xmf\Module\Admin::getInstance();
        xoops_cp_header();
        $adminObject->displayNavigation(basename(__FILE__));
        // View Categories permissions
        $item_list_view = [];
        $block_view     = [];
        // echo "<h3 style='color: #2F5376; '>"._AM_SF_PERMISSIONSADMIN."</h3>\n" ;
        Smartfaq\Utility::collapsableBar('toptable', 'toptableicon');

        $result_view = $xoopsDB->query('SELECT categoryid, name FROM ' . $xoopsDB->prefix('smartfaq_categories') . ' ');
        if ($xoopsDB->getRowsNum($result_view)) {
            while (false !== ($myrow_view = $xoopsDB->fetchArray($result_view))) {
                $item_list_view['cid']   = $myrow_view['categoryid'];
                $item_list_view['title'] = $myrow_view['name'];
                $form_view               = new \XoopsGroupPermForm('', $xoopsModule->getVar('mid'), 'category_read', "<img id='toptableicon' src="
                                                                                                                    . XOOPS_URL
                                                                                                                    . '/modules/'
                                                                                                                    . $xoopsModule->dirname()
                                                                                                                    . "/assets/images/icon/close12.gif alt=''></a>&nbsp;"
                                                                                                                    . _AM_SF_PERMISSIONSVIEWMAN
                                                                                                                    . "</h3><div id='toptable'><span style=\"color: #567; margin: 3px 0 0 0; font-size: small; display: block; \">"
                                                                                                                    . _AM_SF_VIEW_CATS
                                                                                                                    . '</span>', 'admin/permissions.php');
                $block_view[]            = $item_list_view;
                foreach ($block_view as $itemlists) {
                    $form_view->addItem($itemlists['cid'], $myts->displayTarea($itemlists['title']));
                }
            }
            echo $form_view->render();
        } else {
            echo "<img id='toptableicon' src="
                 . XOOPS_URL
                 . '/modules/'
                 . $xoopsModule->dirname()
                 . "/assets/images/icon/close12.gif alt=''></a>&nbsp;"
                 . _AM_SF_PERMISSIONSVIEWMAN
                 . "</h3><div id='toptable'><span style=\"color: #567; margin: 3px 0 0 0; font-size: small; display: block; \">"
                 . _AM_SF_NOPERMSSET
                 . '</span>';
        }
        echo '</div>';

        echo "<br>\n";
}

require_once __DIR__ . '/admin_footer.php';
