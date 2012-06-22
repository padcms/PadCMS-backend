<?php
/**
 * @file
 * AM_Model_Db_Page class definition.
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
 * Page model class
 * @ingroup AM_Model
 */
class AM_Model_Db_Page extends AM_Model_Db_Base_NestedSet
{
    /** @var AM_Model_Db_Rowset_Element **/
    protected $_oElements = null; /**< @type AM_Model_Db_Rowset_Element */

    /** @var AM_Model_Db_PageBackground **/
    protected $_oBackground = null; /**< @type AM_Model_Db_PageBackground */

    /** @var AM_Model_Db_Revision **/
    protected $_oRevision = null; /**< @type AM_Model_Db_Revision */

    /** @var AM_Model_Db_Issue **/
    protected $_oIssue = null; /**< @type AM_Model_Db_Issue */

    /** @var AM_Model_Db_Template **/
    protected $_oTemplate = null; /**< @type AM_Model_Db_Template */

    /** @var array **/
    protected $_aTerms = array(); /**< @type array */

    /** @var int The parent's id */
    public $iParentId = null; /**< @type int The parent's id */
    /** @var string The connection type to the parrent page: right, left, bottom, top **/
    public $sLinkType = null; /**< @type string The connection type to the parrent page */
    /** @var string The orientation type: vertical, horizontal **/
    public $sOrientation = null; /**< @type string The orientation type: vertical, horizontal  */

    const LINK_LEFT   = 'left';
    const LINK_RIGHT  = 'right';
    const LINK_TOP    = 'top';
    const LINK_BOTTOM = 'bottom';

    //Bit masks for connections
    const HAS_CONNECTION_LEFT   = 8; //1000
    const HAS_CONNECTION_RIGHT  = 4; //0100
    const HAS_CONNECTION_TOP    = 2; //0010
    const HAS_CONNECTION_BOTTOM = 1; //0001

    /** @var array The priority of field types. The field with the bigest priority is background */
    /** @todo We have to get page background by field weight */
    public static $aBackgroundPriority = array(
        AM_Model_Db_FieldType::TYPE_BODY           => 1,
        AM_Model_Db_FieldType::TYPE_BACKGROUND     => 2
    ); /**< @type array The priority of field types. The field with the bigest priority is background */

    /** @var array The possible types of connectors */
    public static $aLinkTypes = array(self::LINK_LEFT, self::LINK_RIGHT, self::LINK_TOP, self::LINK_BOTTOM); /**< @type array The possible types of connectors */

    /**
     * @todo refactoring, optimisation
     */
    protected function _init()
    {
        $oPageImposition = AM_Model_Db_Table_Abstract::factory('page_imposition')->findOneBy(array('is_linked_to' => $this->id));
        if (!is_null($oPageImposition)) {
            $this->iParentId = $oPageImposition->page;
            $this->setLinkType($oPageImposition->link_type);
        }
    }

    /**
     * Set connection type to the parrent page
     * @param string $sLinkType
     * @return AM_Model_Db_Page
     * @throws AM_Model_Db_Exception
     */
    public function setLinkType($sLinkType)
    {
        if (!in_array($sLinkType, self::$aLinkTypes)) {
            throw new AM_Model_Db_Exception(sprintf('Wrong link type given "%s"', $sLinkType));
        }

        $this->sLinkType = $sLinkType;

        return $this;
    }

    /**
     * Get reversed link type
     * @param string $sLinkType - Link type
     * @return string - Reversed link type
     */
    public function reverseLinkType($sLinkType)
    {
        $sReversedType = null;

        switch ($sLinkType) {
            case self::LINK_LEFT:
                $sReversedType = self::LINK_RIGHT;
                break;
            case self::LINK_RIGHT:
                $sReversedType = self::LINK_LEFT;
                break;
            case self::LINK_TOP:
                $sReversedType = self::LINK_BOTTOM;
                break;
            case self::LINK_BOTTOM:
                $sReversedType = self::LINK_TOP;
                break;
            default:
                 throw new AM_Model_Db_Exception(sprintf('Wrong link type given "%s"', $sLinkType));
        }

        return $sReversedType;
    }

