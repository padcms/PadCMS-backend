<?php
/**
 * @file
 * AM_Component_Editor class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Page editor component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_Editor extends AM_Component
{
    /** @var AM_Model_Db_Page **/
    private $_oPage; /**< @type AM_Model_Db_Page */

    public function __construct(AM_Controller_Action $oControllerAction, AM_Model_Db_Page $oPage)
    {
        parent::__construct($oControllerAction, 'editor');
        $this->_oPage = $oPage;
    }

    public function show()
    {
        $oEditorViewHelper = new AM_View_Helper_Editor_Page($this->_oPage);
        $oEditorViewHelper->setViewHelper($this->_oView)->show();

        $oFields     = $this->_oPage->getFields();
        $aFieldsData = array();
        foreach ($oFields as $oField) {
            /* @var $oField AM_Model_Db_Field */
            $sFieldType        =  ucfirst(Zend_Filter::filterStatic($oField->getFieldType()->title, 'Word_UnderscoreToCamelCase'));
            $sHelperClass      = 'AM_View_Helper_Field_' . $sFieldType;
            $oEditorViewHelper = new $sHelperClass($oField, $this->_oPage);
            /* @var $oEditorViewHelper AM_View_Helper_Field */
            $oEditorViewHelper->setViewHelper($this->getActionController()->view)->show();

            $aFieldsData[] = array('fid'           => $oField->id,
                                  'template_title' => $this->_oPage->getTemplate()->title,
                                  'name'           => $oField->name,
                                  'descr'          => $oField->description,
                                  'type'           => $oField->getFieldType()->title,
                                  'min'            => $oField->min,
                                  'max'            => $oField->max,
                                  'weight'         => $oField->weight);
        }

        $aRecord = array(
            'pid'    => $this->_oPage->id,
            'fields' => $aFieldsData
        );

        if (isset($this->getActionController()->view->editor)) {
            $aRecord = array_merge($aRecord, $this->getActionController()->view->editor);
        }

        $this->getActionController()->view->editor = $aRecord;
    }
}