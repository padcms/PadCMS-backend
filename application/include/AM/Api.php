<?php
/**
 * @file
 * AM_Api class definition.
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
 * @defgroup AM_Api
 */

/**
 * Abstract class: this is the superclass for all API classes
 * @ingroup AM_Api
 */
abstract class AM_Api
{
    const PLATFORM_IOS     = 'ios';
    const PLATFORM_ANDROID = 'android';

    /** @var array */
    protected $_aValidPlatforms = array(self::PLATFORM_IOS, self::PLATFORM_ANDROID); /**< @type array */

    /** @var Zend_Log */
    protected $_oLogger = null; /**< @type Zend_Log */

    public function __construct()
    { }

    /**
     * Get the logger instance
     *
     * @return Zend_Log
     */
    public function getLogger()
    {
        if (is_null($this->_oLogger)) {
            $this->_oLogger = Zend_Registry::get('log');
            $this->_oLogger->setEventItem('file', get_class($this));
        }

        return $this->_oLogger;
    }
}