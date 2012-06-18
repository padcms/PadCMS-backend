<?php
/**
 * @file
 * ErrorController class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * This class is responsible for downloading magazine package and initializing creating package
 *
 * @ingroup AM_Controller_Action
 * @ingroup AM_Handler_Export
 */
class ExportController extends AM_Controller_Action
{
    /**
     * Export revision action
     *
     * Sends revision package
     */
    public function revisionAction()
    {
        try {
            $message['status'] = 0;

            $iRevisionId = intval($this->_getParam('id'));
            $mIsContinue = $this->_getParam('continue');

            $oRevision = AM_Model_Db_Table_Abstract::factory('revision')->findOneBy('id', $iRevisionId);
            /* @var $oRevision AM_Model_Db_Revision */

            if (is_null($oRevision)) {
                throw new AM_Controller_Exception_BadRequest(sprintf('Revision "%s" not found', $iRevisionId));
            }

            $oHandler = AM_Handler_Locator::getInstance()->getHandler('export');
            /* @var $oHandler AM_Handler_Export */
            $oHandler->sendRevisionPackage($oRevision, $mIsContinue);
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(500)
                    ->setBody('Error. Internal Server Error. ' . $e->getMessage())
                    ->sendResponse();
        }

        exit;
    }
}