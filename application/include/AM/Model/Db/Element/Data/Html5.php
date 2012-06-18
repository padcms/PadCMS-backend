<?php
/**
 * @file
 * AM_Model_Db_Element_Data_Html5 class definition.
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
class AM_Model_Db_Element_Data_Html5 extends AM_Model_Db_Element_Data_Resource
{
    const DATA_KEY_HTML5_POSITION            = 'html5_position';
    const DATA_KEY_HTML5_BODY                = 'html5_body';
    const DATA_KEY_HTML5_POST_CODE           = 'post_code';
    const DATA_KEY_HTML5_RSS_LINK            = 'rss_link';
    const DATA_KEY_HTML5_RSS_LINK_NUM        = 'rss_link_number';
    const DATA_KEY_HTML5_GOOGLE_LINK_TO_MAP  = 'google_link_to_map';
    const DATA_KEY_HTML5_FACEBOOK_NAME_PAGE  = 'facebook_name_page';
    const DATA_KEY_HTML5_TWITTER_ACCOUNT     = 'twitter_account';
    const DATA_KEY_HTML5_TWITTER_TWEET_COUNT = 'twitter_tweet_number';

    protected static $_aAllowedFileExtensions = array(self::DATA_KEY_RESOURCE => array('zip'));

    public static $aBodyList = array('code'   => 'Code',
                             'google_maps'   => 'Google Maps',
                             'rss_feed'      => 'RSS Feed',
                             'facebook_like' => 'Facebook like',
                             'twitter'       => 'Twitter tweets list');

    public static $aFieldList = array('code'   => array(self::DATA_KEY_RESOURCE),
                              'google_maps'   => array('google_link_to_map'),
                              'rss_feed'      => array('rss_link', 'rss_link_number'),
                              'facebook_like' => array('facebook_name_page'),
                              'twitter'       => array('twitter_account', 'twitter_tweet_number'));

    /**
     * Remove old fields
     *
     * @param string $sValue
     * @return string
     */
    protected function _addHtml5Body($sValue)
    {
        $sOldBodyType = $this->getDataValue(self::DATA_KEY_HTML5_BODY);

        if (!$sOldBodyType || !array_key_exists($sOldBodyType, self::$aFieldList)) {
            return $sValue;
        }

        foreach (self::$aFieldList[$sOldBodyType] as $sField) {
            if (self::DATA_KEY_RESOURCE != $sField) {
                $this->delete($sField);
            }
        }

        return $sValue;
    }
}