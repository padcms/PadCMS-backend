<?php
/**
 * @file
 * AM_Model_Db_Game class definition.
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
 * Game model class
 * @ingroup AM_Model
 */
class AM_Model_Db_Game extends AM_Model_Db_Abstract
{
    /** @var AM_Model_Db_Game */
    protected $_oType = null; /**< @type AM_Model_Db_Game */
    /**
     * Get game property value bu it's name
     * @param string $sKey
     * @param mixed $mDefault
     * @return mixed
     */
    public function getDataValue($sKey, $mDefault = null)
    {
        $oData = AM_Model_Db_Table_Abstract::factory('game_data')->findOneBy(array('key_name' => (string) $sKey));

        if (is_null($oData)) {
            return $mDefault;
        }

        return $oData->value;
    }

    /**
     * Set game property
     * @param string $sKey
     * @param mixed $mValue
     * @return AM_Model_Db_Game
     */
    public function setDataValue($sKey, $mValue)
    {
        $sKey = (string) $sKey;

        $oData = AM_Model_Db_Table_Abstract::factory('game_data')->findOneBy(array('key_name' => (string) $sKey));

        if (is_null($oData)) {
            $oData           = new AM_Model_Db_GameData();
            $oData->game     = $this->id;
            $oData->key_name = $sKey;
        }

        $oData->value = (string) $mValue;
        $oData->save();

        return $this;
    }

    /**
     * Returns game's type
     * @return AM_Model_Db_Game
     * @throws AM_Model_Db_Exception
     */
    public function getType()
    {
        if (is_null($this->_oType)) {
            $this->_oType = AM_Model_Db_Table_Abstract::factory('game_type')->findOneBy(array('id' => $this->type));

            if (is_null($this->_oType)) {
                throw new AM_Model_Db_Exception(sprintf('Game has incorrect type "%d"', $this->type));
            }
        }

        return $this->_oType;
    }
}