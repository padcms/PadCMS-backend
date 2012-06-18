<?php
/**
 * Volcano Framework
 *
 * @category Volcano
 * @package Volcano_Component
 * @subpackage Control
 * @author Ilya Gruzinov
 * @version $Revision$
 * @license http://vifm.volcanoideas.com/license/
 */

include_once 'Volcano/Component.php';
/**
 * Basic control
 *
 * @category Volcano
 * @package Volcano_Component
 * @subpackage Control
 */
class Volcano_Component_Control extends Volcano_Component {

    /**
     * Control title (for error messages)
     * @var string
     */
    protected $title;

    /**
     * validation rules
     * @var array
     */
    protected $validations = array();

    /**
     * Control Value
     * @var string
     */
    protected $value;



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
    public function __construct(AM_Controller_Action  $actionController, $name, $title = null, array $validationsRules = null) {
        parent::__construct($actionController, $name);
        if ($title) {
            $this->title = $title;
        } else {
            $this->title = $name;
        }
        if ($validationsRules) {
            $this->addValidationRules($validationsRules);
        }
    }




    /**
     * Set control value
     * @param mixed $newValue New value
     */
    public function setValue($newValue) {
        $this->value = $newValue;
    }

    /**
     * Return control value
     * @return mixed Control Value
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Retrieve control value from POST
     */
    public function retrieveValue() {
        $this->setValue($this->request->getPost($this->name));
    }

    public function show() {
        $control = array(
            "value" => $this->value,
            "title" => $this->title,
            "name" => $this->name,
        );

        if (isset($this->view->{$this->name})) {
            $this->view->{$this->name} = array_merge($control, $this->view->{$this->name});
        } else {
            $this->view->{$this->name} = $control;
        }
    }


    /** Validations **/


    /**
     * Validate required value
     * @return bool Validation result
     */
    protected function validateRequire() {
        return strlen($this->value)> 0;
    }

    /**
     * Validate Integer value
     * @return bool Validation result
     */
    protected function validateInteger() {
        return preg_match("/^[-+]?\\d+$/", $this->value);
    }

    /**
     * Validate Float value
     * @return bool Validation result
     */
    protected function validateFloat() {
        return preg_match("/^[-+]?\\d+(\\.\\d+)?$/", $this->value);
    }

    /**
     * Validate Numeric value
     * (Float validation wrapper)
     * @return bool Validation result
     */
    protected function validateNumeric() {
        return $this->validateFloat();
    }

    /**
     * Validate minimal value
     * @input integer $min Minimal value
     * @return bool Validation result
     */
    protected function validateMinimumValue($min) {
        return $this->value >= $min;
    }

    /**
     * Validate maximum value
     * @input integer max Maximum value
     * @return bool Validation result
     */
    protected function validateMaximumValue($max) {
        return $this->value <= $max;
    }

    /**
     * Validate minimal length
     * @input integer $min Minimal length
     * @return bool Validation result
     */
    protected function validateMinimumLength($min) {
        return strlen($this->value) >= $min;
    }

    /**
     * Validate maximal length
     * @input integer $max Maximum length
     * @return bool Validation result
     */
    protected function validateMaximumLength($max) {
        return strlen($this->value)<= $max;
    }

    /**
     * Validate is value in some range
     * @input integer $min Minimal value
     * @input integer $max Maximum value
     * @return bool Validation result
     */
    protected function validateRangeValue($min, $max) {
        return $this->validateMinimumValue($this->value, $min) && $this->validateMaximumValue($this->value, $max);
    }

    /**
     * Validate is length in some length
     * @input integer $min Minimal length
     * @input integer $max Maximum length
     * @return bool Validation result
     */
    protected function validateRangeLength($min, $max) {
        return $this->validateMinimumLength($this->value, $min) && $this->validateMaximumLength($this->value, $max);
    }

    /**
     * Validate value by regexp
     * @input string $re Regexp to validation
     * @return bool Validation result
     */
    protected function validateRegexp($re) {
        return preg_match($re, $this->value);
    }

    /**
     * Validate if value is correct E-Mail
     * @return bool Validation result
     */
    protected function validateEMail() {
        return $this->validateRegexp("/^[a-z0-9_\\-]+(\\.[a-z0-9_\\-]+)*\\@([a-z0-9_\\-]+\\.)+[a-z]{2,8}\$/i");

    }


