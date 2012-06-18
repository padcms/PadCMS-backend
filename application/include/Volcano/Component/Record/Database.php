<?php
/**
 * Volcano Framework
 *
 * @category Volcano
 * @package Volcano_Component_Record
 * @subpackage Database
 * @author Ilya Gruzinov
 * @version $Revision$
 * @license http://vifm.volcanoideas.com/license/
 */

require_once 'Volcano/Component/Record.php';
require_once 'Volcano/Component/Interface/Database.php';
require_once 'Volcano/Component/Control/Database.php';
require_once 'Volcano/Component/Control/Database/ReadOnly.php';
/**
 * Database Record
 *
 * @category Volcano
 * @package Volcano_Component_Record
 * @subpackage Database
 */

class Volcano_Component_Record_Database extends Volcano_Component_Record implements Volcano_Component_Interface_Database {

    /**
     * Database link
     * @var Zend_Db_Adapter_Pdo_Abstract
     */
    protected $db;


    /**
     * Name of primary key from record
     *
     * @var string
     */
    protected $primaryKeyName;

    /**
     * Primary key value
     *
     * @var mixed
     */
    protected $primaryKeyValue;


    /**
     * Table name
     *
     * @var string
     */
    protected $tableName;

    /**
     * Database binded controls
     *
     * @var array
     */
    protected $databaseControls = array();

    /**
     * Readonly database controls
     * @var array
     */
    protected $readOnlyDbControls = array();

    /**
     * Constructor
     *
     * @param AM_Controller_Action $actionController Controller
     * @param string $name Component name
     * @param array $controls List of controls
     * @param Zend_Db_Adapter_Abstract $db Database Link
     * @param string $tableName Name of binded table
     * @param string $primaryKeyName Name of primary key
     * @param string $primaryKeyValue Value of primary key. Must be null in record in insert mode
     */
    public function __construct(AM_Controller_Action $actionController, $name, array $controls , Zend_Db_Adapter_Abstract $db, $tableName, $primaryKeyName, $primaryKeyValue) {
        $this->db = $db;
        $this->primaryKeyValue = $primaryKeyValue;
        $this->primaryKeyName = $primaryKeyName;
        $this->tableName = $tableName;
        parent::__construct($actionController, $name, $controls);
    }

        /**
     * Initialize record and all controls
     */
    protected function initialize() {
        parent::initialize();
    }

    protected function loadControls() {
        if (!$this->primaryKeyValue) {
            return;
        }
        $fields = array();
        foreach ($this->databaseControls as $name => $control) {
            $fields[] = $this->db->quoteIdentifier($control->getDbField()) . ' AS ' . $name;
        }
        //there not db fields. Hm.... Special case.
        if (!count($fields)) {
            return;
        }
        $selectSQL = "SELECT " . implode(", ", $fields) . " FROM " . $this->db->quoteIdentifier($this->tableName) . "  WHERE " . $this->db->quoteIdentifier($this->primaryKeyName) . " = ?";
        $row = $this->db->fetchRow($selectSQL, $this->primaryKeyValue);
        if (!$row) {
            $this->errors[] = $this->localizer->translate("Database Error: Cannot find specified row");
        } else {
            foreach ($this->databaseControls as $name => $control) {
                $control->setDbValue($row[$name]);
            }
        }
    }

    /**
     * Retrieve controls value from post, or from database if is readony
     */
    protected function retrieveControls() {
        if (!$this->primaryKeyValue || !count($this->readOnlyDbControls)) {
            return parent::retrieveControls();
        }

        foreach ($this->controls as $control) {
            if (!in_array($control, $this->readOnlyDbControls, true)) {
                $control->retrieveValue();
            }
        }
        //load readonly controls
        $fields = array();
        foreach ($this->readOnlyDbControls as $name => $control) {
            $fields[] = $this->db->quoteIdentifier($control->getDbField()) . ' AS ' . $name;
        }
        //there not db fields. Hm.... Special case.
        if (!count($fields)) {
            return;
        }

        $selectSQL = "SELECT " . implode(", ", $fields) . " from " . $this->db->quoteIdentifier($this->tableName) . "  where " . $this->db->quoteIdentifier($this->primaryKeyName) . " = ?";
        $row = $this->db->fetchRow($selectSQL, $this->primaryKeyValue);
        if (!$row) {
            $this->errors[] = $this->localizer->getMessage("Database Error: Cannot find specified row");
        } else {
            foreach ($this->readOnlyDbcontrols as $name => $control) {
                $control->setDbValue($row[$name]);
            }
        }
    }

