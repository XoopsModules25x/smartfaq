<?php

/**
* $Id: comment_functions.php,v 1.6 2004/11/20 16:52:33 malanciault Exp $
* Module: SmartFAQ
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

function smartfaq_com_update($faq_id, $total_num)
{
    $db = &XoopsDatabaseFactory::getDatabaseConnection();
    $sql = 'UPDATE ' . $db->prefix('smartfaq_faq') . ' SET comments = ' . $total_num . ' WHERE faqid = ' . $faq_id;
    $db->query($sql);
}

function smartfaq_com_approve(&$comment)
{
    // notification mail here
}
