<?php
/**
 * Volcano Framework
 *
 * @category Volcano
 * @package Volcano_Component
 * @author Ilya Gruzinov
 * @version $Revision$
 * @license http://vifm.volcanoideas.com/license/
 */


/**
 * Base component whith minimum properties
 *
 * @category Volcano
 * @package Volcano_Component
 */
class Volcano_Component {
  /**
   * View object
   * @var view
   */
  protected $view;

  /**
   * Action controller
   * @var AM_Controller_Action
   */
  protected $actionController;

  /**
   * HTTP request
   * @var Zend_Controller_Request_Http
   */
  protected $request;

  /**
   * HTTP Response
   * @var Zend_Controller_Response_Http
   */
  protected $response;

  /**
   * Name of component
   * @var string;
   */
  protected $name;

  /**
   * Error list
   */
  protected $errors = array();

  /**
   * Localizer interface
   * @var Volcano_Localizer
   */
  protected $localizer = null;

  /**
   * Constructor
   *
   * @param Zend_Controller_Action $actionController action controller
   * @param string $name Component name
   */
  public function __construct(AM_Controller_Action $actionController, $name) {
    $this->preInitialize();
    $this->actionController = $actionController;
    $this->name = $name;
    $this->initialize();
    $this->postInitialize();
  }


   protected function preInitialize() {

   }

   protected function postInitialize() {

   }

  /**
   * Initialize component
   */
  protected function initialize() {
    $actionController = $this->actionController;
    $this->request = $actionController->getRequest();
    $this->response = $actionController->getResponse();
    $this->view = $actionController->view;
    $this->localizer = $actionController->localizer;

  }

  /**
   * Return param
   *
   * @param string $name Parameter name
   * @param string $default Default value
   */
  protected function getParam($name, $default = null) {
    return $this->request->getParam($this->name . $name, $default);
  }

  /**
   * Return name of component
   * @return string
   */
  public function getName() {
  	return $this->name;
  }

  /**
   * Return Errors list
   * @return array
   */
  public function getErrors() {
  	return $this->errors;
  }

}
