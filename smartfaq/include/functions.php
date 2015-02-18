<?php

/**
* $Id: functions.php,v 1.37 2006/09/29 18:49:10 malanciault Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/
// defined("XOOPS_ROOT_PATH") || exit("XOOPS root path not defined");

include_once XOOPS_ROOT_PATH.'/modules/smartfaq/class/category.php';
include_once XOOPS_ROOT_PATH.'/modules/smartfaq/class/faq.php';
include_once XOOPS_ROOT_PATH.'/modules/smartfaq/class/answer.php';

function &sf_getModuleInfo()
{
    static $smartModule;
    if (!isset($smartModule)) {
        global $xoopsModule;
        if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') == 'smartfaq') {
            $smartModule =& $xoopsModule;
        } else {
            $hModule = &xoops_gethandler('module');
            $smartModule = $hModule->getByDirname('smartfaq');
        }
    }

    return $smartModule;
}

function &sf_getModuleConfig()
{
    static $smartConfig;
    if (!$smartConfig) {
        global $xoopsModule;
        if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') == 'smartfaq') {
            global $xoopsModuleConfig;
            $smartConfig =& $xoopsModuleConfig;
        } else {
            $smartModule =& sf_getModuleInfo();
            $hModConfig = &xoops_gethandler('config');
            $smartConfig = $hModConfig->getConfigsByCat(0, $smartModule->getVar('mid'));
        }
    }

    return $smartConfig;
}

function sf_getHelpPath()
{
    $smartConfig =& sf_getModuleConfig();
    switch ($smartConfig['helppath_select']) {
        case 'docs.xoops.org' :
            return 'http://docs.xoops.org/help/sfaqh/index.htm';
        break;

        case 'inside' :
            return XOOPS_URL . "/modules/smartfaq/doc/";
        break;

        case 'custom' :
            return $smartConfig['helppath_custom'];
        break;
    }
}

function sf_formatErrors($errors=array())
{
    $ret = '';
    foreach ($errors as $key=>$value) {
        $ret .= "<br /> - " . $value;
    }

    return $ret;
}

function sf_addCategoryOption($categoryObj, $selectedid=0, $level = 0, $ret='')
{
    // Creating the category handler object
    $category_handler =& sf_gethandler('category');

    $spaces = '';
    for ($j = 0; $j < $level; ++$j) {
        $spaces .= '--';
    }

    $ret .= "<option value='" . $categoryObj->categoryid() . "'";
    if ($selectedid == $categoryObj->categoryid()) {
        $ret .= " selected='selected'";
    }
    $ret .= ">" . $spaces . $categoryObj->name() . "</option>\n";

    $subCategoriesObj = $category_handler->getCategories(0, 0, $categoryObj->categoryid());
    if (count($subCategoriesObj) > 0) {
        ++$level;
        foreach ($subCategoriesObj as $catID => $subCategoryObj) {
            $ret .= sf_addCategoryOption($subCategoryObj, $selectedid, $level);
        }
    }

    return $ret;
}

function sf_createCategorySelect($selectedid=0, $parentcategory=0, $allCatOption=true)
{
    $ret = "" . _MB_SF_SELECTCAT . "&nbsp;<select name='options[]'>";
    if ($allCatOption) {
        $ret .= "<option value='0'";
        $ret .= ">" . _MB_SF_ALLCAT . "</option>\n";
    }

    // Creating the category handler object
    $category_handler =& sf_gethandler('category');

    // Creating category objects
    $categoriesObj = $category_handler->getCategories(0, 0, $parentcategory);

    if (count($categoriesObj) > 0) {
        foreach ($categoriesObj as $catID => $categoryObj) {
            $ret .= sf_addCategoryOption($categoryObj, $selectedid);
        }
    }
    $ret .= "</select>\n";

    return $ret;
}

function sf_getStatusArray ()
{
    $result = array("1" => _AM_SF_STATUS1,
    "2" => _AM_SF_STATUS2,
    "3" => _AM_SF_STATUS3,
    "4" => _AM_SF_STATUS4,
    "5" => _AM_SF_STATUS5,
    "6" => _AM_SF_STATUS6,
    "7" => _AM_SF_STATUS7,
    "8" => _AM_SF_STATUS8);

    return $result;
}

function sf_moderator ()
{
    global $xoopsUser;

    if (!$xoopsUser) {
        $result = false;
    } else {
        $smartPermHandler =& xoops_getmodulehandler('permission', 'smartfaq');

        $categories = $smartPermHandler->getPermissions('moderation');
        if (count($categories) == 0) {
            $result = false;
        } else {
            $result = true;
        }
    }

    return $result;
}

function sf_modFooter ()
{
    $smartModule =& sf_getModuleInfo();

    $modfootertxt = "Module " . $smartModule->getInfo('name') . " - Version " . $smartModule->getInfo('version') . "";

    $modfooter = "<a href='" . $smartModule->getInfo('support_site_url') . "' target='_blank'><img src='" . XOOPS_URL . "/modules/smartfaq/assets/images/sfcssbutton.gif' title='" . $modfootertxt . "' alt='" . $modfootertxt . "'/></a>";

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

    $smartModule =& sf_getModuleInfo();
    $module_id = $smartModule->getVar('mid');

    if (!empty($xoopsUser)) {
        $groups = $xoopsUser->getGroups();
        $result = (in_array(XOOPS_GROUP_ADMIN, $groups)) || ($xoopsUser->isAdmin($module_id));
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
* @param integer $faqid : faqid on which we are setting permissions
* @param integer $ categoryid : categoryid of the faq
* @return integer : -1 if no access, 0 if partialview and 1 if full access
*/

