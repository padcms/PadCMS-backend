/*SELECT `id`, `application`, `welcome` FROM `issue` WHERE `welcome` IS NOT NULL;*/
ALTER TABLE  `application` ADD  `welcome` TEXT NULL DEFAULT NULL AFTER  `description`;
ALTER TABLE `issue` DROP `welcome`;
ALTER TABLE  `issue` ADD  `publish_date` DATETIME NULL DEFAULT NULL AFTER  `state`;
ALTER TABLE  `issue` ADD  `paid` BOOLEAN NULL DEFAULT NULL;
