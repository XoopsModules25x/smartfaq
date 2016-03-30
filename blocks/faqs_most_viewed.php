<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 * @param $options
 * @return array
 */
// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

function b_faqs_most_viewed_show($options)
{
    include_once(XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php');

    $block = array();
    if ($options[0] == 0) {
        $categoryid = -1;
    } else {
        $categoryid = $options[0];
    }
    $sort              = 'counter';
    $limit             = $options[1];
    $maxQuestionLength = $options[2];

    // Creating the faq handler object
    $faqHandler = sf_gethandler('faq');
    // creating the FAQ objects that belong to the selected category
    $faqsObj   = $faqHandler->getAllPublished($limit, 0, $categoryid, $sort);
    $totalfaqs = count($faqsObj);
    if ($faqsObj) {
        for ($i = 0; $i < $totalfaqs; ++$i) {
            $newfaqs             = array();
            $newfaqs['linktext'] = $faqsObj[$i]->question($maxQuestionLength);
            $newfaqs['id']       = $faqsObj[$i]->faqid();
            $newfaqs['new']      = $faqsObj[$i]->counter();
            $block['newfaqs'][]  = $newfaqs;
        }
    }

    return $block;
}

/**
 * @param $options
 * @return string
 */
function b_faqs_most_viewed_edit($options)
{
    global $xoopsDB, $xoopsModule, $xoopsUser;
    include_once(XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php');

    $form = sf_createCategorySelect($options[0]);

    $form .= '&nbsp;<br />' . _MB_SF_DISP . "&nbsp;<input type='text' name='options[]' value='" . $options[1] . "' />&nbsp;" . _MB_SF_FAQS . '';
    $form .= '&nbsp;<br />' . _MB_SF_CHARS . "&nbsp;<input type='text' name='options[]' value='" . $options[2] . "' />&nbsp;" . _MB_SF_LENGTH . '';

    return $form;
}
