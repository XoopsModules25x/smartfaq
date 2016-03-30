<?php

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

// Module Info
// The name of this module
global $xoopsModule;
define('_MI_SF_MD_NAME', 'SmartFAQ');

// A brief description of this module
define('_MI_SF_MD_DESC', 'Advanced Questions and Answers Management System for your XOOPS Site');

// Sub menus in main menu block
define('_MI_SF_SUB_SMNAME1', 'Submit a Q&amp;A');
define('_MI_SF_SUB_SMNAME2', 'Request a Q&amp;A');
define('_MI_SF_SUB_SMNAME3', 'Open questions');
define('_MI_SF_SUB_SMNAME4', 'Moderate Q&amp;A');

// Config options
define('_MI_SF_ALLOWSUBMIT', 'User submissions:');
define('_MI_SF_ALLOWSUBMITDSC', 'Allow users to submit Q&amp;A on your website?');

define('_MI_SF_ALLOWREQUEST', 'User requests:');
define('_MI_SF_ALLOWREQUESTDSC', 'Allow users to request Q&amp;A on your website?');

define('_MI_SF_NEWANSWER', 'Allow new answer posting :');
define('_MI_SF_NEWANSWERDSC', "Select 'Yes' to allow users to submit new answers for already published Q&A.");

define('_MI_SF_ANONPOST', 'Allow anonymous posting');
define('_MI_SF_ANONPOSTDSC', 'Allow anonymous users to submit or request Q&amp;A.');

define('_MI_SF_DATEFORMAT', 'Date format:');
define('_MI_SF_DATEFORMATDSC', 'Use the final part of language/english/global.php to select a display style. Example: "d-M-Y H:i" translates to "30-Mar-2004 22:35"');

define('_MI_SF_DISPLAY_COLLAPS', 'Display collapsable bars');
define('_MI_SF_DISPLAY_COLLAPSDSC', "Select 'Yes' to display the collapsable bar in the index and category page.");

define('_MI_SF_DISPLAYTYPE', "Q&amp;A's display type:");
define('_MI_SF_DISPLAYTYPEDSC', "If 'Summary View' is selected, only the Question, Date and Hits of each Q&amp;A will be displayed in a selected category. If 'Full View' is selected, each Q&amp;A will be entirely displayed in a selected category.");
define('_MI_SF_DISPLAYTYPE_SUMMARY', 'Summary View');
define('_MI_SF_DISPLAYTYPE_FULL', 'Full View');

define('_MI_SF_DISPLAY_LAST_FAQ', 'Display last Q&amp;A column?');
define('_MI_SF_DISPLAY_LAST_FAQDSC', "Select 'Yes' to display the last Q&amp;A in each category in the index and category page.");

define('_MI_SF_DISPLAY_LAST_FAQS', 'Display a list of last Q&amp;As?');
define('_MI_SF_DISPLAY_LAST_FAQSDSC', "Select 'Yes' to display a list of last Q&amp;A's on the index page.");

define('_MI_SF_LAST_FAQ_SIZE', 'Last Q&amp;A size :');
define('_MI_SF_LAST_FAQ_SIZEDSC', 'Set the maximum size of the question in the Last Q&amp;A column.');

define('_MI_SF_QUESTION_SIZE', 'Question size :');
define('_MI_SF_QUESTION_SIZEDSC', 'Set the maximum size of the question as a title in the single Q&amp;A display page.');

define('_MI_SF_DISPLAY_SUBCAT_INDEX', 'Display sub-categories on index');
define('_MI_SF_DISPLAY_SUBCAT_INDEXDSC', "Select 'Yes' to display subcategories on the index page.");

define('_MI_SF_DISPLAY_TOPCAT_DSC', 'Display top categories description?');
define('_MI_SF_DISPLAY_TOPCAT_DSCDSC', "Select 'Yes' to display the description of top categories in the index and category page.");

define('_MI_SF_DISPLAY_SBCAT_DSC', 'Display sub-categories description?');
define('_MI_SF_DISPLAY_SBCAT_DSCDSC', "Select 'Yes' to display the description of sub-categories in the index and category page.");

