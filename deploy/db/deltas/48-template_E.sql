INSERT INTO `padcms`.`template` (`id`, `title`, `description`, `weight`, `has_right_connector`, `has_left_connector`, `has_top_connector`, `has_bottom_connector`, `engine_version`) VALUES (26, 'multi_scroll', 'Multi scroll', '12', '1', '1', '1', '1', '1');

INSERT INTO `padcms`.`field` (`id`, `name`, `description`, `field_type`, `min`, `max`, `max_width`, `max_height`, `weight`, `template`, `engine_version`) VALUES (73, 'scrolling_pane', NULL, '5', '0', '0', '0', '0', '1', '26', '1');

INSERT INTO `padcms`.`field` (`id`, `name`, `description`, `field_type`, `min`, `max`, `max_width`, `max_height`, `weight`, `template`, `engine_version`) VALUES (74, 'body', NULL, '1', '0', '0', '0', '0', '0', '26', '1');
