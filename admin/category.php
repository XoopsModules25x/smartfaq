<?php declare(strict_types=1);

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use Xmf\Module\Admin;
use Xmf\Request;
use XoopsModules\Smartfaq;
use XoopsModules\Smartfaq\Category;
use XoopsModules\Smartfaq\Helper;

require_once __DIR__ . '/admin_header.php';

/** @var Smartfaq\Helper $helper */
$helper = Helper::getInstance();

// Creating the category handler object
/** @var \XoopsModules\Smartfaq\CategoryHandler $categoryHandler */
$categoryHandler = Helper::getInstance()->getHandler('Category');

$op = Request::getCmd('op', '');

// Where do we start?
$startcategory = Request::getInt('startcategory', 0, 'GET');

/**
 * @param \XoopsObject|Smartfaq\Category $categoryObj
 * @param int                            $level
 */
function displayCategory($categoryObj, $level = 0): void
{
    global $xoopsModule, $categoryHandler, $pathIcon16;
    $description = $categoryObj->description();
    if (!XOOPS_USE_MULTIBYTES) {
        if (mb_strlen($description) >= 100) {
            $description = mb_substr($description, 0, 100 - 1) . '...';
        }
    }
    $modify = "<a href='category.php?op=mod&categoryid=" . $categoryObj->categoryid() . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . _AM_SF_EDITCOL . "' alt='" . _AM_SF_EDITCOL . "'></a>";
    $delete = "<a href='category.php?op=del&categoryid=" . $categoryObj->categoryid() . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . _AM_SF_DELETECOL . "' alt='" . _AM_SF_DELETECOL . "'></a>";

    $spaces = '';
    for ($j = 0; $j < $level; ++$j) {
        $spaces .= '&nbsp;&nbsp;&nbsp;';
    }

    echo '<tr>';
    echo "<td class='even' align='lefet'>"
         . $spaces
         . "<a href='"
         . XOOPS_URL
         . '/modules/'
         . $xoopsModule->dirname()
         . '/category.php?categoryid='
         . $categoryObj->categoryid()
         . "'><img src='"
         . XOOPS_URL
         . "/modules/smartfaq/assets/images/icon/subcat.gif' alt=''>&nbsp;"
         . $categoryObj->name()
         . '</a></td>';
    echo "<td class='even' align='left'>" . $description . '</td>';
    echo "<td class='even' align='center'>" . $categoryObj->weight() . '</td>';
    echo "<td class='even' align='center'> $modify $delete </td>";
    echo '</tr>';
    $subCategoriesObj = &$categoryHandler->getCategories(0, 0, $categoryObj->categoryid());
    if (count($subCategoriesObj) > 0) {
        ++$level;
        foreach ($subCategoriesObj as $key => $thiscat) {
            displayCategory($thiscat, $level);
        }
    }
    unset($categoryObj);
}

/**
 * @param bool $showmenu
 * @param int  $categoryid
 */