define('_MI_SF_ORDERBYDATE', 'Order the Q&amp;As by date :');
define('_MI_SF_ORDERBYDATEDSC', 'If you set this option to "Yes", the Q&amp;As inside a category will be ordered by decending date, otherwise, they will be ordered by their weight.');

define('_MI_SF_DISPLAY_DATE_COL', "Display the 'Published on' column?");
define('_MI_SF_DISPLAY_DATE_COLDSC', "When the 'Summary' display type is selected, select 'Yes' to display a 'Published on' date in the Q&amp;A table on the index and category page.");

define('_MI_SF_DISPLAY_HITS_COL', "Display the 'Hits' column?");
define('_MI_SF_DISPLAY_HITS_COLDSC', "When the 'Summary' display type is selected, select 'Yes' to display the 'Hits' column in the Q&amp;A table on the index and category page.");

define('_MI_SF_USEIMAGENAVPAGE', 'Use the image Page Navigation:');
define('_MI_SF_USEIMAGENAVPAGEDSC', 'If you set this option to "Yes", the Page Navigation will be displayed with image, otherwise, the original Page Naviagation will be used.');

define('_MI_SF_ALLOWCOMMENTS', 'Control comments at the Q&amp;A level:');
define('_MI_SF_ALLOWCOMMENTSDSC', 'If you set this option to "Yes", you\'ll see comments only on those Q&amp;A that have their comment checkbox marked. <br /><br />Select "No" to have comments managed at the global level (look below under the tag "Comment rules".');

define('_MI_SF_ALLOWADMINHITS', 'Admin counter reads:');
define('_MI_SF_ALLOWADMINHITSDSC', 'Allow admin hits for counter stats?');

define('_MI_SF_AUTOAPPROVE_SUB_FAQ', 'Auto approve submitted Q&amp;A:');
define('_MI_SF_AUTOAPPROVE_SUB_FAQ_DSC', 'Auto approves submitted Q&amp;A without admin intervention.');

define('_MI_SF_AUTOAPPROVE_REQUEST', 'Auto approve requested Q&amp;A:');
define('_MI_SF_AUTOAPPROVE_REQUEST_DSC', 'Auto approves requested Q&amp;A without admin intervention.');

define('_MI_SF_AUTOAPPROVE_ANS', 'Auto approve answers:');
define('_MI_SF_AUTOAPPROVE_ANS_DSC', 'Auto approves submitted answers for open questions.');

define('_MI_SF_AUTOAPPROVE_ANS_NEW', 'Auto approve new answer:');
define('_MI_SF_AUTOAPPROVE_ANS_NEW_DSC', 'Auto approves new submitted answers for published Q&amp;A.');

define('_MI_SF_LASTFAQSPERCAT', 'Maximum last Q&amp;A per category:');
define('_MI_SF_LASTFAQSPERCATDSC', 'Maximum number of Q&amp;A to be displayed in the Info column of a category.');

define('_MI_SF_CATPERPAGE', 'Maximum Categories per page (User side):');
define('_MI_SF_CATPERPAGEDSC', 'Maximum number of top categories per page to be displayed at once in the user side.');

define('_MI_SF_PERPAGE', 'Maximum Q&amp;A per page (Admin side):');
define('_MI_SF_PERPAGEDSC', 'Maximum number of Q&amp;A per page to be displayed at once in Q&amp;A Admin.');

define('_MI_SF_PERPAGEINDEX', 'Maximum Q&amp;A per page (User side):');
define('_MI_SF_PERPAGEINDEXDSC', 'Maximum number of Q&amp;A  to be displayed per page in the user side.');

define('_MI_SF_INDEXWELCOMEMSG', 'Index welcome message:');
define('_MI_SF_INDEXWELCOMEMSGDSC', 'Welcome message to be displayed in the index page of the module.');
define('_MI_SF_INDEXWELCOMEMSGDEF', 'In this area of our site, you will find the answers to the frequently asked questions, as well as answers to <b>How do I</b> and <b>Did you know</b> questions. Please feel free to post a comment on any Q&amp;A.');

