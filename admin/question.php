<?php declare(strict_types=1);

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use Xmf\Module\Admin;
use Xmf\Request;
use XoopsModules\Smartfaq;
use XoopsModules\Smartfaq\Constants;

require_once __DIR__ . '/admin_header.php';

/** @var Smartfaq\Helper $helper */
$helper            = Smartfaq\Helper::getInstance();
$smartModuleConfig = $helper->getConfig();

global $xoopsUser;

// Creating the faq handler object
/** @var Smartfaq\FaqHandler $faqHandler */
$faqHandler = Smartfaq\Helper::getInstance()->getHandler('Faq');

// Creating the category handler object
/** @var Smartfaq\CategoryHandler $categoryHandler */
$categoryHandler = Smartfaq\Helper::getInstance()->getHandler('Category');

$op = '';
if (Request::hasVar('op', 'GET')) {
    $op = $_GET['op'];
}
if (Request::hasVar('op', 'POST')) {
    $op = $_POST['op'];
}

// Where shall we start?
$startfaq = Request::getInt('startfaq', 0, 'GET');

/**
 * @param bool $showmenu
 * @param int  $faqid
 */
function editfaq($showmenu = false, $faqid = -1): void
{
    global $faqHandler, $categoryHandler, $xoopsUser, $xoopsConfig, $xoopsDB, $modify, $xoopsModule, $XOOPS_URL, $myts;
    /** @var Smartfaq\Helper $helper */
    $helper = Smartfaq\Helper::getInstance();

    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    // If there is a parameter, and the id exists, retrieve data: we're editing a faq
    if (-1 != $faqid) {
        // Creating the FAQ object
        $faqObj = new Smartfaq\Faq($faqid);

        if ($faqObj->notLoaded()) {
            redirect_header('faq.php', 1, _AM_SF_NOARTTOEDIT);
        }
        switch ($faqObj->status()) {
            case Constants::SF_STATUS_ASKED:
                $breadcrumb_action    = _AM_SF_APPROVING;
                $collapsableBar_title = _AM_SF_QUESTION_APPROVING;
                $collapsableBar_info  = _AM_SF_QUESTION_APPROVING_INFO;
                $button_caption       = _AM_SF_QUEUE;
                break;
            case 'default':
            default:
                $breadcrumb_action    = _AM_SF_EDITING;
                $collapsableBar_title = _AM_SF_EDITQUES;
                $collapsableBar_info  = _AM_SF_EDITING_INFO;
                $button_caption       = _AM_SF_MODIFY;
                break;
        }

        // Creating the category of this FAQ
        $categoryObj = $categoryHandler->get($faqObj->categoryid());

        echo "<br>\n";
        Smartfaq\Utility::collapsableBar('bottomtable', 'bottomtableicon');
        echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . $collapsableBar_title . '</h3>';
        echo "<div id='bottomtable'>";
        echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . $collapsableBar_info . '</span>';
    } else {
        // there's no parameter, so we're adding a faq
        $faqObj = $faqHandler->create();
        $faqObj->setVar('uid', $xoopsUser->getVar('uid'));
        $categoryObj = $categoryHandler->create();

        $breadcrumb_action = _AM_SF_CREATINGNEW;
        $button_caption    = _AM_SF_CREATE;

        Smartfaq\Utility::collapsableBar('bottomtable', 'bottomtableicon');
        echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . _AM_SF_CREATEQUESTION . '</h3>';
        echo "<div id='bottomtable'>";
    }
    $sform = new \XoopsThemeForm(_AM_SF_OPEN_QUESTION, 'op', xoops_getenv('SCRIPT_NAME'), 'post', true);
    $sform->setExtra('enctype="multipart/form-data"');

    // faq requester
    $sform->addElement(new \XoopsFormLabel(_AM_SF_REQUESTED_BY, Smartfaq\Utility::getLinkedUnameFromId($faqObj->uid(), $helper->getConfig('userealname'))));

    // CATEGORY
    /*
    * Get information for pulldown menu using XoopsTree.
    * First var is the database table
    * Second var is the unique field ID for the categories
    * Last one is not set as we do not have sub menus in Smartfaq
    */

    $mytree = new Smartfaq\Tree($xoopsDB->prefix('smartfaq_categories'), 'categoryid', 'parentid');
    ob_start();
    $mytree->makeMySelBox('name', 'weight', $categoryObj->categoryid());
    $sform->addElement(new \XoopsFormLabel(_AM_SF_CATEGORY_QUESTION, ob_get_clean()));

    // faq QUESTION
    $sform->addElement(new \XoopsFormTextArea(_AM_SF_QUESTION, 'question', $faqObj->question(), 7, 60));

    // PER ITEM PERMISSIONS
    /** @var \XoopsMemberHandler $memberHandler */
    $memberHandler   = xoops_getHandler('member');
    $group_list      = $memberHandler->getGroupList();
    $groups_checkbox = new \XoopsFormCheckBox(_AM_SF_PERMISSIONS_QUESTION, 'groups[]', $faqObj->getGroups_read());
    foreach ($group_list as $group_id => $group_name) {
        if (XOOPS_GROUP_ADMIN != $group_id) {
            $groups_checkbox->addOption($group_id, $group_name);
        }
    }
    $sform->addElement($groups_checkbox);

    // faq ID
    $sform->addElement(new \XoopsFormHidden('faqid', $faqObj->faqid()));

    $buttonTray = new \XoopsFormElementTray('', '');
    $hidden     = new \XoopsFormHidden('op', 'addfaq');
    $buttonTray->addElement($hidden);

    $sform->addElement(new \XoopsFormHidden('status', $faqObj->status()));
    // Setting the FAQ Status
    /*  $status_select = new \XoopsFormSelect('', 'status', $status);
    $status_select->addOptionArray(Smartfaq\Utility::getStatusArray());
    $status_tray = new \XoopsFormElementTray(_AM_SF_STATUS_EXP , '&nbsp;');
    $status_tray->addElement($status_select);
    $sform->addElement($status_tray);
    */
    if (-1 == $faqid) {
        // there's no faqid? Then it's a new faq
        // $buttonTray -> addElement( new \XoopsFormButton( '', 'mod', _AM_SF_CREATE, 'submit' ) );
        $butt_create = new \XoopsFormButton('', '', _AM_SF_CREATE, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addfaq\'"');
        $buttonTray->addElement($butt_create);

        $butt_clear = new \XoopsFormButton('', '', _AM_SF_CLEAR, 'reset');
        $buttonTray->addElement($butt_clear);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SF_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $buttonTray->addElement($butt_cancel);
    } else {
        // else, we're editing an existing faq
        // $buttonTray -> addElement( new \XoopsFormButton( '', 'mod', _AM_SF_MODIFY, 'submit' ) );
        $butt_create = new \XoopsFormButton('', '', $button_caption, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addfaq\'"');
        $buttonTray->addElement($butt_create);

        $butt_edit = new \XoopsFormButton('', '', _AM_SF_OPEN_QUESTION_EDIT, 'button');
        $butt_edit->setExtra("onclick=\"location='faq.php?op=mod&amp;faqid=" . $faqid . "'\"");
        $buttonTray->addElement($butt_edit);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SF_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $buttonTray->addElement($butt_cancel);
    }

    $sform->addElement($buttonTray);
    $sform->display();
    echo '</div>';
    unset($hidden);
}

