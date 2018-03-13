<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq;
use XoopsModules\Smartfaq\Constants;

require_once __DIR__ . '/admin_header.php';
$myts = \MyTextSanitizer::getInstance();

$faqid = isset($_POST['faqid']) ? (int)$_POST['faqid'] : 0;

//$pick = isset($_GET['pick'])? (int)($_GET['pick']) : 0;
//$pick = isset($_POST['pick'])? (int)($_POST['pick']) : $_GET['pick'];

$statussel = isset($_GET['statussel']) ? (int)$_GET['statussel'] : Constants::SF_STATUS_ALL;
$statussel = isset($_POST['statussel']) ? (int)$_POST['statussel'] : $statussel;

$sortsel = isset($_GET['sortsel']) ? $_GET['sortsel'] : 'faqid';
$sortsel = isset($_POST['sortsel']) ? $_POST['sortsel'] : $sortsel;

$ordersel = isset($_GET['ordersel']) ? $_GET['ordersel'] : 'DESC';
$ordersel = isset($_POST['ordersel']) ? $_POST['ordersel'] : $ordersel;

$module_id    = $xoopsModule->getVar('mid');
$gpermHandler = xoops_getHandler('groupperm');
$groups       = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;

function buildTable()
{
    global $xoopsConfig, $xoopsModuleConfig, $xoopsModule;
    echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
    echo '<tr>';
    echo "<th width='40' class='bg3' align='center'><b>" . _AM_SF_FAQID . '</b></td>';
    echo "<th width='20%' class='bg3' align='center'><b>" . _AM_SF_FAQCAT . '</b></td>';
    echo "<th class='bg3' align='center'><b>" . _AM_SF_QUESTION . '</b></td>';

    echo "<th width='90' class='bg3' align='center'><b>" . _AM_SF_ASKED . '</b></td>';
    echo "<th width='90' class='bg3' align='center'><b>" . _AM_SF_ANSWERED . '</b></td>';

    echo "<th width='90' class='bg3' align='center'><b>" . _AM_SF_CREATED . '</b></td>';
    echo "<th width='90' class='bg3' align='center'><b>" . _AM_SF_STATUS . '</b></td>';
    //echo "<td width='30' class='bg3' align='center'><b>" . _AM_SF_ANSWERS . "</b></td>";
    echo "<th width='90' class='bg3' align='center'><b>" . _AM_SF_ACTION . '</b></td>';
    echo '</tr>';
}

// Code for the page
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

// Creating the category handler object
/** @var \XoopsModules\Smartfaq\CategoryHandler $categoryHandler */
$categoryHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Category');

