<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');

//require_once XOOPS_ROOT_PATH . '/modules/smartfaq/class/category.php';
//require_once XOOPS_ROOT_PATH . '/modules/smartfaq/class/faq.php';
//require_once XOOPS_ROOT_PATH . '/modules/smartfaq/class/answer.php';

/**
 * @return mixed|null
 */
function sf_getModuleInfo()
{
    static $smartModule;
    if (null === $smartModule) {
        global $xoopsModule;
        if (null !== $xoopsModule && is_object($xoopsModule) && 'smartfaq' === $xoopsModule->getVar('dirname')) {
            $smartModule = $xoopsModule;
        } else {
            $hModule     = xoops_getHandler('module');
            $smartModule = $hModule->getByDirname('smartfaq');
        }
    }

    return $smartModule;
}

/**
 * @return mixed
 */
function sf_getModuleConfig()
{
    static $smartConfig;
    if (!$smartConfig) {
        global $xoopsModule;
        if (null !== $xoopsModule && is_object($xoopsModule) && 'smartfaq' === $xoopsModule->getVar('dirname')) {
            global $xoopsModuleConfig;
            $smartConfig = $xoopsModuleConfig;
        } else {
            $smartModule = sf_getModuleInfo();
            $hModConfig  = xoops_getHandler('config');
            $smartConfig = $hModConfig->getConfigsByCat(0, $smartModule->getVar('mid'));
        }
    }

    return $smartConfig;
}

/**
 * @return string
 */
function sf_getHelpPath()
{
    $smartConfig = sf_getModuleConfig();
    switch ($smartConfig['helppath_select']) {
        case 'docs.xoops.org':
            return 'http://docs.xoops.org/help/sfaqh/index.htm';
            break;

        case 'inside':
            return XOOPS_URL . '/modules/smartfaq/doc/';
            break;

        case 'custom':
            return $smartConfig['helppath_custom'];
            break;
    }
}

/**
 * @param  array $errors
 * @return string
 */
function sf_formatErrors($errors = [])
{
    $ret = '';
    foreach ($errors as $key => $value) {
        $ret .= '<br> - ' . $value;
    }

    return $ret;
}

/**
 * @param  XoopsObject $categoryObj
 * @param  int         $selectedid
 * @param  int         $level
 * @param  string      $ret
 * @return string
 */
function sf_addCategoryOption($categoryObj, $selectedid = 0, $level = 0, $ret = '')
{
    // Creating the category handler object
    /** @var \XoopsModules\Smartfaq\CategoryHandler $categoryHandler */
    $categoryHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Category');

    $spaces = '';
    for ($j = 0; $j < $level; ++$j) {
        $spaces .= '--';
    }

    $ret .= "<option value='" . $categoryObj->categoryid() . "'";
    if ($selectedid == $categoryObj->categoryid()) {
        $ret .= ' selected';
    }
    $ret .= '>' . $spaces . $categoryObj->name() . "</option>\n";

    $subCategoriesObj =& $categoryHandler->getCategories(0, 0, $categoryObj->categoryid());
    if (count($subCategoriesObj) > 0) {
        ++$level;
        foreach ($subCategoriesObj as $catID => $subCategoryObj) {
            $ret .= sf_addCategoryOption($subCategoryObj, $selectedid, $level);
        }
    }

    return $ret;
}

/**
 * @param  int  $selectedid
 * @param  int  $parentcategory
 * @param  bool $allCatOption
 * @return string
 */
function sf_createCategorySelect($selectedid = 0, $parentcategory = 0, $allCatOption = true)
{
    $ret = '' . _MB_SF_SELECTCAT . "&nbsp;<select name='options[]'>";
    if ($allCatOption) {
        $ret .= "<option value='0'";
        $ret .= '>' . _MB_SF_ALLCAT . "</option>\n";
    }

    // Creating the category handler object
    $categoryHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Category');

    // Creating category objects
    $categoriesObj = $categoryHandler->getCategories(0, 0, $parentcategory);

    if (count($categoriesObj) > 0) {
        foreach ($categoriesObj as $catID => $categoryObj) {
            $ret .= sf_addCategoryOption($categoryObj, $selectedid);
        }
    }
    $ret .= "</select>\n";

    return $ret;
}

/**
 * @return array
 */
function sf_getStatusArray()
{
    $result = [
        '1' => _AM_SF_STATUS1,
        '2' => _AM_SF_STATUS2,
        '3' => _AM_SF_STATUS3,
        '4' => _AM_SF_STATUS4,
        '5' => _AM_SF_STATUS5,
        '6' => _AM_SF_STATUS6,
        '7' => _AM_SF_STATUS7,
        '8' => _AM_SF_STATUS8
    ];

    return $result;
}

