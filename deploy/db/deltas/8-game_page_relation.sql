ALTER TABLE `game` CHANGE `revision` `page` INT( 12 ) NOT NULL;

ALTER TABLE `game_crossword_word` DROP `start_from`;
ALTER TABLE `game_crossword_word` ADD `start_x` smallint(5) NOT NULL;
ALTER TABLE `game_crossword_word` ADD `start_y` smallint(5) NOT NULL;

--//@UNDO

ALTER TABLE `game` CHANGE `page` `revision` INT( 12 ) NOT NULL;
ALTER TABLE `game_crossword_word` ADD `start_from` smallint(5) NOT NULL;
ALTER TABLE `game_crossword_word` DROP `start_x`;
ALTER TABLE `game_crossword_word` DROP `start_y`;