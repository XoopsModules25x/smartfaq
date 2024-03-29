<?php declare(strict_types=1);

namespace XoopsModules\Smartfaq;

use XoopsModules\Smartfaq;

/**
 * Class Utility
 */
class Utility extends Common\SysUtility
{
    //--------------- Custom module methods -----------------------------
    /**
     * @return mixed|null
     */
    public static function getModuleInfo()
    {
        static $smartModule;
        if (null === $smartModule) {
            global $xoopsModule;
            if (null !== $xoopsModule && \is_object($xoopsModule) && 'smartfaq' === $xoopsModule->getVar('dirname')) {
                $smartModule = $xoopsModule;
            } else {
                $moduleHandler = \xoops_getHandler('module');
                $smartModule   = $moduleHandler->getByDirname('smartfaq');
            }
        }

        return $smartModule;
    }

    /**
     * @return mixed
     */
    public static function getModuleConfig()
    {
        static $smartConfig;
        if (!$smartConfig) {
            global $xoopsModule;
            if (null !== $xoopsModule && \is_object($xoopsModule) && 'smartfaq' === $xoopsModule->getVar('dirname')) {
                global $xoopsModuleConfig;
                $smartConfig = $xoopsModuleConfig;
            } else {
                $smartModule = self::getModuleInfo();
                /** @var \XoopsConfigHandler $configHandler */
                $configHandler  = \xoops_getHandler('config');
                $smartConfig = $configHandler->getConfigsByCat(0, $smartModule->getVar('mid'));
            }
        }

        return $smartConfig;
    }

    /**
     * @return string
     */
    public static function getHelpPath()
    {
        $smartConfig = self::getModuleConfig();
        switch ($smartConfig['helppath_select']) {
            case 'docs.xoops.org':
                return 'https://docs.xoops.org/help/sfaqh/index.htm';
            case 'inside':
                return XOOPS_URL . '/modules/smartfaq/doc/';
            case 'custom':
                return $smartConfig['helppath_custom'];
        }
    }

    /**
     * @param array $errors
     * @return string
     */
    public static function formatErrors($errors = [])
    {
        $ret = '';
        foreach ($errors as $key => $value) {
            $ret .= '<br> - ' . $value;
        }

        return $ret;
    }

    /**
     * @param         $categoryObj
     * @param int     $selectedid
     * @param int     $level
     * @param string  $ret
     * @return string
     */
    public static function addCategoryOption($categoryObj, $selectedid = 0, $level = 0, $ret = '')
    {
        // Creating the category handler object
        /** @var Smartfaq\CategoryHandler $categoryHandler */
        $categoryHandler = Smartfaq\Helper::getInstance()->getHandler('Category');

        $spaces = '';
        for ($j = 0; $j < $level; ++$j) {
            $spaces .= '--';
        }

        $ret .= "<option value='" . $categoryObj->categoryid() . "'";
        if ($selectedid == $categoryObj->categoryid()) {
            $ret .= ' selected';
        }
        $ret .= '>' . $spaces . $categoryObj->name() . "</option>\n";

        $subCategoriesObj = &$categoryHandler->getCategories(0, 0, $categoryObj->categoryid());
        if (\count($subCategoriesObj) > 0) {
            ++$level;
            foreach ($subCategoriesObj as $catID => $subCategoryObj) {
                $ret .= self::addCategoryOption($subCategoryObj, $selectedid, $level);
            }
        }

        return $ret;
    }

    /**
     * @param int  $selectedid
     * @param int  $parentcategory
     * @param bool $allCatOption
     * @return string
     */
    public static function createCategorySelect($selectedid = 0, $parentcategory = 0, $allCatOption = true)
    {
        $ret = \_MB_SF_SELECTCAT . "&nbsp;<select name='options[]'>";
        if ($allCatOption) {
            $ret .= "<option value='0'";
            $ret .= '>' . \_MB_SF_ALLCAT . "</option>\n";
        }

        // Creating the category handler object
        $categoryHandler = Smartfaq\Helper::getInstance()->getHandler('Category');

        // Creating category objects
        $categoriesObj = $categoryHandler->getCategories(0, 0, $parentcategory);

        if (\count($categoriesObj) > 0) {
            foreach ($categoriesObj as $catID => $categoryObj) {
                $ret .= self::addCategoryOption($categoryObj, $selectedid);
            }
        }
        $ret .= "</select>\n";

        return $ret;
    }

