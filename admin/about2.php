<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

include_once __DIR__ . '/admin_header.php';
$myts = MyTextSanitizer::getInstance();

global $xoopsModule;

xoops_cp_header();

$moduleHandler = xoops_getHandler('module');
$versioninfo   =& $moduleHandler->get($xoopsModule->getVar('mid'));

/*  Centered heading
echo "<br />";
echo "<table width='100%'>";
echo "<tr>";
echo "<td align = 'center'>";
echo "<img src='".XOOPS_URL."/modules/smartfaq/".$versioninfo->getInfo('image')."' alt='' align='center'/></a>";
echo "<div style='margin-top: 10px; color: #33538e; margin-bottom: 4px; font-size: 18px; line-height: 18px; font-weight: bold; display: block;'>" . $versioninfo->getInfo('name') . " version " . $versioninfo->getInfo('version') . "</div>";
if ( $versioninfo->getInfo('author_realname') != '') {
$author_name = $versioninfo->getInfo('author') . " (" . $versioninfo->getInfo('author_realname') . ")";
} else {
$author_name = $versioninfo->getInfo('author');
}

echo "<div style = 'line-height: 16px; font-weight: bold; display: block;'>" . _AM_SF_BY . " " .$author_name;
echo "</div>";
echo "<div style = 'line-height: 16px; display: block;'>" . $versioninfo->getInfo('license') . "</div><br></>\n";

echo "</td>";
echo "</tr>";
echo "</table>";
*/
// Left headings...
echo "<img src='" . XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/' . $versioninfo->getInfo('image') . "' alt='' hspace='0' vspace='0' align='left' style='margin-right: 10px;'/></a>";
echo "<div style='margin-top: 10px; color: #33538e; margin-bottom: 4px; font-size: 18px; line-height: 18px; font-weight: bold; display: block;'>" . $versioninfo->getInfo('name') . ' version ' . $versioninfo->getInfo('version') . ' (' . $versioninfo->getInfo('status_version') . ')</div>';
if ($versioninfo->getInfo('author_realname') != '') {
    $author_name = $versioninfo->getInfo('author') . ' (' . $versioninfo->getInfo('author_realname') . ')';
} else {
    $author_name = $versioninfo->getInfo('author');
}

echo "<div style = 'line-height: 16px; font-weight: bold; display: block;'>" . _AM_SF_BY . ' ' . $author_name;
echo '</div>';
echo "<div style = 'line-height: 16px; display: block;'>" . $versioninfo->getInfo('license') . "</div>\n";

// Developers Information
echo "<br /><table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
echo '<tr>';
echo "<td colspan='2' class='bg3' align='left'><b>" . _MI_SF_AUTHOR_INFO . '</b></td>';
echo '</tr>';

if ($versioninfo->getInfo('developer_lead') != '') {
    echo '<tr>';
    echo "<td class='head' width = '150px' align='left'>" . _MI_SF_DEVELOPER_LEAD . '</td>';
    echo "<td class='even' align='left'>" . $versioninfo->getInfo('developer_lead') . '</td>';
    echo '</tr>';
}
if ($versioninfo->getInfo('developer_contributor') != '') {
    echo '<tr>';
    echo "<td class='head' width = '150px' align='left'>" . _MI_SF_DEVELOPER_CONTRIBUTOR . '</td>';
    echo "<td class='even' align='left'>" . $versioninfo->getInfo('developer_contributor') . '</td>';
    echo '</tr>';
}
if ($versioninfo->getInfo('developer_website_url') != '') {
    echo '<tr>';
    echo "<td class='head' width = '150px' align='left'>" . _MI_SF_DEVELOPER_WEBSITE . '</td>';
    echo "<td class='even' align='left'><a href='" . $versioninfo->getInfo('developer_website_url') . "' target='blank'>" . $versioninfo->getInfo('developer_website_name') . '</a></td>';
    echo '</tr>';
}
if ($versioninfo->getInfo('developer_email') != '') {
    echo '<tr>';
    echo "<td class='head' width = '150px' align='left'>" . _MI_SF_DEVELOPER_EMAIL . '</td>';
    echo "<td class='even' align='left'><a href='mailto:" . $versioninfo->getInfo('developer_email') . "'>" . $versioninfo->getInfo('developer_email') . '</a></td>';
    echo '</tr>';
}

