PadCMS backend
==============

Requirements
------------

To run PadCMS Back end application, you need:
* Apache 2.x web server
** mod_rewtite is required
* PHP 5.3 with the following extensions:
** PHP Data Objects (PDO) with MySQL and SQLite drivers
** GD library of image functions
** ZIP extension
** ImageMagick is a native php extension to create and modify images using the ImageMagick API
* MySQL 5.x with the MyISAM storage engine
* SQLite 3.x
* Poppler and MuPDF tools for PDF manipulations
* PHING 2.4 - for configuration wizard and database deployment
* PHPUnit 3.5 (or 3.6 with DbUnit 1.0.0) - optional