    /**
     * @return array
     */
    public static function getStatusArray()
    {
        $result = [
            1 => \_AM_SF_STATUS1,
            2 => \_AM_SF_STATUS2,
            3 => \_AM_SF_STATUS3,
            4 => \_AM_SF_STATUS4,
            5 => \_AM_SF_STATUS5,
            6 => \_AM_SF_STATUS6,
            7 => \_AM_SF_STATUS7,
            8 => \_AM_SF_STATUS8,
        ];

        return $result;
    }

    /**
     * @return bool
     */
    public static function hasModerator()
    {
        global $xoopsUser;

        if ($xoopsUser) {
            /** @var Smartfaq\PermissionHandler $smartPermHandler */
            $smartPermHandler = Smartfaq\Helper::getInstance()->getHandler('Permission');

            $categories = $smartPermHandler->getPermissions('moderation');
            if (0 == \count($categories)) {
                $result = false;
            } else {
                $result = true;
            }
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * @return string
     */
    public static function modFooter()
    {
        $smartModule = self::getModuleInfo();

        $modfootertxt = 'Module ' . $smartModule->getInfo('name') . ' - Version ' . $smartModule->getInfo('version');

        $modfooter = "<a href='" . $smartModule->getInfo('support_site_url') . "' target='_blank'><img src='" . XOOPS_URL . "/modules/smartfaq/assets/images/sfcssbutton.gif' title='" . $modfootertxt . "' alt='" . $modfootertxt . "'></a>";

        return $modfooter;
    }

    /**
     * Checks if a user is admin of Smartfaq
     *
     * self::userIsAdmin()
     *
     * @return bool array with userids and uname
     */
    public static function userIsAdmin()
    {
        global $xoopsUser;

        $result = false;

        $smartModule = self::getModuleInfo();
        $module_id   = $smartModule->getVar('mid');

        if (!empty($xoopsUser)) {
            $groups = &$xoopsUser->getGroups();
            $result = \in_array(XOOPS_GROUP_ADMIN, $groups, true) || $xoopsUser->isAdmin($module_id);
        }

        return $result;
    }

    /**
     * Checks if a user has access to a selected faq. If no item permissions are
     * set, access permission is denied. The user needs to have necessary category
     * permission as well.
     *
     * self::faqAccessGranted()
     *
     * @param $faqObj
     * @return int -1 if no access, 0 if partialview and 1 if full access
     * @internal param int $faqid faqid on which we are setting permissions
     * @internal param int $categoryid categoryid of the faq
     */

    // TODO : Move this function to Smartfaq\Faq class
    public static function faqAccessGranted($faqObj)
    {
        global $xoopsUser;

        if (self::userIsAdmin()) {
            $result = 1;
        } else {
            $result = -1;

            $groups = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
            /** @var \XoopsGroupPermHandler $grouppermHandler */
            $grouppermHandler = \xoops_getHandler('groupperm');
            $smartModule      = self::getModuleInfo();
            $module_id        = $smartModule->getVar('mid');

            // Do we have access to the parent category
            if ($grouppermHandler->checkRight('category_read', $faqObj->categoryid(), $groups, $module_id)) {
                // Do we have access to the faq?
                if ($grouppermHandler->checkRight('item_read', $faqObj->faqid(), $groups, $module_id)) {
                    $result = 1;
                } else { // No we don't !
                    // Check to see if we have partial view access
                    if (!\is_object($xoopsUser) && $faqObj->partialView()) {
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
     *   self::overrideFaqsPermissions()
     *
     * @param array $groups     group with granted permission
     * @param int   $categoryid
     * @return bool|array TRUE if the no errors occurred
     */
    public static function overrideFaqsPermissions($groups, $categoryid)
    {
        global $xoopsDB;

        $result      = true;
        $smartModule = self::getModuleInfo();
        $module_id   = $smartModule->getVar('mid');

        $grouppermHandler = \xoops_getHandler('groupperm');

        $sql    = 'SELECT faqid FROM ' . $xoopsDB->prefix('smartfaq_faq') . " WHERE categoryid = '$categoryid' ";
        $result = $xoopsDB->queryF($sql);

        if ($GLOBALS['xoopsDB']->getRowsNum($result) > 0) {
            while ([$faqid] = $xoopsDB->fetchRow($result)) {
                // First, if the permissions are already there, delete them
                $grouppermHandler->deleteByModule($module_id, 'item_read', $faqid);
                // Save the new permissions
                if (\count($groups) > 0) {
                    foreach ($groups as $group_id) {
                        $grouppermHandler->addRight('item_read', $faqid, $group_id, $module_id);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Saves permissions for the selected faq
     *
     *   self::saveItemPermissions()
     *
     * @param array $groups group with granted permission
     * @param int   $itemID faqid on which we are setting permissions
     * @return bool TRUE if the no errors occurred
     */
    public static function saveItemPermissions($groups, $itemID)
    {
        $result      = true;
        $smartModule = self::getModuleInfo();
        $module_id   = $smartModule->getVar('mid');

        $grouppermHandler = \xoops_getHandler('groupperm');
        // First, if the permissions are already there, delete them
        $grouppermHandler->deleteByModule($module_id, 'item_read', $itemID);
        // Save the new permissions
        if (\count($groups) > 0) {
            foreach ($groups as $group_id) {
                $grouppermHandler->addRight('item_read', $itemID, $group_id, $module_id);
            }
        }

        return $result;
    }

    /**
     * Saves permissions for the selected category
     *
     *   self::saveCategoryPermissions()
     *
     * @param array  $groups      group with granted permission
     * @param int    $categoryid  categoryid on which we are setting permissions
     * @param string $perm_name   name of the permission
     * @return bool  TRUE if the no errors occurred
     */
    public static function saveCategoryPermissions($groups, $categoryid, $perm_name)
    {
        $result      = true;
        $smartModule = self::getModuleInfo();
        $module_id   = $smartModule->getVar('mid');

        $grouppermHandler = \xoops_getHandler('groupperm');
        // First, if the permissions are already there, delete them
        $grouppermHandler->deleteByModule($module_id, $perm_name, $categoryid);
        // Save the new permissions
        if (\count($groups) > 0) {
            foreach ($groups as $group_id) {
                $grouppermHandler->addRight($perm_name, $categoryid, $group_id, $module_id);
            }
        }

        return $result;
    }

    /**
     * Saves permissions for the selected category
     *
     *   self::saveModerators()
     *
     * @param array $moderators moderators uids
     * @param int   $categoryid categoryid on which we are setting permissions
     * @return bool TRUE if the no errors occurred
     */
    public static function saveModerators($moderators, $categoryid)
    {
        $result      = true;
        $smartModule = self::getModuleInfo();
        $module_id   = $smartModule->getVar('mid');

        $grouppermHandler = \xoops_getHandler('groupperm');
        // First, if the permissions are already there, delete them
        $grouppermHandler->deleteByModule($module_id, 'category_moderation', $categoryid);
        // Save the new permissions
        if (\count($moderators) > 0) {
            foreach ($moderators as $uid) {
                $grouppermHandler->addRight('category_moderation', $categoryid, $uid, $module_id);
            }
        }

        return $result;
    }

    /**
     * @param int $faqid
     * @return array
     */
    public static function retrieveFaqByID($faqid = 0)
    {
        $ret = [];
        global $xoopsDB;

        $result = $xoopsDB->queryF('SELECT * FROM ' . $xoopsDB->prefix('smartfaq_faq') . " WHERE faqid = '$faqid'");
        $ret    = $xoopsDB->fetchArray($result);

        return $ret;
    }

    /**
     * self::getAdminLinks()
     *
     * @param int  $faqid
     * @param bool $open
     * @return string
     */

    // TODO : Move this to the Smartfaq\Faq class
    public static function getAdminLinks($faqid = 0, $open = false)
    {
        global $xoopsUser, $xoopsModule, $xoopsConfig;
        /** @var Smartfaq\Helper $helper */
        $helper = Smartfaq\Helper::getInstance();

        $adminLinks = '';
        $modulePath = XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/';
        $page       = $open ? 'question.php' : 'faq.php';
        if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
            // Edit button
            $adminLinks .= "<a href='" . $modulePath . "admin/$page?op=mod&amp;faqid=" . $faqid . "'><img src='" . $modulePath . "assets/images/links/edit.gif'" . " title='" . \_MD_SF_EDIT . "' alt='" . \_MD_SF_EDIT . "'></a>";
            $adminLinks .= ' ';
            // Delete button
            $adminLinks .= "<a href='" . $modulePath . "admin/$page?op=del&amp;faqid=" . $faqid . "'><img src='" . $modulePath . "assets/images/links/delete.gif'" . " title='" . \_MD_SF_DELETE . "' alt='" . \_MD_SF_DELETE . "'></a>";
            $adminLinks .= ' ';
        }
        // Print button
        $adminLinks .= "<a href='" . $modulePath . 'print.php?faqid=' . $faqid . "'><img src='" . $modulePath . "assets/images/links/print.gif' title='" . \_MD_SF_PRINT . "' alt='" . \_MD_SF_PRINT . "'></a>";
        $adminLinks .= ' ';
        // Email button
        $maillink   = 'mailto:?subject=' . \sprintf(\_MD_SF_INTARTICLE, $xoopsConfig['sitename']) . '&amp;body=' . \sprintf(\_MD_SF_INTARTFOUND, $xoopsConfig['sitename']) . ':  ' . $modulePath . 'faq.php?faqid=' . $faqid;
        $adminLinks .= '<a href="' . $maillink . "\"><img src='" . $modulePath . "assets/images/links/friend.gif' title='" . \_MD_SF_MAIL . "' alt='" . \_MD_SF_MAIL . "'></a>";
        $adminLinks .= ' ';
        // Submit New Answer button
        if ($helper->getConfig('allownewanswer') && (\is_object($xoopsUser) || $helper->getConfig('anonpost'))) {
            $adminLinks .= "<a href='" . $modulePath . 'answer.php?faqid=' . $faqid . "'><img src='" . $modulePath . "assets/images/links/newanswer.gif' title='" . \_MD_SF_SUBMITANSWER . "' alt='" . \_MD_SF_SUBMITANSWER . "'></a>";
            $adminLinks .= ' ';
        }

        return $adminLinks;
    }

    /**
     * self::getLinkedUnameFromId()
     *
     * @param int   $userid Userid of poster etc
     * @param int   $name   :  0 Use Usenamer 1 Use realname
     * @param array $users
     * @return string
     */
    public static function getLinkedUnameFromId($userid = 0, $name = 0, $users = [])
    {
        if (!\is_numeric($userid)) {
            return $userid;
        }

        $userid = (int)$userid;
        if ($userid > 0) {
            if ($users == []) {
                //fetching users
                /** @var \XoopsMemberHandler $memberHandler */
                $memberHandler = \xoops_getHandler('member');
                $user          = $memberHandler->getUser($userid);
            } else {
                if (!isset($users[$userid])) {
                    return $GLOBALS['xoopsConfig']['anonymous'];
                }
                $user = &$users[$userid];
            }

            if (\is_object($user)) {
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
                    $linkeduser = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $userid . "'>" . \ucwords($ts->htmlSpecialChars($username)) . '</a>';
                }

                return $linkeduser;
            }
        }

        return $GLOBALS['xoopsConfig']['anonymous'];
    }

    /**
     * @param string $url
     * @return mixed|string
     */
    public static function getXoopslink($url = '')
    {
        $xurl = $url;
        if ('' !== $xurl) {
            $xurl[0] = '/';
            if ($xurl[0]) {
                $xurl = \str_replace('/', '', $xurl);
            }
            $xurl = \str_replace('{SITE_URL}', XOOPS_URL, $xurl);
        }

        //        $xurl = $url;

        return $xurl;
    }

    /**
     * @param string $tablename
     * @param string $iconname
     */
    public static function collapsableBar($tablename = '', $iconname = ''): void
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
}
