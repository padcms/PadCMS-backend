<?php
/**
 * @file
 * AM_Tools_String class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * String helper
 * @ingroup AM_Tools
 */
class AM_Tools_String
{
    const ID_LENGTH = 8;
    /**
     * Generate path for given id
     * @param int $iId
     * @param string $sSeparator
     * @return string Path to the generated folder like '00/00/01/23' if id = 123
     */
    public static function generatePathFromId($iId, $sSeparator = DIRECTORY_SEPARATOR)
    {
        $iId = intval($iId);
        //Pad a string to a certain length
        $iId = str_pad($iId, self::ID_LENGTH, '0', STR_PAD_LEFT);
        preg_match_all('/[0-9]{2}/i', $iId, $match);
        $sPath = implode($sSeparator, $match[0]);

        return $sPath;
    }
}