define('_MI_SF_REQUESTINTROMSG', 'Request introduction message:');
define('_MI_SF_REQUESTINTROMSGDSC', 'Introduction message to be displayed in the Request a Q&amp;A page of the module.');
define('_MI_SF_REQUESTINTROMSGDEF', 'You did not find the answer to the question you were looking for? No problem! Simply fill the following form in order to request the answer for a new question. The site administrator will review your request and publish this new question in the Open Questions section for someone to answer it!');

define('_MI_SF_OPENINTROMSG', 'Open Questions section introduction message:');
define('_MI_SF_OPENINTROMSGDSC', 'Introduction message to be displayed in the Open Questions section of the module.');
define('_MI_SF_OPENINTROMSGDEF', 'Here is a list of Open Questions, that is, questions that have been submitted by users of this site but are still without answer. You can click on an open question if you want to help us with an answer.');

define('_MI_SF_USEREALNAME', 'Use the Real Name of users');
define('_MI_SF_USEREALNAMEDSC', 'When displaying a username, use the real name of that user if he has a set his real name.');

define('_MI_SF_HELP_PATH_SELECT', "Path of SmartFAQ's help files");
define('_MI_SF_HELP_PATH_SELECT_DSC', "Select from where you would like to access SmartFAQ's help files. If you downloaded the 'SmartFAQ's Help Package' and uploaded it in 'modules/smartfaq/doc/', you can select 'Inside the module'. Alternatively, you can access the module's help file directly from docs.xoops.org by chosing this in the selector. You can also select 'Custom Path' and specify yourself the path of the help files in the next config option 'Custom path of SmartFAQ's help files'");

define('_MI_SF_HELP_PATH_CUSTOM', "Custom path of SmartFAQ's help files");
define('_MI_SF_HELP_PATH_CUSTOM_DSC', "If you selected 'Custom path' in the previous option 'Path of SmartFAQ's help files', please specify the URL of SmartFAQ's help files, in that format : http://www.yoursite.com/doc");

define('_MI_SF_HELP_INSIDE', 'Inside the module');
define('_MI_SF_HELP_CUSTOM', 'Custom Path');

//define('_MI_SF_MODERATORSEDIT','Allow moderators to edit (Enhanced moderators)');
//define('_MI_SF_MODERATORSEDITDSC','This option will allow moderators to edit questions and Q&amp;A within categories for which they are moderators. Otherwise, moderators can only approve or reject questions and Q&amp;A.');

// Names of admin menu items
define('_MI_SF_ADMENU1', 'Q&A Manager');
define('_MI_SF_ADMENU2', 'Categories');
define('_MI_SF_ADMENU3', 'Published Q&amp;A');
define('_MI_SF_ADMENU4', 'Open Questions');
define('_MI_SF_ADMENU5', 'Permissions');
define('_MI_SF_ADMENU6', 'Blocks and Groups');
define('_MI_SF_ADMENU7', 'Go to module');
define('_MI_SF_ADMENU8', 'Import');

//Names of Blocks and Block information
define('_MI_SF_ARTSNEW', 'Recent Q&amp;A List');
define('_MI_SF_ARTSRANDOM_DIDUNO', 'Did you know?');
define('_MI_SF_ARTSRANDOM_FAQ', 'Random question!');
define('_MI_SF_ARTSRANDOM_HOW', 'How do I...');
define('_MI_SF_ARTSCONTEXT', 'Contextual Q&amp;A');
define('_MI_SF_RECENTFAQS', 'Recent Q&amp;A (Detail)');
define('_MI_SF_RECENT_QUESTIONS', 'Recent Open Questions');
define('_MI_SF_MOST_VIEWED', 'Most viewed Q&amp;As');

// Text for notifications

define('_MI_SF_GLOBAL_FAQ_NOTIFY', 'Global Q&amp;A');
define('_MI_SF_GLOBAL_FAQ_NOTIFY_DSC', 'Notification options that apply to all Q&amp;A.');

