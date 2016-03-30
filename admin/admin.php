<?php
// 
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                  Copyright (c) 2000-2016 XOOPS.org                        //
//                         <http://xoops.org/>                               //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://xoops.org/, http://jp.xoops.org/ //
// Project: XOOPS Project                                                //
// ------------------------------------------------------------------------- //
// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

if (isset($HTTP_POST_VARS['fct'])) {
    $fct = trim($HTTP_POST_VARS['fct']);
}
if (isset($HTTP_GET_VARS['fct'])) {
    $fct = trim($HTTP_GET_VARS['fct']);
}
if (empty($fct)) {
    $fct = 'preferences';
}
include dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
include XOOPS_ROOT_PATH . '/include/cp_functions.php';

include_once XOOPS_ROOT_PATH . '/kernel/module.php';

$admintest = 0;

if (is_object($xoopsUser)) {
    $xoopsModule = XoopsModule::getByDirname('system');
    if (!$xoopsUser->isAdmin($xoopsModule->mid())) {
        redirect_header(XOOPS_URL . '/user.php', 3, _NOPERM);
    }
    $admintest = 1;
} else {
    redirect_header(XOOPS_URL . '/user.php', 3, _NOPERM);
}

// include system category definitions
include_once XOOPS_ROOT_PATH . '/modules/system/constants.php';
$error = false;
if ($admintest != 0) {
    if (isset($fct) && $fct != '') {
        if (file_exists(XOOPS_ROOT_PATH . '/modules/system/admin/' . $fct . '/xoops_version.php')) {
            include_once(XOOPS_ROOT_PATH . '/modules/system/language/' . $xoopsConfig['language'] . '/admin.php');

            if (file_exists(XOOPS_ROOT_PATH . '/modules/system/language/' . $xoopsConfig['language'] . '/admin/' . $fct . '.php')) {
                include XOOPS_ROOT_PATH . '/modules/system/language/' . $xoopsConfig['language'] . '/admin/' . $fct . '.php';
            } elseif (file_exists(XOOPS_ROOT_PATH . '/modules/system/language/english/admin/' . $fct . '.php')) {
                include XOOPS_ROOT_PATH . '/modules/system/language/english/admin/' . $fct . '.php';
            }
            include XOOPS_ROOT_PATH . '/modules/system/admin/' . $fct . '/xoops_version.php';
            $syspermHandler = xoops_getHandler('groupperm');
            $category       = !empty($modversion['category']) ? (int)$modversion['category'] : 0;
            unset($modversion);
            if ($category > 0) {
                $groups =& $xoopsUser->getGroups();
                if (in_array(XOOPS_GROUP_ADMIN, $groups) || false != $syspermHandler->checkRight('system_admin', $category, $groups, $xoopsModule->getVar('mid'))) {
                    if (file_exists("../include/{$fct}.inc.php")) {
                        include_once "../include/{$fct}.inc.php";
                    } else {
                        $error = true;
                    }
                } else {
                    $error = true;
                }
            } elseif ($fct === 'version') {
                if (file_exists(XOOPS_ROOT_PATH . '/modules/system/admin/version/main.php')) {
                    include_once XOOPS_ROOT_PATH . '/modules/system/admin/version/main.php';
                } else {
                    $error = true;
                }
            } else {
                $error = true;
            }
        } else {
            $error = true;
        }
    } else {
        $error = true;
    }
}

if (false != $error) {
    xoops_cp_header();
    echo '<h4>System Configuration</h4>';
    echo '<table class="outer" cellpadding="4" cellspacing="1">';
    echo '<tr>';
    $groups = $xoopsUser->getGroups();
    $all_ok = false;
    if (!in_array(XOOPS_GROUP_ADMIN, $groups)) {
        $syspermHandler = xoops_getHandler('groupperm');
        $ok_syscats     =& $syspermHandler->getItemIds('system_admin', $groups);
    } else {
        $all_ok = true;
    }
    $admin_dir = XOOPS_ROOT_PATH . '/modules/system/admin';
    $handle    = opendir($admin_dir);
    $counter   = 0;
    $class     = 'even';
    while ($file = readdir($handle)) {
        if (strtolower($file) !== 'cvs' && !preg_match('/[.]/', $file) && is_dir($admin_dir . '/' . $file)) {
            include $admin_dir . '/' . $file . '/xoops_version.php';
            if ($modversion['hasAdmin']) {
                $category = isset($modversion['category']) ? (int)$modversion['category'] : 0;
                if (false != $all_ok || in_array($modversion['category'], $ok_syscats)) {
                    echo "<td class='$class' align='center' valign='bottom' width='19%'>";
                    echo "<a href='" . XOOPS_URL . '/modules/system/admin.php?fct=' . $file . "'><b>" . trim($modversion['name']) . "</b></a>\n";
                    echo '</td>';
                    ++$counter;
                    $class = ($class === 'even') ? 'odd' : 'even';
                }
                if ($counter > 4) {
                    $counter = 0;
                    echo '</tr>';
                    echo '<tr>';
                }
            }
            unset($modversion);
        }
    }
    while ($counter < 5) {
        echo '<td class="' . $class . '">&nbsp;</td>';
        $class = ($class === 'even') ? 'odd' : 'even';
        ++$counter;
    }
    echo '</tr></table>';
    xoops_cp_footer();
}
