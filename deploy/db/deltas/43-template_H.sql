INSERT INTO `padcms`.`template` (`id`, `title`, `description`, `weight`, `has_right_connector`, `has_left_connector`, `has_top_connector`, `has_bottom_connector`, `engine_version`) VALUES (22, 'animated_gifs', 'Animated gifs', '8', '1', '1', '1', '0', '1');

INSERT INTO `padcms`.`field` (`id`, `name`, `description`, `field_type`, `min`, `max`, `max_width`, `max_height`, `weight`, `template`, `engine_version`) VALUES (64, 'gallery', NULL, '2', '0', '0', '0', '0', '1', '22', '1');

INSERT INTO `padcms`.`field` (`id`, `name`, `description`, `field_type`, `min`, `max`, `max_width`, `max_height`, `weight`, `template`, `engine_version`) VALUES (65, 'body', NULL, '1', '0', '0', '0', '0', '0', '22', '1');
