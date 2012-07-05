INSERT INTO `field` (`name`, `description`, `field_type`, `min`, `max`, `max_width`, `max_height`, `weight`, `template`, `engine_version`) VALUES
('popup', NULL, 13, 0, 0, 0, 0, 5, 10, 1);

--//@UNDO

DELETE FROM `field` WHERE `field_type` = 13 AND `template` = 10;