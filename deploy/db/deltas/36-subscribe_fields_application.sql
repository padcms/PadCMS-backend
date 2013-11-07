ALTER TABLE  `application` ADD  `subscribe_title` VARCHAR( 255 ) NULL DEFAULT NULL AFTER  `application_email` ,
ADD  `subscribe_button` VARCHAR( 255 ) NULL DEFAULT NULL AFTER  `subscribe_title`;
