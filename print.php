<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

include_once __DIR__ . '/header.php';

$faqid = isset($_GET['faqid']) ? (int)$_GET['faqid'] : 0;

if ($faqid == 0) {
    redirect_header('javascript:history.go(-1)', 1, _MD_SF_NOFAQSELECTED);
}

// Creating the FAQ handler object
$faqHandler = sf_gethandler('faq');

// Creating the FAQ object for the selected FAQ
$faqObj = new sfFaq($faqid);

// If the selected FAQ was not found, exit
if ($faqObj->notLoaded()) {
    redirect_header('javascript:history.go(-1)', 1, _MD_SF_NOFAQSELECTED);
}

// Creating the category object that holds the selected FAQ
$categoryObj = $faqObj->category();

// Creating the answer object
$answerObj = $faqObj->answer();

// Check user permissions to access that category of the selected FAQ
if (faqAccessGranted($faqObj) < 0) {
    redirect_header('javascript:history.go(-1)', 1, _NOPERM);
}

global $xoopsConfig, $xoopsDB, $xoopsModule, $myts;

$who_where = $faqObj->getWhoAndWhen();
$comeFrom  = $faqObj->getComeFrom();

echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>\n";
echo "<html>\n<head>\n";
echo '<title>' . _MD_SF_FAQCOMEFROM . ' ' . $xoopsConfig['sitename'] . "</title>\n";
echo "<meta http-equiv='Content-Type' content='text/html; charset=" . _CHARSET . "' />\n";
echo "<meta name='AUTHOR' content='" . $xoopsConfig['sitename'] . "' />\n";
echo "<meta name='COPYRIGHT' content='Copyright (c) 2001 by " . $xoopsConfig['sitename'] . "' />\n";
echo "<meta name='DESCRIPTION' content='" . $xoopsConfig['slogan'] . "' />\n";
echo "<meta name='GENERATOR' content='" . XOOPS_VERSION . "' />\n\n\n";

echo "<body bgcolor='#ffffff' text='#000000' onload='window.print()'>
     <div style='width: 650px; border: 1px solid #000; padding: 20px;'>
     <div style='text-align: center; display: block; margin: 0 0 6px 0;'><img src='" . XOOPS_URL . "/modules/smartfaq/assets/images/logo_module.png' border='0' alt='' /><h2 style='margin: 0;'>" . $faqObj->question() . "</h2></div>
     <div align='center'>" . $who_where . "</div>
                <div style='text-align: center; display: block; padding-bottom: 12px; margin: 0 0 6px 0; border-bottom: 2px solid #ccc;'></div>
                <div></div>
                <b><p>" . $faqObj->question() . '</p></b>
                <p>' . $answerObj->answer() . "</p>
                <div style='padding-top: 12px; border-top: 2px solid #ccc;'></div>
                <p>" . $comeFrom . '</p>
            </div>
    <br />';

echo '<br />
          </body>
          </html>';
