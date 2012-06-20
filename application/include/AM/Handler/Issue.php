<?php
/**
 * @file
 * AM_Handler_Issue class definition.
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
 * Issue handler
 *
 * @ingroup AM_Handler
 */
class AM_Handler_Issue extends AM_Handler_Abstract
{
    /** @var AM_Model_Db_Issue **/
    protected $_oIssue = null; /**< @type AM_Model_Db_Issue */

    /**
     * Return issues
     * @return AM_Model_Db_Issue | null
     */
    public function getIssue()
    {
        if (is_null($this->_oIssue)) {
            throw new AM_Handler_Issue_Exception('Set issue before git it');
        }

        return $this->_oIssue;
    }

    /**
     * Set issue
     * @param AM_Model_Db_Issue $oIssue
     * @return AM_Handler_Issue
     */
    public function setIssue(AM_Model_Db_Issue $oIssue)
    {
        $this->_oIssue = $oIssue;

        return $this;
    }

    /**
     * Create revision from simple pdf
     * @param AM_Model_Db_IssueSimplePdf $oSimplePdf
     * @return AM_Model_Db_Revision
     * @throws AM_Handler_Issue_Exception
     */
    public function createRevisionFromSimplePdf(AM_Model_Db_IssueSimplePdf $oSimplePdf)
    {
        $aImages = $oSimplePdf->getAllPagesAsPng();

        //Create revision
        $oRevision        = new AM_Model_Db_Revision();
        $oRevision->state = AM_Model_Db_Issue::STATUS_WIP;
        $oRevision->user  = $this->getIssue()->user;
        $oRevision->issue = $this->getIssue()->id;
        $oRevision->save();
        $oRevision->title = 'Revision #' . $oRevision->id;
        $oRevision->save();

        //Create cover page
        $oPage           = new AM_Model_Db_Page();
        $oPage->title    = 'Root page';
        $oPage->template = AM_Model_Db_Template::TPL_COVER_PAGE;
        $oPage->revision = $oRevision->id;
        $oPage->user     = $this->getIssue()->user;
        $oPage->save();


        //Find field record for type 'Body'
        $oFieldBody = AM_Model_Db_Table_Abstract::factory('field')
                ->findOneBy(array('field_type' => 1, 'template' => AM_Model_Db_Template::TPL_COVER_PAGE));

        if (is_null($oFieldBody)) {
            throw new AM_Handler_Issue_Exception('Field "Body" not found');
        }

        //Create element for cover page
        $oElement             = new AM_Model_Db_Element();
        $oElement->field      = $oFieldBody->id;
        $oElement->page       = $oPage->id;
        $oElement->save();

        AM_Tools::clearContent(AM_Model_Db_Element::RESOURCE_TYPE, $oElement->id);
        AM_Tools::clearResizerCache(AM_Model_Db_Element::RESOURCE_TYPE, $oElement->id);

        //Prepare resources for the element
        $sImage = array_shift($aImages);
        $oElement->getResources()
                ->setData(array(AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE => $sImage))
                ->save();

        //Set page background
        $oElement->getResources()->setPageBackground();

        //Create childs
        $oFieldBody = AM_Model_Db_Table_Abstract::factory('field')
                ->findOneBy(array('field_type' => 1, 'template' => AM_Model_Db_Template::TPL_BASIC_ARTICLE));

        if (is_null($oFieldBody)) {
            throw new AM_Handler_Issue_Exception('Field "Body" not found');
        }

        $oPageRoot = clone $oPage;
        foreach ($aImages as $sImage) {
            $oPage           = new AM_Model_Db_Page();
            $oPage->title    = 'Right connected to page ' . $oPageRoot->id;
            $oPage->template = AM_Model_Db_Template::TPL_BASIC_ARTICLE;
            $oPage->revision = $oRevision->id;
            $oPage->user     = $this->getIssue()->user;
            $oPage->setConnectionBit(AM_Model_Db_page::LINK_LEFT);
            $oPage->save();

            $oPageRoot->setConnectionBit(AM_Model_Db_page::LINK_RIGHT);
            $oPageRoot->save();

            $oPage->setParent($oPageRoot);

            $oPage->setLinkType(AM_Model_Db_page::LINK_RIGHT)
                    ->savePageImposition();

            //Create element for cover page
            $oElement             = new AM_Model_Db_Element();
            $oElement->field      = $oFieldBody->id;
            $oElement->page       = $oPage->id;
            $oElement->save();

            AM_Tools::clearContent(AM_Model_Db_Element::RESOURCE_TYPE, $oElement->id);
            AM_Tools::clearResizerCache(AM_Model_Db_Element::RESOURCE_TYPE, $oElement->id);

            //Prepare resources for the element
            $oElement->getResources()
                    ->setData(array(AM_Model_Db_Element_Data_Resource::DATA_KEY_RESOURCE => $sImage))
                    ->save();

            //Set page background
            $oElement->getResources()->setPageBackground();

            $oPageRoot = clone $oPage;
        }

        $oRevision->exportRevision();

        return $oRevision;
    }
}