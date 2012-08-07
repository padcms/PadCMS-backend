ALTER TABLE `page` ADD
 `color` char(6) DEFAULT NULL;

--//@UNDO

ALTER TABLE `page` DROP `color`;
