<?php
/**
 * @file
 * AM_Resource_Interface class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @defgroup AM_Resource
 */

/**
 * @ingroup AM_Resource
 */
interface AM_Resource_Interface
{
    /**
     * Get file wich will be resized for thumbnail
     * @return string Path to file
     */
    public function getFileForThumbnail();

    /**
     * Get source file path
     * @return string Path to file
     */
    public function getSourceFile();

    /**
     * Get source file extension
     * @return string Path to file
     */
    public function getSourceFileExtension();

    /**
     * Get source file name
     * @return string Path to file
     */
    public function getSourceFileName();

    /**
     * Get source file dir
     * @return string Path to file
     */
    public function getSourceFileDir();
}