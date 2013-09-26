<?php
/**
 * @file
 * AM_Model_Db_TermPage class definition.
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
 * Term page model class
 * @ingroup AM_Model
 */
class AM_Model_Db_TermEntity extends AM_Model_Db_Abstract
{
    public function copyToIssue(AM_Model_Db_Issue $oIssueTo) {
        $oVocabulary = AM_Model_Db_Table_Abstract::factory('application')
            ->findOneBy('id', $oIssueTo->application)
            ->getVocabularyTag();
        $oTag = AM_Model_Db_Table_Abstract::factory('term')->findOneBy('id', $this->term);
        $oNewTag = AM_Model_Db_Table_Abstract::factory('term')->findOneBy(
            array(
                 'title' => $oTag->title,
                 'vocabulary' => $oVocabulary->id
            ));
        if (empty($oNewTag)) {
            $oNewTag = $oVocabulary->createTag($oTag->title);
        }
        $oNewTermEntity                = new AM_Model_Db_TermEntity();
        $oNewTermEntity->term          = $oNewTag->id;
        $oNewTermEntity->entity        = $oIssueTo->id;
        $oNewTermEntity->entity_type   = $this->entity_type;
        $oNewTermEntity->save();
        return $oNewTermEntity;
    }

    public function moveToIssue(AM_Model_Db_Issue $oIssueTo) {
        $oVocabulary = AM_Model_Db_Table_Abstract::factory('application')
            ->findOneBy('id', $oIssueTo->application)
            ->getVocabularyTag();
        $oTag = AM_Model_Db_Table_Abstract::factory('term')->findOneBy('id', $this->term);
        $oNewTag = AM_Model_Db_Table_Abstract::factory('term')->findOneBy(
            array(
                 'title' => $oTag->title,
                 'vocabulary' => $oVocabulary->id
            ));
        if (empty($oNewTag)) {
            $oNewTag = $oVocabulary->createTag($oTag->title);
        }
        $this->term = $oNewTag->id;
        $this->entity = $oIssueTo->id;
        $this->save();
        return $this;
    }
}
