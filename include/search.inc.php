<?php declare(strict_types=1);

use XoopsModules\Smartfaq\Helper;

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 * @param $queryarray
 * @param $andor
 * @param $limit
 * @param $offset
 * @param $userid
 * @return array
 */
function smartfaq_search($queryarray, $andor, $limit, $offset, $userid)
{
    //    require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

    $ret = [];

    /** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
    $faqHandler = Helper::getInstance()->getHandler('Faq');

    $faqsObj = $faqHandler->getFaqsFromSearch($queryarray, $andor, $limit, $offset, $userid);

    for ($i = 0, $iMax = count($faqsObj); $i < $iMax; ++$i) {
        $ret[$i]['image'] = 'assets/images/smartfaq.gif';
        $ret[$i]['link']  = 'faq.php?faqid=' . $faqsObj[$i]->faqid();
        $ret[$i]['title'] = $faqsObj[$i]->question(50);
        $ret[$i]['time']  = $faqsObj[$i]->getVar('datesub');
        $ret[$i]['uid']   = $faqsObj[$i]->uid();
    }

    return $ret;
}
