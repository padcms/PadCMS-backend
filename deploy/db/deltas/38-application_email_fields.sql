ALTER TABLE  `application` ADD  `contact_email_subject` VARCHAR( 255 ) NULL DEFAULT NULL AFTER  `application_email` ,
ADD  `contact_email_text` TEXT NULL DEFAULT NULL AFTER  `contact_email_subject`;

ALTER TABLE  `application` ADD  `welcome_part2` TEXT NULL DEFAULT NULL AFTER  `welcome`;

ALTER TABLE  `padcms`.`issue` DROP INDEX  `product_id` ,
ADD INDEX  `product_id` (  `product_id` );
