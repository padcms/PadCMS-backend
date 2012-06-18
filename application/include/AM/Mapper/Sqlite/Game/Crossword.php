<?php
/**
 * @file
 * AM_Mapper_Sqlite_Game_Crossword class definition.
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
class AM_Mapper_Sqlite_Game_Crossword extends AM_Mapper_Sqlite_Abstract
{
    /**
     * @return AM_Mapper_Sqlite_ElementData_PdfInfo
     */
    protected function _unmapCustom()
    {
        $aCrosswordGameData = array(
            'id'          => $this->getModel()->id,
            'title'       => $this->getModel()->title,
            'grid_width'  => $this->getModel()->getDataValue('grid_width'),
            'grid_height' => $this->getModel()->getDataValue('grid_height')
        );
        $this->_getSqliteGateway('game_crossword')->insert($aCrosswordGameData);

        $oWords = AM_Model_Db_Table_Abstract::factory('game_crossword_word')->findAllBy(array('game' => $this->getModel()->id));

        foreach ($oWords as $oWord) {
            $aCrosswordGameWordData = array(
                'game'       => $this->getModel()->id,
                'answer'     => $oWord->answer,
                'question'   => $oWord->question,
                'length'     => $oWord->length,
                'direction'  => $oWord->direction,
                'start_from' => $oWord->start_from
            );
            $this->_getSqliteGateway('game_crossword_word')->insert($aCrosswordGameWordData);
        }

        return $this;
    }
}