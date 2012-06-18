<?php
/**
 * Volcano Framework
 *
 * @category Volcano
 * @package Volcano_Component
 * @subpackage Record
 * @author Ilya Gruzinov
 * @version $Revision$
 * @license http://vifm.volcanoideas.com/license/
 */

require_once 'Volcano/Component.php';
require_once 'Volcano/Component/Control.php';
/**
 * Record component
 *
 * @category Volcano
 * @package Volcano_Component
 * @subpackage Record
 */
class Volcano_Component_Record extends Volcano_Component {
    /**
     * Name of component
     * @var integer
     */
    protected $name;

    /**
     * Controls collection
     * @var array
     */
    protected $controls = array();

    /**
     * Is form submitted flag
     */
    protected $isSubmitted = false;



    /**
     * Constructor
     *
     * @param Volcano_Controller_Action $actionController Controller
     * @param string $name Component name
     * @param array $controls List of controls
     */
    public function __construct(Zend_Controller_Action $actionController, $name, $controls = null) {
        foreach ($controls as $control) {
            $this->addControl($control);
        }
        parent::__construct($actionController, $name);
    }

    /**
     * Initialize record and all controls
     */
    protected function postInitialize() {
        parent::initialize();
        $this->isSubmitted = $this->request->getPost("form") == $this->name;
        if ($this->isSubmitted) {
            $this->retrieveControls();
        } else {
            $this->loadControls();
        }
    }
    

    /**
     * Process record operation and return it status
     *
     * @return boolean Operation Result
     */
    public function operation() {
    }


    /**
     * Validate all controls
     * @return boolean Validation result
     */
    protected function validate() {
        $success = true;
        foreach ($this->controls as $control) {
            $success = $control->validate() && $success;
        }
        return $success;
    }

    /**
     * Read initial data
     */
    protected function loadControls() {
    }

    /**
     * Retrieve controls value from post
     */
    protected function retrieveControls() {
        foreach ($this->controls as $control) {
            $control->retrieveValue();
        }
    }


    /**
     * Put all form variables to smarty
     */
    public function show() {
        $errors = $this->getErrors();
        $record = array(
                "name" => $this->name,
                "isSubmitted" => $this->isSubmitted,
                "errors" => $errors
        );
        if (isset($this->view->{$this->name})) {
            $this->view->{$this->name} = array_merge($record, $this->view->{$this->name});
        } else {
            $this->view->{$this->name} = $record;
        }
        foreach ($this->controls as $control) {
            $control->show();
        }
    }

    /**
     * Return Errors list
     * @return array
     */
    public function getErrors() {
        $errors = parent::getErrors();
        foreach ($this->controls as $control) {
            $errors = array_merge($errors, $control->getErrors());
        }
        return $errors;
    }

    /**
     * Return list of controls
     *
     * @return array
     */
    public function getControls() {
        return $this->controls;
    }

    /**
     * Add new control
     *
     * @param Volcano_Component_Control $control
     */
    public function addControl(Volcano_Component_Control $control) {
        $name = $control->getName();
        if (array_key_exists($name, $this->controls)) {
            require_once 'Volcano/Exception.php';
            throw new Volcano_Exception($this->localizer->translate('Control with name %s exists in record %s', $name, $this->getName()));
        }
        $this->controls[$name] = $control;
    }

    /**
     * Remove control with given name from controls collection
     *
     * @param string $name
     */
    public function removeControl($name) {
        if (!array_key_exists($name, $this->controls)) {
            require_once 'Volcano/Exception.php';
            throw new Volcano_Exception($this->localizer->translate('Control with name %s is not exists in record %s', $name, $this->getName()));
        }
        unset($this->controls[$name]);
    }

    /**
     * Return control with given name
     *
     * @param string $name
     * @return Volcano_Component_Control
     */
    public function getControl($name) {
        if (!array_key_exists($name, $this->controls)) {
            require_once 'Volcano/Exception.php';
            throw new Volcano_Exception($this->localizer->translate('Control with name %s is not exists in record %s', $name, $this->getName()));
        }
        return $this->controls[$name];
    }
}