/**
 * @return bool
 */
function sf_moderator()
{
    global $xoopsUser;

    if (!$xoopsUser) {
        $result = false;
    } else {
        /** @var \XoopsModules\Smartfaq\PermissionHandler $smartPermHandler */
        $smartPermHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Permission');

        $categories = $smartPermHandler->getPermissions('moderation');
        if (0 == count($categories)) {
            $result = false;
        } else {
            $result = true;
        }
    }

    return $result;
}

/**
 * @return string
 */
function sf_modFooter()
{
    $smartModule = sf_getModuleInfo();

    $modfootertxt = 'Module ' . $smartModule->getInfo('name') . ' - Version ' . $smartModule->getInfo('version') . '';

    $modfooter = "<a href='" . $smartModule->getInfo('support_site_url') . "' target='_blank'><img src='" . XOOPS_URL . "/modules/smartfaq/assets/images/sfcssbutton.gif' title='" . $modfootertxt . "' alt='" . $modfootertxt . "'></a>";

    return $modfooter;
}

/**
 * Checks if a user is admin of Smartfaq
 *
 * sf_userIsAdmin()
 *
 * @return boolean : array with userids and uname
 */
function sf_userIsAdmin()
{
    global $xoopsUser;

    $result = false;

    $smartModule = sf_getModuleInfo();
    $module_id   = $smartModule->getVar('mid');

    if (!empty($xoopsUser)) {
        $groups =& $xoopsUser->getGroups();
        $result = in_array(XOOPS_GROUP_ADMIN, $groups) || $xoopsUser->isAdmin($module_id);
    }

    return $result;
}

/**
 * Checks if a user has access to a selected faq. If no item permissions are
 * set, access permission is denied. The user needs to have necessary category
 * permission as well.
 *
 * faqAccessGranted()
 *
 * @param $faqObj
 * @return int : -1 if no access, 0 if partialview and 1 if full access
 * @internal param int $faqid : faqid on which we are setting permissions
 * @internal param $integer $ categoryid : categoryid of the faq
 */

// TODO : Move this function to Smartfaq\Faq class
function faqAccessGranted($faqObj)
{
    global $xoopsUser;

    if (sf_userIsAdmin()) {
        $result = 1;
    } else {
        $result = -1;

        $groups = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;

        $gpermHandler = xoops_getHandler('groupperm');
        $smartModule  = sf_getModuleInfo();
        $module_id    = $smartModule->getVar('mid');

        // Do we have access to the parent category
        if ($gpermHandler->checkRight('category_read', $faqObj->categoryid(), $groups, $module_id)) {
            // Do we have access to the faq?
            if ($gpermHandler->checkRight('item_read', $faqObj->faqid(), $groups, $module_id)) {
                $result = 1;
            } else { // No we don't !
                // Check to see if we have partial view access
                if (!is_object($xoopsUser) && $faqObj->partialView()) {
                    return 0;
                }
            }
        } else { // No we don't !
            $result = false;
        }
    }

    return $result;
}

/**
 * Override FAQs permissions of a category by the category read permissions
 *
 *   sf_overrideFaqsPermissions()
 *
 * @param  array   $groups     : group with granted permission
 * @param  integer $categoryid :
 * @return boolean : TRUE if the no errors occured
 */
function sf_overrideFaqsPermissions($groups, $categoryid)
{
    global $xoopsDB;

    $result      = true;
    $smartModule = sf_getModuleInfo();
    $module_id   = $smartModule->getVar('mid');

    $gpermHandler = xoops_getHandler('groupperm');

    $sql    = 'SELECT faqid FROM ' . $xoopsDB->prefix('smartfaq_faq') . " WHERE categoryid = '$categoryid' ";
    $result = $xoopsDB->queryF($sql);

    if ($GLOBALS['xoopsDB']->getRowsNum($result) > 0) {
        while (list($faqid) = $xoopsDB->fetchRow($result)) {
            // First, if the permissions are already there, delete them
            $gpermHandler->deleteByModule($module_id, 'item_read', $faqid);
            // Save the new permissions
            if (count($groups) > 0) {
                foreach ($groups as $group_id) {
                    $gpermHandler->addRight('item_read', $faqid, $group_id, $module_id);
                }
            }
        }
    }

    return $result;
}

/**
 * Saves permissions for the selected faq
 *
 *   sf_saveItemPermissions()
 *
 * @param  array   $groups : group with granted permission
 * @param  integer $itemID : faqid on which we are setting permissions
 * @return boolean : TRUE if the no errors occured
 */
