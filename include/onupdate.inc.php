<?php

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * @param $module
 * @return bool
 */
function xoops_module_update_smartfaq($module)
{
    // Load SmartDbUpdater from the SmartObject Framework if present
    $smartdbupdater = XOOPS_ROOT_PATH . '/modules/smartobject/class/smartdbupdater.php';
    if (!file_exists($smartdbupdater)) {
        $smartdbupdater = XOOPS_ROOT_PATH . '/modules/smartfaq/class/smartdbupdater.php';
    }
    include_once($smartdbupdater);

    $dbupdater = new SmartobjectDbupdater();

    ob_start();

    echo '<code>' . _SDU_UPDATE_UPDATING_DATABASE . '<br />';

    // Adding partialview field
    $table = new SmartDbTable('smartfaq_faq');
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
    $table = new SmartDbTable('smartfaq_categories');
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
    $table = new SmartDbTable('smartfaq_answers');
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
    include_once(XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php');
    $smartfaq_faqHandler      = $answerHandler = sf_gethandler('faq');
    $smartfaq_categoryHandler = $answerHandler = sf_gethandler('category');

    //find a valid categoryid
    $categoriesObj = $smartfaq_categoryHandler->getCategories(1, 0, 0, 'weight', 'ASC', false);
    if (count($categoriesObj) > 0) {
        $categoryid = $categoriesObj[0]->getVar('categoryid');
        $criteria   = new CriteriaCompo();
        $criteria->add(new Criteria('categoryid', 0));
        $smartfaq_faqHandler->updateAll('categoryid', $categoryid, $criteria);
        echo '&nbsp;&nbsp;Cleaning up questions with categoryid=0<br />';
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
 * @param $module
 * @return bool
 */
function xoops_module_install_smartfaq($module)
{
    ob_start();

    include_once(XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname') . '/include/functions.php');

    $feedback = ob_get_clean();
    if (method_exists($module, 'setMessage')) {
        $module->setMessage($feedback);
    } else {
        echo $feedback;
    }

    return true;
}
