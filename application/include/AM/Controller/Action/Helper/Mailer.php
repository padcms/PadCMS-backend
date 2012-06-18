<?php
/**
 * @file
 * AM_Controller_Action_Helper_Mailer class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @defgroup AM_Controller_Action_Helper
 */

/**
 * The mailer helper
 * @ingroup AM_Controller_Action_Helper
 * @ingroup AM_Controller_Action
 */
class AM_Controller_Action_Helper_Mailer extends Zend_Controller_Action_Helper_Abstract
{
  /**
   * Send email composed from template with specified name to address
   *
   * @param string $sName Email temlate name
   * @param array $aOptions Template variables
   * @return Zend_Mail
   */
  public function send($sName, $aOptions) {
    $oMailer  = new Zend_Mail();

    $oSmarty = $this->getActionController()->view->getSmarty();
    $oSmarty->assign($aOptions);

    $sText = $oSmarty->fetch("emails/text/" . $sName . ".tpl");
    $sHtml = $oSmarty->fetch("emails/html/" . $sName . ".tpl");

    $oMailer->setBodyText($sText);
    $oMailer->setBodyHtml($sHtml);
    $oMailer->addTo($aOptions['emails']);
    $oMailer->setSubject($aOptions['subject']);

    try {
        $oMailer->send();
    } catch (Exception $oException) {
        $this->getActionController()->getLogger()->warn('Can\'t send email!');
    }
  }
}
