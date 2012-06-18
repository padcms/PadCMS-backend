<?php
/**
 * Volcano Framework
 *
 * @category Volcano
 * @package Volcano_Component_Control
 * @subpackage File
 * @author Ilya Gruzinov
 * @version $Revision$
 * @license http://vifm.volcanoideas.com/license/
 */

include_once 'Volcano/Component/Control.php';
/**
 * FileUpload control
 *
 * @category Volcano
 * @package Volcano_Component_Control
 * @subpackage File
 */


class Volcano_Component_Control_File extends Volcano_Component_Control {

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

    public function __construct(Zend_Controller_Action $actionController, $name, $title = null, $validationsRules = null, $fileFolder, $autoCreateFolder = false) {
        parent::__construct($actionController, $name, $title, $validationsRules);
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
    public function move() {
        if (!$this->value) {
            return true;
        }
        if (!@is_dir($this->fileFolder)) {
            if (!@mkdir($this->fileFolder, self::$folderMode, true)) {
                $this->errors[] = $this->localizer->translate("File I/O Error");
                return false;
            }
        }
        if (!@move_uploaded_file($_FILES[$this->name]["tmp_name"], $this->fileFolder . "/" . $this->value)) {
            @chmod($this->fileFolder . "/" . $this->value, self::$fileMode);
            $this->errors[] = $this->localizer->translate("File I/O Error");
            return false;
        }
        return true;
    }

}