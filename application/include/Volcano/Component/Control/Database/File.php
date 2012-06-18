<?php
/**
 * Volcano Framework
 *
 * @category Volcano
 * @package Volcano_Component_Control_Database
 * @subpackage File
 * @author Ilya Gruzinov
 * @version $Revision$
 * @license http://vifm.volcanoideas.com/license/
 */

include_once 'Volcano/Component/Control/Database.php';
/**
 * FileUpload control
 *
 * @category Volcano
 * @package Volcano_Component_Control_Database
 * @subpackage File
 */


class Volcano_Component_Control_Database_File extends Volcano_Component_Control_Database {

    public static $fileMode = 0777;
    public static $folderMode = 0777;

    /**
     * Folder to store files
     *
     * @var string
     */
    public $fileFolder;

    /**
     * Auto create file folder if not exists
     *
     * @var boolean
     */
    protected $autoCreateFolder = false;

    public function __construct(Zend_Controller_Action $actionController, $name, $title = null, $validationsRules = null, $dbField = null, $fileFolder, $autoCreateFolder = false) {
        parent::__construct($actionController, $name, $title, $validationsRules, $dbField);
        $this->fileFolder = $fileFolder;
        $this->autoCreateFolder = $autoCreateFolder;
    }
    public function retrieveValue() {
        if (isset($_FILES[$this->name]) && is_array($_FILES[$this->name])) {
            $this->setValue(basename($_FILES[$this->name]["name"]));
        }
    }

    /**
     * Move file to specified folder
     */
    public function move($recordKeyValue = null) {
        if (!$this->value)
            return true;
        $fileFolder = str_replace("[ID]", $recordKeyValue, $this->fileFolder);
        if (!@is_dir($fileFolder))
            if (!@mkdir($fileFolder, Volcano_Component_Control_Database_File::$folderMode, true)) {
                $this->errors[] = $this->localizer->translate("File I/O Error");
                return false;
            }
        if (!@move_uploaded_file($_FILES[$this->name]["tmp_name"], $fileFolder . "/" . $this->value)) {
            $this->errors[] = $this->localizer->translate("File I/O Error");
            return false;
        }
        return true;
    }

}