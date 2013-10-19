ALTER TABLE  `device_token` ADD  `type_os` VARCHAR( 20 ) NULL DEFAULT NULL AFTER  `application_id` ,
ADD  `version_os` VARCHAR( 50 ) NULL DEFAULT NULL AFTER  `type_os` ,
ADD  `version_app` VARCHAR( 50 ) NULL DEFAULT NULL AFTER  `version_os`;
UPDATE device_token SET type_os =  'ios' WHERE type_os IS NULL;

ALTER TABLE  `application` DROP  `push_boxcar_name` ,
DROP  `push_boxcar_icon`;
