<?php
/**
 * @file
 * AM_Model_Db_Game class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
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