<?php

/**
* $Id: search.inc.php,v 1.9 2005/08/16 15:39:46 fx2024 Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/
// defined("XOOPS_ROOT_PATH") || exit("XOOPS root path not defined");

function smartfaq_search($queryarray, $andor, $limit, $offset, $userid)
{
    include_once XOOPS_ROOT_PATH.'/modules/smartfaq/include/functions.php';

    $ret = array();

    $faq_handler =& sf_gethandler('faq');

    $faqsObj =& $faq_handler->getFaqsFromSearch($queryarray, $andor, $limit, $offset, $userid);

    for ($i = 0; $i < count($faqsObj); ++$i) {
        $ret[$i]['image'] = "assets/images/smartfaq.gif";
        $ret[$i]['link'] = "faq.php?faqid=" . $faqsObj[$i]->faqid();
        $ret[$i]['title'] = $faqsObj[$i]->question(50);
        $ret[$i]['time'] = $faqsObj[$i]->getVar('datesub');
        $ret[$i]['uid'] = $faqsObj[$i]->uid();
    }

    return $ret;
}
