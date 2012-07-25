ALTER TABLE `application` ADD
 `preview` int(4) NOT NULL DEFAULT '0';

--//@UNDO

ALTER TABLE `application` DROP `preview`;