    /**
     * Get page link type
     * @return string|null
     */
    public function getLinkType()
    {
        return $this->sLinkType;
    }

    /**
     * Move a page from one revision to other
     * @param AM_Model_Db_Revision $oRevisionTo
     * @return AM_Model_Db_Page
     */
    public function moveToRevision(AM_Model_Db_Revision $oRevisionTo)
    {
        if ($this->revision == $oRevisionTo->id && $this->user == $oRevisionTo->user) {
            return $this;
        }

        $this->user        = $oRevisionTo->user;
        $this->revision    = $oRevisionTo->id;
        $this->setReadOnly(false);
        $this->save();

        return $this;
    }

    /**
     * Copy a page from one revision to other
     * @param AM_Model_Db_Revision $oRevisionTo
     * @return AM_Model_Db_Page
     */
    public function copyToRevision(AM_Model_Db_Revision $oRevisionTo)
    {
        $oElements   = $this->getElements();
        $oBackground = $this->getPageBackground();

        $aData             = array();
        $aData['revision'] = $oRevisionTo->id;
        $aData['user']     = $oRevisionTo->user;
        $aData['created']  = null;
        $aData['updated']  = null;
        $aData['pdf_page'] = null;

        $this->copy($aData);

        if (!empty($oElements)) {
              $oElements->copyToPage($this);
        }

        if (!empty($oBackground)) {
            $oBackground->copyToPage($this);
        }

        if (!empty($this->_aTerms)){
            foreach ($this->_aTerms as $oTerm) {
                /* @var $oTerm AM_Model_Db_Term */
                $oTerm->saveToPage($this);
            }
        }

        return $this;
    }

    /**
     * Save page imposition
     * @return int|null Imposition record id
     */
    public function savePageImposition()
    {
        $oParent = $this->getParent();
        if (!empty($oParent)) {
            $oPageImposition = new AM_Model_Db_PageImposition();
            $oPageImposition->page         = $oParent->id;
            $oPageImposition->is_linked_to = $this->id;
            $oPageImposition->link_type    = $this->getLinkType();
            $oPageImposition->save();

            return $oPageImposition->id;
        }

        return null;
    }

    /**
     * Set page elements
     * @param AM_Model_Db_Rowset_Element $oElements
     * @return AM_Model_Db_Page
     */
    public function setElements(AM_Model_Db_Rowset_Element $oElements)
    {
        $this->_oElements = $oElements;

        return $this;
    }

    /**
     * Get page elements
     * @return AM_Model_Db_Rowset_Element | null
     */
    public function getElements()
    {
        if (empty($this->_oElements)) {
            $this->fetchElements();
        }

        return $this->_oElements;
    }

    /**
     * Get elements by field
     * @param AM_Model_Db_Field $oField
     * @return array
     * @todo: make one request
     */
    public function getElementsByField(AM_Model_Db_Field $oField)
    {
        $oElements = $this->getElements();

        $aResult = array();
        foreach ($oElements as $oElement) {
            if ($oField->id == $oElement->field) {
                $aResult[] = $oElement;
            }
        }

        return $aResult;
    }

    /**
     * Find element with given field or create new element
     * @param AM_Model_Db_Field $oField
     * @return AM_Model_Db_Element
     */
    public function getElementForField(AM_Model_Db_Field $oField)
    {
        //@todo refactoring
        $sPostfix   = $oField->getFieldType()->title;
        $sPostfix   = ucfirst(Zend_Filter::filterStatic($sPostfix, 'Word_UnderscoreToCamelCase'));
        $sClassName = AM_Model_Db_Element::RESOURCE_CLASS_PREFIX . '_' . $sPostfix;

        if (!class_exists($sClassName, true)) {
            throw new AM_Model_Db_Exception(sprintf('Element data class "%s" not found', $sClassName));
        }

        $element = $sClassName::getElementForPageAndField($this, $oField);

        return $element;
    }

