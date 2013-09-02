ALTER TABLE  `application` ADD  `message_for_readers` TEXT NULL DEFAULT NULL;
ALTER TABLE  `application` ADD  `share_message` TEXT NULL DEFAULT NULL;
ALTER TABLE  `issue` ADD  `category` TEXT NULL DEFAULT NULL;
ALTER TABLE `issue` DROP `subtitle`;