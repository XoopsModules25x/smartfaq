<?php namespace XoopsModules\Smartfaq;

/**
 * SmartobjectDbupdater class
 *
 * Class performing the database update for the module
 *
 * @package SmartObject
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */

use XoopsModules\Smartfaq;

class SmartobjectDbupdater
{
    /**
     * SmartobjectDbupdater constructor.
     */
    public function __construct()
    {
    }

    /**
     * Use to execute a general query
     *
     * @param string $query   query that will be executed
     * @param string $goodmsg message displayed on success
     * @param string $badmsg  message displayed on error
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function runQuery($query, $goodmsg, $badmsg)
    {
        global $xoopsDB;
        $ret = $xoopsDB->queryF($query);
        if (!$ret) {
            echo "&nbsp;&nbsp;$badmsg<br>";

            return false;
        } else {
            echo "&nbsp;&nbsp;$goodmsg<br>";

            return true;
        }
    }

    /**
     * Use to rename a table
     *
     * @param string $from name of the table to rename
     * @param string $to   new name of the renamed table
     *
     * @return bool true if success, false if an error occured
     */
    public function renameTable($from, $to)
    {
        global $xoopsDB;

        $from = $xoopsDB->prefix($from);
        $to   = $xoopsDB->prefix($to);

        $query = sprintf('ALTER TABLE `%s` RENAME %s', $from, $to);
        $ret   = $xoopsDB->queryF($query);
        if (!$ret) {
            echo '&nbsp;&nbsp;' . sprintf(_SDU_MSG_RENAME_TABLE_ERR, $from) . '<br>';

            return false;
        } else {
            echo '&nbsp;&nbsp;' . sprintf(_SDU_MSG_RENAME_TABLE, $from, $to) . '<br>';

            return true;
        }
    }

    /**
     * Use to update a table
     *
     * @param Smartfaq\SmartDbTable $table {@link SmartDbTable} that will be updated
     *
     * @see SmartDbTable
     *
     * @return bool true if success, false if an error occured
     */
    public function updateTable($table)
    {
        global $xoopsDB;

        $ret = true;

        // If table has a structure, create the table
        if ($table->getStructure()) {
            $ret = $table->createTable() && $ret;
        }

        // If table is flag for drop, drop it
        if ($table->getFlagForDrop()) {
            $ret = $table->dropTable() && $ret;
        }

        // If table has data, insert it
        if ($table->getData()) {
            $ret = $table->addData() && $ret;
        }

        // If table has new fields to be added, add them
        if ($table->getNewFields()) {
            $ret = $table->addNewFields() && $ret;
        }

        // If table has altered field, alter the table
        if ($table->getAlteredFields()) {
            $ret = $table->alterTable() && $ret;
        }

        // If table has updated field values, update the table
        if ($table->getUpdatedFields()) {
            $ret = $table->updateFieldsValues($table) && $ret;
        }

        // If table has dropped field, alter the table
        if ($table->getDroppedFields()) {
            $ret = $table->dropFields($table) && $ret;
        }
        //felix
        // If table has updated field values, update the table
        if ($table->getUpdatedWhere()) {
            $ret = $table->UpdateWhereValues($table) && $ret;
        }

        return $ret;
    }
}
