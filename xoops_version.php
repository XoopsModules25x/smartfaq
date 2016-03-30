<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */
// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

$modversion['name']        = _MI_SF_MD_NAME;
$modversion['version']     = 1.12;
$modversion['description'] = _MI_SF_MD_DESC;
$modversion['author']      = 'The SmartFactory | Xuups';
$modversion['credits']     = 'w4z004, hsalazar, Carnuke, Mariuss, Mithrandir, phppp, Predator, GIJOE, outch, rowdie, Xvitry, Xavier & Catzwolf, trabis';
$modversion['help']        = 'page=help';
$modversion['license']     = 'GNU GPL 2.0 or later';
$modversion['license_url'] = 'www.gnu.org/licenses/gpl-2.0.html';
$modversion['official']    = 1; //1 indicates supported by XOOPS Dev Team, 0 means 3rd party supported
$modversion['image']       = 'assets/images/logo_module.png';
$modversion['dirname']     = basename(__DIR__);

$modversion['dirmoduleadmin'] = '/Frameworks/moduleclasses/moduleadmin';
$modversion['icons16']        = '../../Frameworks/moduleclasses/icons/16';
$modversion['icons32']        = '../../Frameworks/moduleclasses/icons/32';
//about
$modversion['module_status']       = 'RC 1';
$modversion['release_date']        = '2016/03/28';
$modversion['release_file']        = XOOPS_URL . '/modules/' . $modversion['dirname'] . '/docs/changelog.txt';
$modversion['module_website_url']  = 'www.xoops.org';
$modversion['module_website_name'] = 'XOOPS';
$modversion['min_php']             = '5.5';
$modversion['min_xoops']           = '2.5.8';
$modversion['min_admin']           = '1.1';
$modversion['min_db']              = array(
    'mysql'  => '5.0.7',
    'mysqli' => '5.0.7'
);

// Added by marcan for the About page in admin section
$modversion['developer_lead']         = 'marcan [Marc-André Lanciault]';
$modversion['developer_contributor']  = 'w4z004, hsalazar, Carnuke, Mariuss, Mithrandir, phppp, Predator, GIJOE, outch, rowdie, Xvitry, Xavier & Catzwolf, trabis';
$modversion['developer_website_url']  = 'http://www.xuups.com';
$modversion['developer_website_name'] = 'Xuups';
$modversion['developer_email']        = 'lusopoemas@gmail.com';
$modversion['status_version']         = 'Final';
$modversion['status']                 = 'Final';
$modversion['date']                   = '2010-09-20';

$modversion['warning'] = _MI_SF_WARNING_FINAL;

$modversion['demo_site_url']     = 'http://www.xuups.com/modules/smartfaq';
$modversion['demo_site_name']    = 'Xuups';
$modversion['support_site_url']  = 'http://www.xuups.com';
$modversion['support_site_name'] = 'Xuups';
$modversion['submit_bug']        = 'http://www.xuups.com/modules/xhelp';
$modversion['submit_feature']    = 'http://www.xuups.com/modules/xhelp';

$modversion['author_word'] = "
<B>SmartFAQ</B> is the result of multiple ideas from multiple people and a work of outstanding
collaboration. It all began with Herko talking to me about a 'contextual help system' for XOOPS,
inspired by the one on the Developers Forge. I found that idea more than brilliant, so I decided
to start coding the thing !
<BR><BR>As I was new in the developers world, I had to look for quality ideas that had already been
established and represented the best in Xoops programming. I chose the Soapbox module by hsalazar
(Horacio Salazar) which I had found absolutely amazing ! So, many thanks to Horacio, as his work offered
considerable inspiration. I would also like to thank him for helping me establish the workflow of
the SmartFAQ module, as well as for helping me in all the development process.
<BR><BR>When about half the coding was done, I met a special Xoopser who would become an important
player in this project : w4z004 (Sergio Kohl). Many thanks to you w4z004, as you multiplied many
times the possibilities and potential of this module. By testing it over and over again, by
submitting the code to be checked by security experts and other advanced developers, by suggesting
more features, by encouraging me when things were not going the way I wanted and by doing a thousand
other things for this project. Thank you, thank you, thank you !
<BR><BR>Special thanks also to Mithrandir (Jan Pedersen) for all the 'little' answers to my 'little'
questions (lol). You made my life so much easier by helping me see things more clearly !
<BR><BR>I would also like to thank Mariuss (Marius Scurtescu) for adapting <B>FAQ for New Xoopsers
</B> for SmartFAQ, for developing the import scripts, for teaching me the CVS (lol) as well as for
suggesting a lot of interesting improvements along the way.
<BR><BR>Another special thank-you to Carnuke (Richard Strauss) for writing such impressive
documentation for this module. You have now set up a new quality standard for XOOPS module
documentation. I'm confident that all the Xoopsers of the world are gratefull for this :-) !
<BR><BR>Finally, thanks to all the people who made this module possible : Herko, phppp, Solo71,
Yoyo2021, Christian, Hervé and so many others ! Also, a final thank to Zabou who has been
really understanding during all the hours I spent behind my laptop developing SmartFAQ.
<BR><BR>So I guess this is it, I could thank the Academy, my Mother and Father but that would be
pushing it I think ! (lol)
<BR><BR>Enjoy <b>SmartFAQ</b> (by marcan)!
";

// Admin things
$modversion['hasAdmin']    = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/menu.php';
// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
// Tables created by sql file (without prefix!)
$modversion['tables'][0] = 'smartfaq_categories';
$modversion['tables'][1] = 'smartfaq_faq';
$modversion['tables'][2] = 'smartfaq_answers';
//$modversion['tables'][3] = "smartfaq_moderators";
// Search
$modversion['hasSearch']      = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'smartfaq_search';
// Menu
$modversion['hasMain'] = 1;

$modversion['onInstall'] = 'include/onupdate.inc.php';
$modversion['onUpdate']  = 'include/onupdate.inc.php';

global $xoopsModule;

