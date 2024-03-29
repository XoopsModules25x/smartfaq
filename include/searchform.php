<?php declare(strict_types=1);

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq;

$categoryID = $categoryID ?? 0;
$type       = isset($type) ? (int)$type : 3;
$term       = isset($term) ? $type : '';

$sform = new \XoopsThemeForm(_MD_WB_SEARCHFORM, 'searchform', 'search.php');
$sform->setExtra('enctype="multipart/form-data"');

$searchtype = new \XoopsFormSelect(_MD_WB_LOOKON, 'type', $type);
$searchtype->addOptionArray([1 => _MD_WB_TERMS, 2 => _MD_WB_DEFINS, 3 => _MD_WB_TERMSDEFS]);
$sform->addElement($searchtype, true);

/** @var Smartfaq\Helper $helper */
$helper = Smartfaq\Helper::getInstance();

if (1 == $helper->getConfig('multicats')) {
    $searchcat = new \XoopsFormSelect(_MD_WB_CATEGORY, 'categoryID', $categoryID);
    $searchcat->addOption('0', _MD_WB_ALLOFTHEM);

    $resultcat = $xoopsDB->queryF('SELECT categoryID, name FROM ' . $xoopsDB->prefix('wbcategories') . ' ORDER BY categoryID');

    while ([$categoryID, $name] = $xoopsDB->fetchRow($resultcat)) {
        $searchcat->addOption('categoryID', "$categoryID : $name");
    }
    $sform->addElement($searchcat, true);
}

$searchterm = new \XoopsFormText(_MD_WB_TERM, 'term', 25, 100, $term);
$sform->addElement($searchterm, true);

$submit_button = new \XoopsFormButton('', 'submit', _MD_WB_SEARCH, 'submit');
$sform->addElement($submit_button);

/*
add this in search.php
require_once __DIR__ . '/include/searchform.php';
$sform->assign($xoopsTpl);

*/