    /**
     * Return true if value in list
     */
    protected function validateListOfValues($list) {
        return in_array($this->value, $list);
    }

    /**
     * Validate control value
     * @return boolean Validation result
     */
    public function validate() {
        $errors = array();
        $empty = strlen($this->value) == 0;
        foreach($this->validations as $rule) {
            if (!is_array($rule)) {
                $rule = array($rule);
            }
            switch ($rule[0]) {
                case "require":
                case "required":
                    if (!$this->validateRequire()) {
                        $errors[] = $this->localizer->translate("Field %1\$s is required", $this->title);
                    }
                    break;
                case "integer":
                    if (!$empty && !$this->validateInteger()) {
                        $errors[] = $this->localizer->translate("Field %1\$s must be integer", $this->title);
                    }
                    break;
                case "float":
                case "real":
                case "decimal":
                    if (!$empty && !$this->validateFloat()) {
                        $errors[] = $this->localizer->translate("Field %1\$s must be float", $this->title);
                    }
                    break;
                case "numeric":
                    if (!$empty && !$this->validateNumeric()) {
                        $errors[] = $this->localizer->translate("Field %1\$s must be number", $this->title);
                    }
                    break;
                case "maximum value":
                case "maxval":
                    if (!$empty && !$this->validateMaximumValue($rule[1])) {
                        $errors[] = $this->localizer->translate("Field %1\$s cannot be above %2\$d", $this->title, $rule[1]);
                    }
                    break;
                case "minimum value":
                case "minval":
                    if (!$empty && !$this->validateMinimumValue($rule[1])) {
                        $errors[] = $this->localizer->translate("Field %1\$s cannot be less %2\$d", $this->title, $rule[1]);
                    }
                    break;
                case "maximum length":
                case "maxlen":
                    if (!$empty && !$this->validateMaximumLength($rule[1])) {
                        $errors[] = $this->localizer->translate("Size of Field %1\$s cannot be above %2\$d characters", $this->title, $rule[1]);
                    }
                    break;
                case "minimum length":
                case "minlen":
                    if (!$empty && !$this->validateMinimumLength($rule[1])) {
                        $errors[] = $this->localizer->translate("Size of Field %1\$s cannot be less %2\$d characters", $this->title, $rule[1]);
                    }
                    break;
                case "range":
                    if (!$empty && !$this->validateRangeValue($rule[1], $rule[2])) {
                        $errors[] = $this->localizer->translate("Field %1\$s must be in range %2\$d .. %3\$d", $this->title, $rule[1], $rule[2]);
                    }
                    break;
                case "regexp":
                    if (!$empty && !$this->validateRegexp($rule[1])) {
                        $errors[] = $this->localizer->translate("Field %1\$s is incorrect", $this->title);
                    }
                    break;
                case "email":
                    if (!$empty && !$this->validateEMail()) {
                        $errors[] = $this->localizer->translate("Field %1\$s must be email", $this->title);
                    }
                    break;
                case "list of values":
                    if (!$empty && !$this->validateListOfValues($rule[1])) {
                        $errors[] = $this->localizer->translate("Field %1\$s is incorrect", $this->title);
                    }
                    break;

                case "function":
                    if ($err = call_user_func_array($rule[1], array_merge(array($this->value), array_slice($rule, 2)))) {
                        $errors[] = $err;
                    }
                    break;
                default:
                    if ($rule[0] instanceof Zend_Validate_Abstract) {
                        if (!$rule[0]->isValid($this->value)) {
                            $errors = array_merge($errors, $rule[0]->getErrors());
                        }

                    }
            }
        }
        if ($errors)
            $this->errors = array_merge($this->errors, $errors);
        return !$errors || count($errors) == 0;
    }

    /**
     * Return validation rules
     *
     * @return array
     */
    public function getValidationRules() {
        return $this->validations;
    }

    /**
     * Clear validations rules
     */
    public function clearValidationRules() {
        $this->validations = array();
    }

    /**
     * Add new validations to list
     *
     * @param array $validationsRules List of new validations rules
     * @param string $place Place where new validations must be inserted. Now supported 'start', 'end';
     * @todo add
     */
    public function addValidationRules($validationsRules = array(), $place = 'end') {
        switch ($place) {
            case 'start':
                $this->validations = array_merge($validationsRules, $this->validations);
                break;
            case 'end':
            default:
                $this->validations = array_merge($this->validations, $validationsRules);
                break;
        }
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
