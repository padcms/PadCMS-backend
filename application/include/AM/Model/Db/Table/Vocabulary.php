<?php
/**
 * @file
 * AM_Model_Db_Table_Vocabulary class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Model
 */
class AM_Model_Db_Table_Vocabulary extends AM_Model_Db_Table_Abstract
{
    /**
     * Create TOC vocabulary for application
     * @param AM_Model_Db_Application $oApplication
     * @return AM_Model_Db_Vocabulary
     */
    public function createTocVocabulary(AM_Model_Db_Application $oApplication)
    {
        $oVocabulary                = new AM_Model_Db_Vocabulary();
        $oVocabulary->title         = 'TOC vocabulary';
        $oVocabulary->has_hierarchy = 1;
        $oVocabulary->multiple      = 0;
        $oVocabulary->application   = $oApplication->id;
        $oVocabulary->save();

        return $oVocabulary;
    }

    /**
     * Create Tag vocabulary for application
     * @param AM_Model_Db_Application $oApplication
     * @return AM_Model_Db_Vocabulary
     */
    public function createTagVocabulary(AM_Model_Db_Application $oApplication)
    {
        $oVocabulary                = new AM_Model_Db_Vocabulary();
        $oVocabulary->title         = 'Tag vocabulary';
        $oVocabulary->has_hierarchy = 0;
        $oVocabulary->multiple      = 1;
        $oVocabulary->application   = $oApplication->id;
        $oVocabulary->save();

        return $oVocabulary;
    }
}