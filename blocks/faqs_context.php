<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 * @param $options
 * @return array
 */
// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

function b_faqs_context_show($options)
{
    include_once(XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php');

    $block = array();

    if ($options[0] == 0) {
        $categoryid = -1;
    } else {
        $categoryid = $options[0];
    }

    $limit = $options[0];

    // Creating the faq handler object
    $faqHandler = sf_gethandler('faq');

    // creating the FAQ objects that belong to the selected category
    $faqsObj   = $faqHandler->getContextualFaqs($limit);
    $totalfaqs = count($faqsObj);

    if ($faqsObj) {
        for ($i = 0; $i < $totalfaqs; ++$i) {
            $faq             = array();
            $faq['id']       = $faqsObj[$i]->faqid();
            $faq['question'] = $faqsObj[$i]->question();
            $block['faqs'][] = $faq;
        }
    }

    return $block;
}

/**
 * @param $options
 * @return string
 */
function b_faqs_context_edit($options)
{
    $form = '' . _MB_SF_DISP . '&nbsp;';
    $form .= "<input type='text' name='options[]' value='" . $options[0] . "' />&nbsp;" . _MB_SF_FAQS . '';

    return $form;
}