// TODO : Move this function to sfFaq class
function faqAccessGranted($faqObj)
{
    Global $xoopsUser;

    if (sf_userIsAdmin()) {
        $result = 1;
    } else {
        $result = -1;

        $groups = ($xoopsUser)? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;

        $gperm_handler = &xoops_gethandler('groupperm');
        $smartModule =& sf_getModuleInfo();
        $module_id = $smartModule->getVar('mid');

        // Do we have access to the parent category
        if ($gperm_handler->checkRight('category_read', $faqObj->categoryid(), $groups, $module_id)) {
            // Do we have access to the faq?
            if ($gperm_handler->checkRight('item_read', $faqObj->faqid(), $groups, $module_id)) {
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
* @param array $groups : group with granted permission
* @param integer $categoryid :
* @return boolean : TRUE if the no errors occured
*/
function sf_overrideFaqsPermissions($groups, $categoryid)
{
    Global $xoopsDB;

    $result = true;
    $smartModule =& sf_getModuleInfo();
    $module_id = $smartModule->getVar('mid');

    $gperm_handler = &xoops_gethandler('groupperm');

    $sql = "SELECT faqid FROM " . $xoopsDB->prefix("smartfaq_faq") . " WHERE categoryid = '$categoryid' ";
    $result = $xoopsDB->query($sql);

    if (count($result) > 0) {
        while (list($faqid) = $xoopsDB->fetchrow($result)) {
            // First, if the permissions are already there, delete them
            $gperm_handler->deleteByModule($module_id, 'item_read', $faqid);
            // Save the new permissions
            if (count($groups) > 0) {
                foreach ($groups as $group_id) {
                    $gperm_handler->addRight('item_read', $faqid, $group_id, $module_id);
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
* @param array $groups : group with granted permission
* @param integer $itemID : faqid on which we are setting permissions
* @return boolean : TRUE if the no errors occured

*/
function sf_saveItemPermissions($groups, $itemID)
{
    $result = true;
    $smartModule =& sf_getModuleInfo();
    $module_id = $smartModule->getVar('mid');

    $gperm_handler = &xoops_gethandler('groupperm');
    // First, if the permissions are already there, delete them
    $gperm_handler->deleteByModule($module_id, 'item_read', $itemID);
    // Save the new permissions
    if (count($groups) > 0) {
        foreach ($groups as $group_id) {
            $gperm_handler->addRight('item_read', $itemID, $group_id, $module_id);
        }
    }

    return $result;
}

/**
* Saves permissions for the selected category
*
*   sf_saveCategory_Permissions()
*
* @param array $groups : group with granted permission
* @param integer $categoryid : categoryid on which we are setting permissions
* @param string $perm_name : name of the permission
* @return boolean : TRUE if the no errors occured
*/

function sf_saveCategory_Permissions($groups, $categoryid, $perm_name)
{
    $result = true;
    $smartModule =& sf_getModuleInfo();
    $module_id = $smartModule->getVar('mid');

    $gperm_handler = &xoops_gethandler('groupperm');
    // First, if the permissions are already there, delete them
    $gperm_handler->deleteByModule($module_id, $perm_name, $categoryid);
    // Save the new permissions
    if (count($groups) > 0) {
        foreach ($groups as $group_id) {
            $gperm_handler->addRight($perm_name, $categoryid, $group_id, $module_id);
        }
    }

    return $result;
}

/**
* Saves permissions for the selected category
*
*   sf_saveModerators()
*
* @param array $moderators : moderators uids
* @param integer $categoryid : categoryid on which we are setting permissions
* @return boolean : TRUE if the no errors occured
*/

function sf_saveModerators($moderators, $categoryid)
{
    $result = true;
    $smartModule =& sf_getModuleInfo();
    $module_id = $smartModule->getVar('mid');

    $gperm_handler = &xoops_gethandler('groupperm');
    // First, if the permissions are already there, delete them
    $gperm_handler->deleteByModule($module_id, 'category_moderation', $categoryid);
    // Save the new permissions
    if (count($moderators) > 0) {
        foreach ($moderators as $uid) {
            $gperm_handler->addRight('category_moderation', $categoryid, $uid, $module_id);
        }
    }

    return $result;
}

function sf_retrieveFaqByID($faqid = 0)
{
    $ret = array();
    global $xoopsDB;

    $result = $xoopsDB->query("SELECT * FROM " . $xoopsDB->prefix("smartfaq_faq") . " WHERE faqid = '$faqid'");
    $ret = $xoopsDB->fetcharray($result);

    return $ret;
}

/**
* sf_getAdminLinks()
*
* @param integer $faqid
* @return
*/

// TODO : Move this to the sfFaq class
function sf_getAdminLinks($faqid = 0, $open=false)
{
    global $xoopsUser, $xoopsModule, $xoopsModuleConfig, $xoopsConfig;
    $adminLinks = '';
    $modulePath = XOOPS_URL . "/modules/" . $xoopsModule->dirname() . "/";
    $page = $open? 'question.php' : 'faq.php';
    if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
        // Edit button
        $adminLinks .= "<a href='" . $modulePath . "admin/$page?op=mod&amp;faqid=" . $faqid . "'><img src='" . $modulePath . "assets/images/links/edit.gif'" . " title='" . _MD_SF_EDIT . "' alt='" . _MD_SF_EDIT . "'/></a>";
        $adminLinks .= " ";
        // Delete button
        $adminLinks .= "<a href='" . $modulePath . "admin/$page?op=del&amp;faqid=" . $faqid . "'><img src='" . $modulePath . "assets/images/links/delete.gif'" . " title='" . _MD_SF_DELETE . "' alt='" . _MD_SF_DELETE . "'/></a>";
        $adminLinks .= " ";
    }
    // Print button
    $adminLinks .= "<a href='" . $modulePath . "print.php?faqid=" . $faqid . "'><img src='" . $modulePath . "assets/images/links/print.gif' title='" . _MD_SF_PRINT . "' alt='" . _MD_SF_PRINT . "'/></a>";
    $adminLinks .= " ";
    // Email button
    $maillink = 'mailto:?subject=' . sprintf(_MD_SF_INTARTICLE, $xoopsConfig['sitename']) . '&amp;body=' . sprintf(_MD_SF_INTARTFOUND, $xoopsConfig['sitename']) . ':  ' . $modulePath . 'faq.php?faqid=' . $faqid;
    $adminLinks .= "<a href=\"" . $maillink . "\"><img src='" . $modulePath . "assets/images/links/friend.gif' title='" . _MD_SF_MAIL . "' alt='" . _MD_SF_MAIL . "'/></a>";
    $adminLinks .= " ";
    // Submit New Answer button
    if (($xoopsModuleConfig['allownewanswer']) && (is_object($xoopsUser) || $xoopsModuleConfig['anonpost'])) {
        $adminLinks .= "<a href='" . $modulePath . "answer.php?faqid=" . $faqid . "'><img src='" . $modulePath . "assets/images/links/newanswer.gif' title='" . _MD_SF_SUBMITANSWER . "' alt='" . _MD_SF_SUBMITANSWER . "'/></a>";
        $adminLinks .= " ";
    }

    return $adminLinks;
}

/**
* sf_getLinkedUnameFromId()
*
* @param integer $userid Userid of poster etc
* @param integer $name :  0 Use Usenamer 1 Use realname
* @return
*/
function sf_getLinkedUnameFromId($userid = 0, $name = 0, $users = array())
{
    if (!is_numeric($userid)) {
        return $userid;
    }

    $userid = intval($userid);
    if ($userid > 0) {
        if ($users == array()) {
            //fetching users
            $member_handler = &xoops_gethandler('member');
            $user = &$member_handler->getUser($userid);
        } else {
            if (!isset($users[$userid])) {
                return $GLOBALS['xoopsConfig']['anonymous'];
            }
            $user =& $users[$userid];
        }

        if (is_object($user)) {
            $ts = &MyTextSanitizer::getInstance();
            $username = $user->getVar('uname');
            $fullname = '';

            $fullname2 = $user->getVar('name');

            if (($name) && !empty($fullname2)) {
                $fullname = $user->getVar('name');
            }
            if (!empty($fullname)) {
                $linkeduser = "$fullname [<a href='" . XOOPS_URL . "/userinfo.php?uid=" . $userid . "'>" . $ts->htmlSpecialChars($username) . "</a>]";
            } else {
                $linkeduser = "<a href='" . XOOPS_URL . "/userinfo.php?uid=" . $userid . "'>" . ucwords($ts->htmlSpecialChars($username)) . "</a>";
            }

            return $linkeduser;
        }
    }

    return $GLOBALS['xoopsConfig']['anonymous'];
}

function sf_getxoopslink($url = '')
{
    $xurl = $url;
    if (strlen($xurl) > 0) {
        if ($xurl[0] = '/') {
            $xurl = str_replace('/', '', $xurl);
        }
        $xurl = str_replace('{SITE_URL}', XOOPS_URL, $xurl);
    }
    $xurl = $url;

    return $xurl;
}

function sf_adminMenu ($currentoption = 0, $breadcrumb = '')
{

    /* Nice buttons styles */
    echo "
        <style type='text/css'>
        #buttontop { float:left; width:100%; background: #e7e7e7; font-size:93%; line-height:normal; border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; margin: 0; }
        #buttonbar { float:left; width:100%; background: #e7e7e7 url('" . XOOPS_URL . "/modules/smartfaq/assets/images/bg.gif') repeat-x left bottom; font-size:93%; line-height:normal; border-left: 1px solid black; border-right: 1px solid black; margin-bottom: 12px; }
        #buttonbar ul { margin:0; margin-top: 15px; padding:10px 10px 0; list-style:none; }
        #buttonbar li { display:inline; margin:0; padding:0; }
        #buttonbar a { float:left; background:url('" . XOOPS_URL . "/modules/smartfaq/assets/images/left_both.gif') no-repeat left top; margin:0; padding:0 0 0 9px; border-bottom:1px solid #000; text-decoration:none; }
        #buttonbar a span { float:left; display:block; background:url('" . XOOPS_URL . "/modules/smartfaq/assets/images/right_both.gif') no-repeat right top; padding:5px 15px 4px 6px; font-weight:bold; color:#765; }
        /* Commented Backslash Hack hides rule from IE5-Mac \*/
        #buttonbar a span {float:none;}
        /* End IE5-Mac hack */
        #buttonbar a:hover span { color:#333; }
        #buttonbar #current a { background-position:0 -150px; border-width:0; }
        #buttonbar #current a span { background-position:100% -150px; padding-bottom:5px; color:#333; }
        #buttonbar a:hover { background-position:0% -150px; }
        #buttonbar a:hover span { background-position:100% -150px; }
        </style>
    ";

    // global $xoopsDB, $xoopsModule, $xoopsConfig, $xoopsModuleConfig;
    global $xoopsModule, $xoopsConfig;
    $myts = &MyTextSanitizer::getInstance();

    $tblColors = Array();
    $tblColors[0] = $tblColors[1] = $tblColors[2] = $tblColors[3] = $tblColors[4] = $tblColors[5] = $tblColors[6] = $tblColors[7] = $tblColors[8] = '';
    $tblColors[$currentoption] = 'current';
    if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/' . $xoopsConfig['language'] . '/modinfo.php')) {
        include_once XOOPS_ROOT_PATH . '/modules/smartfaq/language/' . $xoopsConfig['language'] . '/modinfo.php';
    } else {
        include_once XOOPS_ROOT_PATH . '/modules/smartfaq/english/modinfo.php';
    }

    echo "<div id='buttontop'>";
    echo "<table style=\"width: 100%; padding: 0; \" cellspacing=\"0\"><tr>";
    //echo "<td style=\"width: 45%; font-size: 10px; text-align: left; color: #2F5376; padding: 0 6px; line-height: 18px;\"><a class=\"nobutton\" href=\"../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $xoopsModule->getVar('mid') . "\">" . _AM_SF_OPTS . "</a> | <a href=\"import.php\">" . _AM_SF_IMPORT . "</a> | <a href=\"../index.php\">" . _AM_SF_GOMOD . "</a> | <a href=\"../help/index.html\" target=\"_blank\">" . _AM_SF_HELP . "</a> | <a href=\"about.php\">" . _AM_SF_ABOUT . "</a></td>";
    echo "<td style='font-size: 10px; text-align: left; color: #2F5376; padding: 0 6px; line-height: 18px;'><a class='nobutton' href='" . XOOPS_URL . "/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $xoopsModule->getVar('mid') . "'>" . _AM_SF_OPTS . "</a> | <a href='" . XOOPS_URL . "/modules/smartfaq/admin/import.php'>" . _AM_SF_IMPORT . "</a> | <a href='" . XOOPS_URL . "/modules/smartfaq/index.php'>" . _AM_SF_GOMOD . "</a> | <a href='" . XOOPS_URL . "/modules/system/admin.php?fct=modulesadmin&op=update&module=" . $xoopsModule->getVar('dirname') . "'>" . _AM_SF_UPDATE . "</a> | <a href='" . sf_getHelpPath() . "' target='_blank'>" . _AM_SF_HELP . "</a> | <a href='" . XOOPS_URL . "/modules/smartfaq/admin/about.php'>" . _AM_SF_ABOUT . "</a></td>";
    echo "<td style='font-size: 10px; text-align: right; color: #2F5376; padding: 0 6px; line-height: 18px;'><b>" . $myts->displayTarea($xoopsModule->name()) . " " . _AM_SF_MODADMIN . "</b> " . $breadcrumb . "</td>";
    echo "</tr></table>";
    echo "</div>";

    echo "<div id='buttonbar'>";
    echo "<ul>";
    echo "<li id='" . $tblColors[0] . "'><a href=\"" . XOOPS_URL . "/modules/smartfaq/admin/index.php\"><span>" . _AM_SF_INDEX . "</span></a></li>";
    echo "<li id='" . $tblColors[1] . "'><a href=\"" . XOOPS_URL . "/modules/smartfaq/admin/category.php\"><span>" . _AM_SF_CATEGORIES . "</span></a></li>";
    echo "<li id='" . $tblColors[2] . "'><a href=\"" . XOOPS_URL . "/modules/smartfaq/admin/faq.php\"><span>" . _AM_SF_SMARTFAQS . "</span></a></li>";
    echo "<li id='" . $tblColors[3] . "'><a href=\"" . XOOPS_URL . "/modules/smartfaq/admin/question.php\"><span>" . _AM_SF_OPEN_QUESTIONS . "</span></a></li>";
    echo "<li id='" . $tblColors[4] . "'><a href=\"" . XOOPS_URL . "/modules/smartfaq/admin/permissions.php\"><span>" . _AM_SF_PERMISSIONS . "</span></a></li>";
    echo "</ul></div>";
}

function sf_collapsableBar($tablename = '', $iconname = '')
{

   ?>
    <script type="text/javascript"><!--
    function goto_URL(object)
    {
        window.location.href = object.options[object.selectedIndex].value;
    }

    function toggle(id)
    {
        if (document.getElementById) { obj = document.getElementById(id); }
        if (document.all) { obj = document.all[id]; }
        if (document.layers) { obj = document.layers[id]; }
        if (obj) {
            if (obj.style.display == "none") {
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

    function toggleIcon ( iconName )
    {
        if (document.images[iconName].src == window.iconOpen.src) {
            document.images[iconName].src = window.iconClose.src;
        } elseif (document.images[iconName].src == window.iconClose.src) {
            document.images[iconName].src = window.iconOpen.src;
        }

        return;
    }

    //-->
    </script>
    <?php
    echo "<h3 style=\"color: #2F5376; margin: 6px 0 0 0; \"><a href='#' onClick=\"toggle('" . $tablename . "'); toggleIcon('" . $iconname . "');\">";
}

function sf_gethandler($name, $optional = false )
{
    static $handlers;
    $name = strtolower(trim($name));
    if (!isset($handlers[$name])) {
        if ( file_exists( $hnd_file = XOOPS_ROOT_PATH.'/modules/smartfaq/class/'.$name.'.php' ) ) {
            require_once $hnd_file;
        }
        $class = 'sf'.ucfirst($name).'Handler';
        if (class_exists($class)) {
            $handlers[$name] = new $class($GLOBALS['xoopsDB']);
        }
    }
    if (!isset($handlers[$name]) && !$optional ) {
        trigger_error('Class <b>'.$class.'</b> does not exist<br />Handler Name: '.$name, E_USER_ERROR);
    }
    $false = false;

    return isset($handlers[$name])? $handlers[$name] : $false;
}
