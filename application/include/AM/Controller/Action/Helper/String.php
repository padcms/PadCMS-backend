<?php
/**
 * @file
 * AM_Controller_Action_Helper_String class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * The helper for string manipulation
 *
 * @ingroup AM_Controller_Action_Helper
 * @ingroup AM_Controller_Action
 */
class AM_Controller_Action_Helper_String extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Cut string with length more then $iMaxStringLength symbols
     *
     * @param string $sString
     * @param int $iMaxStringLength
     * @return string
     */
    public function cut($sString, $iMaxStringLength = 15)
    {
        if (mb_strlen($sString, 'UTF-8') > $iMaxStringLength) {
            $sString = mb_substr($sString, 0, $iMaxStringLength, 'UTF-8') . '...';
        }

        return $sString;
    }
}