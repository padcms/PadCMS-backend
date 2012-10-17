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
    * [POSIX] (http://php.net/posix) This module contains an interface to those functions defined in the IEEE 1003.1 (POSIX.1) standards document which are not accessible through other means.
    * [PCNTL] (http://php.net/pcntl) Process Control support in PHP implements the Unix style of process creation, program execution, signal handling and process termination.
* [MySQL](http://mysql.com/) 5.x with the MyISAM storage engine
* [SQLite](http://www.sqlite.org/) 3.x
* [ImageMagick] (http://www.imagemagick.org/script/index.php) 6.7.x software suite to create, edit, compose, or convert bitmap images
* [PHING](http://www.phing.info/trac/) 2.4 - for configuration wizard and database deployment
* [PHPUnit](http://www.phpunit.de/) 3.5 (or 3.6 with DbUnit 1.0.0) - optional

Installation
------------

* Install all required packages

        sudo apt-get install unzip git apache2 mysql-server php5 sqlite3 php5-mysql php5-sqlite php5-imagick php-apc php-pear php5-dev imagemagick optipng
* Install PadCMSdraw util
    * Compile from sources

        mkdir /tmp/padcmsdraw && cd /tmp/padcmsdraw
        git clone git://github.com/padcms/padcms-draw.git .
        wget http://mupdf.googlecode.com/files/mupdf-thirdparty-2012-04-23.zip
        unzip mupdf-thirdparty-2012-04-23.zip
        make && sudo make install
    * Install from package

        mkdir /tmp/padcmsdraw && cd /tmp/padcmsdraw
        ``# i386``
        wget http://dev.padcms.net/attachments/102/padcmsdraw_1.0-1_i386.deb
        dpkg -i padcmsdraw_1.0-1_i386.deb
        ``# x64``
        wget http://dev.padcms.net/attachments/103/padcmsdraw_1.0-1_amd64.deb
        dpkg -i padcmsdraw_1.0-1_amd64.deb
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
* Run the task manager daemon, it will run in the background after first run

        ./padcms manager
* Add the PadCMS background tasks manager as cron job - we recommend to run task manager every 15 minutes to check whether the background process is not dead.

        */15 * * * * /var/www/padcms/htdocs/padcms manager > /dev/null 2>&1
* Run tests (optional)

        ./padcms phpunitall
* To access the backend as superuser open http://padcms.loc in you browser and use credentials:

        Login: admin
        Password: password

We strongly recommend to change password asap
