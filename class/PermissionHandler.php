<?php namespace XoopsModules\Smartfaq;

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Credits: Mithrandir
 * Licence: GNU
 */

use XoopsModules\Smartfaq;

/**
 * Class PermissionHandler
 * @package XoopsModules\Smartfaq
 */
class PermissionHandler extends \XoopsObjectHandler
{
    /*
    * Returns permissions for a certain type
    *
    * @param string $type "global", "forum" or "topic" (should perhaps have "post" as well - but I don't know)
    * @param int $id id of the item (forum, topic or possibly post) to get permissions for
    *
    * @return array
    */
    /**
     * @param  string $type
     * @param  null   $id
     * @return array
     */
    public function getPermissions($type = 'category', $id = null)
    {
        global $xoopsUser;
        static $permissions;

        if (!isset($permissions[$type]) || (null !== $id && !isset($permissions[$type][$id]))) {
            $smartModule = Smartfaq\Utility::getModuleInfo();
            //Get group permissions handler
            $gpermHandler = xoops_getHandler('groupperm');
            //Get user's groups
            $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : [XOOPS_GROUP_ANONYMOUS];

            switch ($type) {
                case 'category':
                    $gperm_name = 'category_read';
                    break;

                case 'item':
                    $gperm_name = 'item_read';
                    break;

                case 'moderation':
                    $gperm_name = 'category_moderation';
                    $groups     = is_object($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
            }

            //Get all allowed item ids in this module and for this user's groups
            $userpermissions    = $gpermHandler->getItemIds($gperm_name, $groups, $smartModule->getVar('mid'));
            $permissions[$type] = $userpermissions;
        }

        //Return the permission array
        return isset($permissions[$type]) ? $permissions[$type] : [];
    }
}
