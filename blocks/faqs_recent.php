<?php declare(strict_types=1);

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 * @param $options
 * @return array
 */

use XoopsModules\Smartfaq;
use XoopsModules\Smartfaq\Helper;

/**
 * @param $options
 * @return array
 */
function b_faqs_recent_show($options)
{
    //    require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';
    $myts = \MyTextSanitizer::getInstance();

    /** @var \XoopsModules\Smartfaq\Helper $helper */
    $helper            = Helper::getInstance();
    $smartModule       = $helper->getModule();
    $smartModuleConfig = $helper->getConfig();

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
    $faqHandler = $helper->getHandler('Faq');

    // Creating the category handler object
    /** @var \XoopsModules\Smartfaq\CategoryHandler $categoryHandler */
    $categoryHandler = $helper->getHandler('Category');

    // Creating the last FAQs
    $faqsObj       = $faqHandler->getAllPublished($limit, 0, $categoryid, $sort);
    $allcategories = $categoryHandler->getObjects(null, true);
    if ($faqsObj) {
        $userids = [];
        foreach ($faqsObj as $key => $thisfaq) {
            $faqids[]                 = $thisfaq->getVar('faqid');
            $userids[$thisfaq->uid()] = 1;
        }
        /** @var \XoopsModules\Smartfaq\AnswerHandler $answerHandler */
        $answerHandler = $helper->getHandler('Answer');
        $allanswers    = $answerHandler->getLastPublishedByFaq($faqids);

        foreach ($allanswers as $key => $thisanswer) {
            $userids[$thisanswer->uid()] = 1;
        }

        /** @var \XoopsMemberHandler $memberHandler */
        $memberHandler = xoops_getHandler('member');
        $users         = $memberHandler->getUsers(new \Criteria('uid', '(' . implode(',', array_keys($userids)) . ')', 'IN'), true);
        foreach ($faqsObj as $iValue) {
            $faqs['categoryid']   = $iValue->categoryid();
            $faqs['question']     = $iValue->question($maxQuestionLength);
            $faqs['faqid']        = $iValue->faqid();
            $faqs['categoryname'] = $allcategories[$iValue->categoryid()]->getVar('name');

            // Creating the answer object
            $faqid     = $iValue->faqid();
            $answerObj = $allanswers[$faqid];

            $faqs['date']   = $iValue->datesub();
            $faqs['poster'] = '';
            if (null !== $answerObj) {
                $faqs['poster'] = Smartfaq\Utility::getLinkedUnameFromId($answerObj->uid(), $smartModuleConfig['userealname'], $users);
            }

            $block['faqs'][] = $faqs;
        }

        $block['lang_question'] = _MB_SF_FAQS;
        $block['lang_category'] = _MB_SF_CATEGORY;
        $block['lang_poster']   = _MB_SF_ANSWEREDBY;
        $block['lang_date']     = _MB_SF_DATE;
        $modulename             = htmlspecialchars($smartModule->getVar('name'), ENT_QUOTES | ENT_HTML5);
        $block['lang_visitfaq'] = _MB_SF_VISITFAQ . ' ' . $modulename;
    }

    return $block;
}

/**
 * @param $options
 * @return string
 */
function b_faqs_recent_edit($options)
{
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

    $form .= '&nbsp;' . _MB_SF_DISP . "&nbsp;<input type='text' name='options[]' value='" . $options[2] . "'>&nbsp;" . _MB_SF_FAQS;
    $form .= '&nbsp;<br>' . _MB_SF_CHARS . "&nbsp;<input type='text' name='options[]' value='" . $options[3] . "'>&nbsp;" . _MB_SF_LENGTH;

    return $form;
}
