<?php
/**
 * @file
 * AM_Model_Db_Rowset_Revision class definition.
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
 * @ingroup AM_Model
 */
class AM_Model_Db_Rowset_Revision extends AM_Model_Db_Rowset_Abstract
{
    /**
     * Set application to revisions
     * @param AM_Model_Db_Application $oApplication
     * @return AM_Model_Db_Rowset_Revision
     */
    public function setApplication(AM_Model_Db_Application $oApplication)
    {
        foreach ($this as $oRevision) {
            $oRevision->setApplication($oApplication);
        }

        return $this;
    }

    /**
     * Set revisions issue
     * @param AM_Model_Db_Issue $oIssue
     * @return AM_Model_Db_Rowset_Revision
     */
    public function setIssue(AM_Model_Db_Issue $oIssue)
    {
        foreach ($this as $oRevision) {
            $oRevision->setIssue($oIssue);
        }

        return $this;
    }

    /**
     * Copy revisions to other issue
     * @param AM_Model_Db_Issue $user
     * @return AM_Model_Db_Rowset_Revision
     */
    public function copyToIssue(AM_Model_Db_Issue $oIssue)
    {
        foreach ($this as $oRevision) {
            $oRevision->copyToIssue($oIssue);
        }
        return $this;
    }

    /**
     * Move revisions to other issue
     * @param AM_Model_Db_Issue $user
     * @return AM_Model_Db_Rowset_Revision
     */
    public function moveToIssue(AM_Model_Db_Issue $oIssue)
    {
        foreach ($this as $oRevision) {
            /* @var $oRevision AM_Model_Db_Revision */
            $oRevision->moveToIssue($oIssue);
        }
        return $this;
    }
}
