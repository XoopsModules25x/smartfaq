<?php

/**
* $Id: category.php,v 1.24 2006/08/21 13:30:32 malanciault Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

include_once("admin_header.php");

// Creating the category handler object
$category_handler =& sf_gethandler('category');

$op = '';

if (isset($_GET['op'])) $op = $_GET['op'];
if (isset($_POST['op'])) $op = $_POST['op'];

// Where do we start?
$startcategory = isset($_GET['startcategory'])? intval($_GET['startcategory']) : 0;

function displayCategory($categoryObj, $level = 0)
{
	Global $xoopsModule, $category_handler, $pathIcon16;
	$description = $categoryObj->description();
	if (!XOOPS_USE_MULTIBYTES) {
		if (strlen($description) >= 100) {
			$description = substr($description, 0, (100 -1)) . "...";
		}
	}
	$modify = "<a href='category.php?op=mod&categoryid=" . $categoryObj->categoryid() . "'><img src='". $pathIcon16 .'/edit.png'."' title='" . _AM_SF_EDITCOL . "' alt='" . _AM_SF_EDITCOL . "' /></a>";
	$delete = "<a href='category.php?op=del&categoryid=" . $categoryObj->categoryid() . "'><img src='". $pathIcon16 .'/delete.png'."' title='" . _AM_SF_DELETECOL . "' alt='" . _AM_SF_DELETECOL . "' /></a>";

	$spaces = '';
	for ( $j = 0; $j < $level; $j++ ) {
		$spaces .= '&nbsp;&nbsp;&nbsp;';
	}

	echo "<tr>";
	echo "<td class='even' align='lefet'>" . $spaces . "<a href='" . XOOPS_URL . "/modules/" . $xoopsModule->dirname() . "/category.php?categoryid=" . $categoryObj->categoryid() . "'><img src='" . XOOPS_URL . "/modules/smartfaq/images/icon/subcat.gif' alt='' />&nbsp;" . $categoryObj->name() . "</a></td>";
	echo "<td class='even' align='left'>" . $description . "</td>";
	echo "<td class='even' align='center'>" . $categoryObj->weight() . "</td>";
	echo "<td class='even' align='center'> $modify $delete </td>";
	echo "</tr>";
	$subCategoriesObj = $category_handler->getCategories(0, 0, $categoryObj->categoryid());
	if (count($subCategoriesObj) > 0) {
		$level++;
		foreach ( $subCategoriesObj as $key => $thiscat ) {
			displayCategory($thiscat, $level);
		}
	}
	unset($categoryObj);
}

function editcat($showmenu = false, $categoryid = 0)
{
	//$moderators = array(); // just to define the variable
	//$allmods = array();
	$startfaq = isset($_GET['startfaq'])? intval($_GET['startfaq']) : 0;
	Global $category_handler, $xoopsUser, $xoopsUser, $myts, $xoopsConfig, $xoopsDB, $modify, $xoopsModuleConfig, $xoopsModule, $_GET;
	include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

	// Creating the faq handler object
	$faq_handler =& sf_gethandler('faq');

	echo "<script type=\"text/javascript\" src=\"funcs.js\"></script>";
	echo "<style>";
	echo "<!-- ";
	echo "select { width: 130px; }";
	echo "-->";
	echo "</style>";
	// If there is a parameter, and the id exists, retrieve data: we're editing a category
	if ($categoryid != 0) {

		// Creating the category object for the selected category
		$categoryObj = new sfCategory($categoryid);

		if ($showmenu) {
			//sf_adminMenu(1, _AM_SF_CATEGORIES . " > " . _AM_SF_EDITING);
		}
		echo "<br />\n";
		if ($categoryObj->notLoaded()) {
			redirect_header("category.php", 1, _AM_SF_NOCOLTOEDIT);
			exit();
		}
		sf_collapsableBar('bottomtable', 'bottomtableicon');
		echo "<img id='bottomtableicon' src=" . XOOPS_URL . "/modules/" . $xoopsModule->dirname() . "/images/icon/close12.gif alt='' /></a>&nbsp;" . _AM_SF_EDITCOL . "</h3>";
		echo "<div id='bottomtable'>";
	} else {
		$categoryObj = $category_handler->create();
		if ($showmenu) {
			//sf_adminMenu(1, _AM_SF_CATEGORIES . " > " . _AM_SF_CREATINGNEW);
		}
		echo "<br />\n";
		sf_collapsableBar('bottomtable', 'bottomtableicon');
		echo "<img id='bottomtableicon' src=" . XOOPS_URL . "/modules/" . $xoopsModule->dirname() . "/images/icon/close12.gif alt='' /></a>&nbsp;" . _AM_SF_CATEGORY_CREATE . "</h3>";
		echo "<div id='bottomtable'>";
	}
	// Start category form
	$sform = new XoopsThemeForm(_AM_SF_CATEGORY, "op", xoops_getenv('PHP_SELF'));
	$sform->setExtra('enctype="multipart/form-data"');

	// Name
	$sform->addElement(new XoopsFormText(_AM_SF_CATEGORY, 'name', 50, 255, $categoryObj->name('e')), true);

	// Parent Category
	$mytree = new XoopsTree( $xoopsDB -> prefix( "smartfaq_categories" ), "categoryid", "parentid" );
	ob_start();
	$mytree -> makeMySelBox( "name", "weight", $categoryObj->parentid(), 1, 'parentid' );

	//makeMySelBox($title,$order="",$preset_id=0, $none=0, $sel_name="", $onchange="")
	$sform -> addElement( new XoopsFormLabel( _AM_SF_PARENT_CATEGORY_EXP, ob_get_contents() ) );
	ob_end_clean();

/*	$mytree = new XoopsTree($xoopsDB->prefix("smartfaq_categories"), "categoryid" , "parentid");
	ob_start();
	$sform->addElement(new XoopsFormHidden('categoryid', $categoryObj->categoryid()));
	$mytree->makeMySelBox("name", "weight", $categoryObj->categoryid());
	$sform->addElement(new XoopsFormLabel(_AM_SF_CATEGORY_FAQ, ob_get_contents()));
	ob_end_clean();
	*/

	// Decsription
	$sform->addElement(new XoopsFormTextArea(_AM_SF_COLDESCRIPT, 'description', $categoryObj->description('e'), 7, 60));

	// Weight
	$sform->addElement(new XoopsFormText(_AM_SF_COLPOSIT, 'weight', 4, 4, $categoryObj->weight()));

	// READ PERMISSIONS
	$member_handler = &xoops_gethandler('member');
	$group_list = &$member_handler->getGroupList();

	$groups_read_checkbox = new XoopsFormCheckBox(_AM_SF_PERMISSIONS_CAT_READ, 'groups_read[]', $categoryObj->getGroups_read());
	foreach ($group_list as $group_id => $group_name) {
		if ($group_id != XOOPS_GROUP_ADMIN) {
			$groups_read_checkbox->addOption($group_id, $group_name);
		}
	}
	$sform->addElement($groups_read_checkbox);
	// Apply permissions on all faqs
	$addapplyall_radio = new XoopsFormRadioYN(_AM_SF_PERMISSIONS_APPLY_ON_FAQS, 'applyall', 0, ' ' . _AM_SF_YES . '', ' ' . _AM_SF_NO . '');
	$sform->addElement($addapplyall_radio);
	// MODERATORS
	//$moderators_tray = new XoopsFormElementTray(_AM_SF_MODERATORS_DEF, '');

	$module_id = $xoopsModule->getVar('mid');

	/*$gperm_handler = &xoops_gethandler('groupperm');
	$mod_perms = $gperm_handler->getGroupIds('category_moderation', $categoryid, $module_id);

	$moderators_select = new XoopsFormSelect('', 'moderators', $moderators, 5, true);
	$moderators_tray->addElement($moderators_select);

	$butt_mngmods = new XoopsFormButton('', '', 'Manage mods', 'button');
	$butt_mngmods->setExtra('onclick="javascript:small_window(\'pop.php\', 370, 350);"');
	$moderators_tray->addElement($butt_mngmods);

	$butt_delmod = new XoopsFormButton('', '', 'Delete mod', 'button');
	$butt_delmod->setExtra('onclick="javascript:deleteSelectedItemsFromList(this.form.elements[\'moderators[]\']);"');
	$moderators_tray->addElement($butt_delmod);

	$sform->addElement($moderators_tray);
	*/
	$sform -> addElement( new XoopsFormHidden( 'categoryid', $categoryid ) );

	// Action buttons tray
	$button_tray = new XoopsFormElementTray('', '');

	/*for ($i = 0; $i < sizeof($moderators); $i++) {
	$allmods[] = $moderators[$i];
	}

	$hiddenmods = new XoopsFormHidden('allmods', $allmods);
	$button_tray->addElement($hiddenmods);
	*/
	$hidden = new XoopsFormHidden('op', 'addcategory');
	$button_tray->addElement($hidden);
	// No ID for category -- then it's new category, button says 'Create'
	if (!$categoryid) {
		$butt_create = new XoopsFormButton('', '', _AM_SF_CREATE, 'submit');
		$butt_create->setExtra('onclick="this.form.elements.op.value=\'addcategory\'"');
		$button_tray->addElement($butt_create);

		$butt_clear = new XoopsFormButton('', '', _AM_SF_CLEAR, 'reset');
		$button_tray->addElement($butt_clear);

		$butt_cancel = new XoopsFormButton('', '', _AM_SF_CANCEL, 'button');
		$butt_cancel->setExtra('onclick="history.go(-1)"');
		$button_tray->addElement($butt_cancel);
	} else {
		// button says 'Update'
		$butt_create = new XoopsFormButton('', '', _AM_SF_MODIFY, 'submit');
		$butt_create->setExtra('onclick="this.form.elements.op.value=\'addcategory\'"');
		$button_tray->addElement($butt_create);

		$butt_cancel = new XoopsFormButton('', '', _AM_SF_CANCEL, 'button');
		$butt_cancel->setExtra('onclick="history.go(-1)"');
		$button_tray->addElement($butt_cancel);
	}

	$sform->addElement($button_tray);
	$sform->display();
	echo "</div>";

	if ($categoryid) {
		include_once XOOPS_ROOT_PATH . "/modules/smartfaq/include/displayfaqs.php";
	}

	unset($hidden);
}

