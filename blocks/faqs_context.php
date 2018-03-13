<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 * @param $options
 * @return array
 */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');

function b_faqs_context_show($options)
{
//    require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

    $block = [];

    if (0 == $options[0]) {
        $categoryid = -1;
    } else {
        $categoryid = $options[0];
    }

    $limit = $options[0];

    // Creating the faq handler object
    /** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
    $faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');

    // creating the FAQ objects that belong to the selected category
    $faqsObj   = $faqHandler->getContextualFaqs($limit);

    if ($faqsObj) {
        foreach ($faqsObj as $iValue) {
            $faq             = [];
            $faq['id']       = $iValue->faqid();
            $faq['question'] = $iValue->question();
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
    $form .= "<input type='text' name='options[]' value='" . $options[0] . "'>&nbsp;" . _MB_SF_FAQS . '';

    return $form;
}