function editcat($showmenu = false, $categoryid = 0): void
{
    //$moderators = []; // just to define the variable
    //$allmods = [];
    $startfaq = Request::getInt('startfaq', 0, 'GET');
    global $categoryHandler, $xoopsUser, $myts, $xoopsConfig, $xoopsDB, $modify, $xoopsModule, $_GET;
    /** @var Smartfaq\Helper $helper */
    $helper = Helper::getInstance();
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    // Creating the faq handler object
    /** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
    $faqHandler = Helper::getInstance()->getHandler('Faq');

    echo '<script type="text/javascript" src="funcs.js"></script>';
    echo '<style>';
    echo '<!-- ';
    echo 'select { width: 130px; }';
    echo '-->';
    echo '</style>';
    // If there is a parameter, and the id exists, retrieve data: we're editing a category
    if (0 != $categoryid) {
        // Creating the category object for the selected category
        $categoryObj = new Category($categoryid);

        echo "<br>\n";
        if ($categoryObj->notLoaded()) {
            redirect_header('category.php', 1, _AM_SF_NOCOLTOEDIT);
        }
        Smartfaq\Utility::collapsableBar('bottomtable', 'bottomtableicon');
        echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . _AM_SF_EDITCOL . '</h3>';
        echo "<div id='bottomtable'>";
    } else {
        $categoryObj = $categoryHandler->create();
        echo "<br>\n";
        Smartfaq\Utility::collapsableBar('bottomtable', 'bottomtableicon');
        echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . _AM_SF_CATEGORY_CREATE . '</h3>';
        echo "<div id='bottomtable'>";
    }
    // Start category form
    $sform = new \XoopsThemeForm(_AM_SF_CATEGORY, 'op', xoops_getenv('SCRIPT_NAME'), 'post', true);
    $sform->setExtra('enctype="multipart/form-data"');

    // Name
    $sform->addElement(new \XoopsFormText(_AM_SF_CATEGORY, 'name', 50, 255, $categoryObj->name('e')), true);

    // Parent Category
    $mytree = new Smartfaq\Tree($xoopsDB->prefix('smartfaq_categories'), 'categoryid', 'parentid');
    ob_start();
    $mytree->makeMySelBox('name', 'weight', $categoryObj->parentid(), 1, 'parentid');

    //makeMySelBox($title,$order="",$preset_id=0, $none=0, $sel_name="", $onchange="")
    $sform->addElement(new \XoopsFormLabel(_AM_SF_PARENT_CATEGORY_EXP, ob_get_clean()));

    /*  $mytree = new Smartfaq\Tree($xoopsDB->prefix("smartfaq_categories"), "categoryid" , "parentid");
        ob_start();
        $sform->addElement(new \XoopsFormHidden('categoryid', $categoryObj->categoryid()));
        $mytree->makeMySelBox("name", "weight", $categoryObj->categoryid());
        $sform->addElement(new \XoopsFormLabel(_AM_SF_CATEGORY_FAQ, ob_get_contents()));
        ob_end_clean();
        */

    // Decsription
    $sform->addElement(new \XoopsFormTextArea(_AM_SF_COLDESCRIPT, 'description', $categoryObj->description('e'), 7, 60));

    // Weight
    $sform->addElement(new \XoopsFormText(_AM_SF_COLPOSIT, 'weight', 4, 4, $categoryObj->weight()));

    // READ PERMISSIONS
    /** @var \XoopsMemberHandler $memberHandler */
    $memberHandler = xoops_getHandler('member');
    $group_list    = $memberHandler->getGroupList();

    $groups_read_checkbox = new \XoopsFormCheckBox(_AM_SF_PERMISSIONS_CAT_READ, 'groups_read[]', $categoryObj->getGroups_read());
    foreach ($group_list as $group_id => $group_name) {
        if (XOOPS_GROUP_ADMIN != $group_id) {
            $groups_read_checkbox->addOption($group_id, $group_name);
        }
    }
    $sform->addElement($groups_read_checkbox);
    // Apply permissions on all faqs
    $addapplyall_radio = new \XoopsFormRadioYN(_AM_SF_PERMISSIONS_APPLY_ON_FAQS, 'applyall', 0, ' ' . _AM_SF_YES, ' ' . _AM_SF_NO);
    $sform->addElement($addapplyall_radio);
    // MODERATORS
    //$moderators_tray = new \XoopsFormElementTray(_AM_SF_MODERATORS_DEF, '');

    $module_id = $xoopsModule->getVar('mid');

    /** @var \XoopsGroupPermHandler $grouppermHandler */
    /*
    $grouppermHandler = xoops_getHandler('groupperm');
    $mod_perms        = $grouppermHandler->getGroupIds('category_moderation', $categoryid, $module_id);

    $moderators_select = new \XoopsFormSelect('', 'moderators', $moderators, 5, true);
    $moderators_tray->addElement($moderators_select);

    $butt_mngmods = new \XoopsFormButton('', '', 'Manage mods', 'button');
    $butt_mngmods->setExtra('onclick="javascript:small_window(\'pop.php\', 370, 350);"');
    $moderators_tray->addElement($butt_mngmods);

    $butt_delmod = new \XoopsFormButton('', '', 'Delete mod', 'button');
    $butt_delmod->setExtra('onclick="javascript:deleteSelectedItemsFromList(this.form.elements[\'moderators[]\']);"');
    $moderators_tray->addElement($butt_delmod);

    $sform->addElement($moderators_tray);
    */
    $sform->addElement(new \XoopsFormHidden('categoryid', $categoryid));

    // Action buttons tray
    $buttonTray = new \XoopsFormElementTray('', '');

    /*for ($i = 0, $iMax = count($moderators); $i < $iMax; ++$i) {
    $allmods[] = $moderators[$i];
    }

    $hiddenmods = new \XoopsFormHidden('allmods', $allmods);
    $buttonTray->addElement($hiddenmods);
    */
    $hidden = new \XoopsFormHidden('op', 'addcategory');
    $buttonTray->addElement($hidden);
    // No ID for category -- then it is new category, button says 'Create'
    if ($categoryid) {
        // button says 'Update'
        $butt_create = new \XoopsFormButton('', '', _AM_SF_MODIFY, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addcategory\'"');
        $buttonTray->addElement($butt_create);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SF_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $buttonTray->addElement($butt_cancel);
    } else {
        $butt_create = new \XoopsFormButton('', '', _AM_SF_CREATE, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addcategory\'"');
        $buttonTray->addElement($butt_create);

        $butt_clear = new \XoopsFormButton('', '', _AM_SF_CLEAR, 'reset');
        $buttonTray->addElement($butt_clear);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SF_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $buttonTray->addElement($butt_cancel);
    }

    $sform->addElement($buttonTray);
    $sform->display();
    echo '</div>';

    if ($categoryid) {
        require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/displayfaqs.php';
    }

    unset($hidden);
}