define('_MI_SF_CATEGORY_FAQ_NOTIFY', 'Category Q&amp;A');
define('_MI_SF_CATEGORY_FAQ_NOTIFY_DSC', 'Notification options that apply to the current category.');

define('_MI_SF_FAQ_NOTIFY', 'Q&amp;A');
define('_MI_SF_FAQ_NOTIFY_DSC', 'Notification options that apply to this Q&amp;A.');

define('_MI_SF_GLOBAL_QUESTION_NOTIFY', 'Global Opened questions');
define('_MI_SF_GLOBAL_QUESTION_NOTIFY_DSC', 'Notification options that apply to all opened questions');

define('_MI_SF_CATEGORY_QUESTION_NOTIFY', 'Category Q&amp;A');
define('_MI_SF_CATEGORY_QUESTION_NOTIFY_DSC', 'Notification options that apply to the current category.');

define('_MI_SF_QUESTION_NOTIFY', 'Opened Question');
define('_MI_SF_QUESTION_NOTIFY_DSC', 'Notification options that apply to the current Opened question.');

define('_MI_SF_GLOBAL_FAQ_CATEGORY_CREATED_NOTIFY', 'New category');
define('_MI_SF_GLOBAL_FAQ_CATEGORY_CREATED_NOTIFY_CAP', 'Notify me when a new category is created.');
define('_MI_SF_GLOBAL_FAQ_CATEGORY_CREATED_NOTIFY_DSC', 'Receive notification when a new category is created.');
define('_MI_SF_GLOBAL_FAQ_CATEGORY_CREATED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New category');

define('_MI_SF_GLOBAL_FAQ_SUBMITTED_NOTIFY', 'Q&amp;A submitted');
define('_MI_SF_GLOBAL_FAQ_SUBMITTED_NOTIFY_CAP', 'Notify me when any Q&amp;A is submitted and is awaiting approval.');
define('_MI_SF_GLOBAL_FAQ_SUBMITTED_NOTIFY_DSC', 'Receive notification when any Q&amp;A is submitted and is waiting approval.');
define('_MI_SF_GLOBAL_FAQ_SUBMITTED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New Q&amp;A submitted');

define('_MI_SF_GLOBAL_FAQ_PUBLISHED_NOTIFY', 'New Q&amp;A published');
define('_MI_SF_GLOBAL_FAQ_PUBLISHED_NOTIFY_CAP', 'Notify me when any new Q&amp;A is published.');
define('_MI_SF_GLOBAL_FAQ_PUBLISHED_NOTIFY_DSC', 'Receive notification when any new Q&amp;A is published.');
define('_MI_SF_GLOBAL_FAQ_PUBLISHED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New Q&amp;A published');

define('_MI_SF_GLOBAL_FAQ_ANSWER_PROPOSED_NOTIFY', 'New answer proposed');
define('_MI_SF_GLOBAL_FAQ_ANSWER_PROPOSED_NOTIFY_CAP', 'Notify me when a new answer is proposed for any Q&amp;A.');
define('_MI_SF_GLOBAL_FAQ_ANSWER_PROPOSED_NOTIFY_DSC', 'Receive notification when a new answer is proposed for any Q&amp;A.');
define('_MI_SF_GLOBAL_FAQ_ANSWER_PROPOSED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New answer proposed');

define('_MI_SF_GLOBAL_FAQ_ANSWER_PUBLISHED_NOTIFY', 'New answer published');
define('_MI_SF_GLOBAL_FAQ_ANSWER_PUBLISHED_NOTIFY_CAP', 'Notify me when a new answer is published for any Q&amp;A.');
define('_MI_SF_GLOBAL_FAQ_ANSWER_PUBLISHED_NOTIFY_DSC', 'Receive notification when a new answer is published for any Q&amp;A.');
define('_MI_SF_GLOBAL_FAQ_ANSWER_PUBLISHED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New answer published');

