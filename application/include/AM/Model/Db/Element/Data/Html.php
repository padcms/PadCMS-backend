<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Html class definition.
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
 * @todo Rename
 * @ingroup AM_Model
 */
class AM_Model_Db_Element_Data_Html extends AM_Model_Db_Element_Data_Resource
{
    const DATA_KEY_TEMPLATE_TYPE = 'template_type';
    const DATA_KEY_URL           = 'html_url';

    protected static $_aAllowedFileExtensions = array(self::DATA_KEY_RESOURCE => array('zip'));

    /**
     * Check template_type value
     * @param string $sValue
     * @return string
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addTemplateType($sValue)
    {
        $sValue = (string) $sValue;

        if (!in_array($sValue, array('touch', 'rotation'))) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given', self::DATA_KEY_TEMPLATE_TYPE));
        }

        return $sValue;
    }

    /**
     * Check html_url value
     * @param string $sValue
     * @return string
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addHtmlUrl($sValue)
    {
        $sValue = (string) $sValue;

        if (!Zend_Uri::check($sValue)) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given', self::DATA_KEY_URL));
        }

        //Remove all resources keys from element
        $this->delete(self::DATA_KEY_RESOURCE);

        return $sValue;
    }

    /**
     * @param string $sValue
     * @return string
     */
    protected function _addResource($sValue)
    {
        //Remove all url keys from element
        $this->delete(self::DATA_KEY_URL);

        return $sValue;
    }

    public function getImageType($sKeyName = self::DATA_KEY_RESOURCE)
    {
        return 'zip';
    }
}