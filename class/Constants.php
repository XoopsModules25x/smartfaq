<?php namespace XoopsModules\Smartfaq;

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html
 * @package
 * @since
 * @author     XOOPS Development Team
 */

//defined('XOOPS_ROOT_PATH' || die('Restricted access';

/**
 * class Constants
 */
class Constants
{
    /**#@+
     * Constant definition
     */

    // Answers status
    const SF_AN_STATUS_NOTSET = -1;
    const SF_AN_STATUS_PROPOSED = 0;
    const SF_AN_STATUS_APPROVED = 1;
    const SF_AN_STATUS_REJECTED = 2;

    // Notification Events
    const SF_NOT_ANSWER_APPROVED = 3;
    const SF_NOT_ANSWER_REJECTED = 4;

    // FAQ status
    const SF_STATUS_NOTSET = -1;
    const SF_STATUS_ALL = 0;
    const SF_STATUS_ASKED = 1;
    const SF_STATUS_OPENED = 2;
    const SF_STATUS_ANSWERED = 3;
    const SF_STATUS_SUBMITTED = 4;
    const SF_STATUS_PUBLISHED = 5;
    const SF_STATUS_NEW_ANSWER = 6;
    const SF_STATUS_OFFLINE = 7;
    const SF_STATUS_REJECTED_QUESTION = 8;
    const SF_STATUS_REJECTED_SMARTFAQ = 9;

    // Notification Events
    const SF_NOT_CATEGORY_CREATED = 1;
    const SF_NOT_FAQ_SUBMITTED = 2;
    const SF_NOT_FAQ_PUBLISHED = 3;
    const SF_NOT_FAQ_REJECTED = 4;
    const SF_NOT_QUESTION_SUBMITTED = 5;
    const SF_NOT_QUESTION_PUBLISHED = 6;
    const SF_NOT_NEW_ANSWER_PROPOSED = 7;
    const SF_NOT_NEW_ANSWER_PUBLISHED = 8;

    /**#@-*/
}
