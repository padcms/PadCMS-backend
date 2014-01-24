INSERT INTO `padcms`.`template` (`id`, `title`, `description`, `weight`, `has_right_connector`, `has_left_connector`, `has_top_connector`, `has_bottom_connector`, `engine_version`) VALUES (23, 'multi_popups', 'Multi-popups', '9', '1', '1', '1', '0', '1');

INSERT INTO `padcms`.`field` (`id`, `name`, `description`, `field_type`, `min`, `max`, `max_width`, `max_height`, `weight`, `template`, `engine_version`) VALUES (66, 'popup', NULL, '13', '0', '0', '0', '0', '1', '23', '1');

INSERT INTO `padcms`.`field` (`id`, `name`, `description`, `field_type`, `min`, `max`, `max_width`, `max_height`, `weight`, `template`, `engine_version`) VALUES (67, 'body', NULL, '1', '0', '0', '0', '0', '0', '23', '1');
