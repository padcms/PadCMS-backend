<?php
/**
 * @file
 * FieldGamesCrosswordController class definition.
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
 * This class is responsible for editing elements with type AM_Model_Db_FieldType::TYPE_GAMES
 * Controller implementation for Crossword game
 *
 * @ingroup AM_Controller_Action
 * @ingroup AM_Page_Editor
 */
class FieldGamesCrosswordController extends AM_Controller_Action_Field
{
    /**
     * Crossword-game builder action
     */
    public function builderAction()
    {

    }

    /**
     * Action saves key-value data for element
     */
    public function saveAction()
    {
        $aMessage = array('status' => 0);
        try {
            $iGridWidth  = intval($this->_getParam('gridWidth'));
            $iGridHeight = intval($this->_getParam('gridHeight'));
            $aWords      = (array) $this->_getParam('words');

            $oField = AM_Model_Db_Table_Abstract::factory('field')->findOneBy('id', $this->_iFieldId);
            /* @var $oField AM_Model_Db_Field */
            if (is_null($oField)) {
                throw new AM_Exception(sprintf('Field with id "%d" not found.', $this->_iFieldId));
            }

            $oPage = AM_Model_Db_Table_Abstract::factory('page')->findOneBy('id', $this->_iPageId);
            /* @var $oPage AM_Model_Db_Page */
            if (is_null($oPage)) {
                throw new AM_Exception(sprintf('Page with id "%d" not found.', $this->_iPageId));
            }

            $oElement = $oPage->getElementForField($oField);
            /* @var $oElement AM_Model_Db_Element */

            $oGame = AM_Model_Db_Table_Abstract::factory('game')
                    ->findOneBy(array('revision' => $oPage->revision, 'type' => AM_Model_Db_GameType::GAME_TYPE_CROSSWORD));

            if (is_null($oGame)) {
                $oGame           = new AM_Model_Db_Game();
                $oGame->revision = $oPage->revision;
                $oGame->type     = AM_Model_Db_GameType::GAME_TYPE_CROSSWORD;
                $oGame->save();
            }

            $oGame->setDataValue('grid_width', $iGridWidth);
            $oGame->setDataValue('grid_height', $iGridHeight);

            AM_Model_Db_Table_Abstract::factory('game_crossword_word')->deleteBy(array('game' => $oGame->id));

            foreach ($aWords as $aWord) {
                $oWordModel             = new AM_Model_Db_GameCrosswordWord();
                $oWordModel->start_from = $aWord['startFrom'];
                $oWordModel->question   = $aWord['question'];
                $oWordModel->answer     = $aWord['answer'];
                $oWordModel->length     = $aWord['lenght'];
                $oWordModel->direction  = $aWord['direction'];
                $oWordModel->game       = $oGame->id;
                $oWordModel->save();
            }

            $oElement->getResources()->addKeyValue(AM_Model_Db_Element_Data_Games::DATA_KEY_GAME_ID, $oGame->id);
            $oElement->getResources()->addKeyValue(AM_Model_Db_Element_Data_Games::DATA_KEY_GAME_TYPE, $oGame->getType()->title);

            $oPage->setUpdated(false);
            $aMessage['status'] = 1;
        } catch (Exception $oException) {
            $aMessage['message'] = $this->__('Error. Can\'t set value! ') . $oException->getMessage();
        }

        return $this->getHelper('Json')->sendJson($aMessage, false);
    }
}