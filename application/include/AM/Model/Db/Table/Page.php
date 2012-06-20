<?php
/**
 * @file
 * AM_Model_Db_Table_Page class definition.
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
 * @ingroup AM_Model
 */
class AM_Model_Db_Table_Page extends AM_Model_Db_Table_Abstract
{
    /**
     * Checks the client's access to the page
     * @param int $iPageId
     * @param array $aUserInfo
     * @return boolean
     */
    public function checkAccess($iPageId, $aUserInfo)
    {
        if ('admin' == $aUserInfo['role']) {
            return true;
        }

        $iPageId   = intval($iPageId);
        $iClientId = intval($aUserInfo['client']);

        $oQuery = $this->getAdapter()->select()
                              ->from('page', array('page_id' => 'page.id'))

                              ->join('revision', 'revision.id = page.revision', null)
                              ->join('issue', 'issue.id = revision.issue', null)
                              ->join('application', 'application.id = issue.application', null)
                              ->join('user', 'user.client = application.client', null)

                              ->where('page.deleted = ?', 'no')
                              ->where('revision.deleted = ?', 'no')
                              ->where('issue.deleted = ?', 'no')
                              ->where('application.deleted = ?', 'no')
                              ->where('user.deleted = ?', 'no')

                              ->where('page.id = ?', $iPageId)
                              ->where('user.client = application.client')
                              ->where('application.client = ?', $iClientId);

        $oPage   = $this->getAdapter()->fetchOne($oQuery);
        $bResult = $oPage ? true : false;

        return $bResult;
    }

    /**
     * Returns page that has specified page on the specified side
     *
     * @param AM_Model_Db_Page $oPage
     * @param string $sLinkType
     * @return AM_Model_Db_Page
     */
    public function findConnectedPage(AM_Model_Db_Page $oPage, $sLinkType)
    {
        $oQuery = $this->select()->from('page')
                      ->setIntegrityCheck(false)
                      ->joinLeft('page_imposition', 'page_imposition.page = page.id', array('link_type' => 'link_type'))
                      ->where('page_imposition.is_linked_to = ?', $oPage->id)
                      ->where('page_imposition.link_type = ?', $sLinkType)
                      ->where('page.deleted = ?', 'no');

        $oPageConnected = $this->fetchRow($oQuery);

        return $oPageConnected;
    }

    /**
     * Find all page childs (connected from left, right, top, bottom)
     * @param int $iPageId
     * @return AM_Model_Db_Rowset_Page
     * @throws AM_Model_Db_Table_Exception
     */
    public function findAllByParentId($iPageId)
    {
        $iPageId = intval($iPageId);
        if ($iPageId <= 0) {
            throw new AM_Model_Db_Table_Exception('Wrong parameter PAGE_ID given');
        }

        $oChilds = $this->findChildsByPageId($iPageId);
        $oParent = $this->findParentByPageId($iPageId);

        if (!is_null($oParent)) {
            $oChilds->addRow($oParent);
        }

        return $oChilds;
    }

    /**
     * Find parent page
     * @param int- $iPageId
     * @return AM_Model_Db_Page
     * @throws AM_Model_Db_Table_Exception
     */
    public function findParentByPageId($iPageId)
    {
        $iPageId = intval($iPageId);
        if ($iPageId <= 0) {
            throw new AM_Model_Db_Table_Exception('Wrong parameter PAGE_ID given');
        }

        $oSelect = $this->select()
                ->setIntegrityCheck(false)
                ->from('page')
                ->joinLeft('page_imposition', 'page_imposition.page = page.id', array('link_type' => 'link_type'))
                ->where('page_imposition.is_linked_to = ?', $iPageId);;

        $oPage = $this->fetchRow($oSelect);

        if (!is_null($oPage)) {
            $oPage->setReadOnly(false);
        }

        return $oPage;
    }

    /**
     * Get all page childs (connected from right, top, bottom)
     * @param int $iPageId
     * @return AM_Model_Db_Rowset_Page
     * @throws AM_Model_Db_Table_Exception
     */
    public function findChildsByPageId($iPageId)
    {
        $iPageId = intval($iPageId);
        if ($iPageId <= 0) {
            throw new AM_Model_Db_Table_Exception('Wrong parameter PAGE_ID given');
        }

        $oSelect = $this->select()
                ->setIntegrityCheck(false)
                ->from('page')
                ->joinLeft('page_imposition', 'page_imposition.is_linked_to = page.id', array('parent_id' => 'page', 'link_type' => 'link_type'))
                ->where('page_imposition.page = ?', $iPageId);
        $oPages = $this->fetchAll($oSelect);

        return $oPages;
    }

    /**
     * Define if page can be deleted
     * Can if no more then 1 connected to page
     * and page connected to no more then one
     * @param int $iPageId
     * @return bool
     */
    public function canDelete($iPageId)
    {
        $oQuery = $this->select()
                ->setIntegrityCheck(false)
                ->from('page', 'pg.id')
                ->join(array('pin' => 'page_imposition'), 'pin.is_linked_to = page.id', null)
                ->join(array('pg'  => 'page'), 'pg.id = pin.page AND pg.deleted = "no"', null)
                ->joinLeft(array('pi' => 'page_imposition'), 'pi.page = page.id', null)
                ->joinLeft(array('p'  => 'page'), 'p.id = pi.is_linked_to AND p.deleted = "no"', null)
                ->where('page.deleted = ?', 'no')
                ->where('page.id = ?', $iPageId)
                ->group('page.id')
                ->having('COUNT(p.id) <= ?', 1);

        $oRow = $this->fetchRow($oQuery);

        $bResult = is_null($oRow)? false : true;

        return $bResult;
    }

    /**
     * Soft delete
     * @param AM_Model_Db_Page $oPage
     * @return void
     */
    public function softDelete(AM_Model_Db_Page $oPage)
    {
        $oPageParent = $oPage->getParent();

        if ($oPageParent) {
            $this->getAdapter()->update('page_imposition',
                                array('page' => $oPageParent->id),
                                $this->getAdapter()->quoteInto('page = ?', $oPage->id));
            $this->getAdapter()->delete('page_imposition',
                                $this->getAdapter()->quoteInto('page = ?', $oPage->id));
        }
        $this->getAdapter()->delete('page_imposition',
                                $this->getAdapter()->quoteInto('is_linked_to = ?', $oPage->id));

        $oPage->deleted = 'yes';
        $oPage->save();
    }
}
