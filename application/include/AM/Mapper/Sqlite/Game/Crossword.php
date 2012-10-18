<?php
/**
 * @file
 * AM_Mapper_Sqlite_Game_Crossword class definition.
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
                'start_x'    => $oWord->start_x,
                'start_y'    => $oWord->start_y
            );
            $this->_getSqliteGateway('game_crossword_word')->insert($aCrosswordGameWordData);
        }

        return $this;
    }
}