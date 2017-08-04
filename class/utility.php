<?php

/**
 * Class MyalbumUtil
 */
class SmartfaqUtility extends XoopsObject
{
    /**
     * Function responsible for checking if a directory exists, we can also write in and create an index.html file
     *
     * @param string $folder The full path of the directory to check
     *
     * @return void
     */
    public static function createFolder($folder)
    {
        //        try {
        //            if (!mkdir($folder) && !is_dir($folder)) {
        //                throw new \RuntimeException(sprintf('Unable to create the %s directory', $folder));
        //            } else {
        //                file_put_contents($folder . '/index.html', '<script>history.go(-1);</script>');
        //            }
        //        }
        //        catch (Exception $e) {
        //            echo 'Caught exception: ', $e->getMessage(), "\n", '<br>';
        //        }
        try {
            if (!file_exists($folder)) {
                if (!mkdir($folder) && !is_dir($folder)) {
                    throw new \RuntimeException(sprintf('Unable to create the %s directory', $folder));
                } else {
                    file_put_contents($folder . '/index.html', '<script>history.go(-1);</script>');
                }
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n", '<br>';
        }
    }

    /**
     * @param $file
     * @param $folder
     * @return bool
     */
    public static function copyFile($file, $folder)
    {
        return copy($file, $folder);
        //        try {
        //            if (!is_dir($folder)) {
        //                throw new \RuntimeException(sprintf('Unable to copy file as: %s ', $folder));
        //            } else {
        //                return copy($file, $folder);
        //            }
        //        } catch (Exception $e) {
        //            echo 'Caught exception: ', $e->getMessage(), "\n", "<br>";
        //        }
        //        return false;
    }

    /**
     * @param $src
     * @param $dst
     */
    public static function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        //    @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file !== '.') && ($file !== '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     *
     * Verifies XOOPS version meets minimum requirements for this module
     * @static
     * @param XoopsModule $module
     *
     * @param null|string $requiredVer
     * @return bool true if meets requirements, false if not
     */
    public static function checkVerXoops(XoopsModule $module = null, $requiredVer = null)
    {
        $moduleDirName = basename(dirname(__DIR__));
        if (null === $module) {
            $module = XoopsModule::getByDirname($moduleDirName);
        }
        xoops_loadLanguage('admin', $moduleDirName);
        //check for minimum XOOPS version
        $currentVer = substr(XOOPS_VERSION, 6); // get the numeric part of string
        $currArray  = explode('.', $currentVer);
        if (null === $requiredVer) {
            $requiredVer = '' . $module->getInfo('min_xoops'); //making sure it's a string
        }
        $reqArray = explode('.', $requiredVer);
        $success  = true;
        foreach ($reqArray as $k => $v) {
            if (isset($currArray[$k])) {
                if ($currArray[$k] > $v) {
                    break;
                } elseif ($currArray[$k] == $v) {
                    continue;
                } else {
                    $success = false;
                    break;
                }
            } else {
                if ((int)$v > 0) { // handles versions like x.x.x.0_RC2
                    $success = false;
                    break;
                }
            }
        }

        if (!$success) {
            $module->setErrors(sprintf(_AM_SF_ERROR_BAD_XOOPS, $requiredVer, $currentVer));
        }

        return $success;
    }

    /**
     *
     * Verifies PHP version meets minimum requirements for this module
     * @static
     * @param XoopsModule $module
     *
     * @return bool true if meets requirements, false if not
     */
    public static function checkVerPhp(XoopsModule $module)
    {
        xoops_loadLanguage('admin', $module->dirname());
        // check for minimum PHP version
        $success = true;
        $verNum  = PHP_VERSION;
        $reqVer  = $module->getInfo('min_php');
        if (false !== $reqVer && '' !== $reqVer) {
            if (version_compare($verNum, $reqVer, '<')) {
                $module->setErrors(sprintf(_AM_SF_ERROR_BAD_PHP, $reqVer, $verNum));
                $success = false;
            }
        }

        return $success;
    }

    /**
     * @return mixed|null
     */
    public static function sf_getModuleInfo()
    {
        static $smartModule;
        if (!isset($smartModule)) {
            global $xoopsModule;
            if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') === 'smartfaq') {
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
    public static function sf_getModuleConfig()
    {
        static $smartConfig;
        if (!$smartConfig) {
            global $xoopsModule;
            if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') === 'smartfaq') {
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
    public static function sf_getHelpPath()
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
    public static function sf_formatErrors($errors = array())
    {
        $ret = '';
        foreach ($errors as $key => $value) {
            $ret .= '<br> - ' . $value;
        }

        return $ret;
    }

    /**
     * @param         $categoryObj
     * @param  int    $selectedid
     * @param  int    $level
     * @param  string $ret
     * @return string
     */
    public static function sf_addCategoryOption($categoryObj, $selectedid = 0, $level = 0, $ret = '')
    {
        // Creating the category handler object
        $categoryHandler = sf_gethandler('category');

        $spaces = '';
        for ($j = 0; $j < $level; ++$j) {
            $spaces .= '--';
        }

        $ret .= "<option value='" . $categoryObj->categoryid() . "'";
        if ($selectedid == $categoryObj->categoryid()) {
            $ret .= ' selected';
        }
        $ret .= '>' . $spaces . $categoryObj->name() . "</option>\n";

        $subCategoriesObj = $categoryHandler->getCategories(0, 0, $categoryObj->categoryid());
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
    public static function sf_createCategorySelect($selectedid = 0, $parentcategory = 0, $allCatOption = true)
    {
        $ret = '' . _MB_SF_SELECTCAT . "&nbsp;<select name='options[]'>";
        if ($allCatOption) {
            $ret .= "<option value='0'";
            $ret .= '>' . _MB_SF_ALLCAT . "</option>\n";
        }

        // Creating the category handler object
        $categoryHandler = sf_gethandler('category');

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
    public static function sf_getStatusArray()
    {
        $result = array(
            '1' => _AM_SF_STATUS1,
            '2' => _AM_SF_STATUS2,
            '3' => _AM_SF_STATUS3,
            '4' => _AM_SF_STATUS4,
            '5' => _AM_SF_STATUS5,
            '6' => _AM_SF_STATUS6,
            '7' => _AM_SF_STATUS7,
            '8' => _AM_SF_STATUS8
        );

        return $result;
    }

    /**
     * @return bool
     */
    public static function sf_moderator()
    {
        global $xoopsUser;

        if (!$xoopsUser) {
            $result = false;
        } else {
            $smartPermHandler = xoops_getModuleHandler('permission', 'smartfaq');

            $categories = $smartPermHandler->getPermissions('moderation');
            if (count($categories) == 0) {
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
    public static function sf_modFooter()
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
    public static function sf_userIsAdmin()
    {
        global $xoopsUser;

        $result = false;

        $smartModule = sf_getModuleInfo();
        $module_id   = $smartModule->getVar('mid');

        if (!empty($xoopsUser)) {
            $groups = $xoopsUser->getGroups();
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

    // TODO : Move this function to sfFaq class
    public static function faqAccessGranted($faqObj)
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
     * @return boolean|array : TRUE if the no errors occured
     */
    public static function sf_overrideFaqsPermissions($groups, $categoryid)
    {
        global $xoopsDB;

        $result      = true;
        $smartModule = sf_getModuleInfo();
        $module_id   = $smartModule->getVar('mid');

        $gpermHandler = xoops_getHandler('groupperm');

        $sql    = 'SELECT faqid FROM ' . $xoopsDB->prefix('smartfaq_faq') . " WHERE categoryid = '$categoryid' ";
        $result = $xoopsDB->queryF($sql);

        if (count($result) > 0) {
            while (list($faqid) = $xoopsDB->fetchrow($result)) {
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
    public static function sf_saveItemPermissions($groups, $itemID)
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

    public static function sf_saveCategory_Permissions($groups, $categoryid, $perm_name)
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

    public static function sf_saveModerators($moderators, $categoryid)
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
    public static function sf_retrieveFaqByID($faqid = 0)
    {
        $ret = array();
        global $xoopsDB;

        $result = $xoopsDB->queryF('SELECT * FROM ' . $xoopsDB->prefix('smartfaq_faq') . " WHERE faqid = '$faqid'");
        $ret    = $xoopsDB->fetcharray($result);

        return $ret;
    }

    /**
     * sf_getAdminLinks()
     *
     * @param  integer $faqid
     * @param  bool    $open
     * @return string
     */

    // TODO : Move this to the sfFaq class
    public static function sf_getAdminLinks($faqid = 0, $open = false)
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
    public static function sf_getLinkedUnameFromId($userid = 0, $name = 0, $users = array())
    {
        if (!is_numeric($userid)) {
            return $userid;
        }

        $userid = (int)$userid;
        if ($userid > 0) {
            if ($users == array()) {
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
                $ts       = MyTextSanitizer::getInstance();
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
    public static function sf_getxoopslink($url = '')
    {
        $xurl = $url;
        if (strlen($xurl) > 0) {
            if ($xurl[0] = '/') {
                $xurl = str_replace('/', '', $xurl);
            }
            $xurl = str_replace('{SITE_URL}', XOOPS_URL, $xurl);
        }

        //        $xurl = $url;

        return $xurl;
    }

    /**
     * @param string $tablename
     * @param string $iconname
     */
    public static function sf_collapsableBar($tablename = '', $iconname = '')
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
    public static function sf_gethandler($name, $optional = false)
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
}
