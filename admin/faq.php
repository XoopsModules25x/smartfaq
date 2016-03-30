<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

include_once __DIR__ . '/admin_header.php';

// Creating the faq handler object
$faqHandler = sf_gethandler('faq');

// Creating the category handler object
$categoryHandler = sf_gethandler('category');

// Creating the answer handler object
$answerHandler = sf_gethandler('answer');

$op = '';
if (isset($_GET['op'])) {
    $op = $_GET['op'];
}
if (isset($_POST['op'])) {
    $op = $_POST['op'];
}

// Where shall we start?
$startfaq = isset($_GET['startfaq']) ? (int)$_GET['startfaq'] : 0;

/**
 * @param bool $showmenu
 * @param int  $faqid
 * @param int  $answerid
 * @param bool $merge
 */
function editfaq($showmenu = false, $faqid = -1, $answerid = -1, $merge = false)
{
    global $answerHandler, $faqHandler, $categoryHandler, $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $modify, $xoopsModuleConfig, $xoopsModule, $XOOPS_URL, $myts;

    include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    // If there is a parameter, and the id exists, retrieve data: we're editing a faq
    if ($faqid != -1) {
        // Creating the FAQ object
        $faqObj = new sfFaq($faqid);

        if ($faqObj->notLoaded()) {
            redirect_header('faq.php', 1, _AM_SF_NOFAQSELECTED);
        }

        if ($answerid == -1) {
            // Creating the object for the official answer
            $answerObj = $faqObj->answer();
            if (!$answerObj) {
                $answerObj = $answerHandler->create();
            }
        } else {
            $answerObj         = new sfAnswer($answerid);
            $originalAnswerObj = $faqObj->answer();
        }

        switch ($faqObj->status()) {

            case _SF_STATUS_ASKED:
                $breadcrumb_action1   = _AM_SF_ASKED;
                $breadcrumb_action2   = _AM_SF_APPROVING;
                $collapsableBar_title = _AM_SF_ASKED_TITLE;
                $collapsableBar_info  = _AM_SF_ASKED_TITLE_INFO;
                $button_caption       = _AM_SF_PUBLISHED;
                $an_status            = _SF_AN_STATUS_APPROVED;
                $answerObj->setVar('uid', $xoopsUser->getVar('uid'));
                break;

            case _SF_STATUS_ANSWERED:
                $breadcrumb_action1   = _AM_SF_ANSWERED;
                $breadcrumb_action2   = _AM_SF_APPROVING;
                $collapsableBar_title = _AM_SF_ANSWERED_TITLE;
                $collapsableBar_info  = _AM_SF_ANSWERED_TITLE_INFO;
                $button_caption       = _AM_SF_APPROVE;
                $an_status            = _SF_AN_STATUS_PROPOSED;
                break;

            case _SF_STATUS_SUBMITTED:
                $breadcrumb_action1   = _AM_SF_SUBMITTED;
                $breadcrumb_action2   = _AM_SF_APPROVING;
                $collapsableBar_title = _AM_SF_SUBMITTED_TITLE;
                $collapsableBar_info  = _AM_SF_SUBMITTED_INFO;
                $button_caption       = _AM_SF_APPROVE;
                $an_status            = _SF_AN_STATUS_PROPOSED;
                break;

            case _SF_STATUS_PUBLISHED:
                $breadcrumb_action1   = _AM_SF_PUBLISHED;
                $breadcrumb_action2   = _AM_SF_EDITING;
                $collapsableBar_title = _AM_SF_PUBLISHEDEDITING;
                $collapsableBar_info  = _AM_SF_PUBLISHEDEDITING_INFO;
                $button_caption       = _AM_SF_MODIFY;
                $an_status            = _SF_AN_STATUS_APPROVED;
                break;

            case _SF_STATUS_OFFLINE:
                $breadcrumb_action1   = _AM_SF_OFFLINE;
                $breadcrumb_action2   = _AM_SF_EDITING;
                $collapsableBar_title = _AM_SF_OFFLINEEDITING;
                $collapsableBar_info  = _AM_SF_OFFLINEEDITING_INFO;
                $button_caption       = _AM_SF_MODIFY;
                $an_status            = _SF_AN_STATUS_APPROVED;
                break;

            case _SF_STATUS_OPENED:
                $breadcrumb_action1   = _AM_SF_OPEN_QUESTIONS;
                $breadcrumb_action2   = _AM_SF_ANSWERING;
                $collapsableBar_title = _AM_SF_OPEN_QUESTION_ANSWERING;
                $collapsableBar_info  = _AM_SF_OPEN_QUESTION_ANSWERING_INFO;
                $button_caption       = _AM_SF_PUBLISH;
                $an_status            = _SF_AN_STATUS_NOTSET;
                $answerObj->setVar('uid', $xoopsUser->getVar('uid'));
                break;

            case _SF_STATUS_NEW_ANSWER:
                $breadcrumb_action1   = _AM_SF_PUBLISHED;
                $breadcrumb_action2   = _AM_SF_EDITING;
                $collapsableBar_title = _AM_SF_NEW_ANSWER_EDITING;
                $collapsableBar_info  = _AM_SF_NEW_ANSWER_EDITING_INFO;
                $button_caption       = _AM_SF_PUBLISH;
                $an_status            = _SF_AN_STATUS_NOTSET;
                break;

            case 'default':
            default:
                break;
        }

        /*      if (!$answerObj) {
                    redirect_header("faq.php", 2, _AM_SF_ANSWERNOTFOUND);
                }       */

        // Creating the category of this FAQ
        $categoryObj = $faqObj->category();

        echo "<br />\n";
        sf_collapsableBar('bottomtable', 'bottomtableicon');
        echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt='' /></a>&nbsp;" . $collapsableBar_title . '</h3>';
        echo "<div id='bottomtable'>";
        echo "<span style=\"color: #567; margin: 3px 0 12px 0; font-size: small; display: block; \">" . $collapsableBar_info . '</span>';
    } else {
        // there's no parameter, so we're adding a faq
        $faqObj =& $faqHandler->create();
        $faqObj->setVar('uid', $xoopsUser->getVar('uid'));
        $categoryObj = $categoryHandler->create();
        $answerObj   = $answerHandler->create();
        $answerObj->setVar('uid', $xoopsUser->getVar('uid'));

        $breadcrumb_action1 = _AM_SF_SMARTFAQS;
        $breadcrumb_action2 = _AM_SF_CREATINGNEW;
        $button_caption     = _AM_SF_CREATE;

        sf_collapsableBar('bottomtable', 'bottomtableicon');
        echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt='' /></a>&nbsp;" . _AM_SF_CREATESMARTFAQ . '</h3>';
        echo "<div id='bottomtable'>";
    }
    $sform = new XoopsThemeForm(_AM_SF_SMARTFAQ, 'op', xoops_getenv('PHP_SELF'));
    $sform->setExtra('enctype="multipart/form-data"');

    // faq requester
    $sform->addElement(new XoopsFormLabel(_AM_SF_REQUESTED_BY, sf_getLinkedUnameFromId($faqObj->uid(), $xoopsModuleConfig['userealname'])));

    // faq answered by
    $sform->addElement(new XoopsFormLabel(_AM_SF_ANSWERED_BY, sf_getLinkedUnameFromId($answerObj->uid(), $xoopsModuleConfig['userealname'])));

    // CATEGORY
    /*
    * Get information for pulldown menu using XoopsTree.
    * First var is the database table
    * Second var is the unique field ID for the categories
    * Last one is not set as we do not have sub menus in Smartfaq
    */

    $mytree = new XoopsTree($xoopsDB->prefix('smartfaq_categories'), 'categoryid', 'parentid');
    ob_start();
    $mytree->makeMySelBox('name', 'weight', $categoryObj->categoryid());
    $sform->addElement(new XoopsFormLabel(_AM_SF_CATEGORY_FAQ, ob_get_contents()));
    ob_end_clean();

    // faq QUESTION
    $sform->addElement(new XoopsFormTextArea(_AM_SF_QUESTION, 'question', $faqObj->question(0, 'e'), 7, 60));

    // ANSWER
    if ($merge) {
        $theanswer = $originalAnswerObj->answer('e') . "\n\n" . sprintf(_AM_SF_NEW_CONTRIBUTION, sf_getLinkedUnameFromId($answerObj->uid(), $xoopsModuleConfig['userealname']), $answerObj->datesub(), $answerObj->answer('e'));
    } else {
        $theanswer = $answerObj->answer('e');
    }

    //$sform->addElement(new XoopsFormDhtmlTextArea(_AM_SF_ANSWER_FAQ, 'answer', $theanswer, 15, 60), true);

    $editorTray = new XoopsFormElementTray(_AM_SF_ANSWER_FAQ, '<br />');
    if (class_exists('XoopsFormEditor')) {
        $options['name']   = 'answer';
        $options['value']  = $theanswer;
        $options['rows']   = 5;
        $options['cols']   = '100%';
        $options['width']  = '100%';
        $options['height'] = '200px';
        $answerEditor      = new XoopsFormEditor('', $xoopsModuleConfig['form_editorOptions'], $options, $nohtml = false, $onfailure = 'textarea');
        $editorTray->addElement($answerEditor, true);
    } else {
        $answerEditor = new XoopsFormDhtmlTextArea(_AM_SF_ANSWER_FAQ, 'answer', $theanswer, '100%', '100%');
        $editorTray->addElement($answerEditor, true);
    }

    $sform->addElement($editorTray);

    // HOW DO I
    $sform->addElement(new XoopsFormText(_AM_SF_HOWDOI_FAQ, 'howdoi', 50, 255, $faqObj->howdoi('e')), false);

    // DIDUNO
    $sform->addElement(new XoopsFormTextArea(_AM_SF_DIDUNO_FAQ, 'diduno', $faqObj->diduno('e'), 3, 60));

    // CONTEXT MODULE LINK
    // Retreive the list of module currently installed. The key value is the dirname
    $moduleHandler           = xoops_getHandler('module');
    $modules_array           = $moduleHandler->getList(null, true);
    $modulelink_select_array = array('url' => _AM_SF_SPECIFIC_URL_SELECT);
    $modulelink_select_array = array_merge($modules_array, $modulelink_select_array);
    $modulelink_select_array = array_merge(array('None' => _AM_SF_NONE, 'All' => _AM_SF_ALL), $modulelink_select_array);

    $modulelink_select = new XoopsFormSelect('', 'modulelink', $faqObj->modulelink());
    $modulelink_select->addOptionArray($modulelink_select_array);
    $modulelink_tray = new XoopsFormElementTray(_AM_SF_CONTEXTMODULELINK_FAQ, '&nbsp;');
    $modulelink_tray->addElement($modulelink_select);
    $sform->addElement($modulelink_tray);

    // SPECIFICURL
    $sform->addElement(new XoopsFormText(_AM_SF_SPECIFIC_URL, 'contextpage', 50, 60, $faqObj->contextpage()), false);

    // EXACT URL?
    $excaturl_radio = new XoopsFormRadioYN(_AM_SF_EXACTURL, 'exacturl', $faqObj->exacturl(), ' ' . _AM_SF_YES . '', ' ' . _AM_SF_NO . '');
    $sform->addElement($excaturl_radio);
    // WEIGHT
    $sform->addElement(new XoopsFormText(_AM_SF_WEIGHT, 'weight', 5, 5, $faqObj->weight()), true);

    // COMMENTS
    // Code to allow comments
    $addcomments_radio = new XoopsFormRadioYN(_AM_SF_ALLOWCOMMENTS, 'cancomment', $faqObj->cancomment(), ' ' . _AM_SF_YES . '', ' ' . _AM_SF_NO . '');
    $sform->addElement($addcomments_radio);

    // PER ITEM PERMISSIONS
    $memberHandler   = xoops_getHandler('member');
    $group_list      = $memberHandler->getGroupList();
    $groups_checkbox = new XoopsFormCheckBox(_AM_SF_PERMISSIONS_FAQ, 'groups[]', $faqObj->getGroups_read());
    foreach ($group_list as $group_id => $group_name) {
        if ($group_id != XOOPS_GROUP_ADMIN) {
            $groups_checkbox->addOption($group_id, $group_name);
        }
    }
    $sform->addElement($groups_checkbox);

    $partial_view = new XoopsFormRadioYN(_AM_SF_PARTIALVIEW, 'partialview', $faqObj->partialview(), ' ' . _AM_SF_YES . '', ' ' . _AM_SF_NO . '');
    $sform->addElement($partial_view);

    // VARIOUS OPTIONS
    $options_tray = new XoopsFormElementTray(_AM_SF_OPTIONS, '<br />');

    $html_checkbox = new XoopsFormCheckBox('', 'html', $faqObj->html());
    $html_checkbox->addOption(1, _AM_SF_DOHTML);
    $options_tray->addElement($html_checkbox);

    $smiley_checkbox = new XoopsFormCheckBox('', 'smiley', $faqObj->smiley());
    $smiley_checkbox->addOption(1, _AM_SF_DOSMILEY);
    $options_tray->addElement($smiley_checkbox);

    $xcodes_checkbox = new XoopsFormCheckBox('', 'xcodes', $faqObj->xcodes());
    $xcodes_checkbox->addOption(1, _AM_SF_DOXCODE);
    $options_tray->addElement($xcodes_checkbox);

    $sform->addElement($options_tray);

    // OFFLINE
    if ($faqObj->status() == _SF_STATUS_OFFLINE) {
        // Back OnLine
        $offline_radio = new XoopsFormRadioYN(_AM_SF_OFFLINE_FIELD, 'offline', 1, ' ' . _AM_SF_YES . '', ' ' . _AM_SF_NO . '');
        $sform->addElement($offline_radio);
    }

    // faq ID
    $sform->addElement(new XoopsFormHidden('faqid', $faqObj->faqid()));

    // requester id
    $sform->addElement(new XoopsFormHidden('requester_uid', $faqObj->uid()));

    // answerer id
    $sform->addElement(new XoopsFormHidden('answerer_uid', $answerObj->uid()));

    // ANSWER ID
    $sform->addElement(new XoopsFormHidden('answerid', $answerObj->answerid()));

    $button_tray = new XoopsFormElementTray('', '');
    $hidden      = new XoopsFormHidden('op', 'addfaq');
    $button_tray->addElement($hidden);

    $sform->addElement(new XoopsFormHidden('status', $faqObj->status()));

    // Setting the FAQ Status
    /*  $status_select = new XoopsFormSelect('', 'status', $status);
    $status_select->addOptionArray(sf_getStatusArray());
    $status_tray = new XoopsFormElementTray(_AM_SF_STATUS_EXP , '&nbsp;');
    $status_tray->addElement($status_select);
    $sform->addElement($status_tray);
    */
    if (!$faqid) {
        // there's no faqid? Then it's a new faq
        // $button_tray -> addElement( new XoopsFormButton( '', 'mod', _AM_SF_CREATE, 'submit' ) );
        $butt_create = new XoopsFormButton('', '', _AM_SF_CREATE, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addfaq\'"');
        $button_tray->addElement($butt_create);

        $butt_clear = new XoopsFormButton('', '', _AM_SF_CLEAR, 'reset');
        $button_tray->addElement($butt_clear);

        $butt_cancel = new XoopsFormButton('', '', _AM_SF_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $button_tray->addElement($butt_cancel);
    } else {
        // else, we're editing an existing faq
        // $button_tray -> addElement( new XoopsFormButton( '', 'mod', _AM_SF_MODIFY, 'submit' ) );
        $butt_create = new XoopsFormButton('', '', $button_caption, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addfaq\'"');
        $button_tray->addElement($butt_create);

        $butt_cancel = new XoopsFormButton('', '', _AM_SF_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $button_tray->addElement($butt_cancel);
    }

    $sform->addElement($button_tray);
    $sform->display();
    echo '</div>';
    unset($hidden);
}

/* -- Available operations -- */
switch ($op) {
    case 'merge':

        $faqid    = isset($_GET['faqid']) ? $_GET['faqid'] : -1;
        $answerid = isset($_GET['answerid']) ? $_GET['answerid'] : -1;
        if ($faqid == -1) {
            $totalcategories = $categoryHandler->getCategoriesCount(-1);
            if ($totalcategories == 0) {
                redirect_header('category.php?op=mod', 3, _AM_SF_NEED_CATEGORY_FAQ);
            }
        }

        xoops_cp_header();
        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        editfaq(true, $faqid, $answerid, true);
        break;

    case 'mod':

        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModuleConfig, $xoopsModule, $modify, $myts;
        $faqid    = isset($_GET['faqid']) ? $_GET['faqid'] : -1;
        $answerid = isset($_GET['answerid']) ? $_GET['answerid'] : -1;
        if ($faqid == -1) {
            $totalcategories = $categoryHandler->getCategoriesCount(-1);
            if ($totalcategories == 0) {
                redirect_header('category.php?op=mod', 3, _AM_SF_NEED_CATEGORY_FAQ);
            }
        }

        $indexAdmin = new ModuleAdmin();
        xoops_cp_header();

        echo $indexAdmin->addNavigation(basename(__FILE__));
        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        editfaq(true, $faqid, $answerid);
        break;

    case 'addfaq':
        global $xoopsUser;

        $faqid         = isset($_POST['faqid']) ? (int)$_POST['faqid'] : -1;
        $requester_uid = isset($_POST['requester_uid']) ? (int)$_POST['requester_uid'] : 0;
        $answerer_uid  = isset($_POST['answerer_uid']) ? (int)$_POST['answerer_uid'] : 0;

        // Creating the FAQ and answer objects
        if ($faqid != -1) {
            $faqObj    = new sfFaq($faqid);
            $answerObj = $faqObj->answer();
            // If the FAQ does not have an answer, then it's an answered opened question
            if (!$answerObj) {
                echo 'error in faq.php...200412111827';
            }
        } else {
            $faqObj    = $faqHandler->create();
            $answerObj = $answerHandler->create();
        }

        // Putting the values in the FAQ object
        if (isset($_POST['groups'])) {
            $faqObj->setGroups_read($_POST['groups']);
        } else {
            $faqObj->setGroups_read();
        }
        $faqObj->setVar('categoryid', isset($_POST['categoryid']) ? (int)$_POST['categoryid'] : 0);
        $faqObj->setVar('question', $_POST['question']);
        $faqObj->setVar('howdoi', $_POST['howdoi']);
        $faqObj->setVar('diduno', $_POST['diduno']);

        $faqObj->setVar('status', isset($_POST['status']) ? (int)$_POST['status'] : _SF_STATUS_ASKED);

        // If this SmartFAQ is offline and the user set this option to No
        $offline = isset($_POST['offline']) ? $_POST['offline'] : 1;
        if ((0 == $offline) && ($faqObj->status() == _SF_STATUS_OFFLINE)) {
            $faqObj->setVar('status', _SF_STATUS_PUBLISHED);
        }
        $faqObj->setVar('weight', isset($_POST['weight']) ? (int)$_POST['weight'] : $faqObj->weight());
        $faqObj->setVar('html', isset($_POST['html']) ? (int)$_POST['html'] : 0);
        $faqObj->setVar('smiley', isset($_POST['smiley']) ? (int)$_POST['smiley'] : 0);
        $faqObj->setVar('xcodes', isset($_POST['xcodes']) ? (int)$_POST['xcodes'] : 0);
        $faqObj->setVar('cancomment', isset($_POST['cancomment']) ? (int)$_POST['cancomment'] : 0);
        $faqObj->setVar('modulelink', $_POST['modulelink']);
        $faqObj->setVar('contextpage', $_POST['contextpage']);
        $faqObj->setVar('exacturl', $_POST['exacturl']);
        $faqObj->setVar('partialview', $_POST['partialview']);
        $faqObj->setVar('uid', $requester_uid);

        switch ($faqObj->status()) {

            case _SF_STATUS_ASKED:
                $redirect_msg = _AM_SF_ASKED_APPROVE_SUCCESS;
                $error_msg    = _AM_SF_ARTNOTUPDATED;
                // Setting the new status
                $status    = _SF_STATUS_PUBLISHED;
                $an_status = _SF_AN_STATUS_APPROVED;
                $notifToDo = array(_SF_NOT_FAQ_PUBLISHED);
                break;

            case _SF_STATUS_ANSWERED:
                $redirect_msg = _AM_SF_ANSWERED_APPROVE_SUCCESS;
                $error_msg    = _AM_SF_ARTNOTUPDATED;
                // Setting the new status
                $status    = _SF_STATUS_PUBLISHED;
                $an_status = _SF_AN_STATUS_APPROVED;
                $notifToDo = array(_SF_NOT_FAQ_PUBLISHED);
                break;

            case _SF_STATUS_SUBMITTED:
                $redirect_msg = _AM_SF_SUBMITTED_APPROVE_SUCCESS;
                $error_msg    = _AM_SF_ARTNOTUPDATED;
                // Setting the new status
                $status    = _SF_STATUS_PUBLISHED;
                $an_status = _SF_AN_STATUS_APPROVED;
                $notifToDo = array(_SF_NOT_FAQ_PUBLISHED);
                break;

            case _SF_STATUS_PUBLISHED:
                $redirect_msg = _AM_SF_PUBLISHED_MOD_SUCCESS;
                $error_msg    = _AM_SF_ARTNOTUPDATED;
                // Setting the new status
                $status    = _SF_STATUS_PUBLISHED;
                $an_status = _SF_AN_STATUS_APPROVED;
                break;

            case _SF_STATUS_OPENED:
                $redirect_msg = _AM_SF_OPENED_ANSWERING_SUCCESS;
                $error_msg    = _AM_SF_ARTNOTUPDATED;
                // Setting the new status
                $status    = _SF_STATUS_PUBLISHED;
                $an_status = _SF_AN_STATUS_APPROVED;
                $notifToDo = array(_SF_NOT_FAQ_PUBLISHED);
                break;

            case _SF_STATUS_NEW_ANSWER:
                $redirect_msg = _AM_SF_FAQ_NEW_ANSWER_PUBLISHED;
                $error_msg    = _AM_SF_ARTNOTUPDATED;
                // Setting the new status
                $status    = _SF_STATUS_PUBLISHED;
                $an_status = _SF_AN_STATUS_APPROVED;
                //$notifToDo = array(_SF_NOT_FAQ_PUBLISHED);
                break;

            case _SF_STATUS_OFFLINE:
                break;

            case 'default':
            default :
                $redirect_msg = _AM_SF_SUBMITTED_APPROVE_SUCCESS;
                $error_msg    = _AM_SF_ARTNOTCREATED;
                // Setting the new status
                $status    = _SF_STATUS_PUBLISHED;
                $an_status = _SF_AN_STATUS_APPROVED;
                $notifToDo = array(_SF_NOT_FAQ_PUBLISHED);
                break;
        }
        $faqObj->setVar('status', $status);

        // Puting the info in the answer ibject
        $answerObj->setVar('answer', $_POST['answer']);
        $answerObj->setVar('status', $an_status);
        $answerObj->setVar('uid', $answerer_uid);

        // Storing the FAQ
        if (!$faqObj->store()) {
            redirect_header('javascript:history.go(-1)', 3, $error_msg . sf_formatErrors($faqObj->getErrors()));
        }

        // Storing the answer
        $answerObj->setVar('faqid', $faqObj->faqid());
        if (!$answerObj->store()) {
            redirect_header('javascript:history.go(-1)', 3, $error_msg . sf_formatErrors($answerObj->getErrors()));
        }

        // Send notifications
        if (!empty($notifToDo)) {
            $faqObj->sendNotifications($notifToDo);
        }

        redirect_header('faq.php', 2, $redirect_msg);
        break;

    case 'del':
        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $_GET;

        $module_id    = $xoopsModule->getVar('mid');
        $gpermHandler = xoops_getHandler('groupperm');

        $faqid = isset($_POST['faqid']) ? (int)$_POST['faqid'] : 0;
        $faqid = isset($_GET['faqid']) ? (int)$_GET['faqid'] : $faqid;

        $faqObj = new sfFaq($faqid);

        $confirm  = isset($_POST['confirm']) ? $_POST['confirm'] : 0;
        $question = isset($_POST['question']) ? $_POST['question'] : '';

        if ($confirm) {
            if (!$faqHandler->delete($faqObj)) {
                redirect_header('faq.php', 2, _AM_SF_FAQ_DELETE_ERROR . sf_formatErrors($faqObj->getErrors()));
            }

            redirect_header('faq.php', 2, sprintf(_AM_SF_ARTISDELETED, $faqObj->question()));
        } else {
            // no confirm: show deletion condition
            $faqid = isset($_GET['faqid']) ? (int)$_GET['faqid'] : 0;
            xoops_cp_header();
            xoops_confirm(array('op' => 'del', 'faqid' => $faqObj->faqid(), 'confirm' => 1, 'name' => $faqObj->question()), 'faq.php', _AM_SF_DELETETHISARTICLE . " <br />'" . $faqObj->question() . "'. <br /> <br />", _AM_SF_DELETE);
            xoops_cp_footer();
        }

        exit();
        break;

    case 'default':
    default:
        $indexAdmin = new ModuleAdmin();
        xoops_cp_header();

        echo $indexAdmin->addNavigation(basename(__FILE__));

        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        include_once XOOPS_ROOT_PATH . '/class/pagenav.php';

        include_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/displayfaqs.php';

        $totalcategories = $categoryHandler->getCategoriesCount(-1);
        if ($totalcategories > 0) {
            editfaq();
        }

        break;
}

include_once __DIR__ . '/admin_footer.php';
