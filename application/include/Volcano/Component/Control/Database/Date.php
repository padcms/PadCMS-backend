<?php
/**
 * Volcano Framework
 *
 * @category Volcano
 * @package Volcano_Component_Control_Database
 * @subpackage Date
 * @author Ilya Gruzinov
 * @version $Revision$
 * @license http://vifm.volcanoideas.com/license/
 */

include_once 'Volcano/Component/Control/Database.php';
/**
 * Date control
 *
 * @category Volcano
 * @package Volcano_Component_Control_Database
 * @subpackage Date
 */
class Volcano_Control_Database_Date extends Volcano_Control implements Volcano_Control_DatabaseField {

    public function __construct(Zend_Controller_Action $actionController, $name, $title = null, $validationsRules = null, $dbField = null) {
        parent::__construct($actioncontroller, $name, $title, $validationsRules);

    }
    
    public function getValue() {
        return !strlen($this->value) || $this->value == "0000-00-00" ? null : parent::getValue();
    }

}