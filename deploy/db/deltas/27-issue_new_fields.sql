ALTER TABLE  `issue` ADD  `is_issue_individually_paid` BOOLEAN NULL DEFAULT NULL AFTER  `state`;
ALTER TABLE  `issue` CHANGE  `short_intro`  `excerpt` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `application` CHANGE  `long_intro`  `welcome` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `issue` ADD  `title_short` VARCHAR( 128 ) NULL DEFAULT NULL AFTER  `excerpt` ,
ADD  `excerpt_short` TEXT NULL DEFAULT NULL AFTER  `title_short`;
UPDATE `device_token` SET updated = created WHERE updated IS NULL;