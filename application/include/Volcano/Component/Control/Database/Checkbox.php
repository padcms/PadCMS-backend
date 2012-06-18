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

include_once 'Volcano/Component/Control/Database.php';
/**
 * Control with database binding
 *
 * @category Volcano
 * @package Volcano_Component_Control
 * @subpackage Database
 */
class Volcano_Component_Control_Database_Checkbox extends Volcano_Component_Control_Database {

    /**
     * Database values for unchecked state
     * @var array
     */
    protected $unCheckedDbValue = "n";

    /**
     * Database values for unchecked state
     * @var array
     */
    protected $checkedDbValue = "y";


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
     * @param string $dbField Name of binded field
     * @param string $unCheckedDbValue Value for unchecked state
     * @param string $checkedDbValue Value for checked state
     */
    public function __construct(AM_Controller_Action $actionController, $name, $title = null, array $validationsRules = null, $dbField = null, $unCheckedDbValue = "n", $checkedDbValue = "y") {
        parent::__construct($actionController, $name, $title, $validationsRules, $dbField);
        $this->checkedDbValue = $checkedDbValue;
        $this->unCheckedDbValue = $unCheckedDbValue;
    }

    public function setDbValue($value) {
        $this->value = $value == $this->checkedDbValue;
    }

    public function getDbValue() {
        return $this->value ? $this->checkedDbValue : $this->unCheckedDbValue;
    }
}