function sf_saveItemPermissions($groups, $itemID)
{
    $result      = true;
    $smartModule = sf_getModuleInfo();
    $module_id   = $smartModule->getVar('mid');

    $gpermHandler = xoops_getHandler('groupperm');
    // First, if the permissions are already there, delete them
    $gpermHandler->deleteByModule($module_id, 'item_read', $itemID);
    // Save the new permissions
    if (count($groups) > 0) {
        foreach ($groups as $group_id) {
            $gpermHandler->addRight('item_read', $itemID, $group_id, $module_id);
        }
    }

    return $result;
}

/**
 * Saves permissions for the selected category
 *
 *   sf_saveCategory_Permissions()
 *
 * @param  array   $groups     : group with granted permission
 * @param  integer $categoryid : categoryid on which we are setting permissions
 * @param  string  $perm_name  : name of the permission
 * @return boolean : TRUE if the no errors occured
 */

function sf_saveCategory_Permissions($groups, $categoryid, $perm_name)
{
    $result      = true;
    $smartModule = sf_getModuleInfo();
    $module_id   = $smartModule->getVar('mid');

    $gpermHandler = xoops_getHandler('groupperm');
    // First, if the permissions are already there, delete them
    $gpermHandler->deleteByModule($module_id, $perm_name, $categoryid);
    // Save the new permissions
    if (count($groups) > 0) {
        foreach ($groups as $group_id) {
            $gpermHandler->addRight($perm_name, $categoryid, $group_id, $module_id);
        }
    }

    return $result;
}

/**
 * Saves permissions for the selected category
 *
 *   sf_saveModerators()
 *
 * @param  array   $moderators : moderators uids
 * @param  integer $categoryid : categoryid on which we are setting permissions
 * @return boolean : TRUE if the no errors occured
 */

function sf_saveModerators($moderators, $categoryid)
{
    $result      = true;
    $smartModule = sf_getModuleInfo();
    $module_id   = $smartModule->getVar('mid');

    $gpermHandler = xoops_getHandler('groupperm');
    // First, if the permissions are already there, delete them
    $gpermHandler->deleteByModule($module_id, 'category_moderation', $categoryid);
    // Save the new permissions
    if (count($moderators) > 0) {
        foreach ($moderators as $uid) {
            $gpermHandler->addRight('category_moderation', $categoryid, $uid, $module_id);
        }
    }

    return $result;
}

/**
 * @param  int $faqid
 * @return array
 */
function sf_retrieveFaqByID($faqid = 0)
{
    $ret = [];
    global $xoopsDB;

    $result = $xoopsDB->queryF('SELECT * FROM ' . $xoopsDB->prefix('smartfaq_faq') . " WHERE faqid = '$faqid'");
    $ret    = $xoopsDB->fetchArray($result);

    return $ret;
}

/**
 * sf_getAdminLinks()
 *
 * @param  integer $faqid
 * @param  bool    $open
 * @return string
 */

// TODO : Move this to the Smartfaq\Faq class
function sf_getAdminLinks($faqid = 0, $open = false)
{
    global $xoopsUser, $xoopsModule, $xoopsModuleConfig, $xoopsConfig;
    $adminLinks = '';
    $modulePath = XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/';
    $page       = $open ? 'question.php' : 'faq.php';
    if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
        // Edit button
        $adminLinks .= "<a href='" . $modulePath . "admin/$page?op=mod&amp;faqid=" . $faqid . "'><img src='" . $modulePath . "assets/images/links/edit.gif'" . " title='" . _MD_SF_EDIT . "' alt='" . _MD_SF_EDIT . "'></a>";
        $adminLinks .= ' ';
        // Delete button
        $adminLinks .= "<a href='" . $modulePath . "admin/$page?op=del&amp;faqid=" . $faqid . "'><img src='" . $modulePath . "assets/images/links/delete.gif'" . " title='" . _MD_SF_DELETE . "' alt='" . _MD_SF_DELETE . "'></a>";
        $adminLinks .= ' ';
    }
    // Print button
    $adminLinks .= "<a href='" . $modulePath . 'print.php?faqid=' . $faqid . "'><img src='" . $modulePath . "assets/images/links/print.gif' title='" . _MD_SF_PRINT . "' alt='" . _MD_SF_PRINT . "'></a>";
    $adminLinks .= ' ';
    // Email button
    $maillink   = 'mailto:?subject=' . sprintf(_MD_SF_INTARTICLE, $xoopsConfig['sitename']) . '&amp;body=' . sprintf(_MD_SF_INTARTFOUND, $xoopsConfig['sitename']) . ':  ' . $modulePath . 'faq.php?faqid=' . $faqid;
    $adminLinks .= '<a href="' . $maillink . "\"><img src='" . $modulePath . "assets/images/links/friend.gif' title='" . _MD_SF_MAIL . "' alt='" . _MD_SF_MAIL . "'></a>";
    $adminLinks .= ' ';
    // Submit New Answer button
    if ($xoopsModuleConfig['allownewanswer'] && (is_object($xoopsUser) || $xoopsModuleConfig['anonpost'])) {
        $adminLinks .= "<a href='" . $modulePath . 'answer.php?faqid=' . $faqid . "'><img src='" . $modulePath . "assets/images/links/newanswer.gif' title='" . _MD_SF_SUBMITANSWER . "' alt='" . _MD_SF_SUBMITANSWER . "'></a>";
        $adminLinks .= ' ';
    }

    return $adminLinks;
}

