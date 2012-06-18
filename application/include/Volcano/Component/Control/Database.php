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
class Volcano_Component_Control_Database extends Volcano_Component_Control {
    /**
     * Name of binded field
     * @var Database Field Name
     */
    protected $dbField;

    /**
     * Default database value
     *
     * @var mixed
     */
    protected $defaultDBValue = null;

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
     */
    public function __construct(AM_Controller_Action $actionController, $name, $title = null, array $validationsRules = null, $dbField = null) {
        parent::__construct($actionController, $name, $title, $validationsRules);
        $this->dbField = $dbField ? $dbField : $name;
    }

    /**
     * Return name of DB field
     * @return string
     */
    public function getDbField() {
        return $this->dbField;
    }

    /**
     * Return value for DB operations
     * @return string
     */
    public function getDbValue() {
        return strlen($this->value) ? $this->value : $this->defaultDBValue;
    }

    /**
     * Set value, from fetched DB field
     */
    public function setDbValue($value) {
        $this->setValue($value);
    }

    public function setDefaultDBValue($value) {
        $this->defaultDBValue = $value;
    }

    public function getDefaultDBValue() {
        return $this->defaultDBValue;
    }

}