ALTER TABLE  `application` ADD  `application_notification_google` TEXT NULL DEFAULT NULL AFTER  `frequency`;
ALTER TABLE  `application` ADD  `application_email` TEXT NULL DEFAULT NULL AFTER  `application_notification_google`;
ALTER TABLE  `issue` CHANGE  `excerpt`  `short_intro` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `application` CHANGE  `welcome`  `long_intro` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
