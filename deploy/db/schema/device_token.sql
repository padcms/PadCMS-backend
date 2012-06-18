DROP TABLE IF EXISTS `device_token`;
CREATE TABLE IF NOT EXISTS `device_token` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `udid` varchar(40) NOT NULL,
  `token` varchar(64) NOT NULL,
  `application_id` int(10) NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `udid` (`udid`),
  INDEX `udid_token_app` (`udid`, `token`, `application_id`)
)
ENGINE=MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;