    /**
     * Fetching page elements from DB
     * @return AM_Model_Db_Page
     */
    public function fetchElements()
    {
        $this->_oElements = AM_Model_Db_Table_Abstract::factory('element')
                ->findAllBy('page', $this->id, array('weight'));

        return $this;
    }

    /**
     * Set page background
     * @param AM_Model_Db_PageBackground $oPageBackground
     * @return AM_Model_Db_Page
     */
    public function setPageBackground(AM_Model_Db_PageBackground $oPageBackground)
    {
        $this->_oBackground = $oPageBackground;

        return $this;
    }

    /**
     * Get background URI
     * @return string | null URI
     */
    public function getPageBackgroundUri()
    {
        $oBackground            = $this->getPageBackground();
        $oTemplateBackgroundUri = $this->getTemplate()->getPicture('map', $this->getOrientation());

        if (is_null($oBackground)) {
            return $oTemplateBackgroundUri;
        }

        $sFileName = $oBackground->filename;
        $aFileInfo = pathinfo($oBackground->filename);
        if ('pdf' == $aFileInfo['extension']) {
            $sFileName = $aFileInfo['filename'] . '.png';
        }

        $sUri = AM_Tools::getImageUrl(
                    AM_Handler_Thumbnail_Interface::PRESET_MAP_ITEM . '-' . $this->getOrientation(),
                    $oBackground->type,
                    $oBackground->id,
                    $sFileName . '?' . strtotime($this->updated)
                );

        return $sUri;
    }

    /**
     * Get background object
     * @return AM_Model_Db_PageBackground | null
     */
    public function getPageBackground()
    {
        if (empty($this->_oBackground)) {
            $this->fetchPageBackground();
        }

        return $this->_oBackground;
    }

    /**
     * Fetching page background
     * @return AM_Model_Db_Page
     */
    public function fetchPageBackground()
    {
        $this->_oBackground = AM_Model_Db_Table_Abstract::factory('page_background')->findOneBy(array('page' => $this->id));

        if (!is_null($this->_oBackground)) {
            $oElement = AM_Model_Db_Table_Abstract::factory('element')->findOneBy(array('id' => $this->_oBackground->id));
            if (is_null($oElement)) {
                throw new AM_Model_Db_Exception(sprintf('Background has wrong element id ""%d', $this->_oBackground->id));
            }
            $this->_oBackground->setElement($oElement);
        }

        return $this;
    }

    /**
     * Get revision instance
     * @return AM_Model_Db_Revision
     */
    public function getRevision()
    {
        if (is_null($this->_oRevision)) {
            $this->fetchRevision();
        }

        return $this->_oRevision;
    }

    /**
     * Set revision
     * @param AM_Model_Db_Revision $oRevision
     * @return AM_Model_Db_Page
     */
    public function setRevision(AM_Model_Db_Revision $oRevision)
    {
        $this->_oRevision = $oRevision;

        return $this;
    }

    /**
     * Fetch revision from DB
     * @return AM_Model_Db_Page
     */
    public function fetchRevision()
    {
        $this->_oRevision  = AM_Model_Db_Table_Abstract::factory('revision')
                ->findOneBy(array('id' => $this->revision));

        if (is_null($this->_oRevision)) {
            throw new AM_Model_Db_Exception(sprintf('Page "%s" has no revision', $this->id));
        }

        return $this;
    }

    /**
     * Returns list of page's tags
     *
     * @return AM_Model_Db_Rowset_Term
     */
    public function getTags()
    {
        $oTags = AM_Model_Db_Table_Abstract::factory('term')->getTagsForPage($this);

        return $oTags;
    }

    /**
     * Set page term
     * @param AM_Model_Db_Term $oTerm
     * @return AM_Model_Db_Page
     */
    public function addTerm(AM_Model_Db_Term $oTerm)
    {
        $this->_aTerms[$oTerm->id] = $oTerm;

        return $this;
    }

    /**
     * Get pages issue
     * @return AM_Model_Db_Issue
     */
    public function getIssue()
    {
        if (empty($this->_oIssue)) {
            $this->fetchIssue();
        }

        return $this->_oIssue;
    }

