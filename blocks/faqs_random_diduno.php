<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */
// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

function b_faqs_random_diduno_show()
{
    include_once(XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php');

    $block = array();

    // Creating the faq handler object
    $faqHandler = sf_gethandler('faq');

    // creating the FAQ object
    $faqsObj = $faqHandler->getRandomFaq('diduno', array(_SF_STATUS_PUBLISHED, _SF_STATUS_NEW_ANSWER));

    if ($faqsObj) {
        $block['content']     = $faqsObj->diduno();
        $block['id']          = $faqsObj->faqid();
        $block['lang_answer'] = _MB_SF_MOREDETAILS;
    }

    return $block;
}
