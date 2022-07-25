<?php declare(strict_types=1);

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq\Constants;
use XoopsModules\Smartfaq\Helper;

/**
 * @return array
 */
function b_faqs_random_how_show()
{
    //    require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

    $block = [];

    // Creating the faq handler object
    /** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
    $faqHandler = Helper::getInstance()->getHandler('Faq');

    // creating the FAQ object
    $faqsObj = $faqHandler->getRandomFaq('howdoi', [Constants::SF_STATUS_PUBLISHED, Constants::SF_STATUS_NEW_ANSWER]);

    if ($faqsObj) {
        $block['content']     = $faqsObj->howdoi();
        $block['id']          = $faqsObj->faqid();
        $block['lang_answer'] = _MB_SF_ANSWERHERE;
    }

    return $block;
}
