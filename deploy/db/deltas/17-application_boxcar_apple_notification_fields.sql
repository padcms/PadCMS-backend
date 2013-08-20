ALTER TABLE  `application` ADD  `push_apple_enabled` TINYINT NULL DEFAULT NULL;
ALTER TABLE  `application` ADD  `push_boxcar_enabled` TINYINT NULL DEFAULT NULL;
ALTER TABLE  `application` ADD  `push_boxcar_provider_key` VARCHAR( 128 ) NULL DEFAULT NULL;
ALTER TABLE  `application` ADD  `push_boxcar_provider_secret` VARCHAR( 128 ) NULL DEFAULT NULL;
UPDATE `application` SET `push_apple_enabled` = '1';