// Creating the FAQ handler object
/** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
$faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');

$startentry = isset($_GET['startentry']) ? (int)$_GET['startentry'] : 0;

$adminObject = \Xmf\Module\Admin::getInstance();
xoops_cp_header();
$adminObject->displayNavigation(basename(__FILE__));
global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModuleConfig, $xoopsModule, $faqid;

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

// -- //
//Smartfaq\Utility::collapsableBar('toptable', 'toptableicon');
//echo "<img onclick='toggle('toptable'); toggleIcon('toptableicon');' id='toptableicon' name='toptableicon' src=" . XOOPS_URL . "/modules/" . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . _AM_SF_INVENTORY . "</h3>";
//echo "<div id='toptable'>";
//echo "<br>";
//echo "<table width='100%' class='outer' cellspacing='1' cellpadding='3' border='0' ><tr>";
//echo "<td class='head'>" . _AM_SF_TOTALCAT . "</td><td align='center' class='even'>" . $totalcategories . "</td>";
//echo "<td class='head'>" . _AM_SF_TOTALASKED . "</td><td align='center' class='even'>" . $totalasked . "</td>";
//echo "<td class='head'>" . _AM_SF_TOTALOPENED . "</td><td align='center' class='even'>" . $totalopened . "</td>";
//echo "<td class='head'>" . _AM_SF_TOTALSUBMITTED . "</td><td align='center' class='even'>" . $totalsubmitted . "</td>";
//echo "<td class='head'>" . _AM_SF_TOTALPUBLISHED . "</td><td align='center' class='even'>" . $totalpublished . "</td>";
//echo "<td class='head'>" . _AM_SF_TOTALNEWANSWERS . "</td><td align='center' class='even'>" . $totalnewanswers . "</td>";
//echo "</tr></table>";
//echo "<br>";

//$adminObject  = \Xmf\Module\Admin::getInstance();
$adminObject->addItemButton(_AM_SF_CATEGORY_CREATE, 'category.php?op=mod', 'add', '');
$adminObject->addItemButton(_AM_SF_CREATEART, 'faq.php?op=mod', 'add', '');
$adminObject->addItemButton(_AM_SF_CREATEQUESTION, 'question.php?op=mod', 'add', '');
$adminObject->displayButton('left', '');

//echo "<form><div style=\"margin-bottom: 24px;\">";
//echo "<input type='button' name='button' onclick=\"location='category.php?op=mod'\" value='" . _AM_SF_CATEGORY_CREATE . "'>&nbsp;&nbsp;";
//echo "<input type='button' name='button' onclick=\"location='faq.php?op=mod'\" value='" . _AM_SF_CREATEART . "'>&nbsp;&nbsp;";
//echo "<input type='button' name='button' onclick=\"location='question.php?op=mod'\" value='" . _AM_SF_CREATEQUESTION . "'>&nbsp;&nbsp;";
//echo "</div></form>";
//echo "</div>";

// Construction of lower table
Smartfaq\Utility::collapsableBar('bottomtable', 'bottomtableicon');
echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . _AM_SF_ALLFAQS . '</h3>';
echo "<div id='bottomtable'>";
echo '<span style="color: #567; margin: 3px 0 18px 0; font-size: small; display: block; ">' . _AM_SF_ALLFAQSMSG . '</span>';

$showingtxt    = '';
$selectedtxt   = '';
$cond          = '';
$selectedtxt0  = '';
$selectedtxt1  = '';
$selectedtxt2  = '';
$selectedtxt3  = '';
$selectedtxt4  = '';
$selectedtxt5  = '';
$selectedtxt6  = '';
$selectedtxt7  = '';
$selectedtxt8  = '';
$selectedtxt9  = '';
$selectedtxt10 = '';

$sorttxtquestion = '';
$sorttxtcategory = '';
$sorttxtcreated  = '';
$sorttxtweight   = '';
$sorttxtfaqid    = '';

$ordertxtasc  = '';
$ordertxtdesc = '';

switch ($sortsel) {
    case 'faq.question':
        $sorttxtquestion = 'selected';
        break;

    case 'category.name':
        $sorttxtcategory = 'selected';
        break;

    case 'faq.datesub':
        $sorttxtcreated = 'selected';
        break;

    case 'faq.weight':
        $sorttxtweight = 'selected';
        break;

    default:
        $sorttxtfaqid = 'selected';
        break;
}

switch ($ordersel) {
    case 'ASC':
        $ordertxtasc = 'selected';
        break;

    default:
        $ordertxtdesc = 'selected';
        break;
}

switch ($statussel) {
    case Constants::SF_STATUS_ALL:
        $selectedtxt0        = 'selected';
        $caption             = _AM_SF_ALL;
        $cond                = '';
        $status_explaination = _AM_SF_ALL_EXP;
        break;

    case Constants::SF_STATUS_ASKED:
        $selectedtxt1        = 'selected';
        $caption             = _AM_SF_ASKED;
        $cond                = ' WHERE status = ' . Constants::SF_STATUS_ASKED . ' ';
        $status_explaination = _AM_SF_ASKED_EXP;
        break;

    case Constants::SF_STATUS_OPENED:
        $selectedtxt2        = 'selected';
        $caption             = _AM_SF_OPENED;
        $cond                = ' WHERE status = ' . Constants::SF_STATUS_OPENED . ' ';
        $status_explaination = _AM_SF_OPENED_EXP;
        break;

    case Constants::SF_STATUS_ANSWERED:
        $selectedtxt3        = 'selected';
        $caption             = _AM_SF_ANSWERED;
        $cond                = ' WHERE status = ' . Constants::SF_STATUS_ANSWERED . ' ';
        $status_explaination = _AM_SF_ANSWERED_EXP;
        break;

    case Constants::SF_STATUS_SUBMITTED:
        $selectedtxt4        = 'selected';
        $caption             = _AM_SF_SUBMITTED;
        $cond                = ' WHERE status = ' . Constants::SF_STATUS_SUBMITTED . ' ';
        $status_explaination = _AM_SF_SUBMITTED_EXP;
        break;

    case Constants::SF_STATUS_PUBLISHED:
        $selectedtxt5        = 'selected';
        $caption             = _AM_SF_PUBLISHED;
        $cond                = ' WHERE status = ' . Constants::SF_STATUS_PUBLISHED . ' ';
        $status_explaination = _AM_SF_PUBLISHED_EXP;
        break;

    case Constants::SF_STATUS_NEW_ANSWER:
        $selectedtxt6        = 'selected';
        $caption             = _AM_SF_NEW_ANSWER;
        $cond                = ' WHERE status = ' . Constants::SF_STATUS_NEW_ANSWER . ' ';
        $status_explaination = _AM_SF_NEW_ANSWER_EXP;
        break;

    case Constants::SF_STATUS_OFFLINE:
        $selectedtxt7        = 'selected';
        $caption             = _AM_SF_OFFLINE;
        $cond                = ' WHERE status = ' . Constants::SF_STATUS_OFFLINE . ' ';
        $status_explaination = _AM_SF_OFFLINE_EXP;
        break;

    case Constants::SF_STATUS_REJECTED_QUESTION:
        $selectedtxt8        = 'selected';
        $caption             = _AM_SF_REJECTED_QUESTION;
        $cond                = ' WHERE status = ' . Constants::SF_STATUS_REJECTED_QUESTION . ' ';
        $status_explaination = _AM_SF_REJECTED_QUESTION_EXP;
        break;

    case Constants::SF_STATUS_REJECTED_SMARTFAQ:
        $selectedtxt9        = 'selected';
        $caption             = _AM_SF_REJECTED_SMARTFAQ;
        $cond                = ' WHERE status = ' . Constants::SF_STATUS_REJECTED_SMARTFAQ . ' ';
        $status_explaination = _AM_SF_REJECTED_SMARTFAQ_EXP;
        break;
}

/* -- Code to show selected terms -- */
echo "<form name='pick' id='pick' action='" . $_SERVER['PHP_SELF'] . "' method='POST' style='margin: 0;'>";

