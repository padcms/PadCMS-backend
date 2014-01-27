INSERT INTO `padcms_new`.`template` (`id`, `title`, `description`, `weight`, `has_right_connector`, `has_left_connector`, `has_top_connector`, `has_bottom_connector`, `engine_version`) VALUES (25, 'diaporama_in_a_long_article', 'Diaporama in a long article', '11', '1', '1', '1', '0', '1');

INSERT INTO `padcms_new`.`field` (`id`, `name`, `description`, `field_type`, `min`, `max`, `max_width`, `max_height`, `weight`, `template`, `engine_version`) VALUES (70, 'mini_article', NULL, '6', '0', '0', '0', '2', '1', '25', '1');

INSERT INTO `padcms_new`.`field` (`id`, `name`, `description`, `field_type`, `min`, `max`, `max_width`, `max_height`, `weight`, `template`, `engine_version`) VALUES (71, 'slide', NULL, '7', '0', '0', '0', '0', '1', '25', '1');

INSERT INTO `padcms_new`.`field` (`id`, `name`, `description`, `field_type`, `min`, `max`, `max_width`, `max_height`, `weight`, `template`, `engine_version`) VALUES (72, 'body', NULL, '1', '0', '0', '0', '0', '0', '25', '1');
