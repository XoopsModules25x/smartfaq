<?php

/**
* $Id: faqs_context.php,v 1.8 2005/08/16 15:39:45 fx2024 Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/
// defined("XOOPS_ROOT_PATH") || exit("XOOPS root path not defined");

function b_faqs_context_show($options)
{
    include_once(XOOPS_ROOT_PATH."/modules/smartfaq/include/functions.php");

    $block = array();

    if ($options[0] == 0) {
        $categoryid = -1;
    } else {
        $categoryid = $options[0];
    }

    $limit = $options[0];

    // Creating the faq handler object
    $faq_handler =& sf_gethandler('faq');

    // creating the FAQ objects that belong to the selected category
    $faqsObj = $faq_handler->getContextualFaqs($limit);
    $totalfaqs = count($faqsObj);

    if ($faqsObj) {
        for ($i = 0; $i < $totalfaqs; ++$i) {
                $faq = array();
                $faq['id'] = $faqsObj[$i]->faqid();
                $faq['question'] = $faqsObj[$i]->question();
                $block['faqs'][] = $faq;
        }
    }

    return $block;
}
function b_faqs_context_edit($options)
{
    $form = "" . _MB_SF_DISP . "&nbsp;";
    $form .= "<input type='text' name='options[]' value='" . $options[0] . "' />&nbsp;" . _MB_SF_FAQS . "";

    return $form;
}
