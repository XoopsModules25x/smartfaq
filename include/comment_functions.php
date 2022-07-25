<?php declare(strict_types=1);

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 * @param $faq_id
 * @param $total_num
 */
function smartfaq_com_update($faq_id, $total_num): void
{
    $db  = \XoopsDatabaseFactory::getDatabaseConnection();
    $sql = 'UPDATE ' . $db->prefix('smartfaq_faq') . ' SET comments = ' . $total_num . ' WHERE faqid = ' . $faq_id;
    $db->query($sql);
}

/**
 * @param $comment
 */
function smartfaq_com_approve(&$comment): void
{
    // notification mail here
}
