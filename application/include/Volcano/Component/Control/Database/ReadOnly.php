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
 * Readonly control
 * Control with constant value
 *
 * @category Volcano
 * @package Volcano_Component_Control_Database
 * @subpackage Static
 */


class Volcano_Component_Control_Database_ReadOnly extends Volcano_Component_Control_Database {
    /**
     * Constructor
     *
     * @param AM_Controller_Action $actionController Controller
     * @param string $name Component name
     * @param string $title Title of control
     * @param string $dbField Name of binded field
     */
    public function __construct(AM_Controller_Action $actionController, $name, $title = null, $dbField = null) {
        parent::__construct($actionController, $name, $title, null, $dbField);
    }


}