define('_MI_SF_CATEGORY_FAQ_SUBMITTED_NOTIFY', 'Q&amp;A submitted');
define('_MI_SF_CATEGORY_FAQ_SUBMITTED_NOTIFY_CAP', 'Notify me when a new Q&amp;A is submitted in the current category.');
define('_MI_SF_CATEGORY_FAQ_SUBMITTED_NOTIFY_DSC', 'Receive notification when a new Q&amp;A is submitted in the current category.');
define('_MI_SF_CATEGORY_FAQ_SUBMITTED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New Q&amp;A submitted in category');

define('_MI_SF_CATEGORY_FAQ_PUBLISHED_NOTIFY', 'New Q&amp;A published');
define('_MI_SF_CATEGORY_FAQ_PUBLISHED_NOTIFY_CAP', 'Notify me when a new Q&amp;A is published in the current category.');
define('_MI_SF_CATEGORY_FAQ_PUBLISHED_NOTIFY_DSC', 'Receive notification when a new Q&amp;A is published in the current category.');
define('_MI_SF_CATEGORY_FAQ_PUBLISHED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New Q&amp;A published in category');

define('_MI_SF_CATEGORY_FAQ_ANSWER_PROPOSED_NOTIFY', 'New answer proposed');
define('_MI_SF_CATEGORY_FAQ_ANSWER_PROPOSED_NOTIFY_CAP', 'Notify me when a new answer is proposed for a Q&amp;A in this category.');
define('_MI_SF_CATEGORY_FAQ_ANSWER_PROPOSED_NOTIFY_DSC', 'Receive notification when a new answer is proposed for a Q&amp;A in this category.');
define('_MI_SF_CATEGORY_FAQ_ANSWER_PROPOSED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New answer proposed');

define('_MI_SF_CATEGORY_FAQ_ANSWER_PUBLISHED_NOTIFY', 'New answer published');
define('_MI_SF_CATEGORY_FAQ_ANSWER_PUBLISHED_NOTIFY_CAP', 'Notify me when a new answer is published for a Q&amp;A in this category.');
define('_MI_SF_CATEGORY_FAQ_ANSWER_PUBLISHED_NOTIFY_DSC', 'Receive notification when a new answer is published for a Q&amp;A in this category.');
define('_MI_SF_CATEGORY_FAQ_ANSWER_PUBLISHED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New answer published');

define('_MI_SF_FAQ_REJECTED_NOTIFY', 'Q&amp;A rejected');
define('_MI_SF_FAQ_REJECTED_NOTIFY_CAP', 'Notify me if this Q&amp;A is rejected.');
define('_MI_SF_FAQ_REJECTED_NOTIFY_DSC', 'Receive notification if this Q&amp;A is rejected.');
define('_MI_SF_FAQ_REJECTED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Q&amp;A rejected');

define('_MI_SF_FAQ_APPROVED_NOTIFY', 'Q&amp;A approved');
define('_MI_SF_FAQ_APPROVED_NOTIFY_CAP', 'Notify me when this Q&amp;A is approved.');
define('_MI_SF_FAQ_APPROVED_NOTIFY_DSC', 'Receive notification when this Q&amp;A is approved.');
define('_MI_SF_FAQ_APPROVED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Q&amp;A approved');

define('_MI_SF_FAQ_ANSWER_APPROVED_NOTIFY', 'Answer approved');
define('_MI_SF_FAQ_ANSWER_APPROVED_NOTIFY_CAP', 'Notify me when this answer is approved.');
define('_MI_SF_FAQ_ANSWER_APPROVED_NOTIFY_DSC', 'Receive notification when this answer is approved.');
define('_MI_SF_FAQ_ANSWER_APPROVED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Answer approved');

define('_MI_SF_FAQ_ANSWER_REJECTED_NOTIFY', 'Answer rejected');
define('_MI_SF_FAQ_ANSWER_REJECTED_NOTIFY_CAP', 'Notify me if this answer is rejected.');
define('_MI_SF_FAQ_ANSWER_REJECTED_NOTIFY_DSC', 'Receive notification if this answer is rejected.');
define('_MI_SF_FAQ_ANSWER_REJECTED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Answer rejected');

