<?php
/**
 * @file
 * AM_Handler_Temp class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Temp handler - creates temporary files in system temp dir, deletes files/dirs in destructor
 *
 * @ingroup AM_Handler
 */
class AM_Handler_Temp extends AM_Handler_Abstract
{
    const BASE_TEMP_DIR = 'padcms';

    /** @var AM_Handler_Temp */
    public static $oInstance = null; /**< @type AM_Handler_Temp */
    /** @var string **/
    protected $_sDirectory   = null; /**< @type string */

    /**
     * @return AM_Handler_Temp
     */
    public static function getInstance()
    {
        if (is_null(self::$oInstance)) {
            self::$oInstance = new self();
        }

        return self::$oInstance;
    }

    public function __construct()
    {
        $this->begin();
    }

    /**
     * Init temp folder
     * @return AM_Handler_Temp
     */
    public function begin()
    {
        $this->_sDirectory = sys_get_temp_dir()
                            . DIRECTORY_SEPARATOR . self::BASE_TEMP_DIR
                            . DIRECTORY_SEPARATOR . uniqid();

        if (!AM_Tools_Standard::getInstance()->is_dir($this->_sDirectory)) {
            AM_Tools_Standard::getInstance()->mkdir($this->_sDirectory, 0777, true);
        }

        return $this;
    }

    /**
     * Create temp dir for current session
     * @param string $sDirName
     * @return string Path to created dir
     */
    public function getDir($sDirName = null)
    {
        if (empty($this->_sDirectory)) {
            $this->begin();
        }

        if (empty($sDirName)) {
            $sDirName = 'dir-' . uniqid();
        }

        $sDir = $this->_sDirectory . DIRECTORY_SEPARATOR . $sDirName;
        AM_Tools_Standard::getInstance()->mkdir($sDir, 0777, true);

        return $sDir;
    }

    /**
     * Generate temp filename for current session
     * @param string $sFileName
     * @return string
     */
    public function getFile($sFileName = null)
    {
        if (empty($this->_sDirectory)) {
            $this->begin();
        }

        if (empty($sFileName)) {
            $sFileName = 'file-' . uniqid();
        }

        $sFile = $this->getDir() . DIRECTORY_SEPARATOR . $sFileName;

        return $sFile;
    }

    /**
     * Get files in temp dirrectory
     * @todo: refactor
     * @return array
     */
    public function getFiles($dirName, $sPattern = null)
    {
        $sPattern = empty($sPattern)? '*':$sPattern;

        $aFiles = AM_Tools_Finder::type('file')
                ->name($sPattern)
                ->in($dirName);

        return $aFiles;
    }

    /**
     * Remove all files and dirs created during session
     * @return AM_Handler_Temp
     */
    public function end()
    {
        if (empty($this->_sDirectory)) {
            return $this;;
        }

        AM_Tools_Standard::getInstance()->clearDir($this->_sDirectory);
        AM_Tools_Standard::getInstance()->rmdir($this->_sDirectory);

        return $this;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->end();
    }
}