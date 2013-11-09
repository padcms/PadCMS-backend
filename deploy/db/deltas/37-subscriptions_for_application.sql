CREATE TABLE IF NOT EXISTS `subscription` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `itunes_id` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `button_title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE  `subscription` ADD  `application` INT( 10 ) NOT NULL;

ALTER TABLE  `subscription` ADD  `created` DATETIME NOT NULL ,
ADD  `updated` DATETIME NULL DEFAULT NULL;
