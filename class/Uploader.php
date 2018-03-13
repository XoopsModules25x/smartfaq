<?php namespace XoopsModules\Smartfaq;

/**
 * CBB, XOOPS forum module
 *
 * @copyright   The XOOPS Project http://xoops.sf.net
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      Taiwen Jiang (phppp or D.J.) <phppp@users.sourceforge.net>
 * @since       4.00
 * @package     module::newbb
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

require_once XOOPS_ROOT_PATH . '/class/uploader.php';

/**
 * Class Uploader
 */
class Uploader extends \XoopsMediaUploader
{
    /**
     * No admin check for uploads
     */
    /**
     * Constructor
     *
     * @param string    $uploadDir
     * @param array|int $allowedMimeTypes
     * @param int       $maxFileSize
     * @param int       $maxWidth
     * @param int       $maxHeight
     */
    public function __construct($uploadDir, $allowedMimeTypes = 0, $maxFileSize = 0, $maxWidth = 0, $maxHeight = 0)
    {
        if (!is_array($allowedMimeTypes)) {
            if (empty($allowedMimeTypes) || '*' === $allowedMimeTypes) {
                $allowedMimeTypes = [];
            } else {
                $allowedMimeTypes = array_filter(array_map('trim', explode('|', strtolower($allowedMimeTypes))));
            }
        }
        $_allowedMimeTypes = [];
        $extensionToMime   = include $GLOBALS['xoops']->path('/include/mimetypes.inc.php');
        foreach ($allowedMimeTypes as $type) {
            if (isset($extensionToMime[$type])) {
                $_allowedMimeTypes[] = $extensionToMime[$type];
            } else {
                $_allowedMimeTypes[] = $type;
            }
        }
        parent::__construct($uploadDir, $_allowedMimeTypes, $maxFileSize, $maxWidth, $maxHeight);
    }

    /**
     * Set the CheckMediaTypeByExt
     * Deprecated
     *
     * @param bool|string $value
     */
    public function setCheckMediaTypeByExt($value = true)
    {
    }

    /**
     * Set the imageSizeCheck
     * Deprecated
     *
     * @param string $value
     */
    public function setImageSizeCheck($value)
    {
    }

    /**
     * Set the fileSizeCheck
     * Deprecated
     *
     * @param string $value
     */
    public function setFileSizeCheck($value)
    {
    }

    /**
     * Get the file extension
     *
     * @return string
     */
    public function getExt()
    {
        $this->ext = strtolower(ltrim(strrchr($this->getMediaName(), '.'), '.'));

        return $this->ext;
    }
}
