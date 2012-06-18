<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Games class definition.
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
class AM_Model_Db_Element_Data_Games extends AM_Model_Db_Element_Data_Abstract
{
    const DATA_KEY_GAME_ID       = 'game';
    const DATA_KEY_GAME_TYPE     = 'game_type';

    const GAME_TYPE_CROSSWORD = 'crossword';

    public static $aGamesList = array(self::GAME_TYPE_CROSSWORD => 'Crossword');
}