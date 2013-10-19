CREATE TABLE IF NOT EXISTS `old_device_token` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `udid` varchar(40) NOT NULL,
  `token` varchar(64) NOT NULL,
  `application_id` int(10) NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `expired` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `udid` (`udid`),
  KEY `udid_token_client` (`udid`,`token`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
INSERT INTO old_device_token SELECT * FROM device_token;
TRUNCATE device_token;
ALTER TABLE `device_token` DROP `udid`;
ALTER TABLE  `device_token` DROP INDEX  `udid_token_client` , ADD INDEX  `token` (  `token` );
ALTER TABLE  `device_token` ADD UNIQUE  `token_application` (  `token` ,  `application_id` );

INSERT INTO device_token( token, application_id, created, updated, expired )
SELECT token, application_id, created, updated, expired
FROM  `old_device_token`
GROUP BY token, application_id
HAVING updated = MAX( updated )
ORDER BY id;
