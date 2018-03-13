<?php
/**
 * CBB 4.0, or newbb, the forum module for XOOPS project
 *
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      Taiwen Jiang (phppp or D.J.) <phppp@users.sourceforge.net>
 * @since       4.00
 * @package     module::newbb
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

defined('NEWBB_FUNCTIONS_INI') || include __DIR__ . '/functions.ini.php';
define('NEWBB_FUNCTIONS_RENDER_LOADED', true);

if (!defined('NEWBB_FUNCTIONS_RENDER')):
    define('NEWBB_FUNCTIONS_RENDER', 1);

    /*
     * Sorry, we have to use the stupid solution unless there is an option in MyTextSanitizer:: htmlspecialchars();
     */
    /**
     * @param $text
     * @return mixed
     */
    function sf_htmlSpecialChars($text)
    {
        return preg_replace(['/&amp;/i', '/&nbsp;/i'], ['&', '&amp;nbsp;'], htmlspecialchars($text, ENT_QUOTES));
    }

    /**
     * @param        $text
     * @param  int   $html
     * @param  int   $smiley
     * @param  int   $xcode
     * @param  int   $image
     * @param  int   $br
     * @return mixed
     */
    function &sf_displayTarea(&$text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1)
    {
        global $myts;

        if (1 != $html) {
            // html not allowed
            $text = sf_htmlSpecialChars($text);
        }
        $text = $myts->codePreConv($text, $xcode); // Ryuji_edit(2003-11-18)
        $text = $myts->makeClickable($text);
        if (0 != $smiley) {
            // process smiley
            $text = $myts->smiley($text);
        }
        if (0 != $xcode) {
            // decode xcode
            if (0 != $image) {
                // image allowed
                $text =& $myts->xoopsCodeDecode($text);
            } else {
                // image not allowed
                $text =& $myts->xoopsCodeDecode($text, 0);
            }
        }
        if (0 != $br) {
            $text =& $myts->nl2Br($text);
        }
        $text = $myts->codeConv($text, $xcode, $image);    // Ryuji_edit(2003-11-18)

        return $text;
    }

    /**
     * @param $document
     * @return string
     */
    function sf_html2text($document)
    {
        $text = strip_tags($document);

        return $text;
    }

    /**
     * Display forrum button
     *
     * @param  string  $link
     * @param  string  $button  image/button name, without extension
     * @param  string  $alt     alt message
     * @param  boolean $asImage true for image mode; false for text mode
     * @param  string  $extra   extra attribute for the button
     * @return mixed
     */
    function sf_getButton($link, $button, $alt = '', $asImage = true, $extra = "class='forum_button'")
    {
        $button = "<input type='button' name='{$button}' {$extra} value='{$alt}' onclick='window.location.href={$link}'>";
        if (empty($asImage)) {
            $button = "<a href='{$link}' title='{$alt}' {$extra}>" . sf_displayImage($button, $alt, true) . '</a>';
        }

        return $button;
    }

    /**
     * Display forrum images
     *
     * @param  string  $image   image name, without extension
     * @param  string  $alt     alt message
     * @param  boolean $display true for return image anchor; faulse for assign to $xoopsTpl
     * @param  string  $extra   extra attribute for the image
     * @return mixed
     */
    function sf_displayImage($image, $alt = '', $display = true, $extra = "class='forum_icon'")
    {
        $iconHandler = sf_getIconHandler();
        // START hacked by irmtfan
        // to show text links instead of buttons - func_num_args()==2 => only when $image, $alt is set and optional $display not set
        global $xoopsModuleConfig;
        if (2 == func_num_args()) {
            // overall setting
            if (!empty($xoopsModuleConfig['display_text_links'])) {
                $display = false;
            }
            // if set for each link => overwrite $display
            if (isset($xoopsModuleConfig['display_text_each_link'][$image])) {
                $display = empty($xoopsModuleConfig['display_text_each_link'][$image]);
            }
        }
        // END hacked by irmtfan
        if (empty($display)) {
            return $iconHandler->assignImage($image, $alt, $extra);
        } else {
            return $iconHandler->getImage($image, $alt, $extra);
        }
    }

    /**
     * @return \XoopsModules\Newbb\IconHandler
     */
    function sf_getIconHandler()
    {
        global $xoTheme, $xoopsConfig;
        static $iconHandler;

        if (isset($iconHandler)) {
            return $iconHandler;
        }
        /*
                if (!class_exists('NewbbIconHandler')) {
                    // require_once __DIR__ . '/../class/icon.php';
                }
        */
        $iconHandler           = \XoopsModules\Newbb\IconHandler::getInstance();
        $iconHandler->template = $xoTheme->template;
        $iconHandler->init($xoopsConfig['language']);

        return $iconHandler;
    }

endif;
