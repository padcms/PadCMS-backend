<?php
/**
 * @file
 * AM_View_Helper_Field_Games class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_View_Helper
 */
class AM_View_Helper_Field_Games extends AM_View_Helper_Field
{
    public function show()
    {
        $aFieldView = array();

        $aFieldView['current_game_type'] = null;

        $aElements = $this->_oPage->getElementsByField($this->_oField);
        if (count($aElements)) {
            $oElement                        = $aElements[0];
            $aFieldView['current_game_type'] = $oElement->getResources()->getDataValue(AM_Model_Db_Element_Data_Games::DATA_KEY_GAME_TYPE);
        }

        $aFieldView['game_types'] = AM_Model_Db_Element_Data_Games::$aGamesList;

        $this->_setFieldData($aFieldView);

        parent::show();
    }
}
