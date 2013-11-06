ALTER TABLE  `issue` ADD  `subscribe_title` VARCHAR( 255 ) NULL DEFAULT NULL AFTER  `itunes_id` ,
ADD  `subscribe_button` VARCHAR( 255 ) NULL DEFAULT NULL AFTER  `subscribe_title`;