/* -- Available operations -- */
switch ($op) {
    case 'mod':
        global $xoopsConfig, $xoopsDB, $xoopsModule, $modify, $myts;
        $faqid = $_GET['faqid'] ?? -1;

        if (-1 == $faqid) {
            $totalcategories = $categoryHandler->getCategoriesCount(-1);
            if (0 == $totalcategories) {
                redirect_header('category.php?op=mod', 3, _AM_SF_NEED_CATEGORY_QUESTION);
            }
        }

        $adminObject = Admin::getInstance();
        xoops_cp_header();

        $adminObject->displayNavigation(basename(__FILE__));
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        editfaq(true, $faqid);
        break;
    case 'addfaq':
        if (!$xoopsUser) {
            if (1 == $helper->getConfig('anonpost')) {
                $uid = 0;
            } else {
                redirect_header('index.php', 3, _NOPERM);
            }
        } else {
            $uid = $xoopsUser->uid();
        }

        $faqid = Request::getInt('faqid', -1, 'POST');

        // Creating the FAQ
        if (-1 != $faqid) {
            $faqObj = new Smartfaq\Faq($faqid);
        } else {
            $faqObj = $faqHandler->create();
        }

        // Putting the values in the FAQ object
        $faqObj->setGroups_read($_POST['groups'] ?? []);
        $faqObj->setVar('categoryid', Request::getInt('categoryid', 0, 'POST'));
        $faqObj->setVar('question', $_POST['question']);
        $faqObj->setVar('status', Request::getInt('status', Constants::SF_STATUS_ASKED, 'POST'));

        $notifToDo = null;

        switch ($faqObj->status()) {
            case Constants::SF_STATUS_NOTSET:
                $redirect_msg = _AM_SF_QUESTIONCREATEDOK;
                // Setting the new status
                $status    = Constants::SF_STATUS_OPENED;
                $notifToDo = [Constants::SF_NOT_QUESTION_PUBLISHED];
                $faqObj->setVar('uid', $uid);
                break;
            case Constants::SF_STATUS_ASKED:
                $redirect_msg = _AM_SF_QUESTIONPUBLISHED;
                // Setting the new status
                $status    = Constants::SF_STATUS_OPENED;
                $notifToDo = [Constants::SF_NOT_QUESTION_PUBLISHED];
                break;
            case 'default':
            default:
                $redirect_msg = _AM_SF_QUESTIONMODIFIED;
                // Setting the new status
                $status = $faqObj->status();
                break;
        }
        $faqObj->setVar('status', $status);

        // Storing the FAQ
        if (!$faqObj->store()) {
            redirect_header('<script>javascript:history.go(-1)</script>', 3, _AM_SF_ERROR . Smartfaq\Utility::formatErrors($faqObj->getErrors()));
        }

        // Send notifications
        if (!empty($notifToDo)) {
            $faqObj->sendNotifications($notifToDo);
        }

        redirect_header('question.php', 2, $redirect_msg);

        break;
    case 'del':
        global $xoopsConfig, $xoopsDB;

        $module_id = $xoopsModule->getVar('mid');
        /** @var \XoopsGroupPermHandler $grouppermHandler */
        $grouppermHandler = xoops_getHandler('groupperm');

        $faqid = Request::getInt('faqid', 0, 'POST');
        $faqid = Request::getInt('faqid', $faqid, 'GET');

        $faqObj = new Smartfaq\Faq($faqid);

        $confirm  = Request::getInt('confirm', 0, 'POST');
        $question = Request::getString('question', '', 'POST');

        if ($confirm) {
            if (!$faqHandler->delete($faqObj)) {
                redirect_header('question.php', 2, _AM_SF_FAQ_DELETE_ERROR);
            }

            redirect_header('question.php', 2, sprintf(_AM_SF_QUESTIONISDELETED, $faqObj->question()));
        } else {
            // no confirm: show deletion condition
            $faqid = Request::getInt('faqid', 0, 'GET');
            xoops_cp_header();
            xoops_confirm(
                [
                    'op'      => 'del',
                    'faqid'   => $faqObj->faqid(),
                    'confirm' => 1,
                    'name'    => $faqObj->question(),
                ],
                'question.php',
                _AM_SF_DELETETHISQUESTION . " <br>'" . $faqObj->question() . "'. <br> <br>",
                _AM_SF_DELETE
            );
            xoops_cp_footer();
        }

        exit();
    case 'default':
    default:
        $adminObject = Admin::getInstance();
        xoops_cp_header();
        $adminObject->displayNavigation(basename(__FILE__));

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

        global $xoopsConfig, $xoopsDB, $xoopsModule;

        echo "<br>\n";

        Smartfaq\Utility::collapsableBar('toptable', 'toptableicon');

        echo "<img id='toptableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . _AM_SF_OPENED_TITLE . '</h3>';
        echo "<div id='toptable'>";
        echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . _AM_SF_OPENED_DSC . '</span>';

        // Get the total number of published FAQs
        $totalfaqs = $faqHandler->getFaqsCount(-1, [Constants::SF_STATUS_OPENED]);
        // creating the FAQ objects that are published
        $faqsObj = $faqHandler->getFaqs($helper->getConfig('perpage'), $startfaq, Constants::SF_STATUS_OPENED);
        //        $totalFaqsOnPage = count($faqsObj);
        $allCats = $categoryHandler->getObjects(null, true);
        echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
        echo '<tr>';
        echo "<th width='40' class='bg3' align='center'><b>" . _AM_SF_ARTID . '</b></td>';
        echo "<th width='20%' class='bg3' align='left'><b>" . _AM_SF_ARTCOLNAME . '</b></td>';
        echo "<th class='bg3' align='left'><b>" . _AM_SF_QUESTION . '</b></td>';

        echo "<th width='90' class='bg3' align='center'><b>" . _AM_SF_ASKED . '</b></td>';

        echo "<th width='90' class='bg3' align='center'><b>" . _AM_SF_CREATED . '</b></td>';
        echo "<th width='60' class='bg3' align='center'><b>" . _AM_SF_ACTION . '</b></td>';
        echo '</tr>';
        //var_dump( $faqsObj);
        if ($totalfaqs > 0) {
            global $pathIcon16;
            foreach (array_keys($faqsObj) as $i) {
                $categoryObj = $allCats[$faqsObj[$i]->categoryid()];

                $modify = "<a href='question.php?op=mod&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . _AM_SF_EDITART . "' alt='" . _AM_SF_EDITART . "'></a>";
                $delete = "<a href='question.php?op=del&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . _AM_SF_DELETEART . "' alt='" . _AM_SF_DELETEART . "'></a>";

                $requester = Smartfaq\Utility::getLinkedUnameFromId($faqsObj[$i]->uid(), $smartModuleConfig['userealname']);

                echo '<tr>';
                echo "<td class='head' align='center'>" . $faqsObj[$i]->faqid() . '</td>';
                echo "<td class='even' align='left'>" . $categoryObj->name() . '</td>';
                echo "<td class='even' align='left'><a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/answer.php?faqid=' . $faqsObj[$i]->faqid() . "'>" . $faqsObj[$i]->question(100) . '</a></td>';

                echo "<td class='even' align='center'>" . $requester . '</td>';

                echo "<td class='even' align='center'>" . $faqsObj[$i]->datesub('s') . '</td>';
                echo "<td class='even' align='center'> $modify $delete </td>";
                echo '</tr>';
            }
        } else {
            $faqid = -1;
            echo '<tr>';
            echo "<td class='head' align='center' colspan= '7'>" . _AM_SF_NOQUEUED . '</td>';
            echo '</tr>';
        }
        echo "</table>\n";
        echo "<br>\n";

        $pagenav = new \XoopsPageNav($totalfaqs, $helper->getConfig('perpage'), $startfaq, 'startfaq');
        echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';
        echo '</div>';

        $totalcategories = $categoryHandler->getCategoriesCount(-1);
        if ($totalcategories > 0) {
            editfaq();
        }

        break;
}

require_once __DIR__ . '/admin_footer.php';