    /**
     * Fetch pages issue
     * @return AM_Model_Db_Page
     */
    public function fetchIssue()
    {
        $this->_oIssue = $this->getRevision()->getIssue();

        if (is_null($this->_oIssue)) {
            throw new AM_Model_Db_Exception(sprintf('Page "%s" has no issue', $this->id));
        }

        return $this;
    }

    /**
     * Get page template
     * @return AM_Model_Db_Template
     */
    public function getTemplate()
    {
        if (is_null($this->_oTemplate)) {
            $this->fetchTemplate();
        }

        return $this->_oTemplate;
    }

    /**
     * Fetch page template
     * @return AM_Model_Db_Template
     */
    public function fetchTemplate()
    {
        $this->_oTemplate = AM_Model_Db_Table_Abstract::factory('template')->findOneBy('id', $this->template);

        if (is_null($this->_oTemplate)) {
            throw new AM_Model_Db_Exception(sprintf('Page "%s" has no template', $this->id));
        }

        return $this;
    }

    /**
     * Gets templates as
     * @return array
     */
    public function getTemplatesForReplacement()
    {
        $oPageTemplate = $this->getTemplate();

        $oTemplates = AM_Model_Db_Table_Abstract::factory('template')->findAllByVersion($this->getIssue()->getApplication()->version);

        $aResult = array();

        foreach ($oTemplates as $oTemplate) {
            /** @var $oTemplate AM_Model_Db_Template **/

            //Check template compatibility:
            //1) if current template has TOP(LEFT, ...) connector, new template must have same connector
            //2) if current template hasn't TOP(LEFT, ...) connector, new template may have the same or may not
            $bIsDisabled = ($oPageTemplate->hasConnector(self::LINK_TOP) ? !$oTemplate->hasConnector(self::LINK_TOP) : false) ||
                        ($oPageTemplate->hasConnector(self::LINK_BOTTOM)? !$oTemplate->hasConnector(self::LINK_BOTTOM) : false) ||
                        ($oPageTemplate->hasConnector(self::LINK_RIGHT) ? !$oTemplate->hasConnector(self::LINK_RIGHT) : false) ||
                        ($oPageTemplate->hasConnector(self::LINK_LEFT) ? !$oTemplate->hasConnector(self::LINK_LEFT) : false);

            $aResult[] = array(
                'id'          => $oTemplate->id,
                'description' => $oTemplate->description,
                'disabled'    => $bIsDisabled,
                'imageUrl'    => $oTemplate->getPicture($bIsDisabled ? 'disabled' : 'enabled', $this->getOrientation())
            );
        }

        return $aResult;
    }

    /**
     * Gets templates list for connection to the page
     * @param string $sLinkType
     * @return array
     * @throws AM_Model_Db_Exception
     */
    public function getTemplatesForConnection($sLinkType)
    {
        if (!in_array($sLinkType, self::$aLinkTypes)) {
            throw new AM_Model_Db_Exception(sprintf('Wrong link type given "%s"', $sLinkType));
        }

        $oPageTemplate = $this->getTemplate();

        if (!$oPageTemplate->hasConnector($sLinkType)) {
             throw new AM_Model_Db_Exception(sprintf('Wrong link type given "%s". Template "%d" has no link with this type', $sLinkType, $oPageTemplate->id));
        }

        $oTemplates = AM_Model_Db_Table_Abstract::factory('template')->findAllByVersion($this->getIssue()->getApplication()->version);

        $aResult = array();

        foreach ($oTemplates as $oTemplate) {
            /** @var $oTemplate AM_Model_Db_Template **/

            //Check template compatibility:
            // - connected template must have reversed $linkType connector
            $bIsDisabled = !$oTemplate->hasConnector($this->reverseLinkType($sLinkType));

            $aResult[] = array(
                'id'          => $oTemplate->id,
                'description' => $oTemplate->description,
                'disabled'    => $bIsDisabled,
                'imageUrl'    => $oTemplate->getPicture($bIsDisabled ? 'disabled' : 'enabled', $this->getOrientation())
            );
        }

        return $aResult;
    }

