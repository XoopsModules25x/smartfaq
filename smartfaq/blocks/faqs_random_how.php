<?php

/**
* $Id: faqs_random_how.php,v 1.8 2005/08/16 15:39:45 fx2024 Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/
// defined("XOOPS_ROOT_PATH") || exit("XOOPS root path not defined");

function b_faqs_random_how_show()
{
    include_once(XOOPS_ROOT_PATH."/modules/smartfaq/include/functions.php");

    $block = array();

    // Creating the faq handler object
    $faq_handler =& sf_gethandler('faq');

    // creating the FAQ object
    $faqsObj = $faq_handler->getRandomFaq('howdoi', array(_SF_STATUS_PUBLISHED, _SF_STATUS_NEW_ANSWER));

    if ($faqsObj) {
           $block['content'] = $faqsObj->howdoi();
           $block['id'] = $faqsObj->faqid();
           $block['lang_answer'] = _MB_SF_ANSWERHERE;
    }

    return $block;
}
