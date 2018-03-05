<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');

function b_faqs_random_how_show()
{
    require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

    $block = [];

    // Creating the faq handler object
    /** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
    $faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');

    // creating the FAQ object
    $faqsObj = $faqHandler->getRandomFaq('howdoi', [_SF_STATUS_PUBLISHED, _SF_STATUS_NEW_ANSWER]);

    if ($faqsObj) {
        $block['content']     = $faqsObj->howdoi();
        $block['id']          = $faqsObj->faqid();
        $block['lang_answer'] = _MB_SF_ANSWERHERE;
    }

    return $block;
}
