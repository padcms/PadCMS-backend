ALTER TABLE `term` ADD
 `position`int(4) NOT NULL DEFAULT 0;

--//@UNDO

ALTER TABLE `term` DROP `position`;
