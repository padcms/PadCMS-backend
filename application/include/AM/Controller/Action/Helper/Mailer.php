<?php
/**
 * @file
 * AM_Controller_Action_Helper_Mailer class definition.
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
