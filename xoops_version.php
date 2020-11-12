<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq\Constants;

$moduleDirName      = basename(__DIR__);
$moduleDirNameUpper = mb_strtoupper($moduleDirName);

$moduleDirName = basename(__DIR__);

$modversion['version']             = 1.20;
$modversion['module_status']       = 'Beta 2';
$modversion['release_date']        = '2019/08/17';
$modversion['name']                = _MI_SF_MD_NAME;
$modversion['description']         = _MI_SF_MD_DESC;
$modversion['author']              = 'The SmartFactory | Xuups';
$modversion['credits']             = 'w4z004, hsalazar, Carnuke, Mariuss, Mithrandir, phppp, Predator, GIJOE, outch, rowdie, Xvitry, Xavier & Catzwolf, trabis, Mamba';
$modversion['help']                = 'page=help';
$modversion['license']             = 'GNU GPL 2.0 or later';
$modversion['license_url']         = 'www.gnu.org/licenses/gpl-2.0.html';
$modversion['official']            = 1; //1 indicates supported by XOOPS Dev Team, 0 means 3rd party supported
$modversion['image']               = 'assets/images/logoModule.png';
$modversion['dirname']             = basename(__DIR__);
$modversion['modicons16']          = 'assets/images/icons/16';
$modversion['modicons32']          = 'assets/images/icons/32';
$modversion['release_file']        = XOOPS_URL . '/modules/' . $modversion['dirname'] . '/docs/changelog.txt';
$modversion['module_website_url']  = 'www.xoops.org';
$modversion['module_website_name'] = 'XOOPS';
$modversion['min_php']             = '7.2';
$modversion['min_xoops']           = '2.5.10';
$modversion['min_admin']           = '1.2';
$modversion['min_db']              = ['mysql' => '5.5'];

// Added by marcan for the About page in admin section
$modversion['developer_lead']         = 'marcan [Marc-André Lanciault]';
$modversion['developer_contributor']  = 'w4z004, hsalazar, Carnuke, Mariuss, Mithrandir, phppp, Predator, GIJOE, outch, rowdie, Xvitry, Xavier & Catzwolf, trabis';
$modversion['developer_website_url']  = 'http://www.xuups.com';
$modversion['developer_website_name'] = 'Xuups';
$modversion['developer_email']        = 'lusopoemas@gmail.com';
$modversion['status_version']         = 'RC 2';
$modversion['status']                 = 'RC 2';
$modversion['date']                   = '2017-02-25';

$modversion['warning'] = _MI_SF_WARNING_FINAL;

$modversion['demo_site_url']     = 'http://www.xuups.com/modules/smartfaq';
$modversion['demo_site_name']    = 'Xuups';
$modversion['support_site_url']  = 'http://www.xuups.com';
$modversion['support_site_name'] = 'Xuups';
$modversion['submit_bug']        = 'http://www.xuups.com/modules/xhelp';
$modversion['submit_feature']    = 'http://www.xuups.com/modules/xhelp';

$modversion['author_word'] = "
<b>SmartFAQ</b> is the result of multiple ideas from multiple people and a work of outstanding
collaboration. It all began with Herko talking to me about a 'contextual help system' for XOOPS,
inspired by the one on the Developers Forge. I found that idea more than brilliant, so I decided
to start coding the thing !
<br><br>As I was new in the developers world, I had to look for quality ideas that had already been
established and represented the best in Xoops programming. I chose the Soapbox module by hsalazar
(Horacio Salazar) which I had found absolutely amazing ! So, many thanks to Horacio, as his work offered
considerable inspiration. I would also like to thank him for helping me establish the workflow of
the SmartFAQ module, as well as for helping me in all the development process.
<br><br>When about half the coding was done, I met a special Xoopser who would become an important
player in this project : w4z004 (Sergio Kohl). Many thanks to you w4z004, as you multiplied many
times the possibilities and potential of this module. By testing it over and over again, by
submitting the code to be checked by security experts and other advanced developers, by suggesting
more features, by encouraging me when things were not going the way I wanted and by doing a thousand
other things for this project. Thank you, thank you, thank you !
<br><br>Special thanks also to Mithrandir (Jan Pedersen) for all the 'little' answers to my 'little'
questions (lol). You made my life so much easier by helping me see things more clearly !
<br><br>I would also like to thank Mariuss (Marius Scurtescu) for adapting <b>FAQ for New Xoopsers
</b> for SmartFAQ, for developing the import scripts, for teaching me the CVS (lol) as well as for
suggesting a lot of interesting improvements along the way.
<br><br>Another special thank-you to Carnuke (Richard Strauss) for writing such impressive
documentation for this module. You have now set up a new quality standard for XOOPS module
documentation. I'm confident that all the Xoopsers of the world are gratefull for this :-) !
<br><br>Finally, thanks to all the people who made this module possible : Herko, phppp, Solo71,
Yoyo2021, Christian, Hervé and so many others ! Also, a final thank to Zabou who has been
really understanding during all the hours I spent behind my laptop developing SmartFAQ.
<br><br>So I guess this is it, I could thank the Academy, my Mother and Father but that would be
pushing it I think ! (lol)
<br><br>Enjoy <b>SmartFAQ</b> (by marcan)!
";

