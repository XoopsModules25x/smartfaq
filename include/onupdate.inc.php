<?php

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * @param XoopsModule $module
 * @return bool
 */

use XoopsModules\Smartfaq;

/**
 * @param $module
 * @return bool
 */
function xoops_module_update_smartfaq($module)
{
    /*
    // Load SmartDbUpdater from the SmartObject Framework if present
    $smartdbupdater = XOOPS_ROOT_PATH . '/modules/smartobject/class/smartdbupdater.php';
    if (!file_exists($smartdbupdater)) {
        $smartdbupdater = XOOPS_ROOT_PATH . '/modules/smartfaq/class/SmartobjectDbupdater.php';
    }
    require_once $smartdbupdater;
*/
    $dbupdater = new Smartfaq\SmartobjectDbupdater();
    $helper = Smartfaq\Helper::getInstance();
    $helper->loadLanguage('smartdbupdater');

    ob_start();

    echo '<code>' . _SDU_UPDATE_UPDATING_DATABASE . '<br>';

    // Adding partialview field
    $table = new Smartfaq\SmartDbTable('smartfaq_faq');
    if (!$table->fieldExists('partialview')) {
        $table->addNewField('partialview', "tinyint(1) NOT NULL default '0'");
    }

    // Changing categoryid type to int(11)
    $table->addAlteredField('categoryid', "int(11) NOT NULL default '0'", false);

    if (!$dbupdater->updateTable($table)) {
        /**
         * @todo trap the errors
         */
    }
    unset($table);

    // Editing smartfaq_categories table
    $table = new Smartfaq\SmartDbTable('smartfaq_categories');
    // Changing categoryid type to int(11)
    $table->addAlteredField('categoryid', "int(11) NOT NULL default '0'", false);

    // Changing parentid type to int(11)
    $table->addAlteredField('parentid', "int(11) NOT NULL default '0'", false);

    if (!$dbupdater->updateTable($table)) {
        /**
         * @todo trap the errors
         */
    }
    unset($table);

    // Editing smartfaq_answers table
    $table = new Smartfaq\SmartDbTable('smartfaq_answers');
    // Changing categoryid type to int(11)
    $table->addAlteredField('answerid', "int(11) NOT NULL default '0'", false);

    // Changing parentid type to int(11)
    $table->addAlteredField('faqid', "int(11) NOT NULL default '0'", false);

    if (!$dbupdater->updateTable($table)) {
        /**
         * @todo trap the errors
         */
    }
    unset($table);

    /**
     * Check for items with categoryid=0
     */
//    require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';
    /** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
    $smartfaq_faqHandler      = $answerHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');
    /** @var \XoopsModules\Smartfaq\CategoryHandler $smartfaq_categoryHandler */
    $smartfaq_categoryHandler = $answerHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Category');

    //find a valid categoryid
    $categoriesObj = $smartfaq_categoryHandler->getCategories(1, 0, 0, 'weight', 'ASC', false);
    if (count($categoriesObj) > 0) {
        $categoryid = $categoriesObj[0]->getVar('categoryid');
        $criteria   = new \CriteriaCompo();
        $criteria->add(new \Criteria('categoryid', 0));
        $smartfaq_faqHandler->updateAll('categoryid', $categoryid, $criteria);
        echo '&nbsp;&nbsp;Cleaning up questions with categoryid=0<br>';
    }

    echo '</code>';

    $feedback = ob_get_clean();
    if (method_exists($module, 'setMessage')) {
        $module->setMessage($feedback);
    } else {
        echo $feedback;
    }

    return true;
}

/**
 * @param XoopsModule $module
 * @return bool
 */
function xoops_module_install_smartfaq($module)
{
    ob_start();

//    require_once XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname') . '/include/functions.php';

    $feedback = ob_get_clean();
    if (method_exists($module, 'setMessage')) {
        $module->setMessage($feedback);
    } else {
        echo $feedback;
    }

    return true;
}
