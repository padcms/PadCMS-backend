<?php

/**
 * @file
 * AM_Resource_Factory class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Resource
 */
class AM_Resource_Factory
{
    /**
     * Creates resource instance by file extension
     * @param string $sResourceFilePath path to the resource's file
     * @return AM_Resource_Abstract
     * @throws AM_Resource_Exception
     */
    public static function create($sResourceFilePath)
    {
        if (!AM_Tools_Standard::getInstance()->is_file($sResourceFilePath)) {
            throw new AM_Resource_Factory_Exception(sprintf('File \'%s\' not exists', $sResourceFilePath), 501);
        }

        $sFileExtension = pathinfo($sResourceFilePath, PATHINFO_EXTENSION);

        $oFilter = new Zend_Filter();
        $oFilter->addFilter(new Zend_Filter_StringToLower(array('encoding' => 'UTF-8')));

        $sClassPostfix      = ucfirst($oFilter->filter($sFileExtension));
        $sResourceClassName = AM_Resource_Abstract::RESOURCE_CONCRETE_CLASS_PREFIX . $sClassPostfix;
        $sFile = str_replace('_', DIRECTORY_SEPARATOR, $sResourceClassName) . '.php';
        if (!AM_Tools_Standard::getInstance()->isReadable($sFile)) {
            throw new AM_Resource_Factory_Exception(sprintf('Class \'%s\' not found', $sResourceClassName), 502);
        }
        /* @var $oResource AM_Resource_Abstract */
        $oResource = new $sResourceClassName($sResourceFilePath);

        return $oResource;
    }
}