<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 * @param $options
 * @return array
 */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');

function b_faqs_most_viewed_show($options)
{
    require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

    $block = [];
    if (0 == $options[0]) {
        $categoryid = -1;
    } else {
        $categoryid = $options[0];
    }
    $sort              = 'counter';
    $limit             = $options[1];
    $maxQuestionLength = $options[2];

    // Creating the faq handler object
    /** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
    $faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');
    // creating the FAQ objects that belong to the selected category
    $faqsObj   = $faqHandler->getAllPublished($limit, 0, $categoryid, $sort);
    $totalfaqs = count($faqsObj);
    if ($faqsObj) {
        for ($i = 0; $i < $totalfaqs; ++$i) {
            $newfaqs             = [];
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
    require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

    $form = sf_createCategorySelect($options[0]);

    $form .= '&nbsp;<br>' . _MB_SF_DISP . "&nbsp;<input type='text' name='options[]' value='" . $options[1] . "'>&nbsp;" . _MB_SF_FAQS . '';
    $form .= '&nbsp;<br>' . _MB_SF_CHARS . "&nbsp;<input type='text' name='options[]' value='" . $options[2] . "'>&nbsp;" . _MB_SF_LENGTH . '';

    return $form;
}
