<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq\Constants;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * @return array
 */
function b_faqs_random_diduno_show()
{
//    require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

    $block = [];

    // Creating the faq handler object
    /** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
    $faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');

    // creating the FAQ object
    $faqsObj = $faqHandler->getRandomFaq('diduno', [Constants::SF_STATUS_PUBLISHED, Constants::SF_STATUS_NEW_ANSWER]);

    if ($faqsObj) {
        $block['content']     = $faqsObj->diduno();
        $block['id']          = $faqsObj->faqid();
        $block['lang_answer'] = _MB_SF_MOREDETAILS;
    }

    return $block;
}