echo "
    <table width='100%' cellspacing='1' cellpadding='2' border='0' style='border-left: 1px solid silver; border-top: 1px solid silver; border-right: 1px solid silver;'>
        <tr>
            <td><span style='font-weight: bold; font-size: 12px; font-variant: small-caps;'>" . _AM_SF_SHOWING . ' ' . $caption . "</span></td>
            <td align='right'>" . _AM_SF_SELECT_SORT . "
                <select name='sortsel' onchange='submit()'>
                    <option value='faq.faqid' $sorttxtfaqid>" . _AM_SF_ID . "</option>
                    <option value='category.name' $sorttxtcategory>" . _AM_SF_CATEGORY . "</option>
                    <option value='faq.question' $sorttxtquestion>" . _AM_SF_QUESTION . "</option>
                    <option value='faq.datesub' $sorttxtcreated>" . _AM_SF_CREATED . "</option>
                    <option value='faq.weight' $sorttxtweight>" . _AM_SF_WEIGHT . "</option>
                </select>
                <select name='ordersel' onchange='submit()'>
                    <option value='ASC' $ordertxtasc>" . _AM_SF_ASC . "</option>
                    <option value='DESC' $ordertxtdesc>" . _AM_SF_DESC . '</option>
                </select>
            ' . _AM_SF_SELECT_STATUS . " :
                <select name='statussel' onchange='submit()'>
                    <option value='0' $selectedtxt0>" . _AM_SF_ALL . " [$totalfaqs]</option>
                    <option value='1' $selectedtxt1>" . _AM_SF_ASKED . " [$totalasked]</option>
                    <option value='2' $selectedtxt2>" . _AM_SF_OPENED . " [$totalopened]</option>
                    <option value='3' $selectedtxt3>" . _AM_SF_ANSWERED . " [$totalanswered]</option>
                    <option value='4' $selectedtxt4>" . _AM_SF_SUBMITTED . " [$totalsubmitted]</option>
                    <option value='5' $selectedtxt5>" . _AM_SF_PUBLISHED . " [$totalpublished]</option>
                    <option value='6' $selectedtxt6>" . _AM_SF_NEWANSWER . " [$totalnewanswers]</option>
                    <option value='7' $selectedtxt7>" . _AM_SF_OFFLINE . " [$totaloffline]</option>
                    <option value='8' $selectedtxt8>" . _AM_SF_REJECTED_QUESTION . " [$totalrejectedquestion]</option>
                    <option value='9' $selectedtxt9>" . _AM_SF_REJECTED_SMARTFAQ . " [$totalrejectedsmartfaq]</option>
                </select>
            </td>
        </tr>
    </table>
    </form>";

// Get number of entries in the selected state
$numrows        = (0 == $statussel) ? $totalfaqs : $totalfaqbystatus[$statussel];
$statusSelected = (0 == $statussel) ? Constants::SF_STATUS_ALL : $statussel;

