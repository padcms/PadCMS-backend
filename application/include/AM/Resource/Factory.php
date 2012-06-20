<?php

/**
 * @file
 * AM_Resource_Factory class definition.
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