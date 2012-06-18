<?php
/**
 * @file
 * AM_Component_Record_Database class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Base record component
 * @ingroup AM_Component
 * @todo refactoring
 */
class AM_Component_Record_Database extends Volcano_Component_Record_Database
{
    /**
     * @return void
     */
    public function show()
    {
        parent::show();

        foreach ($this->controls as $sName => $oControl) {
            $oControl = array('errors' => $oControl->getErrors());

            if (isset($this->view->{$sName})) {
                $this->view->{$sName} = array_merge($oControl, $this->view->{$sName});
            } else {
                $this->view->{$sName} = $oControl;
            }
        }
    }
}