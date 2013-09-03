<?php
/**
 * Volcano Framework
 *
 * @category Volcano
 * @package Volcano_Component_Control
 * @subpackage DatabaseField
 * @author Ilya Gruzinov
 * @version $Revision$
 * @license http://vifm.volcanoideas.com/license/
 */

include_once 'Volcano/Component/Control.php';
/**
 * Control with database binding
 *
 * @category Volcano
 * @package Volcano_Component_Control
 * @subpackage Database
 */
class AM_Component_Control_Tags extends Volcano_Component_Control {
    /**
     * Constructor
     *
     * @param AM_Controller_Action $actionController Controller
     * @param string $name Component name
     * @param string $title Title of control
     * @param array $validationRules Validation rules. Each rule is array :
     * 		[validationtype, param1, param2,...]
     *     type can be: require, integer, float, numeric, maximum value, minimum value,
     * 	 	maximum length, minimum length, range, range length, regexp, email, function
     */
    public function __construct(AM_Controller_Action $actionController, $name, $title = null, array $validationsRules = null) {
        parent::__construct($actionController, $name, $title, $validationsRules);
    }

    public function insertValue($iIssueId) {
        $iApplicationId = $this->actionController->getApplicationId();
        $aTags = explode(', ', $this->getValue());
        $iATagsSize = count($aTags);
        if (empty($aTags[$iATagsSize-1])) {
            array_pop($aTags);
        }
        if (!empty($aTags[0])) {
            $oVocabulary = AM_Model_Db_Table_Abstract::factory('application')
                ->findOneBy('id', $iApplicationId)
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
                AM_Model_Db_Table_Abstract::factory('termEntity')->createTermEntity($iTagId, $iIssueId, 'issue');
            }
        }
    }

    public function updateValue() {
        $iApplicationId = $this->actionController->getApplicationId();
        $iIssueId = $this->actionController->getIssueId();
        $aTags = explode(', ', $this->getValue());
        $iATagsSize = count($aTags);
        if (empty($aTags[$iATagsSize-1])) {
            array_pop($aTags);
        }
        if (!empty($aTags[0])) {
            $oVocabulary = AM_Model_Db_Table_Abstract::factory('application')
                ->findOneBy('id', $iApplicationId)
                ->getVocabularyTag();
            $oTags = AM_Model_Db_Table_Abstract::factory('term')->getTagsByTitle($aTags, 'issue', $oVocabulary->id, $iIssueId);
            $aExistingTags = array();
            $aCurrentIssueTags = array();
            $oTermEntities = AM_Model_Db_Table_Abstract::factory('termEntity')->findAllBy(
                array(
                     'entity' => $iIssueId,
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
                AM_Model_Db_Table_Abstract::factory('termEntity')->createTermEntity($iTagId, $iIssueId, 'issue');
            }
        }
    }

}
