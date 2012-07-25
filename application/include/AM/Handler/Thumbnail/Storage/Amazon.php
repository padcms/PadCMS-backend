<?php

/*
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
 * @ingroup AM_Handler
 */
class AM_Handler_Thumbnail_Storage_Amazon extends AM_Handler_Thumbnail_Storage_Abstract
{
    /** @var Zend_Service_Amazon_S3 */
    private $_oService = null; /**< @type Zend_Service_Amazon_S3 */
    /** @var string */
    private $_sBucketName = null; /**< @type string */

    /**
     * @return Zend_Service_Amazon_S3
     */
    public function getService()
    {
        return $this->_oService;
    }

    /**
     * @param Zend_Service_Amazon_S3 $oService
     * @return AM_Handler_Thumbnail_Storage_Amazon
     */
    public function setService(Zend_Service_Amazon_S3 $oService)
    {
        $this->_oService = $oService;

        return $this;
    }

    /**
     * Returns the default bucket name
     * @return string
     */
    public function getBucketName()
    {
        if (is_null($this->_sBucketName)) {
            $this->_sBucketName = $this->getConfig()->bucketName;
        }

        return $this->_sBucketName;
    }

    /**
     * Init the Amazon S3 storage
     */
    protected function _init()
    {
        $this->setService(new Zend_Service_Amazon_S3($this->getConfig()->accessKey, $this->getConfig()->secretKey));
    }

    /**
     * Saves all the resources to the local storage
     */
    public function save()
    {
        foreach ($this->getResources() as $sResource) {
            $sObjectName =  $this->getBucketName() . '/' . $this->getPathPrefix() . '/' . pathinfo($sResource, PATHINFO_BASENAME);
            $this->getService()->putFileStream($sResource, $sObjectName, array(Zend_Service_Amazon_S3::S3_ACL_HEADER => Zend_Service_Amazon_S3::S3_ACL_PUBLIC_READ));
        }

        $this->_aResources = array();
    }

    /**
     * Returns image's URL
     * @param string $sPreset
     * @param string $sType
     * @param int $iId
     * @param string $sFileName
     * @return string $sImageUrl
     */
    public function getResourceUrl($sPreset, $sType, $iId, $sFileName)
    {
        $sEndpoint = 'http://' . $this->getBucketName() . '.' . Zend_Service_Amazon_S3::S3_ENDPOINT;

        $sImageUrl = $sEndpoint
            . '/' . (string) $sPreset
            . '/' . (string) $sType
            . '/' . AM_Tools_String::generatePathFromId(intval($iId), '/')
            . '/' . (string) $sFileName;

        return $sImageUrl;
    }

    /**
     * Removes resources
     * @param string $sResourceType
     * @param int $iId
     * @param string $sFileName
     */
    public function clearResources($sFileName = null)
    {
        $sFileName        = str_replace('*', '', $sFileName);
        $sSeartchPrefix   = $this->getPathPrefix() . '/' . $sFileName;

        $aObjects = $this->getService()->getObjectsByBucket($this->getBucketName(), array('prefix' => $sSeartchPrefix));

        foreach ($aObjects as &$sObject) {
            $this->getService()->removeObject($this->getBucketName() . '/' . $sObject);
        }
    }
}