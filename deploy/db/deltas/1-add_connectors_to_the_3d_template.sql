UPDATE `template` SET
 `has_right_connector` = '1',
 `has_left_connector` = '1',
 `has_bottom_connector` = '1',
 `has_top_connector` = '1',
 `engine_version` = '1'
 WHERE `title` = '3d';

--//@UNDO

UPDATE `template` SET
 `has_right_connector`  = '1',
 `has_left_connector`   = '1',
 `has_bottom_connector` = '0',
 `has_top_connector` = '0'
 WHERE `title` = '3d';
