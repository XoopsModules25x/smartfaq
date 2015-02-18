<?php

/**
* $Id: notification.inc.php,v 1.6 2004/11/20 16:52:33 malanciault Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

function smartfaq_notify_iteminfo($category, $item_id)
{
    global $xoopsModule, $xoopsModuleConfig, $xoopsConfig;

    if (empty($xoopsModule) || $xoopsModule->getVar('dirname') != 'smartfaq') {
        $module_handler = &xoops_gethandler('module');
        $module = &$module_handler->getByDirname('smartfaq');
        $config_handler = &xoops_gethandler('config');
        $config = &$config_handler->getConfigsByCat(0, $module->getVar('mid'));
    } else {
        $module = &$xoopsModule;
        $config = &$xoopsModuleConfig;
    }

    if ($category == 'global') {
        $item['name'] = '';
        $item['url'] = '';

        return $item;
    }

    global $xoopsDB;

    if ($category == 'category') {
        // Assume we have a valid category id
        $sql = 'SELECT name FROM ' . $xoopsDB->prefix('smartfaq_categories') . ' WHERE categoryid  = ' . $item_id;
        $result = $xoopsDB->query($sql); // TODO: error check
        $result_array = $xoopsDB->fetchArray($result);
        $item['name'] = $result_array['name'];
        $item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/category.php?categoryid=' . $item_id;

        return $item;
    }

    if ($category == 'faq') {
        // Assume we have a valid story id
        $sql = 'SELECT question FROM ' . $xoopsDB->prefix('smartfaq_faq') . ' WHERE faqid = ' . $item_id;
        $result = $xoopsDB->query($sql); // TODO: error check
        $result_array = $xoopsDB->fetchArray($result);
        $item['name'] = $result_array['question'];
        $item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/faq.php?faqid=' . $item_id;

        return $item;
    }
}
