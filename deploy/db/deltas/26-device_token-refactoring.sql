ALTER TABLE  `device_token` ADD  `updated` DATETIME NULL DEFAULT NULL ,
ADD  `expired` DATETIME NULL DEFAULT NULL;
UPDATE device_token SET device_token.updated = '2013-10-01 00:00:00';