define('_MI_SF_GLOBAL_QUESTION_SUBMITTED_NOTIFY', 'Question submitted');
define('_MI_SF_GLOBAL_QUESTION_SUBMITTED_NOTIFY_CAP', 'Notify me when any question is submitted and is waiting approval.');
define('_MI_SF_GLOBAL_QUESTION_SUBMITTED_NOTIFY_DSC', 'Receive notification when any question is submitted and is waiting approval.');
define('_MI_SF_GLOBAL_QUESTION_SUBMITTED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New question submitted');

define('_MI_SF_GLOBAL_QUESTION_PUBLISHED_NOTIFY', 'Question published');
define('_MI_SF_GLOBAL_QUESTION_PUBLISHED_NOTIFY_CAP', 'Notify me when any question is published in the Open Questions section.');
define('_MI_SF_GLOBAL_QUESTION_PUBLISHED_NOTIFY_DSC', 'Receive notification when any question is published in the Open Questions section.');
define('_MI_SF_GLOBAL_QUESTION_PUBLISHED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New question published');

define('_MI_SF_GLOBAL_QUESTION_ANSWER_PROPOSED_NOTIFY', 'Answer proposed');
define('_MI_SF_GLOBAL_QUESTION_ANSWER_PROPOSED_NOTIFY_CAP', 'Notify me when an answer is proposed for any open question.');
define('_MI_SF_GLOBAL_QUESTION_ANSWER_PROPOSED_NOTIFY_DSC', 'Receive notification when an answer is proposed for any open question.');
define('_MI_SF_GLOBAL_QUESTION_ANSWER_PROPOSED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New answer proposed');

define('_MI_SF_CATEGORY_QUESTION_SUBMITTED_NOTIFY', 'Question submitted');
define('_MI_SF_CATEGORY_QUESTION_SUBMITTED_NOTIFY_CAP', 'Notify me when a question is submitted in the current category.');
define('_MI_SF_CATEGORY_QUESTION_SUBMITTED_NOTIFY_DSC', 'Receive notification when a question is submitted in the current category.');
define('_MI_SF_CATEGORY_QUESTION_SUBMITTED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New question submitted');

define('_MI_SF_CATEGORY_QUESTION_PUBLISHED_NOTIFY', 'Question published');
define('_MI_SF_CATEGORY_QUESTION_PUBLISHED_NOTIFY_CAP', 'Notify me when a question is published in the current category.');
define('_MI_SF_CATEGORY_QUESTION_PUBLISHED_NOTIFY_DSC', 'Receive notification when a question is published in the current category.');
define('_MI_SF_CATEGORY_QUESTION_PUBLISHED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New question published');

define('_MI_SF_CATEGORY_QUESTION_ANSWER_PROPOSED_NOTIFY', 'Answer proposed');
define('_MI_SF_CATEGORY_QUESTION_ANSWER_PROPOSED_NOTIFY_CAP', 'Notify me when a new answer is proposed for an opened question in this category.');
define('_MI_SF_CATEGORY_QUESTION_ANSWER_PROPOSED_NOTIFY_DSC', 'Receive notification when a new answer is proposed for an opened question in this category.');
define('_MI_SF_CATEGORY_QUESTION_ANSWER_PROPOSED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New answer proposed');

define('_MI_SF_QUESTION_REJECTED_NOTIFY', 'Question rejected');
define('_MI_SF_QUESTION_REJECTED_NOTIFY_CAP', 'Notify me if this question is rejected.');
define('_MI_SF_QUESTION_REJECTED_NOTIFY_DSC', 'Receive notification if this question is rejected.');
define('_MI_SF_QUESTION_REJECTED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Question rejected');

define('_MI_SF_QUESTION_APPROVED_NOTIFY', 'Question approved');
define('_MI_SF_QUESTION_APPROVED_NOTIFY_CAP', 'Notify me when this question is approved.');
define('_MI_SF_QUESTION_APPROVED_NOTIFY_DSC', 'Receive notification when this question is approved.');
define('_MI_SF_QUESTION_APPROVED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Question approved');