    /**
     * Change page template. Save elements if new template has the same fields
     * @param AM_Model_Db_Template $oTemplate
     * @return AM_Model_Db_Page
     */
    public function setTemplate(AM_Model_Db_Template $oTemplate)
    {
        $oElements = $this->getElements();

        foreach ($oElements as $oElement) {
            /* @var $oElement AM_Model_Db_Element */
            $oElement->setReadOnly(false);

            $oField = $oElement->getField();

            //Looking for field with the same type in new template
            $oNewField = AM_Model_Db_Table_Abstract::factory('field')
                    ->findOneBy(array('field_type' => $oField->field_type, 'template' => $oTemplate->id));

            if (is_null($oNewField)) {
                //Delete page background
                AM_Model_Db_Table_Abstract::factory('page_background')
                        ->deleteBy(array('id' => $oElement->id));

                $oElement->delete();
                continue;
            }

            $oElement->field = $oNewField->id;
            $oElement->save();
        }

        $this->template = $oTemplate->id;
        $this->save();

        return $this;
    }

    /**
     * Get all fields of page
     * @return AM_Model_Db_Rowset_Field
     */
    public function getFields()
    {
        $oFields = AM_Model_Db_Table_Abstract::factory('field')
                ->findAllByPageId($this->id);

        return $oFields;
    }

    /**
     * Define if page can be deleted
     * Can if no more then 1 connected to page
     * and page connected to no more then one
     *
     * @return bool
     */
    public function canDelete()
    {
        $bResult = $this->getTable()->canDelete($this->id);

        return $bResult;
    }

    /**
     * @todo refactoring. We have to get page background by field weight
     * @param boolean $bUpdateBackground
     * @return void
     */
    public function setUpdated($bUpdateBackground = true)
    {
        $oExportHandler = AM_Handler_Locator::getInstance()->getHandler('export');
        /* @var $handler AM_Handler_Export */

        $this->updated                = new Zend_Db_Expr('NOW()');
        $this->save();
        $this->getRevision()->updated = new Zend_Db_Expr('NOW()');
        $this->getRevision()->save();
        $this->getIssue()->updated    = new Zend_Db_Expr('NOW()');
        $this->getIssue()->save();

        if (!$bUpdateBackground) {
            $oExportHandler->initExportProcess($this->getRevision());

            return;
        }

        $oNotSortedFields = $this->getFields();

        $aFields = array();
        foreach ($oNotSortedFields as $oField) {
            if (!isset (self::$aBackgroundPriority[$oField->getFieldType()->title])) {
               continue;
            }
            $aFields[self::$aBackgroundPriority[$oField->getFieldType()->title]] = $oField;
        }

        ksort($aFields);

        $oField    = null;
        $oElement  = null;
        $sFileName = null;
        foreach ($aFields as $oFieldItem) {
            $oElements = $this->getElementsByField($oFieldItem);
            if (!count($oElements)) {
                continue;
            }

            foreach ($oElements as $elementsItem) {
                /* @var $elementsItem AM_Model_Db_Element */
                $sFileName = $elementsItem->getResources()->getDataValue(AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE);
                if (!$sFileName) {
                    continue;
                }
                $oField   = $oFieldItem;
                $oElement = $elementsItem;
                break;
            }

            if ($oElement && $oField) {
                break;
            }
        }

        if (is_null($oField) || is_null($oElement)) {
            AM_Model_Db_Table_Abstract::factory('page_background')->deleteBy(array('page' => $this->id));
            $oExportHandler->initExportProcess($this->getRevision());

            return;
        }

        $oBackground = $this->getPageBackground();
        if (!is_null($oBackground)) {
            $oBackground->id       = $oElement->id;
            $oBackground->filename = AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE . '.' . pathinfo($sFileName, PATHINFO_EXTENSION);
            $oBackground->updated  = $oElement->updated;
            $oBackground->setElement($oElement);
        } else {
            $oBackground = new AM_Model_Db_PageBackground();
            $oBackground->id       = $oElement->id;
            $oBackground->filename = AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE . '.' . pathinfo($sFileName, PATHINFO_EXTENSION);
            $oBackground->updated  = $oElement->updated;
            $oBackground->page     = $this->id;
            $oBackground->type     = 'element';
            $oBackground->setElement($oElement);
        }

        $oBackground->save();
        $oExportHandler->initExportProcess($this->getRevision());

        return;
    }

