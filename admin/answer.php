<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

include_once __DIR__ . '/admin_header.php';

$op = '';

// Getting the operation we are doing
if (isset($_GET['op'])) {
    $op = $_GET['op'];
}
if (isset($_POST['op'])) {
    $op = $_POST['op'];
}

// Creating the answer handler object
$answerHandler = sf_gethandler('answer');

/**
 * @param string $faqid
 */
function editfaq($faqid = '')
{
    global $answerHandler, $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $modify, $xoopsModuleConfig, $xoopsModule, $XOOPS_URL, $myts, $pathIcon16, $smartModuleConfig;

    include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    // Creating the FAQ object
    $faqObj = new sfFaq($faqid);

    // Creating the category object
    $categoryObj = $faqObj->category();

    if ($faqObj->notLoaded()) {
        redirect_header('index.php', 1, _AM_SF_NOFAQSELECTED);
    }

    switch ($faqObj->status()) {

        case _SF_STATUS_ANSWERED:
            $breadcrumb_action1   = _AM_SF_SUBMITTED;
            $breadcrumb_action2   = _AM_SF_APPROVING;
            $collapsableBar_title = _AM_SF_SUBMITTED_TITLE;
            $collapsableBar_info  = _AM_SF_SUBMITTED_INFO;
            $button_caption       = _AM_SF_APPROVE;
            $an_status            = _SF_AN_STATUS_PROPOSED;
            break;

    }

    $module_id    = $xoopsModule->getVar('mid');
    $gpermHandler = xoops_getHandler('groupperm');
    $groups       = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;

    if (!sf_userIsAdmin() && (!$gpermHandler->checkRight('category_admin', $faqObj->categoryid(), $groups, $module_id))) {
        redirect_header('javascript:history.go(-1)', 1, _NOPERM);
    }
    // Retreiving the official answer
    $official_answer = $faqObj->answer();

    sf_collapsableBar('bottomtable', 'bottomtableicon');
    echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt='' /></a>&nbsp;" . _AM_SF_SUBMITTED_ANSWER . '</h3>';
    echo "<div id='bottomtable'>";
    echo "<span style=\"color: #567; margin: 3px 0 12px 0; font-size: small; display: block; \">" . _AM_SF_SUBMITTED_ANSWER_INFO . '</span>';

    $proposed_answers = $answerHandler->getAllAnswers($faqid, _SF_AN_STATUS_PROPOSED);

    if (count($proposed_answers) == 0) {
        redirect_header('index.php', 1, _AM_SF_NOANSWERS);
    }

    echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>
            <tr>
              <td class='head' width='100px'>" . _AM_SF_CATEGORY . "</td>
              <td class='even'>" . $categoryObj->name() . "</td>
            </tr>
            <tr>
              <td class='head' width='100px'>" . _AM_SF_QUESTION . "</td>
              <td class='even'>" . $faqObj->question() . '</td>
            </tr>';
    if ($official_answer) {
        echo "
            <tr>
              <td class='head' width='100px'>" . _AM_SF_ANSWER_OFFICIAL . "</td>
              <td class='even'>" . $official_answer->answer() . '</td>
            </tr>';
    }
    echo "</table><br />\n";

    echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
    echo '<tr>';
    echo "<th width='40' class='bg3' align='center'><b>" . _AM_SF_ARTID . '</b></td>';
    echo "<th class='bg3' class='bg3' align='center'><b>" . _AM_SF_ANSWER . '</b></td>';

    echo "<th width='90' class='bg3' align='center'><b>" . _AM_SF_ANSWERED . '</b></td>';

    echo "<th width='180' class='bg3' align='center'><b>" . _AM_SF_CREATED . '</b></td>';
    echo "<th width='120' class='bg3' align='center'><b>" . _AM_SF_ACTION . '</b></td>';
    echo '</tr>';

    $merge   = '';
    $modify  = '';
    $approve = '';
    foreach ($proposed_answers as $proposed_answer) {
        if ($faqObj->status() == _SF_STATUS_NEW_ANSWER) {
            $merge   = "<a href='faq.php?op=merge&amp;faqid=" . $faqObj->faqid() . '&amp;answerid=' . $proposed_answer->answerid() . "'><img src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/merge.gif' title='" . _AM_SF_FAQ_MERGE . "' alt='" . _AM_SF_FAQ_MERGE . "' /></a>&nbsp;";
            $approve = "<a href='answer.php?op=selectanswer&amp;faqid=" . $faqid . '&amp;answerid=' . $proposed_answer->answerid() . "'><img src='" . $pathIcon16 . '/on.png' . "' title='" . _AM_SF_FAQ_APPROVE_NEW_ANSWER . "' alt='" . _AM_SF_APPROVESUB . "' /></a>";
        }
        $modify = "<a href='faq.php?op=mod&amp;faqid=" . $faqObj->faqid() . '&amp;answerid=' . $proposed_answer->answerid() . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . _AM_SF_FAQ_REVIEW . "' alt='" . _AM_SF_FAQ_REVIEW . "' /></a>&nbsp;";
        $delete = "<a href='answer.php?op=del&amp;faqid=" . $faqObj->faqid() . '&amp;answerid=' . $proposed_answer->answerid() . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . _AM_SF_DELETESUBM . "' alt='" . _AM_SF_DELETESUBM . "' /></a>";
        echo '<tr>';
        echo "<td class='head' align='center'>" . $proposed_answer->answerid() . '</td>';
        echo "<td class='even' align='left'>" . $proposed_answer->answer() . '</td>';

        //show name of the answer submitter
        $submitter = sf_getLinkedUnameFromId($proposed_answer->uid(), $smartModuleConfig['userealname']);
        echo "<td class='even' align='center'>" . $submitter . '</td>';

        echo "<td class='even' align='center'>" . $proposed_answer->datesub() . '</td>';
        echo "<td class='even' align='center'> $merge $modify $approve $delete </td>";
        echo '</tr>';
    }

    echo "</table>\n<br />";
}

