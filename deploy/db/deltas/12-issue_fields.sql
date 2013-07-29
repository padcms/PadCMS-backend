ALTER TABLE  `issue` ADD  `subtitle` VARCHAR( 50 ) NULL DEFAULT NULL AFTER  `title`;
ALTER TABLE  `issue` ADD  `image` VARCHAR( 255 ) NULL DEFAULT NULL AFTER  `subtitle`;
ALTER TABLE  `issue` CHANGE  `image`  `image` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `issue` ADD  `excerpt` VARCHAR( 255 ) NOT NULL AFTER  `subtitle`;
ALTER TABLE  `issue` ADD  `author` VARCHAR( 100 ) NOT NULL AFTER  `excerpt`;
ALTER TABLE  `issue` ADD  `words` INT UNSIGNED NOT NULL AFTER  `author`;
ALTER TABLE  `issue` ADD  `welcome` TEXT NULL DEFAULT NULL AFTER  `excerpt`;
