<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Html class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
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
}