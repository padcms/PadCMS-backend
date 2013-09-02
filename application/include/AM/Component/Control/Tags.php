<?php
/**
 * Volcano Framework
 *
 * @category Volcano
 * @package Volcano_Component_Control
 * @subpackage DatabaseField
 * @author Ilya Gruzinov
 * @version $Revision$
 * @license http://vifm.volcanoideas.com/license/
 */

include_once 'Volcano/Component/Control.php';
/**
 * Control with database binding
 *
 * @category Volcano
 * @package Volcano_Component_Control
 * @subpackage Database
 */
class AM_Component_Control_Tags extends Volcano_Component_Control {
    /**
     * Constructor
     *
     * @param AM_Controller_Action $actionController Controller
     * @param string $name Component name
     * @param string $title Title of control
     * @param array $validationRules Validation rules. Each rule is array :
     * 		[validationtype, param1, param2,...]
     *     type can be: require, integer, float, numeric, maximum value, minimum value,
     * 	 	maximum length, minimum length, range, range length, regexp, email, function
     */
    public function __construct(AM_Controller_Action $actionController, $name, $title = null, array $validationsRules = null) {
        parent::__construct($actionController, $name, $title, $validationsRules);
    }
}