define('_MI_SF_QUESTION_ANSWER_APPROVED_NOTIFY', 'Answer approved');
define('_MI_SF_QUESTION_ANSWER_APPROVED_NOTIFY_CAP', 'Notify me when this answer is approved.');
define('_MI_SF_QUESTION_ANSWER_APPROVED_NOTIFY_DSC', 'Receive notification when this answer is approved.');
define('_MI_SF_QUESTION_ANSWER_APPROVED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Answer approved');

define('_MI_SF_QUESTION_ANSWER_REJECTED_NOTIFY', 'Answer rejected');
define('_MI_SF_QUESTION_ANSWER_REJECTED_NOTIFY_CAP', 'Notify me if this answer is rejected.');
define('_MI_SF_QUESTION_ANSWER_REJECTED_NOTIFY_DSC', 'Receive notification if this answer is rejected.');
define('_MI_SF_QUESTION_ANSWER_REJECTED_NOTIFY_SBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Answer rejected');

// About.php constants
define('_MI_SF_AUTHOR_INFO', 'Developers');
define('_MI_SF_DEVELOPER_LEAD', 'Lead developer(s)');
define('_MI_SF_DEVELOPER_CONTRIBUTOR', 'Contributor(s)');
define('_MI_SF_DEVELOPER_WEBSITE', 'Website');
define('_MI_SF_DEVELOPER_EMAIL', 'Email');
define('_MI_SF_DEVELOPER_CREDITS', 'Credits');
define('_MI_SF_DEMO_SITE', 'SmartFactory Demo Site');
define('_MI_SF_MODULE_INFO', 'Module Developpment Informations');
define('_MI_SF_MODULE_STATUS', 'Status');
define('_MI_SF_MODULE_RELEASE_DATE', 'Release date');
define('_MI_SF_MODULE_DEMO', 'Demo Site');
define('_MI_SF_MODULE_SUPPORT', 'Official support site');
define('_MI_SF_MODULE_BUG', 'Report a bug for this module');
define('_MI_SF_MODULE_FEATURE', 'Suggest a new feature for this module');
define('_MI_SF_MODULE_DISCLAIMER', 'Disclaimer');
define('_MI_SF_AUTHOR_WORD', "The Author's Word");
define('_MI_SF_VERSION_HISTORY', 'Version History');

