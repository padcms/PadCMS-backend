<?php
/**
 * @file
 * AM_Mapper_Abstract class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
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