// creating the Q&As objects
$faqsObj = $faqHandler->getFaqsAdminSide($xoopsModuleConfig['perpage'], $startentry, $statusSelected, -1, $sortsel, $ordersel);

// fetching all categories
$allCats          = $categoryHandler->getObjects(null, true);
//$totalItemsOnPage = count($faqsObj);
buildTable();

if ($numrows > 0) {

    //$answer_criteria = new \Criteria('faqid', "(".implode(',', array_keys($faqsObj)).")", 'IN');
    //$answer_criteria->setGroupby("faqid");
    //$answerHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Answer');
    //$answer_arr = $answerHandler->getCountByFAQ($answer_criteria);

    foreach (array_keys($faqsObj) as $i) {
        // Creating the category object to which this faq is linked
        $categoryObj = $allCats[$faqsObj[$i]->categoryid()];
        global $pathIcon16, $smartModuleConfig;

        //$answers = $answer_arr[$i];

        $approve = '';

        switch ($faqsObj[$i]->status()) {
            case Constants::SF_STATUS_ASKED:
                $statustxt = _AM_SF_ASKED;
                $approve   = "<a href='question.php?op=mod&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/on.png' . "'  title='" . _AM_SF_QUESTION_MODERATE . "'  alt='" . _AM_SF_QUESTION_MODERATE . "'></a>&nbsp;";
                $modify    = '';
                $delete    = "<a href='question.php?op=del&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . _AM_SF_DELETEQUESTION . "' alt='" . _AM_SF_DELETEQUESTION . "'></a>";
                break;

            case Constants::SF_STATUS_OPENED:
                $statustxt = _AM_SF_OPENED;
                $approve   = '';
                $modify    = "<a href='question.php?op=mod&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . _AM_SF_QUESTION_EDIT . "' alt='" . _AM_SF_QUESTION_EDIT . "'></a>&nbsp;";
                $delete    = "<a href='question.php?op=del&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . _AM_SF_DELETEQUESTION . "' alt='" . _AM_SF_DELETEQUESTION . "'></a>";
                break;

            case Constants::SF_STATUS_ANSWERED:
                $statustxt = _AM_SF_ANSWERED;
                $approve   = "<a href='answer.php?op=mod&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/approve.gif' title='" . _AM_SF_ANSWERED_MODERATE . "' alt='" . _AM_SF_ANSWERED_MODERATE . "'></a>&nbsp;";
                $modify    = '';
                $delete    = "<a href='question.php?op=del&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/delete.gif' title='" . _AM_SF_DELETEQUESTION . "' alt='" . _AM_SF_DELETEQUESTION . "'></a>";
                break;

            case Constants::SF_STATUS_SUBMITTED:
                $statustxt = _AM_SF_SUBMITTED;
                $approve   = "<a href='faq.php?op=mod&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/approve.gif' title='" . _AM_SF_SUBMISSION_MODERATE . "' alt='" . _AM_SF_SUBMISSION_MODERATE . "'></a>&nbsp;";
                $delete    = "<a href='faq.php?op=del&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/delete.gif' title='" . _AM_SF_DELETEART . "' alt='" . _AM_SF_DELETEART . "'></a>";
                $modify    = '';
                break;

            case Constants::SF_STATUS_PUBLISHED:
                $statustxt = _AM_SF_PUBLISHED;
                $approve   = '';
                $modify    = "<a href='faq.php?op=mod&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . _AM_SF_FAQ_EDIT . "' alt='" . _AM_SF_FAQ_EDIT . "'></a>&nbsp;";
                $delete    = "<a href='faq.php?op=del&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . _AM_SF_DELETEART . "' alt='" . _AM_SF_DELETEART . "'></a>";
                break;

            case Constants::SF_STATUS_NEW_ANSWER:
                $statustxt = _AM_SF_NEWANSWER;
                $approve   = "<a href='answer.php?op=mod&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/approve.gif' title='" . _AM_SF_FAQ_EDIT . "' alt='" . _AM_SF_FAQ_EDIT . "'></a>&nbsp;";
                $delete    = "<a href='faq.php?op=del&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/delete.gif' title='" . _AM_SF_DELETEART . "' alt='" . _AM_SF_DELETEART . "'></a>";
                $modify    = '';
                break;

            case Constants::SF_STATUS_OFFLINE:
                $statustxt = _AM_SF_OFFLINE;
                $approve   = '';
                $modify    = "<a href='faq.php?op=mod&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . _AM_SF_FAQ_EDIT . "' alt='" . _AM_SF_FAQ_EDIT . "'></a>&nbsp;";
                $delete    = "<a href='faq.php?op=del&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . _AM_SF_DELETEART . "' alt='" . _AM_SF_DELETEART . "'></a>";
                break;

            case Constants::SF_STATUS_REJECTED_QUESTION:
                $statustxt = _AM_SF_REJECTED_QUESTION;
                $approve   = '';
                $modify    = "<a href='faq.php?op=mod&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . _AM_SF_REJECTED_EDIT . "' alt='" . _AM_SF_REJECTED_EDIT . "'></a>&nbsp;";
                $delete    = "<a href='question.php?op=del&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . _AM_SF_DELETEQUESTION . "' alt='" . _AM_SF_DELETEQUESTION . "'></a>";
                break;

            case Constants::SF_STATUS_REJECTED_SMARTFAQ:
                $statustxt = _AM_SF_REJECTED_SMARTFAQ;
                $approve   = '';
                $modify    = "<a href='faq.php?op=mod&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . _AM_SF_REJECTED_EDIT . "' alt='" . _AM_SF_REJECTED_EDIT . "'></a>&nbsp;";
                $delete    = "<a href='faq.php?op=del&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . _AM_SF_DELETEART . "' alt='" . _AM_SF_DELETEART . "'></a>";
                break;

            case 'default':
            default:
                $statustxt = _AM_SF_STATUS0;
                $approve   = '';
                $modify    = '';
                break;
        }

        //$modify = "<a href='faq.php?op=mod&amp;faqid=" . $faqid . "'><img src='" . XOOPS_URL . "/modules/" . $xoopsModule->dirname() . "/assets/images/icon/edit.gif' alt='" . _AM_SF_EDITART . "'></a>&nbsp;";

        echo '<tr>';
        echo "<td class='head' align='center'>" . $faqsObj[$i]->faqid() . '</td>';
        echo "<td class='even' align='left'>" . $categoryObj->name() . '</td>';
        echo "<td class='even' align='left'>" . $faqsObj[$i]->question(100) . '</td>';

        //mb---------------------------------------
        //adding name of the Question Submitter
        $requester = Smartfaq\Utility::getLinkedUnameFromId($faqsObj[$i]->uid(), $smartModuleConfig['userealname']);
        echo "<td class='even' align='center'>" . $requester . '</td>';

        //adding name of the Answer Submitter
        /** @var \XoopsModules\Smartfaq\AnswerHandler $answerHandler */
        $answerHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Answer');

        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('faqid', $faqsObj[$i]->faqid()));
        $criteria->add(new \Criteria('status', true));

        $answerObjects =& $answerHandler->getObjects($criteria, true);

        foreach (array_keys($answerObjects) as $j) {
            $answerObj = $answerObjects[$j];
        }

        if (isset($answerObj->vars['uid']['value'])) {
            $answerSubmitterID = $answerObj->vars['uid']['value'];

            $answerSubmitter = Smartfaq\Utility::getLinkedUnameFromId($answerSubmitterID, $smartModuleConfig['userealname']);
        } else {
            $answerSubmitter = '--------';
        }
        echo "<td class='even' align='center'>" . $answerSubmitter . '</td>';

        //mb---------------------------------------

        echo "<td class='even' align='center'>" . $faqsObj[$i]->datesub('s') . '</td>';
        echo "<td class='even' align='center'>" . $statustxt . '</td>';
        //echo "<td class='even' align='center'>" . $answers . "</td>";
        echo "<td class='even' align='center'> " . $approve . $modify . $delete . '</td>';
        echo '</tr>';
    }
} else {
    // that is, $numrows = 0, there's no entries yet
    echo '<tr>';
    echo "<td class='head' align='center' colspan= '7'>" . _AM_SF_NOFAQSSEL . '</td>';
    echo '</tr>';
}
echo "</table>\n";
echo "<span style=\"color: #567; margin: 3px 0 18px 0; font-size: small; display: block; \">$status_explaination</span>";
$pagenav = new \XoopsPageNav($numrows, $xoopsModuleConfig['perpage'], $startentry, 'startentry', "statussel=$statussel&amp;sortsel=$sortsel&amp;ordersel=$ordersel");

if (1 == $xoopsModuleConfig['useimagenavpage']) {
    echo '<div style="text-align:right; background-color: white; margin: 10px 0;">' . $pagenav->renderImageNav() . '</div>';
} else {
    echo '<div style="text-align:right; background-color: white; margin: 10px 0;">' . $pagenav->renderNav() . '</div>';
}
// ENDs code to show active entries
echo '</div>';
// Close the collapsable div
echo '</div>';
echo '</div>';

require_once __DIR__ . '/admin_footer.php';
