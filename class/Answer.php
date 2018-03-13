<?php namespace XoopsModules\Smartfaq;

/**
 * Module: SmartFAQ
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartfaq;
use XoopsModules\Smartfaq\Constants;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');


/**
 * Class Answer
 */
class Answer extends \XoopsObject
{
    public $attachment_array = [];

    /**
     * constructor
     * @param null $id
     */
    public function __construct($id = null)
    {
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('answerid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('status', XOBJ_DTYPE_INT, -1, false);
        $this->initVar('faqid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('answer', XOBJ_DTYPE_TXTAREA, null, true);
        $this->initVar('uid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('datesub', XOBJ_DTYPE_INT, null, false);
        $this->initVar('notifypub', XOBJ_DTYPE_INT, 0, false);

        $this->initVar('attachment', XOBJ_DTYPE_TXTAREA, '');

        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('doxcode', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('dosmiley', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('doimage', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('dobr', XOBJ_DTYPE_INT, 1, false);

        // for backward compatibility
        if (isset($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                $answerHandler = new AnswerHandler($this->db);
                $answer        = $answerHandler->get($id);
                foreach ($answer->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
            }
        }
    }

    // ////////////////////////////////////////////////////////////////////////////////////
    // attachment functions    TODO: there should be a file/attachment management class
    /**
     * @return array|mixed|null
     */
    public function getAttachment()
    {
        if (count($this->attachment_array)) {
            return $this->attachment_array;
        }
        $attachment = $this->getVar('attachment');
        if (empty($attachment)) {
            $this->attachment_array = null;
        } else {
            $this->attachment_array = @unserialize(base64_decode($attachment));
        }

        return $this->attachment_array;
    }

    /**
     * @param $attach_key
     * @return bool
     */
    public function incrementDownload($attach_key)
    {
        if (!$attach_key) {
            return false;
        }
        $this->attachment_array[(string)$attach_key]['num_download']++;

        return $this->attachment_array[(string)$attach_key]['num_download'];
    }

    /**
     * @return bool
     */
    public function saveAttachment()
    {
        $attachment_save = '';
        if (is_array($this->attachment_array) && count($this->attachment_array) > 0) {
            $attachment_save = base64_encode(serialize($this->attachment_array));
        }
        $this->setVar('attachment', $attachment_save);
        $sql = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smartfaq_answers') . ' SET attachment=' . $GLOBALS['xoopsDB']->quoteString($attachment_save) . ' WHERE post_id = ' . $this->getVar('answerid');
        if (!$result = $GLOBALS['xoopsDB']->queryF($sql)) {
            //xoops_error($GLOBALS["xoopsDB"]->error());
            return false;
        }

        return true;
    }

    /**
     * @param  null $attach_array
     * @return bool
     */
    public function deleteAttachment($attach_array = null)
    {
        global $xoopsModuleConfig;

        $attach_old = $this->getAttachment();
        if (!is_array($attach_old) || count($attach_old) < 1) {
            return true;
        }
        $this->attachment_array = [];

        if (null === $attach_array) {
            $attach_array = array_keys($attach_old);
        } // to delete all!
        if (!is_array($attach_array)) {
            $attach_array = [$attach_array];
        }

        foreach ($attach_old as $key => $attach) {
            if (in_array($key, $attach_array)) {
                @unlink(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['dir_attachments'] . '/' . $attach['name_saved']);
                @unlink(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['dir_attachments'] . '/thumbs/' . $attach['name_saved']); // delete thumbnails
                continue;
            }
            $this->attachment_array[$key] = $attach;
        }
        $attachment_save = '';
        if (is_array($this->attachment_array) && count($this->attachment_array) > 0) {
            $attachment_save = base64_encode(serialize($this->attachment_array));
        }
        $this->setVar('attachment', $attachment_save);

        return true;
    }

    /**
     * @param  string $name_saved
     * @param  string $name_display
     * @param  string $mimetype
     * @param  int    $num_download
     * @return bool
     */
    public function setAttachment($name_saved = '', $name_display = '', $mimetype = '', $num_download = 0)
    {
        static $counter = 0;
        $this->attachment_array = $this->getAttachment();
        if ($name_saved) {
            $key                          = (string)(time() + ($counter++));
            $this->attachment_array[$key] = [
                'name_saved'   => $name_saved,
                'name_display' => isset($name_display) ? $name_display : $name_saved,
                'mimetype'     => $mimetype,
                'num_download' => isset($num_download) ? (int)$num_download : 0
            ];
        }
        $attachment_save = null;
        if (is_array($this->attachment_array)) {
            $attachment_save = base64_encode(serialize($this->attachment_array));
        }
        $this->setVar('attachment', $attachment_save);

        return true;
    }

    /**
     * TODO: refactor
     * @param  bool $asSource
     * @return string
     */
    public function displayAttachment($asSource = false)
    {
        global $xoopsModule, $xoopsModuleConfig;

        $post_attachment = '';
        $attachments     = $this->getAttachment();
        if (is_array($attachments) && count($attachments) > 0) {
            $iconHandler = sf_getIconHandler();
            $mime_path   = $iconHandler->getPath('mime');
            require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname', 'n') . '/include/functions.image.php';
            $image_extensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp']; // need improve !!!
            $post_attachment  .= '<br><strong>' . _MD_ATTACHMENT . '</strong>:';
            $post_attachment  .= '<br><hr size="1" noshade="noshade"><br>';
            foreach ($attachments as $key => $att) {
                $file_extension = ltrim(strrchr($att['name_saved'], '.'), '.');
                $filetype       = $file_extension;
                if (file_exists(XOOPS_ROOT_PATH . '/' . $mime_path . '/' . $filetype . '.gif')) {
                    $icon_filetype = XOOPS_URL . '/' . $mime_path . '/' . $filetype . '.gif';
                } else {
                    $icon_filetype = XOOPS_URL . '/' . $mime_path . '/unknown.gif';
                }
                $file_size = @filesize(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['dir_attachments'] . '/' . $att['name_saved']);
                $file_size = number_format($file_size / 1024, 2) . ' KB';
                if ($xoopsModuleConfig['media_allowed'] && in_array(strtolower($file_extension), $image_extensions)) {
                    $post_attachment .= '<br><img src="' . $icon_filetype . '" alt="' . $filetype . '"><strong>&nbsp; ' . $att['name_display'] . '</strong> <small>(' . $file_size . ')</small>';
                    $post_attachment .= '<br>' . sf_attachmentImage($att['name_saved']);
                    $isDisplayed     = true;
                } else {
                    global $xoopsUser;
                    if (empty($xoopsModuleConfig['show_userattach'])) {
                        $post_attachment .= '<a href="'
                                            . XOOPS_URL
                                            . '/modules/'
                                            . $xoopsModule->getVar('dirname', 'n')
                                            . '/dl_attachment.php?attachid='
                                            . $key
                                            . '&amp;post_id='
                                            . $this->getVar('post_id')
                                            . '"> <img src="'
                                            . $icon_filetype
                                            . '" alt="'
                                            . $filetype
                                            . '"> '
                                            . $att['name_display']
                                            . '</a> '
                                            . _MD_FILESIZE
                                            . ': '
                                            . $file_size
                                            . '; '
                                            . _MD_HITS
                                            . ': '
                                            . $att['num_download'];
                    } elseif ($xoopsUser && $xoopsUser->uid() > 0 && $xoopsUser->isActive()) {
                        $post_attachment .= '<a href="'
                                            . XOOPS_URL
                                            . '/modules/'
                                            . $xoopsModule->getVar('dirname', 'n')
                                            . '/dl_attachment.php?attachid='
                                            . $key
                                            . '&amp;post_id='
                                            . $this->getVar('post_id')
                                            . '"> <img src="'
                                            . $icon_filetype
                                            . '" alt="'
                                            . $filetype
                                            . '"> '
                                            . $att['name_display']
                                            . '</a> '
                                            . _MD_FILESIZE
                                            . ': '
                                            . $file_size
                                            . '; '
                                            . _MD_HITS
                                            . ': '
                                            . $att['num_download'];
                    } else {
                        $post_attachment .= _MD_NEWBB_SEENOTGUEST;
                    }
                }
                $post_attachment .= '<br>';
            }
        }

        return $post_attachment;
    }
    // attachment functions
    // ////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param  bool $force
     * @return bool
     */
    public function store($force = true)
    {
        $answerHandler = new AnswerHandler($this->db);

        if (Constants::SF_AN_STATUS_APPROVED == $this->status()) {
            $criteria = new \CriteriaCompo(new \Criteria('faqid', $this->faqid()));
            $answerHandler->updateAll('status', Constants::SF_AN_STATUS_REJECTED, $criteria);
        }

        return $answerHandler->insert($this, $force);
    }

    /**
     * @return mixed
     */
    public function answerid()
    {
        return $this->getVar('answerid');
    }

    /**
     * @return mixed
     */
    public function faqid()
    {
        return $this->getVar('faqid');
    }

    /**
     * @param  string $format
     * @return mixed
     */
    public function answer($format = 'S')
    {
        return $this->getVar('answer', $format);
    }

    /**
     * @return mixed
     */
    public function uid()
    {
        return $this->getVar('uid');
    }

    /**
     * @param  string $dateFormat
     * @param  string $format
     * @return string
     */
    public function datesub($dateFormat = 'none', $format = 'S')
    {
        if ('none' === $dateFormat) {
            $smartModuleConfig = Smartfaq\Utility::getModuleConfig();
            $dateFormat        = $smartModuleConfig['dateformat'];
        }

        return formatTimestamp($this->getVar('datesub', $format), $dateFormat);
    }

    /**
     * @return mixed
     */
    public function status()
    {
        return $this->getVar('status');
    }

    /**
     * @return bool
     */
    public function notLoaded()
    {
        return (-1 == $this->getVar('answerid'));
    }

    /**
     * @param array $notifications
     */
    public function sendNotifications($notifications = [])
    {
        $smartModule = Smartfaq\Utility::getModuleInfo();

        $myts                = \MyTextSanitizer::getInstance();
        $notificationHandler = xoops_getHandler('notification');

        $faqObj = new Smartfaq\Faq($this->faqid());

        $tags                  = [];
        $tags['MODULE_NAME']   = $myts->displayTarea($smartModule->getVar('name'));
        $tags['FAQ_NAME']      = $faqObj->question();
        $tags['FAQ_URL']       = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/faq.php?faqid=' . $faqObj->faqid();
        $tags['CATEGORY_NAME'] = $faqObj->getCategoryName();
        $tags['CATEGORY_URL']  = XOOPS_URL . '/modules/' . $smartModule->getVar('dirname') . '/category.php?categoryid=' . $faqObj->categoryid();
        $tags['FAQ_QUESTION']  = $faqObj->question();

        // TODO : Not sure about the 'formpreview' ...
        $tags['FAQ_ANSWER'] = $this->answer('formpreview');
        $tags['DATESUB']    = $this->datesub();

        foreach ($notifications as $notification) {
            switch ($notification) {
                case Constants::SF_NOT_ANSWER_APPROVED:
                    // This notification is not working for PM, but is for email... and I don't understand why???
                    $notificationHandler->triggerEvent('faq', $this->answerid(), 'answer_approved', $tags);
                    break;
                case -1:
                default:
                    break;
            }
        }
    }
}