    /**
     * Soft delete
     * @todo refactoring
     * @return AM_Model_Db_Page
     */
    public function delete()
    {
        $this->getElements()->delete();

        //Delete page background
        AM_Model_Db_Table_Abstract::factory('page_background')
                ->deleteBy(array('type' => 'element', 'page'   => $this->id));

        AM_Model_Db_Table_Abstract::factory('page')->softDelete($this);

        $this->getRevision()->exportRevision();

        return $this;
    }

    /**
     * Check if page has connection
     *
     * @param string $sConnectionType
     * @return boolean
     * @throws AM_Model_Db_Exception
     */
    public function hasConnection($sConnectionType)
    {
        if (!in_array($sConnectionType, self::$aLinkTypes)) {
            throw new AM_Model_Db_Exception('Undefined connection type was given');
        }

        $sConnectionType = $this->_parseConnectionBitConstant($sConnectionType);

        $bResult = (boolean) ($this->connections & constant(sprintf('self::%s', $sConnectionType)));

        return $bResult;
    }

    /**
     * Sets connection bit
     *
     * @param string $sConnectionType
     * @return AM_Model_Db_Page
     * @throws AM_Model_Db_Exception
     */
    public function setConnectionBit($sConnectionType)
    {
        if (!in_array($sConnectionType, self::$aLinkTypes)) {
            throw new AM_Model_Db_Exception('Undefined connection type was given');
        }

        $sConnectionType = $this->_parseConnectionBitConstant($sConnectionType);

        $this->connections |= constant(sprintf('self::%s', $sConnectionType));

        return $this;
    }

    /**
     * Removes connection bit
     *
     * @param string $sConnectionType
     * @return AM_Model_Db_Page
     * @throws AM_Model_Db_Exception
     */
    public function removeConnectionBit($sConnectionType)
    {
        if (!in_array($sConnectionType, self::$aLinkTypes)) {
            throw new AM_Model_Db_Exception('Undefined connection type was given');
        }

        $sConnectionType = $this->_parseConnectionBitConstant($sConnectionType);

        $this->connections &= ~ constant(sprintf('self::%s', $sConnectionType));

        return $this;
    }

    /**
     * Prepeare constant for operations with connection bits
     *
     * @param string $sConnectionType
     * @return string
     */
    private function _parseConnectionBitConstant($sConnectionType)
    {
        $sConnectionType = 'HAS_CONNECTION_' . Zend_Filter::filterStatic($sConnectionType, 'StringToUpper', array('encoding' => 'UTF-8'));

        return $sConnectionType;
    }

    /**
     * @see AM_Tree_NodeInterface::getParent()
     */
    public function getParent()
    {
        if (is_null($this->_oParent)) {
            $this->fetchParent();
        }

        return $this->_oParent;
    }

    /**
     * Fetch page parent
     * @return AM_Model_Db_Page
     */
    public function fetchParent()
    {
        $oPageParent = AM_Model_Db_Table_Abstract::factory('page')->findParentByPageId($this->id);
        if (!is_null($oPageParent)) {
            $this->setParent($oPageParent);
        }

        return $this;
    }

    /**
     * Filter for toc field
     *
     * @param integer $iValue
     * @return integer
     */
    public function filterValueToc($iValue)
    {
        $iValue = intval($iValue);

        $oTermTable = AM_Model_Db_Table_Abstract::factory('term');
        /* @var $oTermTable AM_Model_Db_Table_Term */
        $oTermTable->removeTocFromPage($this);
        //Checking that page has id
        if (0 !== $iValue && !empty($this->id)) {
            $oTermPage       = new AM_Model_Db_TermPage();
            $oTermPage->term = $iValue;
            $oTermPage->page = $this->id;
            $oTermPage->save();
        }

        return $iValue;
    }

