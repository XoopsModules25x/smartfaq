<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModuleConfig, $xoopsModule;

echo "<br />\n";
if (!isset($categoryid) || ($categoryid < 1)) {
    $faqs_title         = _AM_SF_PUBLISHEDFAQS;
    $faqs_info          = _AM_SF_PUBLISHED_DSC;
    $sel_cat            = -1;
    $pagenav_extra_args = '';
} else {
    $faqs_title         = _AM_SF_PUBLISHEDFAQS_CAT;
    $faqs_info          = _AM_SF_PUBLISHED_CAT_DSC;
    $sel_cat            = $categoryid;
    $pagenav_extra_args = "op=mod&categoryid=$categoryid";
}

sf_collapsableBar('toptable', 'toptableicon');

echo "<img id='toptableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt='' /></a>&nbsp;" . $faqs_title . '</h3>';
echo "<div id='toptable'>";
echo "<span style=\"color: #567; margin: 3px 0 12px 0; font-size: small; display: block; \">" . $faqs_info . '</span>';

// Get the total number of published FAQs
$totalfaqs = $faqHandler->getFaqsCount($sel_cat, array(_SF_STATUS_PUBLISHED, _SF_STATUS_NEW_ANSWER));

// creating the FAQ objects that are published
$faqsObj         = $faqHandler->getAllPublished($xoopsModuleConfig['perpage'], $startfaq, $sel_cat);
$totalFaqsOnPage = count($faqsObj);
$allcats         = $categoryHandler->getObjects(null, true);
echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
echo '<tr>';
echo "<th width='40' class='bg3' align='center'><b>" . _AM_SF_ARTID . '</b></td>';
echo "<th width='20%' class='bg3' align='left'><b>" . _AM_SF_ARTCOLNAME . '</b></td>';
echo "<th class='bg3' align='left'><b>" . _AM_SF_QUESTION . '</b></td>';

echo "<th width='90' class='bg3' align='center'><b>" . _AM_SF_ASKED . '</b></td>';
echo "<th width='90' class='bg3' align='center'><b>" . _AM_SF_ANSWERED . '</b></td>';

echo "<th width='90' class='bg3' align='center'><b>" . _AM_SF_CREATED . '</b></td>';
echo "<th width='60' class='bg3' align='center'><b>" . _AM_SF_ACTION . '</b></td>';
echo '</tr>';
if ($totalfaqs > 0) {
    global $pathIcon16, $smartModuleConfig;
    for ($i = 0; $i < $totalFaqsOnPage; ++$i) {
        $categoryObj =& $allcats[$faqsObj[$i]->categoryid()];
        $modify      = "<a href='faq.php?op=mod&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . _AM_SF_EDITART . "' alt='" . _AM_SF_EDITART . "' /></a>";
        $delete      = "<a href='faq.php?op=del&amp;faqid=" . $faqsObj[$i]->faqid() . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . _AM_SF_EDITART . "' alt='" . _AM_SF_DELETEART . "'/></a>";

        //adding name of the Question Submitter
        $requester = sf_getLinkedUnameFromId($faqsObj[$i]->uid(), $smartModuleConfig['userealname']);

        //adding name of the Answer Submitter

        $answerHandler = sf_gethandler('answer');

        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('faqid', $faqsObj[$i]->faqid()));
        $criteria->add(new Criteria('status', true));

        $answerObjects = $answerHandler->getObjects($criteria, true);

        foreach (array_keys($answerObjects) as $j) {
            $answerObj = $answerObjects[$j];
        }

        if (isset($answerObj->vars['uid']['value'])) {
            $answerSubmitterID = $answerObj->vars['uid']['value'];

            $answerSubmitter = sf_getLinkedUnameFromId($answerSubmitterID, $smartModuleConfig['userealname']);
        } else {
            $answerSubmitter = '--------';
        }

        echo '<tr>';
        echo "<td class='head' align='center'>" . $faqsObj[$i]->faqid() . '</td>';
        echo "<td class='even' align='left'>" . $categoryObj->name() . '</td>';
        echo "<td class='even' align='left'><a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/faq.php?faqid=' . $faqsObj[$i]->faqid() . "'>" . $faqsObj[$i]->question(100) . '</a></td>';

        echo "<td class='even' align='center'>" . $requester . '</td>';
        echo "<td class='even' align='center'>" . $answerSubmitter . '</td>';

        echo "<td class='even' align='center'>" . $faqsObj[$i]->datesub('s') . '</td>';
        echo "<td class='even' align='center'> $modify $delete </td>";
        echo '</tr>';
    }
} else {
    $faqid = -1;
    echo '<tr>';
    echo "<td class='head' align='center' colspan= '7'>" . _AM_SF_NOFAQS . '</td>';
    echo '</tr>';
}
echo "</table>\n";
echo "<br />\n";

$pagenav = new XoopsPageNav($totalfaqs, $xoopsModuleConfig['perpage'], $startfaq, 'startfaq', $pagenav_extra_args);
echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';
echo '</div>';
