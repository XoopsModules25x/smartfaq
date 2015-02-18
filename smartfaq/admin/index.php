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
 * @copyright    The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author     XOOPS Development Team
 * @version    $Id $
 */

require_once dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
include_once __DIR__ . '/admin_header.php';

xoops_cp_header();

    $indexAdmin = new ModuleAdmin();

//----------------------

// Creating the category handler object
$category_handler =& sf_gethandler('category');

// Creating the FAQ handler object
$faq_handler =& sf_gethandler('faq');

// Total FAQs -- includes everything on the table
$totalfaqs = $faq_handler->getFaqsCount();

// Total categories
$totalcategories = $category_handler->getCategoriesCount(-1);

// Total FAQ count by status
$totalfaqbystatus = $faq_handler->getFaqsCountByStatus();

// Total asked FAQs
$totalasked = isset($totalfaqbystatus[_SF_STATUS_ASKED])? $totalfaqbystatus[_SF_STATUS_ASKED] : 0;

// Total opened FAQs
$totalopened = isset($totalfaqbystatus[_SF_STATUS_OPENED])? $totalfaqbystatus[_SF_STATUS_OPENED] : 0;

// Total answered FAQs
$totalanswered = isset($totalfaqbystatus[_SF_STATUS_ANSWERED])? $totalfaqbystatus[_SF_STATUS_ANSWERED] : 0;

// Total submitted FAQs
$totalsubmitted = isset($totalfaqbystatus[_SF_STATUS_SUBMITTED])? $totalfaqbystatus[_SF_STATUS_SUBMITTED] : 0;

// Total published FAQs
$totalpublished = isset($totalfaqbystatus[_SF_STATUS_PUBLISHED])? $totalfaqbystatus[_SF_STATUS_PUBLISHED] : 0;

// Total offline FAQs
$totaloffline = isset($totalfaqbystatus[_SF_STATUS_OFFLINE])? $totalfaqbystatus[_SF_STATUS_OFFLINE] : 0;

// Total rejected question
$totalrejectedquestion = isset($totalfaqbystatus[_SF_STATUS_REJECTED_QUESTION])? $totalfaqbystatus[_SF_STATUS_REJECTED_QUESTION] : 0;

// Total rejected smartfaq
$totalrejectedsmartfaq = isset($totalfaqbystatus[_SF_STATUS_REJECTED_SMARTFAQ])? $totalfaqbystatus[_SF_STATUS_REJECTED_SMARTFAQ] : 0;

// Total Q&A with new answers
$totalnewanswers = isset($totalfaqbystatus[_SF_STATUS_NEW_ANSWER])? $totalfaqbystatus[_SF_STATUS_NEW_ANSWER] : 0;

//set info block
$indexAdmin->addInfoBox(_AM_SF_INVENTORY);

if ($totalcategories > 0) {
    $indexAdmin->addInfoBoxLine(
        _AM_SF_INVENTORY, "<infolabel>" . '<a href="category.php">' . _AM_SF_TOTALCAT . '</a><b>'
        . "</infolabel>", $totalcategories, 'Green'
    );
} else {
    $indexAdmin->addInfoBoxLine(
        _AM_SF_INVENTORY, "<infolabel>" . _AM_SF_TOTALCAT . "</infolabel>", $totalcategories, 'Green'
    );
}
if ($totalasked > 0) {
    $indexAdmin->addInfoBoxLine(
        _AM_SF_INVENTORY, "<infolabel>" . '<a href="main.php">' . _AM_SF_TOTALASKED . '</a><b>'
        . "</infolabel>", $totalasked, 'Green'
    );
} else {
    $indexAdmin->addInfoBoxLine(
        _AM_SF_INVENTORY, "<infolabel>" . _AM_SF_TOTALASKED . "</infolabel>", $totalasked, 'Green'
    );
}
if ($totalopened > 0) {
    $indexAdmin->addInfoBoxLine(
        _AM_SF_INVENTORY, "<infolabel>" . '<a href="question.php">' . _AM_SF_TOTALOPENED . '</a><b>'
        . "</infolabel>", $totalopened, 'Red'
    );
} else {
    $indexAdmin->addInfoBoxLine(
        _AM_SF_INVENTORY, "<infolabel>" . _AM_SF_TOTALOPENED . "</infolabel>", $totalopened, 'Green'
    );
}
if ($totalsubmitted > 0) {
    $indexAdmin->addInfoBoxLine(
        _AM_SF_INVENTORY, "<infolabel>" . '<a href="category.php">' . _AM_SF_TOTALSUBMITTED . '</a><b>'
        . "</infolabel>", $totalsubmitted, 'Green'
    );
} else {
    $indexAdmin->addInfoBoxLine(
        _AM_SF_INVENTORY, "<infolabel>" . _AM_SF_TOTALSUBMITTED . "</infolabel>", $totalsubmitted, 'Green'
    );
}
if ($totalpublished > 0) {
    $indexAdmin->addInfoBoxLine(
        _AM_SF_INVENTORY, "<infolabel>" . '<a href="faq.php">' . _AM_SF_TOTALPUBLISHED . '</a><b>'
        . "</infolabel>", $totalpublished, 'Green'
    );
} else {
    $indexAdmin->addInfoBoxLine(
        _AM_SF_INVENTORY, "<infolabel>" . _AM_SF_TOTALPUBLISHED . "</infolabel>", $totalpublished, 'Green'
    );
}
if ($totalnewanswers > 0) {
    $indexAdmin->addInfoBoxLine(
        _AM_SF_INVENTORY, "<infolabel>" . '<a href="main.php">' . _AM_SF_TOTALNEWANSWERS . '</a><b>'
        . "</infolabel>", $totalnewanswers, 'Red'
    );
} else {
    $indexAdmin->addInfoBoxLine(
        _AM_SF_INVENTORY, "<infolabel>" . _AM_SF_TOTALNEWANSWERS . "</infolabel>", $totalnewanswers, 'Green'
    );
}

//----------------------

    echo $indexAdmin->addNavigation('index.php');
    echo $indexAdmin->renderIndex();

include_once __DIR__ . '/admin_footer.php';