// Beta
define('_MI_SF_WARNING_BETA', 'This module comes as is, without any guarantees whatsoever.
This module is BETA, meaning it is still under active development. This release is meant for
<b>testing purposes only</b> and we <b>strongly</b> recommend that you do not use it on a live
website or in a production environment.');

// RC
define('_MI_SF_WARNING_RC', 'This module comes as is, without any guarantees whatsoever. This module
is a Release Candidate and should not be used on a production web site. The module is still under
active development and its use is under your own responsibility, which means the author is not responsible.');

// Final
define('_MI_SF_WARNING_FINAL', 'This module comes as is, without any guarantees whatsoever. Although this
module is not beta, it is still under active development. This release can be used in a live website
or a production environment, but its use is under your own responsibility, which means the author
is not responsible.');

//1.11 RC1

define('_MI_SF_EDITOR', 'Editor to use (admin):');
define('_MI_SF_EDITORCHOICE', "Select the editor to use for admin side. If you have a 'simple' install (e.g you use only XOOPS core editor class, provided in the standard xoops core package), then you can just select DHTML and Compact");
define('_MI_SF_EDITORUSER', 'Editor to use (user):');
define('_MI_SF_EDITORCHOICEUSER', "Select the editor to use for user side. If you have a 'simple' install (e.g you use only XOOPS core editor class, provided in the standard xoops core package), then you can just select DHTML and Compact");

//1.11 RC2

define('_MI_SF_MAGICK', 'ImageMagick');
define('_MI_SF_NETPBM', 'Netpbm');
define('_MI_SF_GD1', 'GD1 Library');
define('_MI_SF_GD2', 'GD2 Library');
define('_MI_SF_AUTO', 'AUTO');

//------------------------------

define('_MI_SF_DIR_ATTACHMENT', 'Attachments physical path.');
define('_MI_SF_DIR_ATTACHMENT_DESC', "Physical path only needs to be set from your xoops root and not before, for example you may have attachments uploaded to www.yoururl.com/uploads/newbb the path entered would then be '/uploads/newbb' never include a trailing slash '/' the thumbnails path becomes '/uploads/newbb/thumbs'");
define('_MI_SF_PATH_MAGICK', 'Path for ImageMagick');
define('_MI_SF_PATH_MAGICK_DESC', "Usually it is '/usr/bin/X11'. Leave it BLANK if you do not have ImageMagicK installed or for autodetecting.");
define('_MI_SF_SUBFORUM_DISPLAY', 'Display Mode of subforums on index page');
define('_MI_SF_SUBFORUM_DISPLAY_DESC', 'Choose one of the methods to display subforums');
define('_MI_SF_SUBFORUM_EXPAND', 'Expand');
define('_MI_SF_SUBFORUM_COLLAPSE', 'Collapse');
define('_MI_SF_SUBFORUM_HIDDEN', 'Hidden');
define('_MI_SF_POST_EXCERPT', 'Post excerpt on forum page');
define('_MI_SF_POST_EXCERPT_DESC', 'Length of post excerpt by mouse over. 0 for no excerpt.');
define('_MI_SF_PATH_NETPBM', 'Path for Netpbm');
define('_MI_SF_PATH_NETPBM_DESC', "Usually it is '/usr/bin'. Leave it BLANK if you do not have Netpbm installed or  for autodetecting.");
define('_MI_SF_IMAGELIB', 'Select the Image library to use');
define('_MI_SF_IMAGELIB_DESC', 'Select which Image library to use for creating Thumbnails. Leave AUTO for automatic choice.');
define('_MI_SF_MAX_IMG_WIDTH', 'Maximum Image Width');
define('_MI_SF_MAX_IMG_WIDTH_DESC', 'Sets the maximum allowed <strong>Width</strong> size of an uploaded image otherwise thumbnail will be used. <br >Input 0 if you do not want to create thumbnails.');
define('_MI_SF_MAX_IMG_HEIGHT', 'Maximum height of an image');
define('_MI_SF_MAX_IMG_HEIGHT_DESC', 'Sets the maximum allowed height of an uploaded image.');
define('_MI_SF_MAX_IMAGE_WIDTH', 'Maximum Image Width for creating thumbnail');
define('_MI_SF_MAX_IMAGE_WIDTH_DESC', 'Sets the maximum width of an uploaded image to create thumbnail. <br >Image with width larger than the value will not use thumbnail.');
define('_MI_SF_MAX_IMAGE_HEIGHT', 'Maximum Image Height for creating thumbnail');
define('_MI_SF_MAX_IMAGE_HEIGHT_DESC', 'Sets the maximum height of an uploaded image to create thumbnail. <br >Image with height larger than the value will not use thumbnail.');

define('_MI_SF_MAX_IMAGE_SIZE', 'Size in KB');
define('_MI_SF_MAX_IMAGE_SIZE_DESC', 'Indicate the maximum file size in KB');

define('_AM_SF_ALLOWED_EXTENSIONS', "Allowed Extensions:<span style='font-size: xx-small; font-weight: normal; display: block;'>'*' indicates no limititations.<br /> Extensions delimited by '|'</span>");

define('_MI_SF_USERATTACH_ENABLE', 'Display attachments only for registered users');
define('_MI_SF_USERATTACH_ENABLE_DESC', 'shows attachments in the forum only after logging in.');

define('_MI_SF_MEDIA_ENABLE', 'Enable Media Features');
define('_MI_SF_MEDIA_ENABLE_DESC', 'Display attached Images directly in the post.');
