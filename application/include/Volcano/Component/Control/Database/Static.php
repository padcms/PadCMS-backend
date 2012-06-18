<?php

/**
 * Volcano Framework
 *
 * @category Volcano
 * @package Volcano_Component_Control_Database
 * @subpackage Static
 * @author Ilya Gruzinov
 * @version $Revision$
 * @license http://vifm.volcanoideas.com/license/
 */

include_once 'Volcano/Component/Control/Database.php';
/**
 * Static control
 * Control with constant value
 *
 * @category Volcano
 * @package Volcano_Component_Control_Database
 * @subpackage Static
 */


class Volcano_Component_Control_Database_Static extends Volcano_Component_Control_Database {



    /**
     * Constructor
     *
     * @param AM_Controller_Action $actionController Controller
     * @param string $dbField Name of binded field
     * @param mixed $value Value of control
     */
    public function __construct(AM_Controller_Action $actionController, $dbField, $value) {
        parent::__construct($actionController, $dbField, null, null, $dbField);
        $this->value = $value;
    }

    public function retrieveValue() {
        return;
    }

    public function validate() {
        return true;
    }


}