if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $modversion['dirname']) {
    global $xoopsModuleConfig, $xoopsUser;

    $isAdmin = false;
    if (!empty($xoopsUser) && is_object($xoopsModule)) {
        $isAdmin = $xoopsUser->isAdmin($xoopsModule->getVar('mid'));
    }

    if ($smartModule = $xoopsModule) {
        $smartConfig = $xoopsModuleConfig;
        // Add the Submit new faq button
        if ($isAdmin || (isset($smartConfig['allowsubmit']) && $smartConfig['allowsubmit'] == 1 && (is_object($xoopsUser) || (isset($smartConfig['anonpost']) && $smartConfig['anonpost'] == 1)))) {
            $modversion['sub'][1]['name'] = _MI_SF_SUB_SMNAME1;
            $modversion['sub'][1]['url']  = 'submit.php?op=add';
        }
        // Add the Request new faq
        if ($isAdmin || (isset($smartConfig['allowrequest']) && $smartConfig['allowrequest'] == 1 && (is_object($xoopsUser) || (isset($smartConfig['anonpost']) && $smartConfig['anonpost'] == 1)))) {
            $modversion['sub'][2]['name'] = _MI_SF_SUB_SMNAME2;
            $modversion['sub'][2]['url']  = 'request.php?op=add';
        }

        include_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

        // Creating the FAQ handler object
        $faqHandler = sf_gethandler('faq');

        if ($faqHandler->getFaqsCount(-1, _SF_STATUS_OPENED) > 0) {
            $modversion['sub'][3]['name'] = _MI_SF_SUB_SMNAME3;
            $modversion['sub'][3]['url']  = 'open_index.php';
        }
    }
}

$modversion['blocks'][1]['file']        = 'faqs_new.php';
$modversion['blocks'][1]['name']        = _MI_SF_ARTSNEW;
$modversion['blocks'][1]['description'] = 'Shows new faqs';
$modversion['blocks'][1]['show_func']   = 'b_faqs_new_show';
$modversion['blocks'][1]['edit_func']   = 'b_faqs_new_edit';
$modversion['blocks'][1]['options']     = '0|datesub|5|65|1';
$modversion['blocks'][1]['template']    = 'faqs_new.tpl';

$modversion['blocks'][2]['file']        = 'faqs_recent.php';
$modversion['blocks'][2]['name']        = _MI_SF_RECENTFAQS;
$modversion['blocks'][2]['description'] = 'Shows recent faqs';
$modversion['blocks'][2]['show_func']   = 'b_faqs_recent_show';
$modversion['blocks'][2]['edit_func']   = 'b_faqs_recent_edit';
$modversion['blocks'][2]['options']     = '0|datesub|5|65';
$modversion['blocks'][2]['template']    = 'faqs_recent.tpl';

$modversion['blocks'][3]['file']        = 'faqs_context.php';
$modversion['blocks'][3]['name']        = _MI_SF_ARTSCONTEXT;
$modversion['blocks'][3]['description'] = 'Shows contextual faqs';
$modversion['blocks'][3]['show_func']   = 'b_faqs_context_show';
$modversion['blocks'][3]['edit_func']   = 'b_faqs_context_edit';
$modversion['blocks'][3]['options']     = '5';
$modversion['blocks'][3]['template']    = 'faqs_context.tpl';

$modversion['blocks'][4]['file']        = 'faqs_random_how.php';
$modversion['blocks'][4]['name']        = _MI_SF_ARTSRANDOM_HOW;
$modversion['blocks'][4]['description'] = "Shows a random 'How do I' faq";
$modversion['blocks'][4]['show_func']   = 'b_faqs_random_how_show';
$modversion['blocks'][4]['template']    = 'faqs_random_how.tpl';

$modversion['blocks'][5]['file']        = 'faqs_random_diduno.php';
$modversion['blocks'][5]['name']        = _MI_SF_ARTSRANDOM_DIDUNO;
$modversion['blocks'][5]['description'] = "Shows a random 'Did You Know' faq";
$modversion['blocks'][5]['show_func']   = 'b_faqs_random_diduno_show';
$modversion['blocks'][5]['template']    = 'faqs_random_diduno.tpl';

$modversion['blocks'][6]['file']        = 'faqs_random_faq.php';
$modversion['blocks'][6]['name']        = _MI_SF_ARTSRANDOM_FAQ;
$modversion['blocks'][6]['description'] = "Shows a random 'faq' faq";
$modversion['blocks'][6]['show_func']   = 'b_faqs_random_faq_show';
$modversion['blocks'][6]['template']    = 'faqs_random_faq.tpl';

$modversion['blocks'][7]['file']        = 'faqs_recent_questions.php';
$modversion['blocks'][7]['name']        = _MI_SF_RECENT_QUESTIONS;
$modversion['blocks'][7]['description'] = 'Shows recent questions';
$modversion['blocks'][7]['show_func']   = 'b_faqs_recent_questions_show';
$modversion['blocks'][7]['edit_func']   = 'b_faqs_recent_questions_edit';
$modversion['blocks'][7]['options']     = '0|datesub|5|65|1';
$modversion['blocks'][7]['template']    = 'faqs_recent_questions.tpl';

$modversion['blocks'][8]['file']        = 'faqs_most_viewed.php';
$modversion['blocks'][8]['name']        = _MI_SF_MOST_VIEWED;
$modversion['blocks'][8]['description'] = 'Shows most viewed Q&A';
$modversion['blocks'][8]['show_func']   = 'b_faqs_most_viewed_show';
$modversion['blocks'][8]['edit_func']   = 'b_faqs_most_viewed_edit';
$modversion['blocks'][8]['options']     = '0|5|65';
$modversion['blocks'][8]['template']    = 'faqs_most_viewed.tpl';

// Templates
$modversion['templates'][1]['file']        = 'smartfaq_singlefaq.tpl';
$modversion['templates'][1]['description'] = 'Display a single FAQ';

$modversion['templates'][2]['file']        = 'smartfaq_lastfaqs.tpl';
$modversion['templates'][2]['description'] = 'Display the last faqs';

$modversion['templates'][3]['file']        = 'smartfaq_category.tpl';
$modversion['templates'][3]['description'] = 'Display a category';

$modversion['templates'][4]['file']        = 'smartfaq_index.tpl';
$modversion['templates'][4]['description'] = 'Display index';

$modversion['templates'][5]['file']        = 'smartfaq_faq.tpl';
$modversion['templates'][5]['description'] = 'Display faq';

$modversion['templates'][6]['file']        = 'smartfaq_submit.tpl';
$modversion['templates'][6]['description'] = 'Form to submit request or answer a question';

// Config Settings (only for modules that need config settings generated automatically)

