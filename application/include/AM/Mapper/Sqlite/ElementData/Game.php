<?php
/**
 * @file
 * AM_Mapper_Sqlite_ElementData_Game class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Mapper
 */
class AM_Mapper_Sqlite_ElementData_Game extends AM_Mapper_Sqlite_Abstract
{
    const GAME_MAPPER_CLASS_PREFIX = 'AM_Mapper_Sqlite_Game_';

    /**
     * @return AM_Mapper_Sqlite_ElementData_Game
     */
    protected function _unmapCustom()
    {
        if (!empty($this->getModel()->value)) {
            $oGame = AM_Model_Db_Table_Abstract::factory('game')->findOneBy(array('id' => $this->getModel()->value));

            if (is_null($oGame)) {
                throw new AM_Mapper_Sqlite_Exception(sprintf('Element has wrong game id "%d"', $this->getModel()->value));
            }

            $sGameMapperClassName = self::GAME_MAPPER_CLASS_PREFIX . Zend_Filter::filterStatic($oGame->getType()->title, 'Word_UnderscoreToCamelCase');

            if (class_exists($sGameMapperClassName, true)) {
                $oGameMapper = new $sGameMapperClassName($oGame, array('adapter' => $this->_getAdapter()));
                $oGameMapper->unmap();
            }

            $aData = array(
                'element_id'  => $this->getModel()->id_element,
                'type'        => $this->getModel()->key_name,
                'value'       => $this->getModel()->value,
                'position_id' => 0
            );
            $this->_getSqliteGateway('element_data')->insert($aData);
        }

        return $this;
    }
}