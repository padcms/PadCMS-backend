<?php
/**
 * @file
 * AM_Handler_Thumbnail class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Handler to upload files
 * @todo: handler must be an instance of AM_Handler_Abstract
 * @ingroup AM_Handler
 */
class AM_Handler_Upload extends Zend_File_Transfer_Adapter_Http
{
    /**
     * Return all messages as string
     * @return string
     */
    public function getMessagesAsString()
    {
        $sMessage = implode('\n', $this->getMessages());

        return $sMessage;
    }
}