/**
 * sf_getLinkedUnameFromId()
 *
 * @param  integer $userid Userid of poster etc
 * @param  integer $name   :  0 Use Usenamer 1 Use realname
 * @param  array   $users
 * @return string
 */
function sf_getLinkedUnameFromId($userid = 0, $name = 0, $users = [])
{
    if (!is_numeric($userid)) {
        return $userid;
    }

    $userid = (int)$userid;
    if ($userid > 0) {
        if ($users == []) {
            //fetching users
            $memberHandler = xoops_getHandler('member');
            $user          = $memberHandler->getUser($userid);
        } else {
            if (!isset($users[$userid])) {
                return $GLOBALS['xoopsConfig']['anonymous'];
            }
            $user =& $users[$userid];
        }

        if (is_object($user)) {
            $ts       = \MyTextSanitizer::getInstance();
            $username = $user->getVar('uname');
            $fullname = '';

            $fullname2 = $user->getVar('name');

            if ($name && !empty($fullname2)) {
                $fullname = $user->getVar('name');
            }
            if (!empty($fullname)) {
                $linkeduser = "$fullname [<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $userid . "'>" . $ts->htmlSpecialChars($username) . '</a>]';
            } else {
                $linkeduser = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $userid . "'>" . ucwords($ts->htmlSpecialChars($username)) . '</a>';
            }

            return $linkeduser;
        }
    }

    return $GLOBALS['xoopsConfig']['anonymous'];
}

/**
 * @param  string $url
 * @return mixed|string
 */
function sf_getxoopslink($url = '')
{
    $xurl = $url;
    if (strlen($xurl) > 0) {
        if ($xurl[0] = '/') {
            $xurl = str_replace('/', '', $xurl);
        }
        $xurl = str_replace('{SITE_URL}', XOOPS_URL, $xurl);
    }

    //    $xurl = $url;

    return $xurl;
}

/**
 * @param string $tablename
 * @param string $iconname
 */
function sf_collapsableBar($tablename = '', $iconname = '')
{
    ?>
    <script type="text/javascript"><!--
        function goto_URL(object) {
            window.location.href = object.options[object.selectedIndex].value;
        }

        function toggle(id) {
            if (document.getElementById) {
                obj = document.getElementById(id);
            }
            if (document.all) {
                obj = document.all[id];
            }
            if (document.layers) {
                obj = document.layers[id];
            }
            if (obj) {
                if (obj.style.display === "none") {
                    obj.style.display = "";
                } else {
                    obj.style.display = "none";
                }
            }

            return false;
        }

        var iconClose = new Image();
        iconClose.src = '../assets/images/icon/close12.gif';
        var iconOpen = new Image();
        iconOpen.src = '../assets/images/icon/open12.gif';

        function toggleIcon(iconName) {
            if (document.images[iconName].src == window.iconOpen.src) {
                document.images[iconName].src = window.iconClose.src;
            }
            elseif(document.images[iconName].src == window.iconClose.src)
            {
                document.images[iconName].src = window.iconOpen.src;
            }

            return;
        }

        //-->
    </script>
    <?php
    echo "<h3 style=\"color: #2F5376; margin: 6px 0 0 0; \"><a href='#' onClick=\"toggle('" . $tablename . "'); toggleIcon('" . $iconname . "');\">";
}

/**
 * @param       $name
 * @param  bool $optional
 * @return bool
 */
function sf_gethandler($name, $optional = false)
{
    static $handlers;
    $name = strtolower(trim($name));
    if (!isset($handlers[$name])) {
        if (file_exists($hnd_file = XOOPS_ROOT_PATH . '/modules/smartfaq/class/' . $name . '.php')) {
            require_once $hnd_file;
        }
        $class = 'sf' . ucfirst($name) . 'Handler';
        if (class_exists($class)) {
            $handlers[$name] = new $class($GLOBALS['xoopsDB']);
        }
    }
    if (!$optional && !isset($handlers[$name])) {
        trigger_error('Class <b>' . $class . '</b> does not exist<br>Handler Name: ' . $name, E_USER_ERROR);
    }
    $false = false;

    return isset($handlers[$name]) ? $handlers[$name] : $false;
}
