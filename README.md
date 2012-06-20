PadCMS backend
==============

Requirements
------------

* [Apache](http://httpd.apache.org/) 2.x web server
    * [mod_rewtite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html) is required
* [PHP](http://php.net/) 5.3 with the following extensions:
    * PHP Data Objects ([PDO](http://php.net/manual/en/book.pdo.php)) with MySQL and SQLite drivers
    * [GD](http://php.net/manual/en/book.image.php) library of image functions
    * [ZIP](http://www.php.net/manual/en/book.zip.php) extension
    * [ImageMagick](http://php.net/manual/en/book.imagick.php) is a native php extension to create and modify images using the ImageMagick API
* [MySQL](http://mysql.com/) 5.x with the MyISAM storage engine
* [SQLite](http://www.sqlite.org/) 3.x
* [Poppler](http://poppler.freedesktop.org/) and [MuPDF](http://mupdf.com/) tools for PDF manipulations
* [PHING](http://www.phing.info/trac/) 2.4 - for configuration wizard and database deployment
* [PHPUnit](http://www.phpunit.de/) 3.5 (or 3.6 with DbUnit 1.0.0) - optional

Installation
------------

* Download or fork the latest stable release of PadCMS backend
* Move the files to the convenient location on your web server (this place should not be the web server's document root).
* Create symbolic link from the web server's document root directory to the "front" directory.

        ln -s /path/to/padcms/front /path/to/document/root
* [Create](http://dev.mysql.com/doc/refman/5.0/en/creating-database.html) MySQL database for production and test environments
* Run phing configuration wizard in the padcms folder. You will be guided through few questions about system configuration. You can use default values or set specific. After configuration, the script will create a folders for temporary files and resources.

        cd /path/to/padcms
        phing init
* Run build task to create the necessary tables in databases

        ./padcms build
        APPLICATION_ENV=test ./padcms build
* Run test

        ./padcms phpunitall
