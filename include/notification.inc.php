<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 * @param $category
 * @param $item_id
 * @return mixed
 */

function smartfaq_notify_iteminfo($category, $item_id)
{
    global $xoopsModule, $xoopsModuleConfig, $xoopsConfig;

    if (empty($xoopsModule) || 'smartfaq' !== $xoopsModule->getVar('dirname')) {
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module        = $moduleHandler->getByDirname('smartfaq');
        $configHandler = xoops_getHandler('config');
        $config        = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
    } else {
        $module = $xoopsModule;
        $config = $xoopsModuleConfig;
    }

    if ('global' === $category) {
        $item['name'] = '';
        $item['url']  = '';

        return $item;
    }

    global $xoopsDB;

    if ('category' === $category) {
        // Assume we have a valid category id
        $sql          = 'SELECT name FROM ' . $xoopsDB->prefix('smartfaq_categories') . ' WHERE categoryid  = ' . $item_id;
        $result       = $xoopsDB->queryF($sql); // TODO: error check
        $result_array = $xoopsDB->fetchArray($result);
        $item['name'] = $result_array['name'];
        $item['url']  = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/category.php?categoryid=' . $item_id;

        return $item;
    }

    if ('faq' === $category) {
        // Assume we have a valid story id
        $sql          = 'SELECT question FROM ' . $xoopsDB->prefix('smartfaq_faq') . ' WHERE faqid = ' . $item_id;
        $result       = $xoopsDB->queryF($sql); // TODO: error check
        $result_array = $xoopsDB->fetchArray($result);
        $item['name'] = $result_array['question'];
        $item['url']  = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/faq.php?faqid=' . $item_id;

        return $item;
    }
}
