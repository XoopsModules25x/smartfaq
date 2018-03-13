<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 * @param $options
 * @return array
 */

use XoopsModules\Smartfaq;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * @param $options
 * @return array
 */
function b_faqs_new_show($options)
{
//    require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

    $block = [];

    if (0 == $options[0]) {
        $categoryid = -1;
    } else {
        $categoryid = $options[0];
    }

    $sort              = $options[1];
    $limit             = $options[2];
    $maxQuestionLength = $options[3];

    // Creating the faq handler object
    /** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
    $faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');
    // creating the FAQ objects that belong to the selected category
    $faqsObj   = $faqHandler->getAllPublished($limit, 0, $categoryid, $sort);

    if ($faqsObj) {
        foreach ($faqsObj as $iValue) {
            $newfaqs             = [];
            $newfaqs['linktext'] = $iValue->question($maxQuestionLength);
            $newfaqs['id']       = $iValue->faqid();
            if ('datesub' === $sort) {
                $newfaqs['new'] = $iValue->datesub();
            } elseif ('counter' === $sort) {
                $newfaqs['new'] = $iValue->counter();
            } elseif ('weight' === $sort) {
                $newfaqs['new'] = $iValue->weight();
            }
            $newfaqs['show_date'] = $options[4];
            $block['newfaqs'][]   = $newfaqs;
        }
    }

    return $block;
}

/**
 * @param $options
 * @return string
 */
function b_faqs_new_edit($options)
{
    global $xoopsDB, $xoopsModule, $xoopsUser;
//    require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

    $form = Smartfaq\Utility::createCategorySelect($options[0]);

    $form .= '&nbsp;<br>' . _MB_SF_ORDER . "&nbsp;<select name='options[]'>";

    $form .= "<option value='datesub'";
    if ('datesub' === $options[1]) {
        $form .= ' selected';
    }
    $form .= '>' . _MB_SF_DATE . "</option>\n";

    $form .= "<option value='counter'";
    if ('counter' === $options[1]) {
        $form .= ' selected';
    }
    $form .= '>' . _MB_SF_HITS . "</option>\n";

    $form .= "<option value='weight'";
    if ('weight' === $options[1]) {
        $form .= ' selected';
    }
    $form .= '>' . _MB_SF_WEIGHT . "</option>\n";

    $form .= "</select>\n";

    $form .= '&nbsp;' . _MB_SF_DISP . "&nbsp;<input type='text' name='options[]' value='" . $options[2] . "'>&nbsp;" . _MB_SF_FAQS . '';
    $form .= '&nbsp;<br>' . _MB_SF_CHARS . "&nbsp;<input type='text' name='options[]' value='" . $options[3] . "'>&nbsp;" . _MB_SF_LENGTH . '';

    $form .= '<br>' . _MB_SF_SHOW_DATE . "&nbsp;<input type='radio' id='options[]' name='options[]' value='1'";
    if (1 == $options[4]) {
        $form .= ' checked';
    }
    $form .= '>&nbsp;' . _YES . "<input type='radio' id='options[]' name='options[]' value='0'";
    if (0 == $options[4]) {
        $form .= ' checked';
    }
    $form .= '>&nbsp;' . _NO . '';

    return $form;
}