switch ($op) {
	case "mod":
	$categoryid = isset($_GET['categoryid'])? intval($_GET['categoryid']) : 0 ;
	$destList = isset($_POST['destList'])? $_POST['destList'] : '';
    $indexAdmin = new ModuleAdmin();
    xoops_cp_header();

    echo $indexAdmin->addNavigation('category.php');
	editcat(true, $categoryid);
	break;

	case "addcategory":
	global $_POST, $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModule, $xoopsModuleConfig, $modify, $myts, $categoryid;

	$categoryid = (isset($_POST['categoryid']))? intval($_POST['categoryid']) : 0;

	if ($categoryid != 0) {
		$categoryObj = new sfCategory($categoryid);
	} else {
		$categoryObj = $category_handler->create();
	}

	//if (isset($_POST['allmods'])) $allmods = $_POST['allmods'];
	//if (isset($_POST['moderators'])) $moderators = $_POST['moderators'];

	$categoryObj->setVar('parentid', (isset($_POST['parentid']))? intval($_POST['parentid']) : 0);
	$applyall = (isset($_POST['applyall']))? intval($_POST['applyall']) : 0;
	$categoryObj->setVar('weight', (isset($_POST['weight']))? intval($_POST['weight']) : 1);

	// Groups and permissions
	if (isset($_POST['groups_read'])){
		$categoryObj->setGroups_read($_POST['groups_read']);
	}
	else{
		$categoryObj->setGroups_read();
	}
	//	$groups_admin = isset($_POST['groups_admin'])? $_POST['groups_admin'] : array();
	//	$mod_perms = isset($_POST['mod_perms'])? $_POST['mod_perms'] : array();

	$categoryObj->setVar('name', $_POST['name']);

	$categoryObj->setVar('description', $_POST['description']);
	if ($categoryObj->isNew()) {
		$redirect_msg = _AM_SF_CATCREATED;
		$redirect_to = 'category.php?op=mod';
	} else {
		$redirect_msg = _AM_SF_COLMODIFIED;
		$redirect_to = 'category.php';
	}

	if ( !$categoryObj->store() ) {
		redirect_header("javascript:history.go(-1)", 3, _AM_SF_CATEGORY_SAVE_ERROR . sf_formatErrors($categoryObj->getErrors()));
		exit;
	}
	// TODO : put this function in the category class
	sf_saveCategory_Permissions($categoryObj->getGroups_read(), $categoryObj->categoryid(), 'category_read');
	//sf_saveCategory_Permissions($groups_admin, $categoriesObj->categoryid(), 'category_admin');


	if ($applyall) {
		// TODO : put this function in the category class
		sf_overrideFaqsPermissions($categoryObj->getGroups_read(), $categoryObj->categoryid());
	}

	redirect_header($redirect_to, 2, $redirect_msg);

	exit();
	break;

	case "del":
	global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $_GET;

	$module_id = $xoopsModule->getVar('mid');
	$gperm_handler = &xoops_gethandler('groupperm');

	$categoryid = (isset($_POST['categoryid']))? intval($_POST['categoryid']) : 0;
	$categoryid = (isset($_GET['categoryid']))? intval($_GET['categoryid']) : $categoryid;

	$categoryObj = new sfCategory($categoryid);

	$confirm = (isset($_POST['confirm']))? $_POST['confirm'] : 0;
	$name = (isset($_POST['name']))? $_POST['name'] : '';

	if ($confirm) {
		if ( !$category_handler->delete($categoryObj)) {
			redirect_header("category.php", 1, _AM_SF_DELETE_CAT_ERROR);
			exit;
		}

		redirect_header("category.php", 1, sprintf(_AM_SF_COLISDELETED, $name));
		exit();
	} else {
		// no confirm: show deletion condition
		$categoryid = (isset($_GET['categoryid']))? intval($_GET['categoryid']) : 0;
		xoops_cp_header();
		xoops_confirm(array('op' => 'del', 'categoryid' => $categoryObj->categoryid(), 'confirm' => 1, 'name' => $categoryObj->name()), 'category.php', _AM_SF_DELETECOL . " '" . $categoryObj->name() . "'. <br /> <br />" . _AM_SF_DELETE_CAT_CONFIRM, _AM_SF_DELETE);
		xoops_cp_footer();
	}
	exit();
	break;

	case "cancel":
	redirect_header("category.php", 1, sprintf(_AM_SF_BACK2IDX, ''));
	exit();

	case "default":
	default:

    $indexAdmin = new ModuleAdmin();
    xoops_cp_header();

    echo $indexAdmin->addNavigation('category.php');
	//sf_adminMenu(1, _AM_SF_CATEGORIES);

	echo "<br />\n";

	// Creating the objects for top categories
	$categoriesObj = $category_handler->getCategories($xoopsModuleConfig['perpage'], $startcategory, 0);

	sf_collapsableBar('toptable', 'toptableicon');
	echo "<img id='toptableicon' src=" . XOOPS_URL . "/modules/" . $xoopsModule->dirname() . "/images/icon/close12.gif alt='' /></a>&nbsp;" . _AM_SF_CATEGORIES_TITLE . "</h3>";
	echo "<div id='toptable'>";
	echo "<span style=\"color: #567; margin: 3px 0 12px 0; font-size: small; display: block; \">" . _AM_SF_CATEGORIES_DSC . "</span>";

	echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
	echo "<tr>";
	echo "<th width='35%' class='bg3' align='left'><b>" . _AM_SF_ARTCOLNAME . "</b></td>";
	echo "<th class='bg3' align='left'><b>" . _AM_SF_DESCRIP . "</b></td>";
	echo "<th class='bg3' width='65' align='center'><b>" . _AM_SF_WEIGHT . "</b></td>";
	echo "<th width='60' class='bg3' align='center'><b>" . _AM_SF_ACTION . "</b></td>";
	echo "</tr>";
	$totalCategories = $category_handler->getCategoriesCount(0);
	if (count($categoriesObj) > 0) {
		foreach ( $categoriesObj as $key => $thiscat) {
			displayCategory($thiscat);

		}
	} else {
		echo "<tr>";
		echo "<td class='head' align='center' colspan= '7'>" . _AM_SF_NOCAT . "</td>";
		echo "</tr>";
		$categoryid = '0';
	}
	echo "</table>\n";
	include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
	$pagenav = new XoopsPageNav($totalCategories, $xoopsModuleConfig['perpage'], $startcategory, 'startcategory');
	echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';
	echo "</div>";

	editcat(false);

	break;
}

include_once 'admin_footer.php';