/* -- Available operations -- */
switch ($op) {
    case 'mod':
        xoops_cp_header();
        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModuleConfig, $xoopsModule, $modify, $myts;
        $faqid = isset($_GET['faqid']) ? (int)$_GET['faqid'] : 0;
        editfaq($faqid);
        break;

    case 'selectanswer':
        global $xoopsUser, $_GET, $xoopsModuleConfig;

        $faqid    = isset($_GET['faqid']) ? (int)$_GET['faqid'] : 0;
        $answerid = isset($_GET['answerid']) ? (int)$_GET['answerid'] : 0;

        // Creating the FAQ object
        $faqObj = new sfFaq($faqid);

        if ($faqObj->notLoaded()) {
            redirect_header('index.php', 1, _AM_SF_NOFAQSELECTED);
        }

        // Creating the answer object
        $answerObj = new sfAnswer($answerid);

        if ($answerObj->notLoaded()) {
            redirect_header('index.php', 1, _AM_SF_NOFAQSELECTED);
        }

        $answerObj->setVar('status', _SF_AN_STATUS_APPROVED);

        $notifToDo_answer = null;
        $notifToDo_faq    = null;

        switch ($faqObj->status()) {
            // This was an Open Question that became a Submitted FAQ
            case _SF_STATUS_ANSWERED:
                if ($xoopsModuleConfig['autoapprove_submitted_faq'] == 1) {
                    // We automatically approve Submitted Q&A
                    $redirect_msg = _AM_SF_ANSWER_APPROVED_PUBLISHED;
                    $faqObj->setVar('status', _SF_STATUS_PUBLISHED);
                    $answerObj->setVar('status', _SF_AN_STATUS_APPROVED);
                    $notifToDo_faq = array(_SF_NOT_FAQ_PUBLISHED);
                } else {
                    // Submitted Q&A need approbation
                    $redirect_msg = _AM_SF_ANSWER_APPROVED_NEED_APPROVED;
                    $faqObj->setVar('status', _SF_STATUS_SUBMITTED);
                    $answerObj->setVar('status', _SF_AN_STATUS_APPROVED);
                    $notifToDo_faq = array(_SF_NOT_FAQ_SUBMITTED);
                }
                break;

            // This is a published FAQ for which a user submitted a new answer and we just accepeted one
            case _SF_STATUS_NEW_ANSWER:
                $redirect_msg = _AM_SF_FAQ_NEW_ANSWER_PUBLISHED;
                $faqObj->setVar('status', _SF_STATUS_PUBLISHED);
                $answerObj->setVar('status', _SF_AN_STATUS_APPROVED);
                $notifToDo_answer = array(_SF_NOT_ANSWER_APPROVED);
                break;
        }

        // Storing the FAQ object in the database
        if (!$faqObj->store()) {
            redirect_header('javascript:history.go(-1)', 2, _AM_SF_ERROR_FAQ_NOT_SAVED);
        }

        // Storing the answer object in the database
        if (!$answerObj->store()) {
            redirect_header('javascript:history.go(-1)', 2, _AM_SF_ERROR_ANSWER_NOT_SAVED);
        }

        // Send FAQ notifications
        if (!empty($notifToDo_faq)) {
            $faqObj->sendNotifications($notifToDo_faq);
        }

        // Send answer notifications
        if (!empty($notifToDo_answer)) {
            $answerObj->sendNotifications($notifToDo_answer);
        }

        redirect_header('index.php', 2, $redirect_msg);
        break;

    case 'del':
        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB;

        $faqid     = isset($_POST['faqid']) ? (int)$_POST['faqid'] : 0;
        $faqid     = isset($_GET['faqid']) ? (int)$_GET['faqid'] : $faqid;
        $answerid  = isset($_POST['answerid']) ? (int)$_POST['answerid'] : 0;
        $answerid  = isset($_GET['answerid']) ? (int)$_GET['answerid'] : $answerid;
        $confirm   = isset($_POST['confirm']) ? (int)$_POST['confirm'] : 0;
        $faqObj    = new sfFaq($faqid);
        $answerObj = new sfAnswer($answerid);
        if ($confirm) {
            $answerObj->setVar('status', _SF_AN_STATUS_REJECTED);
            $answerObj->store();

            switch ($faqObj->status()) {
                // Open Question for which we are rejecting an answer
                case _SF_STATUS_ANSWERED:
                    $redirect_page = 'index.php';
                    $redirect_msg  = _AM_SF_ANSWER_REJECTED_OPEN_QUESTION;
                    $faqObj->setVar('status', _SF_STATUS_OPENED);
                    break;

                case _SF_STATUS_NEW_ANSWER:
                    $proposed_answers = $answerHandler->getAllAnswers($faqid, _SF_AN_STATUS_PROPOSED);
                    if (count($proposed_answers) > 0) {
                        // This question has other proposed answer
                        $redirect_page = 'answer.php?op=mod&faqid=' . $faqid;
                        $redirect_msg  = _AM_SF_ANSWER_REJECTED;
                    } else {
                        // The question has no other proposed answer
                        $redirect_page = 'index.php';
                        $redirect_msg  = _AM_SF_ANSWER_REJECTED;
                        $faqObj->setVar('status', _SF_STATUS_PUBLISHED);
                    }
                    break;
            }
            $faqObj->store();
            redirect_header($redirect_page, 3, $redirect_msg);
        } else {
            xoops_cp_header();
            xoops_confirm(array('op' => 'del', 'answerid' => $answerid, 'confirm' => 1, 'faqid' => $faqid), 'answer.php', _AM_SF_DELETETHISANSWER, _AM_SF_DELETE);
            xoops_cp_footer();
        }
        exit();
        break;

    case 'default':
    default:
        xoops_cp_header();

        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModuleConfig, $xoopsModule;

        editfaq();
        break;
}

include_once __DIR__ . '/admin_footer.php';
