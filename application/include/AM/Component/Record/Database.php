<?php
/**
 * @file
 * AM_Component_Record_Database class definition.
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
 * Base record component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_Record_Database extends Volcano_Component_Record_Database
{
    /**
     * @return void
     */
    public function show()
    {
        parent::show();

        foreach ($this->controls as $sName => $oControl) {
            $oControl = array('errors' => $oControl->getErrors());

            if (isset($this->view->{$sName})) {
                $this->view->{$sName} = array_merge($oControl, $this->view->{$sName});
            } else {
                $this->view->{$sName} = $oControl;
            }
        }
    }

    protected function update() {
        foreach ($this->controls as $control) {
            if ($control instanceof AM_Component_Control_Tags) {
                $aTags = explode(', ', $control->getValue());
                $iATagsSize = count($aTags);
                if (empty($aTags[$iATagsSize-1])) {
                    array_pop($aTags);
                }
                if (!empty($aTags[0])) {
                    $oVocabulary = AM_Model_Db_Table_Abstract::factory('application')
                        ->findOneBy('id', $this->applicationId)
                        ->getVocabularyTag();
                    $oTags = AM_Model_Db_Table_Abstract::factory('term')->getTagsByTitle($aTags, 'issue', $oVocabulary->id, $this->primaryKeyValue);
                    $aExistingTags = array();
                    $aExistingTermEntities = array();
                    $aCurrentIssueTags = array();
                    $oTermEntities = AM_Model_Db_Table_Abstract::factory('termEntity')->findAllBy(
                        array(
                             'entity' => $this->primaryKeyValue,
                             'entity_type' => 'issue',
                        ));

                    foreach ($oTermEntities as $oTermEntity) {
                        $aExistingTermEntitiesId[$oTermEntity->term] = $oTermEntity->id;
                    }
                    foreach ($oTags as $oTag) {
                        if (empty($oTag->term_id)) {
                            $aExistingTags[$oTag->id] = $oTag->title;
                        }
                        elseif (!empty($aExistingTermEntitiesId[$oTag->id])) {
                            unset($aExistingTermEntitiesId[$oTag->id]);
                            $aCurrentIssueTags[$oTag->id] = $oTag->title;
                        }
                    }
                    $aNewTags = array_diff($aTags, $aExistingTags);
                    $aNewTags = array_diff($aNewTags, $aCurrentIssueTags);
                    foreach ($aNewTags as $sTagTitle) {
                        $oTagTerm = $oVocabulary->createTag($sTagTitle);
                        $aExistingTags[$oTagTerm->id] = $oTagTerm->title;
                    }
                    if (!empty($aExistingTermEntitiesId)) {
                        AM_Model_Db_Table_Abstract::factory('termEntity')->deleteTermEntities($aExistingTermEntitiesId);
                    }
                    foreach ($aExistingTags as $iTagId => $sTagTitle) {
                        $oTermEntity = AM_Model_Db_Table_Abstract::factory('termEntity')->createTermEntity($iTagId, $this->primaryKeyValue, 'issue');
                    }
                }
            }
        }

        return parent::update();
    }

    protected function insert() {
        if (!parent::insert()) {
            return false;
        }

        foreach ($this->controls as $control) {
            if ($control instanceof AM_Component_Control_Tags) {
                $aTags = explode(', ', $control->getValue());
                $iATagsSize = count($aTags);
                if (empty($aTags[$iATagsSize-1])) {
                    array_pop($aTags);
                }
                if (!empty($aTags[0])) {
                    $oVocabulary = AM_Model_Db_Table_Abstract::factory('application')
                        ->findOneBy('id', $this->applicationId)
                        ->getVocabularyTag();
                    $oTags = AM_Model_Db_Table_Abstract::factory('term')->getTagsByTitle($aTags, 'issue', $oVocabulary->id);
                    $aExistingTags = array();
                    foreach ($oTags as $oTag) {
                        if (empty($oTag->term)) {
                            $aExistingTags[$oTag->id] = $oTag->title;
                        }
                    }
                    $aNewTags = array_diff($aTags, $aExistingTags);
                   foreach ($aNewTags as $sTagTitle) {
                       $oTagTerm = $oVocabulary->createTag($sTagTitle);
                       $aExistingTags[$oTagTerm->id] = $oTagTerm->title;

                   }
                   foreach ($aExistingTags as $iTagId => $sTagTitle) {
                       $oTermEntity = AM_Model_Db_Table_Abstract::factory('termEntity')->createTermEntity($iTagId, $this->primaryKeyValue, 'issue');
                   }
                }
            }
        }
        return "insert";
    }
}