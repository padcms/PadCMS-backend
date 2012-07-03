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

* Install all required packages

        sudo apt-get install git apache2 mysql-server php5 sqlite3 poppler-utils xorg-dev php5-mysql php5-sqlite php5-imagick php5-gd php-apc php-pear
* Compile MuPdf utils

        mkdir /tmp/mupdf && cd /tmp/mupdf
        git clone git://git.ghostscript.com/mupdf.git .
        wget http://mupdf.googlecode.com/files/mupdf-thirdparty-2012-04-23.zip
        unzip mupdf-thirdparty-2012-04-23.zip
        sudo make prefix=/usr/local install

* Install needed packages from PEAR

        sudo pear config-set auto_discover 1
        sudo pear upgrade-all
        sudo pear install --alldeps pear.phing.info/phing
        sudo pear install pear.phpunit.de/DbUnit-1.0.0
* Create databases

        mysql -u root -proot -e 'create database padcms; create database padcms_test;'
* Clone the latest stable release of PadCMS backend

        sudo mkdir /var/www/padcms
        sudo chown username:usergroup /var/www/padcms
        git clone git://github.com/padcms/PadCMS-backend.git /var/www/padcms/htdocs
        cd /var/www/padcms/htdocs
* Prepare Apache virtual host

        sudo cp vhost.conf.sample /etc/apache2/sites-available/padcms
        sudo a2ensite padcms
        sudo a2enmod rewrite
        sudo apache2ctl restart
* Bind host 'padcms.loc' to the 127.0.0.1 IP in /etc/hosts file

        sudo nano /etc/hosts
add to the end of file

        127.0.0.1    padcms.loc
* Run phing configuration wizard in the padcms folder. You will be guided through few questions about system configuration. You can use default values or set specific. After configuration, the script will create a folders for temporary files and resources.

        phing init
* Run build task to create the necessary tables in databases

        ./padcms build
        APPLICATION_ENV=test ./padcms build
* Run tests (optional)

        ./padcms phpunitall
* To access the backend as superuser open http://padcms.loc in you browser and use credentials:

        Login: admin
        Password: password

We strongly recommend to change password asap