echo '</table>';
echo "<br />\n";
// Module Developpment information
echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
echo '<tr>';
echo "<td colspan='2' class='bg3' align='left'><b>" . _MI_SF_MODULE_INFO . '</b></td>';
echo '</tr>';

if ($versioninfo->getInfo('date') != '') {
    echo '<tr>';
    echo "<td class='head' width = '200' align='left'>" . _MI_SF_MODULE_RELEASE_DATE . '</td>';
    echo "<td class='even' align='left'>" . $versioninfo->getInfo('date') . '</td>';
    echo '</tr>';
}

if ($versioninfo->getInfo('status') != '') {
    echo '<tr>';
    echo "<td class='head' width = '200' align='left'>" . _MI_SF_MODULE_STATUS . '</td>';
    echo "<td class='even' align='left'>" . $versioninfo->getInfo('status') . '</td>';
    echo '</tr>';
}

if ($versioninfo->getInfo('demo_site_url') != '') {
    echo '<tr>';
    echo "<td class='head' align='left'>" . _MI_SF_MODULE_DEMO . '</td>';
    echo "<td class='even' align='left'><a href='" . $versioninfo->getInfo('demo_site_url') . "' target='blank'>" . $versioninfo->getInfo('demo_site_name') . '</a></td>';
    echo '</tr>';
}

if ($versioninfo->getInfo('support_site_url') != '') {
    echo '<tr>';
    echo "<td class='head' align='left'>" . _MI_SF_MODULE_SUPPORT . '</td>';
    echo "<td class='even' align='left'><a href='" . $versioninfo->getInfo('support_site_url') . "' target='blank'>" . $versioninfo->getInfo('support_site_name') . '</a></td>';
    echo '</tr>';
}

if ($versioninfo->getInfo('submit_bug') != '') {
    echo '<tr>';
    echo "<td class='head' align='left'>" . _MI_SF_MODULE_BUG . '</td>';
    echo "<td class='even' align='left'><a href='" . $versioninfo->getInfo('submit_bug') . "' target='blank'>" . 'Submit a Bug in SmartFAQ Bug Tracker' . '</a></td>';
    echo '</tr>';
}
if ($versioninfo->getInfo('submit_feature') != '') {
    echo '<tr>';
    echo "<td class='head' align='left'>" . _MI_SF_MODULE_FEATURE . '</td>';
    echo "<td class='even' align='left'><a href='" . $versioninfo->getInfo('submit_feature') . "' target='blank'>" . 'Request a feature in the SmartFAQ Feature Tracker' . '</a></td>';
    echo '</tr>';
}

echo '</table>';
// Warning
if ($versioninfo->getInfo('warning') != '') {
    echo "<br />\n";
    echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
    echo '<tr>';
    echo "<td class='bg3' align='left'><b>" . _MI_SF_MODULE_DISCLAIMER . '</b></td>';
    echo '</tr>';

    echo '<tr>';
    echo "<td class='even' align='left'>" . $versioninfo->getInfo('warning') . '</td>';
    echo '</tr>';

    echo '</table>';
}
// Author's note
if ($versioninfo->getInfo('author_word') != '') {
    echo "<br />\n";
    echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
    echo '<tr>';
    echo "<td class='bg3' align='left'><b>" . _MI_SF_AUTHOR_WORD . '</b></td>';
    echo '</tr>';

    echo '<tr>';
    echo "<td class='even' align='left'>" . $versioninfo->getInfo('author_word') . '</td>';
    echo '</tr>';

    echo '</table>';
}

// Version History
if ($versioninfo->getInfo('version_history') != '') {
    echo "<br />\n";
    echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
    echo '<tr>';
    echo "<td class='bg3' align='left'><b>" . _MI_SF_VERSION_HISTORY . '</b></td>';
    echo '</tr>';

    echo '<tr>';
    echo "<td class='even' align='left'>" . $versioninfo->getInfo('version_history') . '</td>';
    echo '</tr>';

    echo '</table>';
}

echo '<br />';
//$modfooter = sf_modFooter();
//echo "<div align='center'>" . $modfooter . "</div>";
//xoops_cp_footer();
include_once __DIR__ . '/admin_footer.php';