// Admin things
$modversion['hasAdmin']    = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/menu.php';

// ------------------- Mysql ------------------- //
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';

// Tables created by sql file (without prefix!)
$modversion['tables'] = [
    $moduleDirName . '_' . 'categories',
    $moduleDirName . '_' . 'faq',
    $moduleDirName . '_' . 'answers',
];

// Search
$modversion['hasSearch']      = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'smartfaq_search';
// Menu
$modversion['hasMain'] = 1;

$modversion['onInstall'] = 'include/onupdate.inc.php';
$modversion['onUpdate']  = 'include/onupdate.inc.php';

// ------------------- Help files ------------------- //
$modversion['helpsection'] = [
    ['name' => _MI_SF_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_SF_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_SF_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_SF_SUPPORT, 'link' => 'page=support'],
];

global $xoopsModule;

if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $modversion['dirname']) {
    global $xoopsModuleConfig, $xoopsUser;

    $isAdmin = false;
    if (!empty($xoopsUser) && is_object($xoopsModule)) {
        $isAdmin = $xoopsUser->isAdmin($xoopsModule->getVar('mid'));
    }

    $smartModule = $xoopsModule;
    if ($smartModule) {
        $smartConfig = $xoopsModuleConfig;
        // Add the Submit new faq button
        if ($isAdmin
            || (isset($smartConfig['allowsubmit']) && 1 == $smartConfig['allowsubmit']
                && (is_object($xoopsUser)
                    || (isset($smartConfig['anonpost'])
                        && 1 == $smartConfig['anonpost'])))) {
            $modversion['sub'][1]['name'] = _MI_SF_SUB_SMNAME1;
            $modversion['sub'][1]['url']  = 'submit.php?op=add';
        }
        // Add the Request new faq
        if ($isAdmin
            || (isset($smartConfig['allowrequest']) && 1 == $smartConfig['allowrequest']
                && (is_object($xoopsUser)
                    || (isset($smartConfig['anonpost'])
                        && 1 == $smartConfig['anonpost'])))) {
            $modversion['sub'][2]['name'] = _MI_SF_SUB_SMNAME2;
            $modversion['sub'][2]['url']  = 'request.php?op=add';
        }

        //        require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

        // Creating the FAQ handler object
        /** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
        $faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');

        if ($faqHandler->getFaqsCount(-1, Constants::SF_STATUS_OPENED) > 0) {
            $modversion['sub'][3]['name'] = _MI_SF_SUB_SMNAME3;
            $modversion['sub'][3]['url']  = 'open_index.php';
        }
    }
}

$modversion['blocks'][] = [
    'file'        => 'faqs_new.php',
    'name'        => _MI_SF_ARTSNEW,
    'description' => 'Shows new faqs',
    'show_func'   => 'b_faqs_new_show',
    'edit_func'   => 'b_faqs_new_edit',
    'options'     => '0|datesub|5|65|1',
    'template'    => 'faqs_new.tpl',
];

$modversion['blocks'][] = [
    'file'        => 'faqs_recent.php',
    'name'        => _MI_SF_RECENTFAQS,
    'description' => 'Shows recent faqs',
    'show_func'   => 'b_faqs_recent_show',
    'edit_func'   => 'b_faqs_recent_edit',
    'options'     => '0|datesub|5|65',
    'template'    => 'faqs_recent.tpl',
];

$modversion['blocks'][] = [
    'file'        => 'faqs_context.php',
    'name'        => _MI_SF_ARTSCONTEXT,
    'description' => 'Shows contextual faqs',
    'show_func'   => 'b_faqs_context_show',
    'edit_func'   => 'b_faqs_context_edit',
    'options'     => '5',
    'template'    => 'faqs_context.tpl',
];

$modversion['blocks'][] = [
    'file'        => 'faqs_random_how.php',
    'name'        => _MI_SF_ARTSRANDOM_HOW,
    'description' => "Shows a random 'How do I' faq",
    'show_func'   => 'b_faqs_random_how_show',
    'template'    => 'faqs_random_how.tpl',
];

$modversion['blocks'][] = [
    'file'        => 'faqs_random_diduno.php',
    'name'        => _MI_SF_ARTSRANDOM_DIDUNO,
    'description' => "Shows a random 'Did You Know' faq",
    'show_func'   => 'b_faqs_random_diduno_show',
    'template'    => 'faqs_random_diduno.tpl',
];

$modversion['blocks'][] = [
    'file'        => 'faqs_random_faq.php',
    'name'        => _MI_SF_ARTSRANDOM_FAQ,
    'description' => "Shows a random 'faq' faq",
    'show_func'   => 'b_faqs_random_faq_show',
    'template'    => 'faqs_random_faq.tpl',
];

$modversion['blocks'][] = [
    'file'        => 'faqs_recent_questions.php',
    'name'        => _MI_SF_RECENT_QUESTIONS,
    'description' => 'Shows recent questions',
    'show_func'   => 'b_faqs_recent_questions_show',
    'edit_func'   => 'b_faqs_recent_questions_edit',
    'options'     => '0|datesub|5|65|1',
    'template'    => 'faqs_recent_questions.tpl',
];

$modversion['blocks'][] = [
    'file'        => 'faqs_most_viewed.php',
    'name'        => _MI_SF_MOST_VIEWED,
    'description' => 'Shows most viewed Q&A',
    'show_func'   => 'b_faqs_most_viewed_show',
    'edit_func'   => 'b_faqs_most_viewed_edit',
    'options'     => '0|5|65',
    'template'    => 'faqs_most_viewed.tpl',
];

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

$modversion['config'][] = [
    'name'        => 'allowsubmit',
    'title'       => '_MI_SF_ALLOWSUBMIT',
    'description' => '_MI_SF_ALLOWSUBMITDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'allowrequest',
    'title'       => '_MI_SF_ALLOWREQUEST',
    'description' => '_MI_SF_ALLOWREQUESTDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'allownewanswer',
    'title'       => '_MI_SF_NEWANSWER',
    'description' => '_MI_SF_NEWANSWERDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'anonpost',
    'title'       => '_MI_SF_ANONPOST',
    'description' => '_MI_SF_ANONPOSTDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

/** @var \XoopsMemberHandler $memberHandler */
$memberHandler = xoops_getHandler('member');
$groups        = $memberHandler->getGroupList();

$modversion['config'][] = [
    'name'        => 'dateformat',
    'title'       => '_MI_SF_DATEFORMAT',
    'description' => '_MI_SF_DATEFORMATDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'd-M-Y H:i',
];

$modversion['config'][] = [
    'name'        => 'displaycollaps',
    'title'       => '_MI_SF_DISPLAY_COLLAPS',
    'description' => '_MI_SF_DISPLAY_COLLAPSDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'displaylastfaqs',
    'title'       => '_MI_SF_DISPLAY_LAST_FAQS',
    'description' => '_MI_SF_DISPLAY_LAST_FAQSDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'displaytype',
    'title'       => '_MI_SF_DISPLAYTYPE',
    'description' => '_MI_SF_DISPLAYTYPEDSC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => [
        _MI_SF_DISPLAYTYPE_SUMMARY => 'summary',
        _MI_SF_DISPLAYTYPE_FULL    => 'full',
    ],
    'default'     => 'full',
];

$modversion['config'][] = [
    'name'        => 'displaylastfaq',
    'title'       => '_MI_SF_DISPLAY_LAST_FAQ',
    'description' => '_MI_SF_DISPLAY_LAST_FAQDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'lastfaqsize',
    'title'       => '_MI_SF_LAST_FAQ_SIZE',
    'description' => '_MI_SF_LAST_FAQ_SIZEDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '50',
];

$modversion['config'][] = [
    'name'        => 'questionsize',
    'title'       => '_MI_SF_QUESTION_SIZE',
    'description' => '_MI_SF_QUESTION_SIZEDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '60',
];

$modversion['config'][] = [
    'name'        => 'displaytopcatdsc',
    'title'       => '_MI_SF_DISPLAY_TOPCAT_DSC',
    'description' => '_MI_SF_DISPLAY_TOPCAT_DSCDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'displaysubcatonindex',
    'title'       => '_MI_SF_DISPLAY_SUBCAT_INDEX',
    'description' => '_MI_SF_DISPLAY_SUBCAT_INDEXDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'displaysubcatdsc',
    'title'       => '_MI_SF_DISPLAY_SBCAT_DSC',
    'description' => '_MI_SF_DISPLAY_SBCAT_DSCDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'orderbydate',
    'title'       => '_MI_SF_ORDERBYDATE',
    'description' => '_MI_SF_ORDERBYDATEDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'display_date_col',
    'title'       => '_MI_SF_DISPLAY_DATE_COL',
    'description' => '_MI_SF_DISPLAY_DATE_COLDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'display_hits_col',
    'title'       => '_MI_SF_DISPLAY_HITS_COL',
    'description' => '_MI_SF_DISPLAY_HITS_COLDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'useimagenavpage',
    'title'       => '_MI_SF_USEIMAGENAVPAGE',
    'description' => '_MI_SF_USEIMAGENAVPAGEDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'globaldisplaycomments',
    'title'       => '_MI_SF_ALLOWCOMMENTS',
    'description' => '_MI_SF_ALLOWCOMMENTSDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'adminhits',
    'title'       => '_MI_SF_ALLOWADMINHITS',
    'description' => '_MI_SF_ALLOWADMINHITSDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'autoapprove_submitted_faq',
    'title'       => '_MI_SF_AUTOAPPROVE_SUB_FAQ',
    'description' => '_MI_SF_AUTOAPPROVE_SUB_FAQ_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'autoapprove_request',
    'title'       => '_MI_SF_AUTOAPPROVE_REQUEST',
    'description' => '_MI_SF_AUTOAPPROVE_REQUEST_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'autoapprove_answer',
    'title'       => '_MI_SF_AUTOAPPROVE_ANS',
    'description' => '_MI_SF_AUTOAPPROVE_ANS_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'autoapprove_answer_new',
    'title'       => '_MI_SF_AUTOAPPROVE_ANS_NEW',
    'description' => '_MI_SF_AUTOAPPROVE_ANS_NEW_DSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'catperpage',
    'title'       => '_MI_SF_CATPERPAGE',
    'description' => '_MI_SF_CATPERPAGEDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 5,
    'options'     => [
        '5'  => 5,
        '10' => 10,
        '15' => 15,
        '20' => 20,
        '25' => 25,
        '30' => 30,
        '50' => 50,
    ],
];

$modversion['config'][] = [
    'name'        => 'perpage',
    'title'       => '_MI_SF_PERPAGE',
    'description' => '_MI_SF_PERPAGEDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 5,
    'options'     => [
        '5'  => 5,
        '10' => 10,
        '15' => 15,
        '20' => 20,
        '25' => 25,
        '30' => 30,
        '50' => 50,
    ],
];

$modversion['config'][] = [
    'name'        => 'indexperpage',
    'title'       => '_MI_SF_PERPAGEINDEX',
    'description' => '_MI_SF_PERPAGEINDEXDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 5,
    'options'     => [
        '5'  => 5,
        '10' => 10,
        '15' => 15,
        '20' => 20,
        '25' => 25,
        '30' => 30,
        '50' => 50,
    ],
];

$modversion['config'][] = [
    'name'        => 'indexwelcomemsg',
    'title'       => '_MI_SF_INDEXWELCOMEMSG',
    'description' => '_MI_SF_INDEXWELCOMEMSGDSC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => _MI_SF_INDEXWELCOMEMSGDEF,
];

$modversion['config'][] = [
    'name'        => 'requestintromsg',
    'title'       => '_MI_SF_REQUESTINTROMSG',
    'description' => '_MI_SF_REQUESTINTROMSGDSC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => _MI_SF_REQUESTINTROMSGDEF,
];

$modversion['config'][] = [
    'name'        => 'openquestionintromsg',
    'title'       => '_MI_SF_OPENINTROMSG',
    'description' => '_MI_SF_OPENINTROMSGDSC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => _MI_SF_OPENINTROMSGDEF,
];

$modversion['config'][] = [
    'name'        => 'userealname',
    'title'       => '_MI_SF_USEREALNAME',
    'description' => '_MI_SF_USEREALNAMEDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

/*
$modversion['config'][] = [
'name' =>  'moderatorsedit',
'title' =>  '_MI_SF_MODERATORSEDIT',
'description' =>  '_MI_SF_MODERATORSEDITDSC',
'formtype' =>  'yesno',
'valuetype' =>  'int',
'default' =>  0,
];
*/

$modversion['config'][] = [
    'name'        => 'helppath_select',
    'title'       => '_MI_SF_HELP_PATH_SELECT',
    'description' => '_MI_SF_HELP_PATH_SELECT_DSC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => [_MI_SF_HELP_INSIDE => 'inside', _MI_SF_HELP_CUSTOM => 'custom'],
    'default'     => 'docs.xoops.org',
];

$modversion['config'][] = [
    'name'        => 'helppath_custom',
    'title'       => '_MI_SF_HELP_PATH_CUSTOM',
    'description' => '_MI_SF_HELP_PATH_CUSTOM_DSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '',
];

xoops_load('XoopsEditorHandler');
$editorHandler = \XoopsEditorHandler::getInstance();
$editorList    = array_flip($editorHandler->getList());

$modversion['config'][] = [
    'name'        => 'form_editorOptions',
    'title'       => '_MI_SF_EDITOR',
    'description' => '_MI_SF_EDITORCHOICE',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => $editorList,
    'default'     => 'dhtmltextarea',
];

$modversion['config'][] = [
    'name'        => 'form_editorOptionsUser',
    'title'       => '_MI_SF_EDITORUSER',
    'description' => '_MI_SF_EDITORCHOICEUSER',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => $editorList,
    'default'     => 'dhtmltextarea',
];
//mb------------ START ---------------------

define('_MI_SF_SHOTWIDTH2', '<span style="color:#FF0000; font-size:12px;"><b>Upload Files/Images</b></span> ');

$modversion['config'][] = [
    'name'        => 'logfile',
    'title'       => '_MI_SF_SHOTWIDTH2',
    'description' => '_MI_SF_USERLOG_CONFCAT_LOGFILE_DSC',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'odd',
];

$modversion['config'][] = [
    'name'        => 'attach_ext',
    'title'       => '_AM_SF_ALLOWED_EXTENSIONS',
    'description' => '_AM_SF_ALLOWED_EXTENSIONS_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'zip|jpg|gif|png',
];

$modversion['config'][] = [
    'name'        => 'dir_attachments',
    'title'       => '_MI_SF_DIR_ATTACHMENT',
    'description' => '_MI_SF_DIR_ATTACHMENT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'uploads/smartfaq',
];

$modversion['config'][] = [
    'name'        => 'media_allowed',
    'title'       => '_MI_SF_MEDIA_ENABLE',
    'description' => '_MI_SF_MEDIA_ENABLE_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'path_magick',
    'title'       => '_MI_SF_PATH_MAGICK',
    'description' => '_MI_SF_PATH_MAGICK_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '/usr/bin/X11',
];

$modversion['config'][] = [
    'name'        => 'path_netpbm',
    'title'       => '_MI_SF_PATH_NETPBM',
    'description' => '_MI_SF_PATH_NETPBM_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '/usr/bin',
];

$modversion['config'][] = [
    'name'        => 'image_lib',
    'title'       => '_MI_SF_IMAGELIB',
    'description' => '_MI_SF_IMAGELIB_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => [
        _MI_SF_AUTO   => 0,
        _MI_SF_MAGICK => 1,
        _MI_SF_NETPBM => 2,
        _MI_SF_GD1    => 3,
        _MI_SF_GD2    => 4,
    ],
];

$modversion['config'][] = [
    'name'        => 'show_userattach',
    'title'       => '_MI_SF_USERATTACH_ENABLE',
    'description' => '_MI_SF_USERATTACH_ENABLE_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'max_img_width',
    'title'       => '_MI_SF_MAX_IMG_WIDTH',
    'description' => '_MI_SF_MAX_IMG_WIDTH_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 800,
];

$modversion['config'][] = [
    'name'        => 'max_img_height',
    'title'       => '_MI_SF_MAX_IMG_HEIGHT',
    'description' => '_MI_SF_MAX_IMG_HEIGHT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 640,
];

$modversion['config'][] = [
    'name'        => 'max_image_width',
    'title'       => '_MI_SF_MAX_IMAGE_WIDTH',
    'description' => '_MI_SF_MAX_IMAGE_WIDTH_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 150,
];

$modversion['config'][] = [
    'name'        => 'max_image_height',
    'title'       => '_MI_SF_MAX_IMAGE_HEIGHT',
    'description' => '_MI_SF_MAX_IMAGE_HEIGHT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 150,
];

$modversion['config'][] = [
    'name'        => 'max_image_size',
    'title'       => '_MI_SF_MAX_IMAGE_SIZE',
    'description' => '_MI_SF_MAX_IMAGE_SIZE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 1024,
];

/**
 * Make Sample button visible?
 */
$modversion['config'][] = [
    'name'        => 'displaySampleButton',
    'title'       => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_SAMPLE_BUTTON',
    'description' => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_SAMPLE_BUTTON_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

/**
 * Show Developer Tools?
 */
$modversion['config'][] = [
    'name'        => 'displayDeveloperTools',
    'title'       => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_DEV_TOOLS',
    'description' => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_DEV_TOOLS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

define('_MI_XDIR_SHOTWIDTH3', '<span style="color:#FF0000; font-size:12px;"><b>Comments/Notifications</b></span> ');

$modversion['config'][] = [
    'name'        => 'logfile',
    'title'       => '_MI_XDIR_SHOTWIDTH3',
    'description' => '_MI_USERLOG_CONFCAT_LOGFILE_DSC',
    'formtype'    => 'line_break',
    'valuetype'   => 'textbox',
    'default'     => 'odd',
];



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

$modversion['notification']['category'][] = [
    'name'           => 'global_faq',
    'title'          => _MI_SF_GLOBAL_FAQ_NOTIFY,
    'description'    => _MI_SF_GLOBAL_FAQ_NOTIFY_DSC,
    'subscribe_from' => ['index.php', 'category.php', 'faq.php'],
];

$modversion['notification']['category'][] = [
    'name'           => 'category_faq',
    'title'          => _MI_SF_CATEGORY_FAQ_NOTIFY,
    'description'    => _MI_SF_CATEGORY_FAQ_NOTIFY_DSC,
    'subscribe_from' => ['index.php', 'category.php', 'faq.php'],
    'item_name'      => 'categoryid',
    'allow_bookmark' => 1,
];

$modversion['notification']['category'][] = [
    'name'           => 'faq',
    'title'          => _MI_SF_FAQ_NOTIFY,
    'description'    => _MI_SF_FAQ_NOTIFY_DSC,
    'subscribe_from' => ['faq.php'],
    'item_name'      => 'faqid',
    'allow_bookmark' => 1,
];

$modversion['notification']['category'][] = [
    'name'           => 'global_question',
    'title'          => _MI_SF_GLOBAL_QUESTION_NOTIFY,
    'description'    => _MI_SF_GLOBAL_QUESTION_NOTIFY_DSC,
    'subscribe_from' => ['open_index.php'],
];

$modversion['notification']['category'][] = [
    'name'           => 'category_question',
    'title'          => _MI_SF_CATEGORY_QUESTION_NOTIFY,
    'description'    => _MI_SF_CATEGORY_QUESTION_NOTIFY_DSC,
    'subscribe_from' => ['open_index.php, open_category.php'],
];

$modversion['notification']['category'][] = [
    'name'           => 'question',
    'title'          => _MI_SF_QUESTION_NOTIFY,
    'description'    => _MI_SF_QUESTION_NOTIFY_DSC,
    'subscribe_from' => ['open_index.php'],
];

$modversion['notification']['event'][] = [
    'name'          => 'category_created',
    'category'      => 'global_faq',
    'title'         => _MI_SF_GLOBAL_FAQ_CATEGORY_CREATED_NOTIFY,
    'caption'       => _MI_SF_GLOBAL_FAQ_CATEGORY_CREATED_NOTIFY_CAP,
    'description'   => _MI_SF_GLOBAL_FAQ_CATEGORY_CREATED_NOTIFY_DSC,
    'mail_template' => 'global_faq_category_created',
    'mail_subject'  => _MI_SF_GLOBAL_FAQ_CATEGORY_CREATED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'submitted',
    'category'      => 'global_faq',
    'admin_only'    => 1,
    'title'         => _MI_SF_GLOBAL_FAQ_SUBMITTED_NOTIFY,
    'caption'       => _MI_SF_GLOBAL_FAQ_SUBMITTED_NOTIFY_CAP,
    'description'   => _MI_SF_GLOBAL_FAQ_SUBMITTED_NOTIFY_DSC,
    'mail_template' => 'global_faq_submitted',
    'mail_subject'  => _MI_SF_GLOBAL_FAQ_SUBMITTED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'published',
    'category'      => 'global_faq',
    'title'         => _MI_SF_GLOBAL_FAQ_PUBLISHED_NOTIFY,
    'caption'       => _MI_SF_GLOBAL_FAQ_PUBLISHED_NOTIFY_CAP,
    'description'   => _MI_SF_GLOBAL_FAQ_PUBLISHED_NOTIFY_DSC,
    'mail_template' => 'global_faq_published',
    'mail_subject'  => _MI_SF_GLOBAL_FAQ_PUBLISHED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'answer_proposed',
    'category'      => 'global_faq',
    'admin_only'    => 1,
    'title'         => _MI_SF_GLOBAL_FAQ_ANSWER_PROPOSED_NOTIFY,
    'caption'       => _MI_SF_GLOBAL_FAQ_ANSWER_PROPOSED_NOTIFY_CAP,
    'description'   => _MI_SF_GLOBAL_FAQ_ANSWER_PROPOSED_NOTIFY_DSC,
    'mail_template' => 'global_faq_answer_proposed',
    'mail_subject'  => _MI_SF_GLOBAL_FAQ_ANSWER_PROPOSED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'answer_published',
    'category'      => 'global_faq',
    'title'         => _MI_SF_GLOBAL_FAQ_ANSWER_PUBLISHED_NOTIFY,
    'caption'       => _MI_SF_GLOBAL_FAQ_ANSWER_PUBLISHED_NOTIFY_CAP,
    'description'   => _MI_SF_GLOBAL_FAQ_ANSWER_PUBLISHED_NOTIFY_DSC,
    'mail_template' => 'global_faq_answer_published',
    'mail_subject'  => _MI_SF_GLOBAL_FAQ_ANSWER_PUBLISHED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'submitted',
    'category'      => 'category_faq',
    'admin_only'    => 1,
    'title'         => _MI_SF_CATEGORY_FAQ_SUBMITTED_NOTIFY,
    'caption'       => _MI_SF_CATEGORY_FAQ_SUBMITTED_NOTIFY_CAP,
    'description'   => _MI_SF_CATEGORY_FAQ_SUBMITTED_NOTIFY_DSC,
    'mail_template' => 'category_faq_submitted',
    'mail_subject'  => _MI_SF_CATEGORY_FAQ_SUBMITTED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'published',
    'category'      => 'category_faq',
    'title'         => _MI_SF_CATEGORY_FAQ_PUBLISHED_NOTIFY,
    'caption'       => _MI_SF_CATEGORY_FAQ_PUBLISHED_NOTIFY_CAP,
    'description'   => _MI_SF_CATEGORY_FAQ_PUBLISHED_NOTIFY_DSC,
    'mail_template' => 'category_faq_published',
    'mail_subject'  => _MI_SF_CATEGORY_FAQ_PUBLISHED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'answer_proposed',
    'category'      => 'category_faq',
    'admin_only'    => 1,
    'title'         => _MI_SF_CATEGORY_FAQ_ANSWER_PROPOSED_NOTIFY,
    'caption'       => _MI_SF_CATEGORY_FAQ_ANSWER_PROPOSED_NOTIFY_CAP,
    'description'   => _MI_SF_CATEGORY_FAQ_ANSWER_PROPOSED_NOTIFY_DSC,
    'mail_template' => 'category_faq_answer_proposed',
    'mail_subject'  => _MI_SF_CATEGORY_FAQ_ANSWER_PROPOSED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'answer_published',
    'category'      => 'category_faq',
    'title'         => _MI_SF_CATEGORY_FAQ_ANSWER_PUBLISHED_NOTIFY,
    'caption'       => _MI_SF_CATEGORY_FAQ_ANSWER_PUBLISHED_NOTIFY_CAP,
    'description'   => _MI_SF_CATEGORY_FAQ_ANSWER_PUBLISHED_NOTIFY_DSC,
    'mail_template' => 'category_faq_answer_published',
    'mail_subject'  => _MI_SF_CATEGORY_FAQ_ANSWER_PUBLISHED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'rejected',
    'category'      => 'faq',
    'invisible'     => 1,
    'title'         => _MI_SF_FAQ_REJECTED_NOTIFY,
    'caption'       => _MI_SF_FAQ_REJECTED_NOTIFY_CAP,
    'description'   => _MI_SF_FAQ_REJECTED_NOTIFY_DSC,
    'mail_template' => 'faq_rejected',
    'mail_subject'  => _MI_SF_FAQ_REJECTED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'approved',
    'category'      => 'faq',
    'invisible'     => 1,
    'title'         => _MI_SF_FAQ_APPROVED_NOTIFY,
    'caption'       => _MI_SF_FAQ_APPROVED_NOTIFY_CAP,
    'description'   => _MI_SF_FAQ_APPROVED_NOTIFY_DSC,
    'mail_template' => 'faq_approved',
    'mail_subject'  => _MI_SF_FAQ_APPROVED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'answer_approved',
    'category'      => 'faq',
    'invisible'     => 1,
    'title'         => _MI_SF_FAQ_ANSWER_APPROVED_NOTIFY,
    'caption'       => _MI_SF_FAQ_ANSWER_APPROVED_NOTIFY_CAP,
    'description'   => _MI_SF_FAQ_ANSWER_APPROVED_NOTIFY_DSC,
    'mail_template' => 'faq_answer_approved',
    'mail_subject'  => _MI_SF_FAQ_ANSWER_APPROVED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'answer_rejected',
    'category'      => 'faq',
    'invisible'     => 1,
    'title'         => _MI_SF_FAQ_ANSWER_REJECTED_NOTIFY,
    'caption'       => _MI_SF_FAQ_ANSWER_REJECTED_NOTIFY_CAP,
    'description'   => _MI_SF_FAQ_ANSWER_REJECTED_NOTIFY_DSC,
    'mail_template' => 'faq_answer_rejected',
    'mail_subject'  => _MI_SF_FAQ_ANSWER_REJECTED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'submitted',
    'category'      => 'global_question',
    'admin_only'    => 1,
    'title'         => _MI_SF_GLOBAL_QUESTION_SUBMITTED_NOTIFY,
    'caption'       => _MI_SF_GLOBAL_QUESTION_SUBMITTED_NOTIFY_CAP,
    'description'   => _MI_SF_GLOBAL_QUESTION_SUBMITTED_NOTIFY_DSC,
    'mail_template' => 'global_question_submitted',
    'mail_subject'  => _MI_SF_GLOBAL_QUESTION_SUBMITTED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'published',
    'category'      => 'global_question',
    'title'         => _MI_SF_GLOBAL_QUESTION_PUBLISHED_NOTIFY,
    'caption'       => _MI_SF_GLOBAL_QUESTION_PUBLISHED_NOTIFY_CAP,
    'description'   => _MI_SF_GLOBAL_QUESTION_PUBLISHED_NOTIFY_DSC,
    'mail_template' => 'global_question_published',
    'mail_subject'  => _MI_SF_GLOBAL_QUESTION_PUBLISHED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'answer_proposed',
    'category'      => 'global_question',
    'admin_only'    => 1,
    'title'         => _MI_SF_GLOBAL_QUESTION_ANSWER_PROPOSED_NOTIFY,
    'caption'       => _MI_SF_GLOBAL_QUESTION_ANSWER_PROPOSED_NOTIFY_CAP,
    'description'   => _MI_SF_GLOBAL_QUESTION_ANSWER_PROPOSED_NOTIFY_DSC,
    'mail_template' => 'global_question_answer_proposed',
    'mail_subject'  => _MI_SF_GLOBAL_QUESTION_ANSWER_PROPOSED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'submitted',
    'category'      => 'category_question',
    'admin_only'    => 1,
    'title'         => _MI_SF_CATEGORY_QUESTION_SUBMITTED_NOTIFY,
    'caption'       => _MI_SF_CATEGORY_QUESTION_SUBMITTED_NOTIFY_CAP,
    'description'   => _MI_SF_CATEGORY_QUESTION_SUBMITTED_NOTIFY_DSC,
    'mail_template' => 'category_question_submitted',
    'mail_subject'  => _MI_SF_CATEGORY_QUESTION_SUBMITTED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'published',
    'category'      => 'category_question',
    'title'         => _MI_SF_CATEGORY_QUESTION_PUBLISHED_NOTIFY,
    'caption'       => _MI_SF_CATEGORY_QUESTION_PUBLISHED_NOTIFY_CAP,
    'description'   => _MI_SF_CATEGORY_QUESTION_PUBLISHED_NOTIFY_DSC,
    'mail_template' => 'category_question_published',
    'mail_subject'  => _MI_SF_CATEGORY_QUESTION_PUBLISHED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'answer_proposed',
    'category'      => 'category_question',
    'title'         => _MI_SF_CATEGORY_QUESTION_ANSWER_PROPOSED_NOTIFY,
    'caption'       => _MI_SF_CATEGORY_QUESTION_ANSWER_PROPOSED_NOTIFY_CAP,
    'description'   => _MI_SF_CATEGORY_QUESTION_ANSWER_PROPOSED_NOTIFY_DSC,
    'mail_template' => 'category_question_answer_proposed',
    'mail_subject'  => _MI_SF_CATEGORY_QUESTION_ANSWER_PROPOSED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'rejected',
    'category'      => 'question',
    'invisible'     => 1,
    'title'         => _MI_SF_QUESTION_REJECTED_NOTIFY,
    'caption'       => _MI_SF_QUESTION_REJECTED_NOTIFY_CAP,
    'description'   => _MI_SF_QUESTION_REJECTED_NOTIFY_DSC,
    'mail_template' => 'question_rejected',
    'mail_subject'  => _MI_SF_QUESTION_REJECTED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'approved',
    'category'      => 'question',
    'invisible'     => 1,
    'title'         => _MI_SF_QUESTION_APPROVED_NOTIFY,
    'caption'       => _MI_SF_QUESTION_APPROVED_NOTIFY_CAP,
    'description'   => _MI_SF_QUESTION_APPROVED_NOTIFY_DSC,
    'mail_template' => 'question_approved',
    'mail_subject'  => _MI_SF_QUESTION_APPROVED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'answer_approved',
    'category'      => 'question',
    'invisible'     => 1,
    'title'         => _MI_SF_QUESTION_ANSWER_APPROVED_NOTIFY,
    'caption'       => _MI_SF_QUESTION_ANSWER_APPROVED_NOTIFY_CAP,
    'description'   => _MI_SF_QUESTION_ANSWER_APPROVED_NOTIFY_DSC,
    'mail_template' => 'question_answer_approved',
    'mail_subject'  => _MI_SF_QUESTION_ANSWER_APPROVED_NOTIFY_SBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'answer_rejected',
    'category'      => 'question',
    'invisible'     => 1,
    'title'         => _MI_SF_QUESTION_ANSWER_REJECTED_NOTIFY,
    'caption'       => _MI_SF_QUESTION_ANSWER_REJECTED_NOTIFY_CAP,
    'description'   => _MI_SF_QUESTION_ANSWER_REJECTED_NOTIFY_DSC,
    'mail_template' => 'question_answer_rejected',
    'mail_subject'  => _MI_SF_QUESTION_ANSWER_REJECTED_NOTIFY_SBJ,
];

