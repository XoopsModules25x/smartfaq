<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team
 */

use XoopsModules\Smartfaq\Constants;

require_once __DIR__ . '/../../../include/cp_header.php';
require_once __DIR__ . '/admin_header.php';

xoops_cp_header();

$adminObject = \Xmf\Module\Admin::getInstance();

//----------------------

// Creating the category handler object
/** @var \XoopsModules\Smartfaq\CategoryHandler $categoryHandler */
$categoryHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Category');

// Creating the FAQ handler object
/** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
$faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');

// Total FAQs -- includes everything on the table
$totalfaqs = $faqHandler->getFaqsCount();

// Total categories
$totalcategories = $categoryHandler->getCategoriesCount(-1);

// Total FAQ count by status
$totalfaqbystatus = $faqHandler->getFaqsCountByStatus();

// Total asked FAQs
$totalasked = isset($totalfaqbystatus[Constants::SF_STATUS_ASKED]) ? $totalfaqbystatus[Constants::SF_STATUS_ASKED] : 0;

// Total opened FAQs
$totalopened = isset($totalfaqbystatus[Constants::SF_STATUS_OPENED]) ? $totalfaqbystatus[Constants::SF_STATUS_OPENED] : 0;

// Total answered FAQs
$totalanswered = isset($totalfaqbystatus[Constants::SF_STATUS_ANSWERED]) ? $totalfaqbystatus[Constants::SF_STATUS_ANSWERED] : 0;

// Total submitted FAQs
$totalsubmitted = isset($totalfaqbystatus[Constants::SF_STATUS_SUBMITTED]) ? $totalfaqbystatus[Constants::SF_STATUS_SUBMITTED] : 0;

// Total published FAQs
$totalpublished = isset($totalfaqbystatus[Constants::SF_STATUS_PUBLISHED]) ? $totalfaqbystatus[Constants::SF_STATUS_PUBLISHED] : 0;

// Total offline FAQs
$totaloffline = isset($totalfaqbystatus[Constants::SF_STATUS_OFFLINE]) ? $totalfaqbystatus[Constants::SF_STATUS_OFFLINE] : 0;

// Total rejected question
$totalrejectedquestion = isset($totalfaqbystatus[Constants::SF_STATUS_REJECTED_QUESTION]) ? $totalfaqbystatus[Constants::SF_STATUS_REJECTED_QUESTION] : 0;

// Total rejected smartfaq
$totalrejectedsmartfaq = isset($totalfaqbystatus[Constants::SF_STATUS_REJECTED_SMARTFAQ]) ? $totalfaqbystatus[Constants::SF_STATUS_REJECTED_SMARTFAQ] : 0;

// Total Q&A with new answers
$totalnewanswers = isset($totalfaqbystatus[Constants::SF_STATUS_NEW_ANSWER]) ? $totalfaqbystatus[Constants::SF_STATUS_NEW_ANSWER] : 0;

//set info block
$adminObject->addInfoBox(_AM_SF_INVENTORY);

if ($totalcategories > 0) {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . '<a href="category.php">' . _AM_SF_TOTALCAT . '</a><b>' . '</infolabel>', $totalcategories), '', 'Green');
} else {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . _AM_SF_TOTALCAT . '</infolabel>', $totalcategories), '', 'Green');
}
if ($totalasked > 0) {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . '<a href="main.php">' . _AM_SF_TOTALASKED . '</a><b>' . '</infolabel>', $totalasked), '', 'Green');
} else {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . _AM_SF_TOTALASKED . '</infolabel>', $totalasked), '', 'Green');
}
if ($totalopened > 0) {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . '<a href="question.php">' . _AM_SF_TOTALOPENED . '</a><b>' . '</infolabel>', $totalopened), '', 'Red');
} else {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . _AM_SF_TOTALOPENED . '</infolabel>', $totalopened), '', 'Green');
}
if ($totalsubmitted > 0) {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . '<a href="category.php">' . _AM_SF_TOTALSUBMITTED . '</a><b>' . '</infolabel>', $totalsubmitted), '', 'Green');
} else {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . _AM_SF_TOTALSUBMITTED . '</infolabel>', $totalsubmitted), '', 'Green');
}
if ($totalpublished > 0) {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . '<a href="faq.php">' . _AM_SF_TOTALPUBLISHED . '</a><b>' . '</infolabel>', $totalpublished), '', 'Green');
} else {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . _AM_SF_TOTALPUBLISHED . '</infolabel>', $totalpublished), '', 'Green');
}
if ($totalnewanswers > 0) {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . '<a href="main.php">' . _AM_SF_TOTALNEWANSWERS . '</a><b>' . '</infolabel>', $totalnewanswers), '', 'Red');
} else {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . _AM_SF_TOTALNEWANSWERS . '</infolabel>', $totalnewanswers), '', 'Green');
}

//----------------------

$adminObject->displayNavigation(basename(__FILE__));
$adminObject->displayIndex();

require_once __DIR__ . '/admin_footer.php';
