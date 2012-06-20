<?php
/**
 * @file
 * AM_Handler_Temp class definition.
 *
 * LICENSE
 *
 * This software is governed by the CeCILL-C  license under French law and
 * abiding by the rules of distribution of free software.  You can  use,
 * modify and/ or redistribute the software under the terms of the CeCILL-C
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and  rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty  and the software's author,  the holder of the
 * economic rights,  and the successive licensors  have only  limited
 * liability.
 *
 * In this respect, the user's attention is drawn to the risks associated
 * with loading,  using,  modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean  that it is complicated to manipulate,  and  that  also
 * therefore means  that it is reserved for developers  and  experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or
 * data to be ensured and,  more generally, to use and operate it in the
 * same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL-C license and that you accept its terms.
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