<?php

include_once '/Volcano/Component/Control/Database/File.php';

class AM_Component_Control_Database_File extends Volcano_Component_Control_Database_File
{
    protected $sCurrentValue = null;
    protected $bPreventFileMoving = false;

    public function __construct(
        Zend_Controller_Action $actionController, $name, $title = null, $validationsRules = null, $dbField = null,
        $fileFolder, $autoCreateFolder = false, $sCurrentValue = null) {

        parent::__construct($actionController, $name, $title, $validationsRules, $dbField, $fileFolder, $autoCreateFolder);

        $this->sCurrentValue = $sCurrentValue;
    }


    public function retrieveValue()
    {
        if (isset($_FILES[$this->name]) && !empty($_FILES[$this->name]["name"])) {
            $this->setValue(basename($_FILES[$this->name]["name"]));
        }
        elseif ($this->sCurrentValue) {
            $this->setValue($this->sCurrentValue);
            $this->bPreventFileMoving = true;
        }
    }

    public function move($recordKeyValue = null)
    {
        if (!$this->value || $this->bPreventFileMoving) {
            return true;
        }
        $fileFolder = str_replace("[ID]", trim(AM_Tools_String::generatePathFromId($recordKeyValue), DIRECTORY_SEPARATOR), $this->fileFolder);
        if (!@is_dir($fileFolder)) {
            if (!@mkdir($fileFolder, Volcano_Component_Control_Database_File::$folderMode, true)) {
                $this->errors[] = $this->localizer->translate("File I/O Error");
                return false;
            }
        }
        $sDestination = $fileFolder . "/" . $this->value;
        if (!@move_uploaded_file($_FILES[$this->name]["tmp_name"], $sDestination)) {
            $this->errors[] = $this->localizer->translate("File I/O Error");
            return false;
        }

        $oThumbnailerHandler = AM_Handler_Locator::getInstance()->getHandler('thumbnail');
        /* @var $oThumbnailerHandler AM_Handler_Thumbnail */

        $oThumbnailerHandler->clearSources()
          ->addSourceFile($sDestination)
          ->loadAllPresets(AM_Model_Db_Issue::PRESET_ISSUE_IMAGE)
          ->createThumbnails();
      return true;
    }
}
