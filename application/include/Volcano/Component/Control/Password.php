<?php
/**
 * Volcano Framework
 *
 * @category Volcano
 * @package Volcano_Component_Control_Database
 * @subpackage Password
 * @author Ilya Gruzinov
 * @version $Revision$
 * @license http://vifm.volcanoideas.com/license/
 */

include_once 'Volcano/Component/Control.php';

/**
 * Password control
 *
 * @category Volcano
 * @package Volcano_Component_Control
 * @subpackage Password
 */

class Volcano_Component_Control_Password extends Volcano_Component_Control {

    public function show() {
        $control = array(
                "value" => null,
        );
        if (isset($this->view->{$this->name})) {
            $this->view->{$this->name} = array_merge($control, $this->view->{$this->name});
        } else {
            $this->view->{$this->name} = $control;
        }
        return parent::show();
    }

}