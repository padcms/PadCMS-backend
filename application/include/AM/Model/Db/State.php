<?php
/**
 * @file
 * AM_Model_Db_State class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * State model class
 * @ingroup AM_Model
 */
class AM_Model_Db_State extends AM_Model_Db_Abstract
{
    const STATE_WORK_IN_PROGRESS = 1;
    const STATE_PUBLISHED        = 2;
    const STATE_ARCHIVED         = 3;
    const STATE_FOR_REVIEW       = 4;

    /**
     * Returns status name by it's number
     *
     * @param string $sState
     * @return string|null
     */
    public static function stateToText($sState)
    {
        switch ($sState) {
            case self::STATE_PUBLISHED:
                return 'published';
            case self::STATE_ARCHIVED:
                return 'archived';
            case self::STATE_WORK_IN_PROGRESS:
                return 'work-in-progress';
        }

        return null;
    }
}