$i                                       = 1;
$modversion['config'][$i]['name']        = 'allowsubmit';
$modversion['config'][$i]['title']       = '_MI_SF_ALLOWSUBMIT';
$modversion['config'][$i]['description'] = '_MI_SF_ALLOWSUBMITDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
++$i;
$modversion['config'][$i]['name']        = 'allowrequest';
$modversion['config'][$i]['title']       = '_MI_SF_ALLOWREQUEST';
$modversion['config'][$i]['description'] = '_MI_SF_ALLOWREQUESTDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
++$i;
$modversion['config'][$i]['name']        = 'allownewanswer';
$modversion['config'][$i]['title']       = '_MI_SF_NEWANSWER';
$modversion['config'][$i]['description'] = '_MI_SF_NEWANSWERDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
++$i;
$modversion['config'][$i]['name']        = 'anonpost';
$modversion['config'][$i]['title']       = '_MI_SF_ANONPOST';
$modversion['config'][$i]['description'] = '_MI_SF_ANONPOSTDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 0;

$memberHandler = xoops_getHandler('member');
$groups        = $memberHandler->getGroupList();
++$i;
$modversion['config'][$i]['name']        = 'dateformat';
$modversion['config'][$i]['title']       = '_MI_SF_DATEFORMAT';
$modversion['config'][$i]['description'] = '_MI_SF_DATEFORMATDSC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = 'd-M-Y H:i';
++$i;
$modversion['config'][$i]['name']        = 'displaycollaps';
$modversion['config'][$i]['title']       = '_MI_SF_DISPLAY_COLLAPS';
$modversion['config'][$i]['description'] = '_MI_SF_DISPLAY_COLLAPSDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
++$i;
$modversion['config'][$i]['name']        = 'displaylastfaqs';
$modversion['config'][$i]['title']       = '_MI_SF_DISPLAY_LAST_FAQS';
$modversion['config'][$i]['description'] = '_MI_SF_DISPLAY_LAST_FAQSDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
++$i;
$modversion['config'][$i]['name']        = 'displaytype';
$modversion['config'][$i]['title']       = '_MI_SF_DISPLAYTYPE';
$modversion['config'][$i]['description'] = '_MI_SF_DISPLAYTYPEDSC';
$modversion['config'][$i]['formtype']    = 'select';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['options']     = array(_MI_SF_DISPLAYTYPE_SUMMARY => 'summary', _MI_SF_DISPLAYTYPE_FULL => 'full');
$modversion['config'][$i]['default']     = 'full';
++$i;
$modversion['config'][$i]['name']        = 'displaylastfaq';
$modversion['config'][$i]['title']       = '_MI_SF_DISPLAY_LAST_FAQ';
$modversion['config'][$i]['description'] = '_MI_SF_DISPLAY_LAST_FAQDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
++$i;
$modversion['config'][$i]['name']        = 'lastfaqsize';
$modversion['config'][$i]['title']       = '_MI_SF_LAST_FAQ_SIZE';
$modversion['config'][$i]['description'] = '_MI_SF_LAST_FAQ_SIZEDSC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = '50';
++$i;
$modversion['config'][$i]['name']        = 'questionsize';
$modversion['config'][$i]['title']       = '_MI_SF_QUESTION_SIZE';
$modversion['config'][$i]['description'] = '_MI_SF_QUESTION_SIZEDSC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = '60';
++$i;
$modversion['config'][$i]['name']        = 'displaytopcatdsc';
$modversion['config'][$i]['title']       = '_MI_SF_DISPLAY_TOPCAT_DSC';
$modversion['config'][$i]['description'] = '_MI_SF_DISPLAY_TOPCAT_DSCDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
++$i;
$modversion['config'][$i]['name']        = 'displaysubcatonindex';
$modversion['config'][$i]['title']       = '_MI_SF_DISPLAY_SUBCAT_INDEX';
$modversion['config'][$i]['description'] = '_MI_SF_DISPLAY_SUBCAT_INDEXDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
++$i;
$modversion['config'][$i] ['name']       = 'displaysubcatdsc';
$modversion['config'][$i]['title']       = '_MI_SF_DISPLAY_SBCAT_DSC';
$modversion['config'][$i]['description'] = '_MI_SF_DISPLAY_SBCAT_DSCDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
++$i;
$modversion['config'][$i]['name']        = 'orderbydate';
$modversion['config'][$i]['title']       = '_MI_SF_ORDERBYDATE';
$modversion['config'][$i]['description'] = '_MI_SF_ORDERBYDATEDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 0;
++$i;
$modversion['config'][$i]['name']        = 'display_date_col';
$modversion['config'][$i]['title']       = '_MI_SF_DISPLAY_DATE_COL';
$modversion['config'][$i]['description'] = '_MI_SF_DISPLAY_DATE_COLDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
++$i;
$modversion['config'][$i]['name']        = 'display_hits_col';
$modversion['config'][$i]['title']       = '_MI_SF_DISPLAY_HITS_COL';
$modversion['config'][$i]['description'] = '_MI_SF_DISPLAY_HITS_COLDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
++$i;
$modversion['config'][$i]['name']        = 'useimagenavpage';
$modversion['config'][$i]['title']       = '_MI_SF_USEIMAGENAVPAGE';
$modversion['config'][$i]['description'] = '_MI_SF_USEIMAGENAVPAGEDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 0;
++$i;
$modversion['config'][$i]['name']        = 'globaldisplaycomments';
$modversion['config'][$i]['title']       = '_MI_SF_ALLOWCOMMENTS';
$modversion['config'][$i]['description'] = '_MI_SF_ALLOWCOMMENTSDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
++$i;
$modversion['config'][$i]['name']        = 'adminhits';
$modversion['config'][$i]['title']       = '_MI_SF_ALLOWADMINHITS';
$modversion['config'][$i]['description'] = '_MI_SF_ALLOWADMINHITSDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 0;
++$i;
$modversion['config'][$i]['name']        = 'autoapprove_submitted_faq';
$modversion['config'][$i]['title']       = '_MI_SF_AUTOAPPROVE_SUB_FAQ';
$modversion['config'][$i]['description'] = '_MI_SF_AUTOAPPROVE_SUB_FAQ_DSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 0;
++$i;
$modversion['config'][$i]['name']        = 'autoapprove_request';
$modversion['config'][$i]['title']       = '_MI_SF_AUTOAPPROVE_REQUEST';
$modversion['config'][$i]['description'] = '_MI_SF_AUTOAPPROVE_REQUEST_DSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 0;
++$i;
$modversion['config'][$i]['name']        = 'autoapprove_answer';
$modversion['config'][$i]['title']       = '_MI_SF_AUTOAPPROVE_ANS';
$modversion['config'][$i]['description'] = '_MI_SF_AUTOAPPROVE_ANS_DSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 0;
++$i;
$modversion['config'][$i]['name']        = 'autoapprove_answer_new';
$modversion['config'][$i]['title']       = '_MI_SF_AUTOAPPROVE_ANS_NEW';
$modversion['config'][$i]['description'] = '_MI_SF_AUTOAPPROVE_ANS_NEW_DSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 0;
++$i;
$modversion['config'][$i]['name']        = 'catperpage';
$modversion['config'][$i]['title']       = '_MI_SF_CATPERPAGE';
$modversion['config'][$i]['description'] = '_MI_SF_CATPERPAGEDSC';
$modversion['config'][$i]['formtype']    = 'select';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 5;
$modversion['config'][$i]['options']     = array('5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50);
++$i;
$modversion['config'][$i]['name']        = 'perpage';
$modversion['config'][$i]['title']       = '_MI_SF_PERPAGE';
$modversion['config'][$i]['description'] = '_MI_SF_PERPAGEDSC';
$modversion['config'][$i]['formtype']    = 'select';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 5;
$modversion['config'][$i]['options']     = array('5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50);
++$i;
$modversion['config'][$i]['name']        = 'indexperpage';
$modversion['config'][$i]['title']       = '_MI_SF_PERPAGEINDEX';
$modversion['config'][$i]['description'] = '_MI_SF_PERPAGEINDEXDSC';
$modversion['config'][$i]['formtype']    = 'select';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 5;
$modversion['config'][$i]['options']     = array('5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50);
++$i;
$modversion['config'][$i]['name']        = 'indexwelcomemsg';
$modversion['config'][$i]['title']       = '_MI_SF_INDEXWELCOMEMSG';
$modversion['config'][$i]['description'] = '_MI_SF_INDEXWELCOMEMSGDSC';
$modversion['config'][$i]['formtype']    = 'textarea';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = _MI_SF_INDEXWELCOMEMSGDEF;
++$i;
$modversion['config'][$i]['name']        = 'requestintromsg';
$modversion['config'][$i]['title']       = '_MI_SF_REQUESTINTROMSG';
$modversion['config'][$i]['description'] = '_MI_SF_REQUESTINTROMSGDSC';
$modversion['config'][$i]['formtype']    = 'textarea';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = _MI_SF_REQUESTINTROMSGDEF;
++$i;
$modversion['config'][$i]['name']        = 'openquestionintromsg';
$modversion['config'][$i]['title']       = '_MI_SF_OPENINTROMSG';
$modversion['config'][$i]['description'] = '_MI_SF_OPENINTROMSGDSC';
$modversion['config'][$i]['formtype']    = 'textarea';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = _MI_SF_OPENINTROMSGDEF;
++$i;
$modversion['config'][$i]['name']        = 'userealname';
$modversion['config'][$i]['title']       = '_MI_SF_USEREALNAME';
$modversion['config'][$i]['description'] = '_MI_SF_USEREALNAMEDSC';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 0;

/*
++$i;
$modversion['config'][$i]['name'] = 'moderatorsedit';
$modversion['config'][$i]['title'] = '_MI_SF_MODERATORSEDIT';
$modversion['config'][$i]['description'] = '_MI_SF_MODERATORSEDITDSC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 0;*/
++$i;
$modversion['config'][$i]['name']        = 'helppath_select';
$modversion['config'][$i]['title']       = '_MI_SF_HELP_PATH_SELECT';
$modversion['config'][$i]['description'] = '_MI_SF_HELP_PATH_SELECT_DSC';
$modversion['config'][$i]['formtype']    = 'select';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['options']     = array(_MI_SF_HELP_INSIDE => 'inside', _MI_SF_HELP_CUSTOM => 'custom');
$modversion['config'][$i]['default']     = 'docs.xoops.org';
++$i;
$modversion['config'][$i]['name']        = 'helppath_custom';
$modversion['config'][$i]['title']       = '_MI_SF_HELP_PATH_CUSTOM';
$modversion['config'][$i]['description'] = '_MI_SF_HELP_PATH_CUSTOM_DSC';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = '';
++$i;
xoops_load('XoopsEditorHandler');
$editorHandler = XoopsEditorHandler::getInstance();
$editorList    = array_flip($editorHandler->getList());

$modversion['config'][$i] = array(
    'name'        => 'form_editorOptions',
    'title'       => '_MI_SF_EDITOR',
    'description' => '_MI_SF_EDITORCHOICE',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => $editorList,
    'default'     => 'dhtmltextarea'
);

++$i;
$modversion['config'][$i] = array(
    'name'        => 'form_editorOptionsUser',
    'title'       => '_MI_SF_EDITORUSER',
    'description' => '_MI_SF_EDITORCHOICEUSER',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => $editorList,
    'default'     => 'dhtmltextarea'
);
//mb------------ START ---------------------

define('_MI_SF_SHOTWIDTH2', '<span style="color:#FF0000; font-size:12px;"><b>Upload Files/Images</b></span> ');

$modversion['config'][] = array(
    'name'        => 'logfile',
    'title'       => '_MI_SF_SHOTWIDTH2',
    'description' => '_MI_SF_USERLOG_CONFCAT_LOGFILE_DSC',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'odd'
);

$modversion['config'][] = array(
    'name'        => 'attach_ext',
    'title'       => '_AM_SF_ALLOWED_EXTENSIONS',
    'description' => '_AM_SF_ALLOWED_EXTENSIONS_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'zip|jpg|gif|png'
);

$modversion['config'][] = array(
    'name'        => 'dir_attachments',
    'title'       => '_MI_SF_DIR_ATTACHMENT',
    'description' => '_MI_SF_DIR_ATTACHMENT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'uploads/smartfaq'
);

$modversion['config'][] = array(
    'name'        => 'media_allowed',
    'title'       => '_MI_SF_MEDIA_ENABLE',
    'description' => '_MI_SF_MEDIA_ENABLE_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1
);

$modversion['config'][] = array(
    'name'        => 'path_magick',
    'title'       => '_MI_SF_PATH_MAGICK',
    'description' => '_MI_SF_PATH_MAGICK_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '/usr/bin/X11'
);

$modversion['config'][] = array(
    'name'        => 'path_netpbm',
    'title'       => '_MI_SF_PATH_NETPBM',
    'description' => '_MI_SF_PATH_NETPBM_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '/usr/bin'
);

$modversion['config'][] = array(
    'name'        => 'image_lib',
    'title'       => '_MI_SF_IMAGELIB',
    'description' => '_MI_SF_IMAGELIB_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => array(
        _MI_SF_AUTO   => 0,
        _MI_SF_MAGICK => 1,
        _MI_SF_NETPBM => 2,
        _MI_SF_GD1    => 3,
        _MI_SF_GD2    => 4
    )
);

$modversion['config'][] = array(
    'name'        => 'show_userattach',
    'title'       => '_MI_SF_USERATTACH_ENABLE',
    'description' => '_MI_SF_USERATTACH_ENABLE_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1
);

$modversion['config'][] = array(
    'name'        => 'max_img_width',
    'title'       => '_MI_SF_MAX_IMG_WIDTH',
    'description' => '_MI_SF_MAX_IMG_WIDTH_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 800
);

$modversion['config'][] = array(
    'name'        => 'max_img_height',
    'title'       => '_MI_SF_MAX_IMG_HEIGHT',
    'description' => '_MI_SF_MAX_IMG_HEIGHT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 640
);

$modversion['config'][] = array(
    'name'        => 'max_image_width',
    'title'       => '_MI_SF_MAX_IMAGE_WIDTH',
    'description' => '_MI_SF_MAX_IMAGE_WIDTH_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 150
);

$modversion['config'][] = array(
    'name'        => 'max_image_height',
    'title'       => '_MI_SF_MAX_IMAGE_HEIGHT',
    'description' => '_MI_SF_MAX_IMAGE_HEIGHT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 150
);

$modversion['config'][] = array(
    'name'        => 'max_image_size',
    'title'       => '_MI_SF_MAX_IMAGE_SIZE',
    'description' => '_MI_SF_MAX_IMAGE_SIZE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 1024
);

define('_MI_XDIR_SHOTWIDTH3', '<span style="color:#FF0000; font-size:12px;"><b>Comments/Notifications</b></span> ');

$modversion['config'][] = array(
    'name'        => 'logfile',
    'title'       => '_MI_XDIR_SHOTWIDTH3',
    'description' => '_MI_USERLOG_CONFCAT_LOGFILE_DSC',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'odd'
);

//mb ------------- end --------------------------

// Comments
$modversion['hasComments']          = 1;
$modversion['comments']['itemName'] = 'faqid';
$modversion['comments']['pageName'] = 'faq.php';
// Comment callback functions
$modversion['comments']['callbackFile']        = 'include/comment_functions.php';
$modversion['comments']['callback']['approve'] = 'smartfaq_com_approve';
$modversion['comments']['callback']['update']  = 'smartfaq_com_update';
// Notification
$modversion['hasNotification']             = 1;
$modversion['notification']['lookup_file'] = 'include/notification.inc.php';
$modversion['notification']['lookup_func'] = 'smartfaq_notify_iteminfo';

$modversion['notification']['category'][1]['name']           = 'global_faq';
$modversion['notification']['category'][1]['title']          = _MI_SF_GLOBAL_FAQ_NOTIFY;
$modversion['notification']['category'][1]['description']    = _MI_SF_GLOBAL_FAQ_NOTIFY_DSC;
$modversion['notification']['category'][1]['subscribe_from'] = array('index.php', 'category.php', 'faq.php');

$modversion['notification']['category'][2]['name']           = 'category_faq';
$modversion['notification']['category'][2]['title']          = _MI_SF_CATEGORY_FAQ_NOTIFY;
$modversion['notification']['category'][2]['description']    = _MI_SF_CATEGORY_FAQ_NOTIFY_DSC;
$modversion['notification']['category'][2]['subscribe_from'] = array('index.php', 'category.php', 'faq.php');
$modversion['notification']['category'][2]['item_name']      = 'categoryid';
$modversion['notification']['category'][2]['allow_bookmark'] = 1;

$modversion['notification']['category'][3]['name']           = 'faq';
$modversion['notification']['category'][3]['title']          = _MI_SF_FAQ_NOTIFY;
$modversion['notification']['category'][3]['description']    = _MI_SF_FAQ_NOTIFY_DSC;
$modversion['notification']['category'][3]['subscribe_from'] = array('faq.php');
$modversion['notification']['category'][3]['item_name']      = 'faqid';
$modversion['notification']['category'][3]['allow_bookmark'] = 1;

$modversion['notification']['category'][4]['name']           = 'global_question';
$modversion['notification']['category'][4]['title']          = _MI_SF_GLOBAL_QUESTION_NOTIFY;
$modversion['notification']['category'][4]['description']    = _MI_SF_GLOBAL_QUESTION_NOTIFY_DSC;
$modversion['notification']['category'][4]['subscribe_from'] = array('open_index.php');

$modversion['notification']['category'][5]['name']           = 'category_question';
$modversion['notification']['category'][5]['title']          = _MI_SF_CATEGORY_QUESTION_NOTIFY;
$modversion['notification']['category'][5]['description']    = _MI_SF_CATEGORY_QUESTION_NOTIFY_DSC;
$modversion['notification']['category'][5]['subscribe_from'] = array('open_index.php, open_category.php');

$modversion['notification']['category'][6]['name']           = 'question';
$modversion['notification']['category'][6]['title']          = _MI_SF_QUESTION_NOTIFY;
$modversion['notification']['category'][6]['description']    = _MI_SF_QUESTION_NOTIFY_DSC;
$modversion['notification']['category'][6]['subscribe_from'] = array('open_index.php');

$modversion['notification']['event'][1]['name']          = 'category_created';
$modversion['notification']['event'][1]['category']      = 'global_faq';
$modversion['notification']['event'][1]['title']         = _MI_SF_GLOBAL_FAQ_CATEGORY_CREATED_NOTIFY;
$modversion['notification']['event'][1]['caption']       = _MI_SF_GLOBAL_FAQ_CATEGORY_CREATED_NOTIFY_CAP;
$modversion['notification']['event'][1]['description']   = _MI_SF_GLOBAL_FAQ_CATEGORY_CREATED_NOTIFY_DSC;
$modversion['notification']['event'][1]['mail_template'] = 'global_faq_category_created';
$modversion['notification']['event'][1]['mail_subject']  = _MI_SF_GLOBAL_FAQ_CATEGORY_CREATED_NOTIFY_SBJ;

$modversion['notification']['event'][2]['name']          = 'submitted';
$modversion['notification']['event'][2]['category']      = 'global_faq';
$modversion['notification']['event'][2]['admin_only']    = 1;
$modversion['notification']['event'][2]['title']         = _MI_SF_GLOBAL_FAQ_SUBMITTED_NOTIFY;
$modversion['notification']['event'][2]['caption']       = _MI_SF_GLOBAL_FAQ_SUBMITTED_NOTIFY_CAP;
$modversion['notification']['event'][2]['description']   = _MI_SF_GLOBAL_FAQ_SUBMITTED_NOTIFY_DSC;
$modversion['notification']['event'][2]['mail_template'] = 'global_faq_submitted';
$modversion['notification']['event'][2]['mail_subject']  = _MI_SF_GLOBAL_FAQ_SUBMITTED_NOTIFY_SBJ;

$modversion['notification']['event'][3]['name']          = 'published';
$modversion['notification']['event'][3]['category']      = 'global_faq';
$modversion['notification']['event'][3]['title']         = _MI_SF_GLOBAL_FAQ_PUBLISHED_NOTIFY;
$modversion['notification']['event'][3]['caption']       = _MI_SF_GLOBAL_FAQ_PUBLISHED_NOTIFY_CAP;
$modversion['notification']['event'][3]['description']   = _MI_SF_GLOBAL_FAQ_PUBLISHED_NOTIFY_DSC;
$modversion['notification']['event'][3]['mail_template'] = 'global_faq_published';
$modversion['notification']['event'][3]['mail_subject']  = _MI_SF_GLOBAL_FAQ_PUBLISHED_NOTIFY_SBJ;

$modversion['notification']['event'][4]['name']          = 'answer_proposed';
$modversion['notification']['event'][4]['category']      = 'global_faq';
$modversion['notification']['event'][4]['admin_only']    = 1;
$modversion['notification']['event'][4]['title']         = _MI_SF_GLOBAL_FAQ_ANSWER_PROPOSED_NOTIFY;
$modversion['notification']['event'][4]['caption']       = _MI_SF_GLOBAL_FAQ_ANSWER_PROPOSED_NOTIFY_CAP;
$modversion['notification']['event'][4]['description']   = _MI_SF_GLOBAL_FAQ_ANSWER_PROPOSED_NOTIFY_DSC;
$modversion['notification']['event'][4]['mail_template'] = 'global_faq_answer_proposed';
$modversion['notification']['event'][4]['mail_subject']  = _MI_SF_GLOBAL_FAQ_ANSWER_PROPOSED_NOTIFY_SBJ;

$modversion['notification']['event'][5]['name']          = 'answer_published';
$modversion['notification']['event'][5]['category']      = 'global_faq';
$modversion['notification']['event'][5]['title']         = _MI_SF_GLOBAL_FAQ_ANSWER_PUBLISHED_NOTIFY;
$modversion['notification']['event'][5]['caption']       = _MI_SF_GLOBAL_FAQ_ANSWER_PUBLISHED_NOTIFY_CAP;
$modversion['notification']['event'][5]['description']   = _MI_SF_GLOBAL_FAQ_ANSWER_PUBLISHED_NOTIFY_DSC;
$modversion['notification']['event'][5]['mail_template'] = 'global_faq_answer_published';
$modversion['notification']['event'][5]['mail_subject']  = _MI_SF_GLOBAL_FAQ_ANSWER_PUBLISHED_NOTIFY_SBJ;

$modversion['notification']['event'][6]['name']          = 'submitted';
$modversion['notification']['event'][6]['category']      = 'category_faq';
$modversion['notification']['event'][6]['admin_only']    = 1;
$modversion['notification']['event'][6]['title']         = _MI_SF_CATEGORY_FAQ_SUBMITTED_NOTIFY;
$modversion['notification']['event'][6]['caption']       = _MI_SF_CATEGORY_FAQ_SUBMITTED_NOTIFY_CAP;
$modversion['notification']['event'][6]['description']   = _MI_SF_CATEGORY_FAQ_SUBMITTED_NOTIFY_DSC;
$modversion['notification']['event'][6]['mail_template'] = 'category_faq_submitted';
$modversion['notification']['event'][6]['mail_subject']  = _MI_SF_CATEGORY_FAQ_SUBMITTED_NOTIFY_SBJ;

$modversion['notification']['event'][7]['name']          = 'published';
$modversion['notification']['event'][7]['category']      = 'category_faq';
$modversion['notification']['event'][7]['title']         = _MI_SF_CATEGORY_FAQ_PUBLISHED_NOTIFY;
$modversion['notification']['event'][7]['caption']       = _MI_SF_CATEGORY_FAQ_PUBLISHED_NOTIFY_CAP;
$modversion['notification']['event'][7]['description']   = _MI_SF_CATEGORY_FAQ_PUBLISHED_NOTIFY_DSC;
$modversion['notification']['event'][7]['mail_template'] = 'category_faq_published';
$modversion['notification']['event'][7]['mail_subject']  = _MI_SF_CATEGORY_FAQ_PUBLISHED_NOTIFY_SBJ;

$modversion['notification']['event'][8]['name']          = 'answer_proposed';
$modversion['notification']['event'][8]['category']      = 'category_faq';
$modversion['notification']['event'][8]['admin_only']    = 1;
$modversion['notification']['event'][8]['title']         = _MI_SF_CATEGORY_FAQ_ANSWER_PROPOSED_NOTIFY;
$modversion['notification']['event'][8]['caption']       = _MI_SF_CATEGORY_FAQ_ANSWER_PROPOSED_NOTIFY_CAP;
$modversion['notification']['event'][8]['description']   = _MI_SF_CATEGORY_FAQ_ANSWER_PROPOSED_NOTIFY_DSC;
$modversion['notification']['event'][8]['mail_template'] = 'category_faq_answer_proposed';
$modversion['notification']['event'][8]['mail_subject']  = _MI_SF_CATEGORY_FAQ_ANSWER_PROPOSED_NOTIFY_SBJ;

$modversion['notification']['event'][9]['name']          = 'answer_published';
$modversion['notification']['event'][9]['category']      = 'category_faq';
$modversion['notification']['event'][9]['title']         = _MI_SF_CATEGORY_FAQ_ANSWER_PUBLISHED_NOTIFY;
$modversion['notification']['event'][9]['caption']       = _MI_SF_CATEGORY_FAQ_ANSWER_PUBLISHED_NOTIFY_CAP;
$modversion['notification']['event'][9]['description']   = _MI_SF_CATEGORY_FAQ_ANSWER_PUBLISHED_NOTIFY_DSC;
$modversion['notification']['event'][9]['mail_template'] = 'category_faq_answer_published';
$modversion['notification']['event'][9]['mail_subject']  = _MI_SF_CATEGORY_FAQ_ANSWER_PUBLISHED_NOTIFY_SBJ;

$modversion['notification']['event'][10]['name']          = 'rejected';
$modversion['notification']['event'][10]['category']      = 'faq';
$modversion['notification']['event'][10]['invisible']     = 1;
$modversion['notification']['event'][10]['title']         = _MI_SF_FAQ_REJECTED_NOTIFY;
$modversion['notification']['event'][10]['caption']       = _MI_SF_FAQ_REJECTED_NOTIFY_CAP;
$modversion['notification']['event'][10]['description']   = _MI_SF_FAQ_REJECTED_NOTIFY_DSC;
$modversion['notification']['event'][10]['mail_template'] = 'faq_rejected';
$modversion['notification']['event'][10]['mail_subject']  = _MI_SF_FAQ_REJECTED_NOTIFY_SBJ;

$modversion['notification']['event'][11]['name']          = 'approved';
$modversion['notification']['event'][11]['category']      = 'faq';
$modversion['notification']['event'][11]['invisible']     = 1;
$modversion['notification']['event'][11]['title']         = _MI_SF_FAQ_APPROVED_NOTIFY;
$modversion['notification']['event'][11]['caption']       = _MI_SF_FAQ_APPROVED_NOTIFY_CAP;
$modversion['notification']['event'][11]['description']   = _MI_SF_FAQ_APPROVED_NOTIFY_DSC;
$modversion['notification']['event'][11]['mail_template'] = 'faq_approved';
$modversion['notification']['event'][11]['mail_subject']  = _MI_SF_FAQ_APPROVED_NOTIFY_SBJ;

$modversion['notification']['event'][12]['name']          = 'answer_approved';
$modversion['notification']['event'][12]['category']      = 'faq';
$modversion['notification']['event'][12]['invisible']     = 1;
$modversion['notification']['event'][12]['title']         = _MI_SF_FAQ_ANSWER_APPROVED_NOTIFY;
$modversion['notification']['event'][12]['caption']       = _MI_SF_FAQ_ANSWER_APPROVED_NOTIFY_CAP;
$modversion['notification']['event'][12]['description']   = _MI_SF_FAQ_ANSWER_APPROVED_NOTIFY_DSC;
$modversion['notification']['event'][12]['mail_template'] = 'faq_answer_approved';
$modversion['notification']['event'][12]['mail_subject']  = _MI_SF_FAQ_ANSWER_APPROVED_NOTIFY_SBJ;

$modversion['notification']['event'][13]['name']          = 'answer_rejected';
$modversion['notification']['event'][13]['category']      = 'faq';
$modversion['notification']['event'][13]['invisible']     = 1;
$modversion['notification']['event'][13]['title']         = _MI_SF_FAQ_ANSWER_REJECTED_NOTIFY;
$modversion['notification']['event'][13]['caption']       = _MI_SF_FAQ_ANSWER_REJECTED_NOTIFY_CAP;
$modversion['notification']['event'][13]['description']   = _MI_SF_FAQ_ANSWER_REJECTED_NOTIFY_DSC;
$modversion['notification']['event'][13]['mail_template'] = 'faq_answer_rejected';
$modversion['notification']['event'][13]['mail_subject']  = _MI_SF_FAQ_ANSWER_REJECTED_NOTIFY_SBJ;

$modversion['notification']['event'][14]['name']          = 'submitted';
$modversion['notification']['event'][14]['category']      = 'global_question';
$modversion['notification']['event'][14]['admin_only']    = 1;
$modversion['notification']['event'][14]['title']         = _MI_SF_GLOBAL_QUESTION_SUBMITTED_NOTIFY;
$modversion['notification']['event'][14]['caption']       = _MI_SF_GLOBAL_QUESTION_SUBMITTED_NOTIFY_CAP;
$modversion['notification']['event'][14]['description']   = _MI_SF_GLOBAL_QUESTION_SUBMITTED_NOTIFY_DSC;
$modversion['notification']['event'][14]['mail_template'] = 'global_question_submitted';
$modversion['notification']['event'][14]['mail_subject']  = _MI_SF_GLOBAL_QUESTION_SUBMITTED_NOTIFY_SBJ;

$modversion['notification']['event'][15]['name']          = 'published';
$modversion['notification']['event'][15]['category']      = 'global_question';
$modversion['notification']['event'][15]['title']         = _MI_SF_GLOBAL_QUESTION_PUBLISHED_NOTIFY;
$modversion['notification']['event'][15]['caption']       = _MI_SF_GLOBAL_QUESTION_PUBLISHED_NOTIFY_CAP;
$modversion['notification']['event'][15]['description']   = _MI_SF_GLOBAL_QUESTION_PUBLISHED_NOTIFY_DSC;
$modversion['notification']['event'][15]['mail_template'] = 'global_question_published';
$modversion['notification']['event'][15]['mail_subject']  = _MI_SF_GLOBAL_QUESTION_PUBLISHED_NOTIFY_SBJ;

$modversion['notification']['event'][16]['name']          = 'answer_proposed';
$modversion['notification']['event'][16]['category']      = 'global_question';
$modversion['notification']['event'][16]['admin_only']    = 1;
$modversion['notification']['event'][16]['title']         = _MI_SF_GLOBAL_QUESTION_ANSWER_PROPOSED_NOTIFY;
$modversion['notification']['event'][16]['caption']       = _MI_SF_GLOBAL_QUESTION_ANSWER_PROPOSED_NOTIFY_CAP;
$modversion['notification']['event'][16]['description']   = _MI_SF_GLOBAL_QUESTION_ANSWER_PROPOSED_NOTIFY_DSC;
$modversion['notification']['event'][16]['mail_template'] = 'global_question_answer_proposed';
$modversion['notification']['event'][16]['mail_subject']  = _MI_SF_GLOBAL_QUESTION_ANSWER_PROPOSED_NOTIFY_SBJ;

$modversion['notification']['event'][17]['name']          = 'submitted';
$modversion['notification']['event'][17]['category']      = 'category_question';
$modversion['notification']['event'][17]['admin_only']    = 1;
$modversion['notification']['event'][17]['title']         = _MI_SF_CATEGORY_QUESTION_SUBMITTED_NOTIFY;
$modversion['notification']['event'][17]['caption']       = _MI_SF_CATEGORY_QUESTION_SUBMITTED_NOTIFY_CAP;
$modversion['notification']['event'][17]['description']   = _MI_SF_CATEGORY_QUESTION_SUBMITTED_NOTIFY_DSC;
$modversion['notification']['event'][17]['mail_template'] = 'category_question_submitted';
$modversion['notification']['event'][17]['mail_subject']  = _MI_SF_CATEGORY_QUESTION_SUBMITTED_NOTIFY_SBJ;

$modversion['notification']['event'][18]['name']          = 'published';
$modversion['notification']['event'][18]['category']      = 'category_question';
$modversion['notification']['event'][18]['title']         = _MI_SF_CATEGORY_QUESTION_PUBLISHED_NOTIFY;
$modversion['notification']['event'][18]['caption']       = _MI_SF_CATEGORY_QUESTION_PUBLISHED_NOTIFY_CAP;
$modversion['notification']['event'][18]['description']   = _MI_SF_CATEGORY_QUESTION_PUBLISHED_NOTIFY_DSC;
$modversion['notification']['event'][18]['mail_template'] = 'category_question_published';
$modversion['notification']['event'][18]['mail_subject']  = _MI_SF_CATEGORY_QUESTION_PUBLISHED_NOTIFY_SBJ;

$modversion['notification']['event'][19]['name']          = 'answer_proposed';
$modversion['notification']['event'][19]['category']      = 'category_question';
$modversion['notification']['event'][19]['title']         = _MI_SF_CATEGORY_QUESTION_ANSWER_PROPOSED_NOTIFY;
$modversion['notification']['event'][19]['caption']       = _MI_SF_CATEGORY_QUESTION_ANSWER_PROPOSED_NOTIFY_CAP;
$modversion['notification']['event'][19]['description']   = _MI_SF_CATEGORY_QUESTION_ANSWER_PROPOSED_NOTIFY_DSC;
$modversion['notification']['event'][19]['mail_template'] = 'category_question_answer_proposed';
$modversion['notification']['event'][19]['mail_subject']  = _MI_SF_CATEGORY_QUESTION_ANSWER_PROPOSED_NOTIFY_SBJ;

$modversion['notification']['event'][20]['name']          = 'rejected';
$modversion['notification']['event'][20]['category']      = 'question';
$modversion['notification']['event'][20]['invisible']     = 1;
$modversion['notification']['event'][20]['title']         = _MI_SF_QUESTION_REJECTED_NOTIFY;
$modversion['notification']['event'][20]['caption']       = _MI_SF_QUESTION_REJECTED_NOTIFY_CAP;
$modversion['notification']['event'][20]['description']   = _MI_SF_QUESTION_REJECTED_NOTIFY_DSC;
$modversion['notification']['event'][20]['mail_template'] = 'question_rejected';
$modversion['notification']['event'][20]['mail_subject']  = _MI_SF_QUESTION_REJECTED_NOTIFY_SBJ;

$modversion['notification']['event'][21]['name']          = 'approved';
$modversion['notification']['event'][21]['category']      = 'question';
$modversion['notification']['event'][21]['invisible']     = 1;
$modversion['notification']['event'][21]['title']         = _MI_SF_QUESTION_APPROVED_NOTIFY;
$modversion['notification']['event'][21]['caption']       = _MI_SF_QUESTION_APPROVED_NOTIFY_CAP;
$modversion['notification']['event'][21]['description']   = _MI_SF_QUESTION_APPROVED_NOTIFY_DSC;
$modversion['notification']['event'][21]['mail_template'] = 'question_approved';
$modversion['notification']['event'][21]['mail_subject']  = _MI_SF_QUESTION_APPROVED_NOTIFY_SBJ;

$modversion['notification']['event'][22]['name']          = 'answer_approved';
$modversion['notification']['event'][22]['category']      = 'question';
$modversion['notification']['event'][22]['invisible']     = 1;
$modversion['notification']['event'][22]['title']         = _MI_SF_QUESTION_ANSWER_APPROVED_NOTIFY;
$modversion['notification']['event'][22]['caption']       = _MI_SF_QUESTION_ANSWER_APPROVED_NOTIFY_CAP;
$modversion['notification']['event'][22]['description']   = _MI_SF_QUESTION_ANSWER_APPROVED_NOTIFY_DSC;
$modversion['notification']['event'][22]['mail_template'] = 'question_answer_approved';
$modversion['notification']['event'][22]['mail_subject']  = _MI_SF_QUESTION_ANSWER_APPROVED_NOTIFY_SBJ;

$modversion['notification']['event'][23]['name']          = 'answer_rejected';
$modversion['notification']['event'][23]['category']      = 'question';
$modversion['notification']['event'][23]['invisible']     = 1;
$modversion['notification']['event'][23]['title']         = _MI_SF_QUESTION_ANSWER_REJECTED_NOTIFY;
$modversion['notification']['event'][23]['caption']       = _MI_SF_QUESTION_ANSWER_REJECTED_NOTIFY_CAP;
$modversion['notification']['event'][23]['description']   = _MI_SF_QUESTION_ANSWER_REJECTED_NOTIFY_DSC;
$modversion['notification']['event'][23]['mail_template'] = 'question_answer_rejected';
$modversion['notification']['event'][23]['mail_subject']  = _MI_SF_QUESTION_ANSWER_REJECTED_NOTIFY_SBJ;