    protected function _preOperation()
    { }

    /**
     * Process record operation and return it status
     *
     * @return boolean Operation Result
     */
    public function operation() {
        $this->_preOperation();

        if ($this->isSubmitted && $this->validate()) {
            if ($this->primaryKeyValue) {
                return $this->update();
            } else {
                return $this->insert();
            }
        }
        return false;
    }

    protected function update() {
        $bind = array();
        foreach ($this->databaseControls as $control) {
            if (!($control instanceof Volcano_Component_Control_Database_ReadOnly)) {
                $bind[$control->getDbField()] = $control->getDbValue();
            }
        }
        $where = $this->db->quoteIdentifier($this->primaryKeyName) . " = " . $this->db->quote($this->primaryKeyValue);
        try {
            $this->db->update($this->tableName, $bind, $where);
        } catch(Zend_Db_Exception $e) {
            $this->errors[] = $this->localizer->translate("Database Error: %1\$s", $e->getMessage());
            return false;
        }
        foreach ($this->databaseControls as $control) {
            if ($control instanceof Volcano_Component_Control_Database_File || $control instanceof Volcano_Component_Control_File) {
                if (!$control->move($this->primaryKeyValue)) {
                    return false;
                }
            }
        }


        return "update";
    }

    protected function insert() {
        $bind = array();
        foreach ($this->databaseControls as $control) {
            if (!$control instanceof Volcano_Component_Control_Database_ReadOnly) {
                $bind[$control->getDbField()] = $control->getDbValue();
            }
        }
        try {
            $result = $this->db->insert($this->tableName, $bind);
            $this->primaryKeyValue = $this->db->lastInsertId();
        } catch (Zend_Db_Exception $e) {
            $this->errors[] = $this->localizer->translate("Database Error: %1\$s", $e->getMessage());
            return false;
        }
        foreach ($this->databaseControls as $control) {
            if ($control instanceof Volcano_Component_Control_Database_File || $control instanceof Volcano_Component_Control_File) {
                if (!$control->move($this->primaryKeyValue)) {
                    $this->delete();
                    return false;
                }
            }
        }
        return "insert";

    }

    public function delete() {
        try {
            $this->db->delete($this->tableName, $this->primaryKeyName . " = " . $this->db->quote($this->primaryKeyValue));
            $this->primaryKeyValue = null;
        } catch(Zend_Db_Exception $e) {
            $this->errors[] = $this->localizer->translate("Database Error: %1\$s", $e->getMessage());
            return false;
        }
        return "delete";
    }

    /**
     * Set DB connection link
     * @param Zend_Db_Adapter_Abstract $db Database link
     * @return Zend_Db_Adapter_Abstract
     */
    public function setDb(Zend_Db_Adapter_Abstract $db) {
        $this->db = $db;
    }


    /**
     * Return DB connection link
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDb() {
        return $this->db;
    }

    /**
     * Return primary key value
     */
    public function getPrimaryKeyValue() {
        return $this->primaryKeyValue;
    }

    public function show() {
        $record = array(
                "primaryKeyValue" => $this->primaryKeyValue
        );
        if (isset($this->view->{$this->name})) {
            $this->view->{$this->name} = array_merge($record, $this->view->{$this->name});
        } else {
            $this->view->{$this->name} = $record;
        }
        return parent::show();
    }

    /**
     * Add new control
     *
     * @param Volcano_Component_Control $control
     */
    public function  addControl(Volcano_Component_Control $control) {
        parent::addControl($control);
        $name = $control->getName();
        if ($control instanceof Volcano_Component_Control_Database) {
            $this->databaseControls[$name] = $control;
            if ($control instanceof Volcano_Component_Control_Database_ReadOnly) {
                $this->readOnlyDbControls[$name] = $control;
            }
        }
    }

    /**
     * Remove control with given name from controls collection
     *
     * @param string $name
     */
    public function  removeControl($name) {
        parent::removeControl($name);
        if (array_key_exists($name, $this->databaseControls)) {
            unset($this->databaseControls[$name]);
            if (array_key_exists($name, $this->readOnlyDbControls)) {
                unset($this->readOnlyDbControls[$name]);
            }
        }
    }

}
