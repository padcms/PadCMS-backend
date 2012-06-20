<?php
/**
 * @file
 * AM_Component_Editor class definition.
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