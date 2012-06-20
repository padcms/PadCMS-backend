<?php
/**
 * @file
 * AM_Mapper_Abstract class definition.
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
 * @author Copyright (c) PadCMS (http://www.padcms.net)
 * @version $DOXY_VERSION
 */

/**
 * @defgroup AM_Mapper
 */

/**
 * @ingroup AM_Mapper
 */
abstract class AM_Mapper_Abstract implements AM_Mapper_Interface
{
    /** @var AM_Model_Db_Abstract **/
    protected $_oModel = null; /**< @type AM_Model_Db_Abstract */
    /** @var Zend_Config **/
    protected $_oConfig = null; /**< @type Zend_Config */

    /**
     * @param AM_Model_Db_Abstract $oModel
     * @param array $aOptions
     */
    public final function __construct(AM_Model_Db_Abstract $oModel, $aOptions = array())
    {
        $this->setModel($oModel);
        $this->_init($aOptions);
    }

    /**
     * Initialization
     *
     * @param array $aOptions
     * @return AM_Mapper_Abstract
     */
    protected function _init($aOptions = array())
    {
        return $this;
    }

    /**
     * Create mapper object
     * @param AM_Model_Db_Abstract $oModel
     * @param string $sProvider
     * @param array $aOptions
     * @return AM_Mapper_Abstract
     */
    public static final function factory(AM_Model_Db_Abstract $oModel, $sProvider, $aOptions = array())
    {
        $sModelTableName  = $oModel->getTableName();
        $sProvider        = ucfirst(Zend_Filter::filterStatic($sProvider, 'Word_UnderscoreToCamelCase'));
        $sModelTableName  = ucfirst(Zend_Filter::filterStatic($sModelTableName, 'Word_UnderscoreToCamelCase'));
        $sMapperClassName = 'AM_Mapper_' . $sProvider . "_" . $sModelTableName;

        $sFile = str_replace('_', DIRECTORY_SEPARATOR, $sMapperClassName).'.php';
        if (!AM_Tools_Standard::isReadable($sFile)) {
            throw new AM_Mapper_Exception(sprintf('Mapper class "%s" not found', $sMapperClassName), 503);
        }

        $oMapper = new $sMapperClassName($oModel, $aOptions);

        return $oMapper;
    }

    /**
     * Get model name
     * @return string
     * @throws AM_Mapper_Exception
     */
    protected final function _getModelName()
    {
        $sClassName = get_class($this);

        $aChunks = explode("_", $sClassName);

        $sName = array_pop($aChunks);

        if (empty($sName)) {
            throw new AM_Mapper_Exception(sprintf('Mapper "%s" doesn\'t have model', $sClassName));
        }

        return $sName;
    }

    /**
     * Set model instance
     * @param AM_Model_Db_Abstract $oModel
     * @throws AM_Mapper_Exception
     */
    public final function setModel(AM_Model_Db_Abstract $oModel)
    {
        $this->_oModel = $oModel;

        return $this;
    }

    /**
     * Returns model
     * @return AM_Model_Db_Abstract
     */
    public final function getModel()
    {
        return $this->_oModel;
    }

    /**
     * Get config instance
     * @return Zend_Config
     */
    public function getConfig()
    {
        if (is_null($this->_oConfig)) {
            $this->_oConfig = Zend_Registry::get('config');
        }

        return $this->_oConfig;
    }

    /**
     * Set config
     * @param Zend_Config $oConfig
     * @return AM_Mapper_Abstract
     * @throws AM_Mapper_Exception
     */
    public function setConfig($oConfig)
    {
        if (!$oConfig instanceof Zend_Config) {
            throw new AM_Mapper_Exception('Config must be an instance of "Zend_Config"');
        }

        $this->_oConfig = $oConfig;

        return $this;
    }
}