switch ($op) {
    case 'mod':
        $categoryid  = Request::getInt('categoryid', 0, 'GET');
        $destList    = Request::getString('destList', '', 'POST');
        $adminObject = Admin::getInstance();
        xoops_cp_header();

        $adminObject->displayNavigation(basename(__FILE__));
        editcat(true, $categoryid);
        break;
    case 'addcategory':
        global $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModule, $modify, $myts, $categoryid;

        $categoryid = Request::getInt('categoryid', 0, 'POST');

        if (0 != $categoryid) {
            $categoryObj = new Category($categoryid);
        } else {
            $categoryObj = $categoryHandler->create();
        }

        //if (Request::hasVar('allmods', 'POST')) $allmods = $_POST['allmods'];
        //if (Request::hasVar('moderators', 'POST')) $moderators = $_POST['moderators'];

        $categoryObj->setVar('parentid', Request::getInt('parentid', 0, 'POST'));
        $applyall = Request::getInt('applyall', 0, 'POST');
        $categoryObj->setVar('weight', Request::getInt('weight', 1, 'POST'));

        // Groups and permissions
        if (Request::hasVar('groups_read', 'POST')) {
            $categoryObj->setGroups_read($_POST['groups_read']);
        } else {
            $categoryObj->setGroups_read();
        }
        //  $groups_admin = isset($_POST['groups_admin'])? $_POST['groups_admin'] : array();
        //  $mod_perms = isset($_POST['mod_perms'])? $_POST['mod_perms'] : array();

        $categoryObj->setVar('name', $_POST['name']);

        $categoryObj->setVar('description', $_POST['description']);
        if ($categoryObj->isNew()) {
            $redirect_msg = _AM_SF_CATCREATED;
            $redirect_to  = 'category.php?op=mod';
        } else {
            $redirect_msg = _AM_SF_COLMODIFIED;
            $redirect_to  = 'category.php';
        }

        if (!$categoryObj->store()) {
            redirect_header('<script>javascript:history.go(-1)</script>', 3, _AM_SF_CATEGORY_SAVE_ERROR . Smartfaq\Utility::formatErrors($categoryObj->getErrors()));
        }
        // TODO : put this function in the category class
        Smartfaq\Utility::saveCategoryPermissions($categoryObj->getGroups_read(), $categoryObj->categoryid(), 'category_read');
        //Smartfaq\Utility::saveCategoryPermissions($groups_admin, $categoriesObj->categoryid(), 'category_admin');

        if ($applyall) {
            // TODO : put this function in the category class
            Smartfaq\Utility::overrideFaqsPermissions($categoryObj->getGroups_read(), $categoryObj->categoryid());
        }

        redirect_header($redirect_to, 2, $redirect_msg);
        break;
    case 'del':
        global $xoopsUser, $xoopsConfig, $xoopsDB;

        $module_id = $xoopsModule->getVar('mid');
        /** @var \XoopsGroupPermHandler $grouppermHandler */
        $grouppermHandler = xoops_getHandler('groupperm');

        $categoryid = Request::getInt('categoryid', 0, 'POST');
        $categoryid = Request::getInt('categoryid', $categoryid, 'GET');

        $categoryObj = new Category($categoryid);

        $confirm = Request::getInt('confirm', 0, 'POST');
        $name    = Request::getString('name', '', 'POST');

        if ($confirm) {
            if (!$categoryHandler->delete($categoryObj)) {
                redirect_header('category.php', 1, _AM_SF_DELETE_CAT_ERROR);
            }
            redirect_header('category.php', 1, sprintf(_AM_SF_COLISDELETED, $name));
        } else {
            // no confirm: show deletion condition
            $categoryid = Request::getInt('categoryid', 0, 'GET');
            xoops_cp_header();
            xoops_confirm(
                [
                    'op'         => 'del',
                    'categoryid' => $categoryObj->categoryid(),
                    'confirm'    => 1,
                    'name'       => $categoryObj->name(),
                ],
                'category.php',
                _AM_SF_DELETECOL . " '" . $categoryObj->name() . "'. <br> <br>" . _AM_SF_DELETE_CAT_CONFIRM,
                _AM_SF_DELETE
            );
            xoops_cp_footer();
        }
        exit();
    case 'cancel':
        redirect_header('category.php', 1, sprintf(_AM_SF_BACK2IDX, ''));
        break;
    case 'default':
    default:
        $adminObject = Admin::getInstance();
        xoops_cp_header();

        $adminObject->displayNavigation(basename(__FILE__));
        echo "<br>\n";

        // Creating the objects for top categories
        $categoriesObj = &$categoryHandler->getCategories($helper->getConfig('perpage'), $startcategory, 0);

        Smartfaq\Utility::collapsableBar('toptable', 'toptableicon');
        echo "<img id='toptableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . _AM_SF_CATEGORIES_TITLE . '</h3>';
        echo "<div id='toptable'>";
        echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . _AM_SF_CATEGORIES_DSC . '</span>';

        echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
        echo '<tr>';
        echo "<th width='35%' class='bg3' align='left'><b>" . _AM_SF_ARTCOLNAME . '</b></td>';
        echo "<th class='bg3' align='left'><b>" . _AM_SF_DESCRIP . '</b></td>';
        echo "<th class='bg3' width='65' align='center'><b>" . _AM_SF_WEIGHT . '</b></td>';
        echo "<th width='60' class='bg3' align='center'><b>" . _AM_SF_ACTION . '</b></td>';
        echo '</tr>';
        $totalCategories = $categoryHandler->getCategoriesCount(0);
        if (count($categoriesObj) > 0) {
            foreach ($categoriesObj as $key => $thiscat) {
                displayCategory($thiscat);
            }
        } else {
            echo '<tr>';
            echo "<td class='head' align='center' colspan= '7'>" . _AM_SF_NOCAT . '</td>';
            echo '</tr>';
            $categoryid = '0';
        }
        echo "</table>\n";
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $pagenav = new \XoopsPageNav($totalCategories, $helper->getConfig('perpage'), $startcategory, 'startcategory');
        echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';
        echo '</div>';

        editcat(false);

        break;
}

require_once __DIR__ . '/admin_footer.php';
