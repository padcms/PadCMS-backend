<?php
/**
 * @file
 * AM_Model_Db_StaticPdf_Data_Abstract class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * This class encapsulates logic of work with horisontal pdf's resources - files, strings, etc.
 * @todo Rename
 * @ingroup AM_Model
 */
abstract class AM_Model_Db_StaticPdf_Data_Abstract implements AM_Model_Db_StaticPdf_Data_Interface
{
    const TYPE       = 'static-pdf';
    const TYPE_CACHE = 'cache-static-pdf';

    /** @var AM_Model_Db_StaticPdf */
    protected $_oHorizontalPdf = null; /**< @type AM_Model_Db_StaticPdf */

    /**
     * @param AM_Model_Db_StaticPdf $oHorizontalPdf
     */
    public final function __construct(AM_Model_Db_StaticPdf $oHorizontalPdf)
    {
        $this->_oHorizontalPdf = $oHorizontalPdf;

        $this->_init();
    }

    /**
     * Prepare data
     */
    protected function _init()
    {}

    /**
     * @return AM_Model_Db_StaticPdf
     */
    protected function _getHorizontalPdf()
    {
        return $this->_oHorizontalPdf;
    }

    /**
     * @see AM_Model_Db_StaticPdf_Data_Interface::copy()
     */
    public function copy()
    {}

    /**
     * @see AM_Model_Db_StaticPdf_Data_Interface::upload()
     */
    public function upload()
    {}

    /**
     * @see AM_Model_Db_StaticPdf_Data_Interface::delete()
     */
    public function delete()
    {
        $this->_postDelete();
    }

    /**
     * Allows post-delete logic to be applied to resource.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _postDelete()
    {

    }
}