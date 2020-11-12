<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq;

require_once __DIR__ . '/admin_header.php';

$op = 'none';

if (\Xmf\Request::hasVar('op', 'GET')) {
    $op = $_GET['op'];
}
if (\Xmf\Request::hasVar('op', 'POST')) {
    $op = $_POST['op'];
}

global $xoopsDB;

switch ($op) {
    case 'importExecute':

        $importfile      = isset($_POST['importfile']) ? $_POST['importfile'] : 'nonselected';
        $importfile_path = XOOPS_ROOT_PATH . '/modules/smartfaq/admin/' . $importfile . '.php';
        if (!file_exists($importfile_path)) {
            $errs[] = sprintf(_AM_SF_IMPORT_FILE_NOT_FOUND, $importfile_path);
            $error  = true;
        } else {
            require_once $importfile_path;
        }
        foreach ($msgs as $m) {
            echo $m . '<br>';
        }
        echo '<br>';
        $endMsg = _AM_SF_IMPORT_SUCCESS;
        if (true === $error) {
            $endMsg = _AM_SF_IMPORT_ERROR;
        }

        echo $endMsg;
        echo '<br><br>';
        echo "<a href='import.php'>" . _AM_SF_IMPORT_BACK . '</a>';
        echo '<br><br>';
        break;
    case 'default':
    default:

        $importfile = 'none';

        xoops_cp_header();

        Smartfaq\Utility::collapsableBar('bottomtable', 'bottomtableicon');
        echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . _AM_SF_IMPORT_TITLE . '</h3>';
        echo "<div id='bottomtable'>";
        echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . _AM_SF_IMPORT_INFO . '</span>';

        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $modify, $xoopsModuleConfig, $xoopsModule, $XOOPS_URL, $myts;

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        if ($moduleHandler->getByDirname('xoopsfaq')) {
            $importfile_select_array['xoopsfaq'] = _AM_SF_IMPORT_XOOPSFAQ_110;
        }

        if ($moduleHandler->getByDirname('wffaq')) {
            $importfile_select_array['wffaq'] = _AM_SF_IMPORT_WFFAQ_105;
        }

        if (isset($importfile_select_array) && count($importfile_select_array) > 0) {
            $sform = new \XoopsThemeForm(_AM_SF_IMPORT_SELECTION, 'op', xoops_getenv('SCRIPT_NAME'), 'post', true);
            $sform->setExtra('enctype="multipart/form-data"');

            // Q&A set to import
            $importfile_select = new \XoopsFormSelect('', 'importfile', $importfile);
            $importfile_select->addOptionArray($importfile_select_array);
            $importfile_tray = new \XoopsFormElementTray(_AM_SF_IMPORT_SELECT_FILE, '&nbsp;');
            $importfile_tray->addElement($importfile_select);
            $sform->addElement($importfile_tray);

            // Buttons
            $buttonTray = new \XoopsFormElementTray('', '');
            $hidden     = new \XoopsFormHidden('op', 'importExecute');
            $buttonTray->addElement($hidden);

            $butt_import = new \XoopsFormButton('', '', _AM_SF_IMPORT, 'submit');
            $butt_import->setExtra('onclick="this.form.elements.op.value=\'importExecute\'"');
            $buttonTray->addElement($butt_import);

            $butt_cancel = new \XoopsFormButton('', '', _AM_SF_CANCEL, 'button');
            $butt_cancel->setExtra('onclick="history.go(-1)"');
            $buttonTray->addElement($butt_cancel);

            $sform->addElement($buttonTray);
            $sform->display();
            unset($hidden);
        } else {
            echo '<span style="color: #567; margin: 3px 0 12px 0; font-weight: bold; font-size: small; display: block; ">' . _AM_SF_IMPORT_NO_MODULE . '</span>';
        }

        // End of collapsable bar
        echo '</div>';

        break;
}

require_once __DIR__ . '/admin_footer.php';
