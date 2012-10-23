ALTER TABLE `game` CHANGE `revision` `page` INT( 12 ) NOT NULL;

ALTER TABLE `game_crossword_word` DROP `start_from`;
ALTER TABLE `game_crossword_word` ADD `start_x` smallint(5) NOT NULL;
ALTER TABLE `game_crossword_word` ADD `start_y` smallint(5) NOT NULL;

INSERT INTO `field` (`name`, `description`, `field_type`, `min`, `max`, `max_width`, `max_height`, `weight`, `template`, `engine_version`) VALUES
('background', NULL, 4, 0, 0, 0, 0, 1, 18, 1);

--//@UNDO

ALTER TABLE `game` CHANGE `page` `revision` INT( 12 ) NOT NULL;
ALTER TABLE `game_crossword_word` ADD `start_from` smallint(5) NOT NULL;
ALTER TABLE `game_crossword_word` DROP `start_x`;
ALTER TABLE `game_crossword_word` DROP `start_y`;

DELETE FROM `field` WHERE `field_type` = 1 AND `template` = 18;