    /**
     * Filter for machine_name field
     *
     * @param string $sValue
     * @return string
     */
    public function filterValueMachineName($sValue)
    {
        $sValue = AM_Tools::filter_xss($sValue);

        if (!preg_match('/^[0-9A-Za-z\-\.\_]*$/', $sValue)) {
            throw new AM_Model_Db_Exception('Error. Invalid macine name');
        }

        return $sValue;
    }

    /**
     * Filter for pdf_page field
     *
     * @param integer $iValue
     * @return integer
     */
    public function filterValuePdfPage($iValue)
    {
        $iValue = intval($iValue);

        return $iValue;
    }

    /**
     * Filter for title field
     *
     * @param string $sValue
     * @return string
     */
    public function filterValueTitle($sValue)
    {
        $sValue = AM_Tools::filter_xss($sValue);

        return $sValue;
    }

    /**
     * Gets page's cover image (API uses this method)
     * @todo use one method for background and api cover
     * @return false|string
     */
    public function getPageCoverUri()
    {
        $oFieldBody = AM_Model_Db_Table_Abstract::factory('field')
                ->findOneBy(array('name'     => AM_Model_Db_FieldType::TYPE_BODY,
                                  'template' =>AM_Model_Db_Template::TPL_COVER_PAGE));

        $oElementBody = AM_Model_Db_Table_Abstract::factory('element')
                ->findOneBy(array('page'  => $this->id,
                                  'field' => $oFieldBody->id));
        /* @var $oElementBody AM_Model_Db_Element */

        if (is_null($oElementBody)) {
            return null;
        }

        $sResource = $oElementBody->getResources()->getDataValue(AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE);

        if (empty($sResource)) {
            return null;
        }

        $sFileExtension = pathinfo($sResource, PATHINFO_EXTENSION);
        $sFileName      = AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE . '.' . $sFileExtension;

        $sUri = AM_Tools::getImageUrl(
            AM_Handler_Thumbnail_Interface::PRESET_EXPORT_COVER . '-' . $this->getOrientation(),
            AM_Model_Db_Element_Data_Abstract::TYPE,
            $oElementBody->id,
            $sFileName);

        $sUri .= '?' . strtotime($oElementBody->updated);

        return $sUri;
    }

    /**
     * Gets page's start video (API uses this method)
     * @return false|string
     */
    public function getStartVideoUri()
    {
        $oFieldVideo = AM_Model_Db_Table_Abstract::factory('field')
                ->findOneBy(array('name'     => AM_Model_Db_FieldType::TYPE_VIDEO,
                                  'template' => AM_Model_Db_Template::TPL_COVER_PAGE));

        $oElementVideo = AM_Model_Db_Table_Abstract::factory('element')
                ->findOneBy(array('page'  => $this->id,
                                  'field' => $oFieldVideo->id));
        /* @var $oElementVideo AM_Model_Db_Element */

        if (is_null($oElementVideo)) {
            return null;
        }

        $sResource = $oElementVideo->getResources()->getDataValue(AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE);

        if (empty($sResource)) {
            return null;
        }

        $sFileExtension = pathinfo($sResource, PATHINFO_EXTENSION);
        $sFileName      = 'resource.' . $sFileExtension;

        $sUri = AM_Tools::getImageUrl('none', AM_Model_Db_Element_Data_Abstract::TYPE, $oElementVideo->id, $sFileName);

        return $sUri;
    }

    /**
     * Returns orientation of the page
     * @return string
     */
    public function getOrientation()
    {
        if (is_null($this->sOrientation)) {
            $this->sOrientation = empty($this->getIssue()->orientation)? AM_Model_Db_Issue::ORIENTATION_VERTICAL : $this->getIssue()->orientation;
        }

        return $this->sOrientation;
    }
}
