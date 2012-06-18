<?php
/**
 * Volcano Framework
 *
 * @category Volcano
 * @package Volcano_Tools
 * @author Ilya Gruzinov
 * @version $Revision$
 * @license http://vifm.volcanoideas.com/license/
 */


/**
 * Static class for soubroutines
 *
 * @category Volcano
 * @package Volcano_Tools
 */

class Volcano_Tools {

    /**
     * Fix given path, by adding application folder as current
     * for relative paths
     *
     * @param string $path Path
     */
    static function fixSinglePath($path) {
        if (substr($path, 0, 2) == "." . DIRECTORY_SEPARATOR) {
            $path = Zend_Registry::get("applicationFolder") . DIRECTORY_SEPARATOR . substr($path, 2);
        } elseif (substr($path, 0, 2) == ".." . DIRECTORY_SEPARATOR) {
            $path = Zend_Registry::get("applicationFolder") . DIRECTORY_SEPARATOR . $path;
        }
        return $path;
    }

    static function fixPath($path) {
        if (is_string($path)) {
            $path = self::fixSinglePath($path);
        } elseif (is_array($path)) {
            foreach ($path as &$item) {
                $item = self::fixSinglePath($item);
            }
        }
        return $path;
    }

    /**
     * Return hash of string
     * @param string $str String to hashing
     * @return string
     */
    static function getHash($str) {
        return md5($str);
    }

    /**
     * Remove dir with all files and folders
     * @param string $name Folder name
     * @return Result of operation
     */
    static function rmDirRecursively($name) {
        $dh = @opendir($name);
        if (!$dh) {
            return false;
        }

        $result = true;

        while ($file = @readdir($dh)) {
            if ($file != ".." && $file != ".") {
                if (@is_file($name . "/" . $file)) {
                    $result = @unlink($name . "/" . $file) && $result;
                } else {
                    $result = self::rmDirRecursively($name . "/" . $file) && $result;
                }
            }
        }
        @closedir($fh);
        return @rmdir($name) && $result;
    }

    /**
     * Copy directory content
     * @param string $src Source directory
     * @param string $dst Destanation directory
     * @param bool $overwrite Overwrite file if exists
     */
    static function copyDir($src, $dst, $overwrite = true) {
        if (!is_dir($dst) && !@mkdir($dst)) {
            return false;
        }
        $dh = @opendir($src);
        if (!$dh) {
            return false;
        }

        $result = true;

        while ($file = @readdir($dh)) {
            if ($file != ".." && $file != ".") {
                if (@is_file($src . "/" . $file)) {
                    if ($overwrite || !file_exists($dst . "/" . $file)) {
                        $result = @copy($src . "/" . $file, $dst . "/" . $file) && $result;
                    }

                } else {
                    $result = self::copyDir($src . "/" . $file, $dst . "/" . $file, $overwrite) && $result;
                }
            }
        }
        @closedir($fh);
        return $result;
    }
}	 