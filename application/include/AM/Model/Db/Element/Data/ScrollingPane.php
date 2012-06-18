<?php
/**
 * @file
 * AM_Model_Db_Element_Data_ScrollingPane class definition.
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
class AM_Model_Db_Element_Data_ScrollingPane extends AM_Model_Db_Element_Data_Resource
{
    const DATA_KEY_TOP = 'top';

    protected static $_aAllowedFileExtensions = array(self::DATA_KEY_RESOURCE => array('pdf', 'zip', 'png'));

    /**
     * Check top value
     * @param int $iValue
     * @return int
     * @throws AM_Model_Db_Element_Data_Exception
     */
    protected function _addTop($iValue)
    {
        $iValue = intval($iValue);

        if ($iValue < 0) {
            throw new AM_Model_Db_Element_Data_Exception(sprintf('Wrong parameter "%s" given', self::DATA_KEY_TOP));
        }

        return $iValue;
    }

    /**
     * Retruns name of the resource's preset name to resize
     *
     * @return string
     */
    protected function _getThumbnailPresetName()
    {
        $iTemplate  = $this->getElement()->getPage()->template;
        $sFieldType = $this->getElement()->getFieldTypeTitle();

        if (AM_Model_Db_Template::TPL_SCROLLING_PAGE_HORIZONTAL == $iTemplate && AM_Model_Db_FieldType::TYPE_SCROLLING_PANE == $sFieldType) {
            return self::TYPE . '-horizontal-scroll';
        }

        return self::TYPE . '-' . $this->getElement()->getPage()->getOrientation();
    }
}