<?php
/**
 * @file
 * AM_Model_Db_StaticPdf_Data_Abstract class definition.
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