INSERT INTO `template` (`id`, `title`, `description`, `has_right_connector`, `has_left_connector`, `has_top_connector`, `has_bottom_connector`, `engine_version`, `weight`) VALUES
(7, 'cover_page', 'Cover page (P003)', 1, 0, 0, 0, 1, 0),

(1, 'basic_article', 'Basic Article (A001)', 1, 1, 0, 0, 1, 1),
(3, 'simple_page', 'Simple page (P001)', 1, 1, 1, 1, 1, 1),

(2, 'fixed_illustration_article', 'Article with fixed illustration  (A002)', 1, 1, 0, 0, 1, 2),
(10, 'fixed_illustration_article_touchable', 'Touchable article with fixed illustration (A006)', 1, 1, 1, 1, 1, 2),
(9, 'article_with_overlay', 'Article with Overlay (A005)', 1, 1, 1, 1, 1, 2),

(5, 'sliders_based_mini_articles_horizontal', 'Sliders based mini-articles (horizontal)(A003)', 1, 1, 1, 1, 1, 3),
(8, 'sliders_based_mini_articles_vertical', 'Sliders based mini-articles (vertical) (A004)', 1, 1, 1, 1, 1, 3),
(13, 'sliders_based_mini_articles_top', 'Sliders based mini-articles (top) (A008)', 1, 1, 1, 1, 1, 3),

(4, 'scrolling_page', 'Scrolling page (vertical) (P002)', 1, 1, 1, 1, 1, 4),
(20, 'scrolling_page_horizontal', 'Scrolling page (horizontal) (P005)', 1, 1, 1, 1, 1, 4),

(6, 'slideshow', 'Slideshow  (S001)', 1, 1, 1, 1, 1, 5),
(12, 'slideshow_page', 'Slideshow page (S002)', 1, 1, 1, 1, 1, 5),

(11, 'interactives_bullets', 'Interactives bullets (A007)', 1, 1, 1, 1, 1, 6),
(16, 'flash_bullet_interactive', 'Interactive bullet with flash (A010)', 1, 1, 1, 1, 1, 6),
(15, 'drag_and_drop', 'Drag and drop with mini-articles (A009)', 1, 1, 1, 1, 1, 6),
(17, 'gallery_flash_bullet_interactive', 'Gallery with flash bullet interactive (A011)', 1, 1, 1, 1, 1, 6),

(14, 'html_page', 'HTML page (P004)', 1, 1, 1, 1, 1, 7),
(18, 'html5', 'HTML5 (A012)', 1, 1, 1, 1, 1, 7),
(19, 'games', 'Games (A013)', 1, 1, 0, 0, 1, 7),

(21, '3d', '3D (A014)', 7, 1, 1, 0, 0, 1)
;

INSERT INTO `field_type` (`id`, `title`) VALUES
(1, 'body'),
(2, 'gallery'),
(3, 'video'),
(4, 'background'),
(5, 'scrolling_pane'),
(6, 'mini_article'),
(7, 'slide'),
(8, 'overlay'),
(9, 'sound'),
(10, 'html'),
(11, 'advert'),
(12, 'drag_and_drop'),
(13, 'popup'),
(14, 'html5'),
(15, 'games'),
(16, '3d')
;

INSERT INTO `field` (`id`, `name`, `description`, `field_type`, `min`, `max`, `max_width`, `max_height`, `weight`, `template`, `engine_version`) VALUES
(12, 'video', NULL, 3, 0, 0, 0, 0, 3, 1, 1),
(3, 'gallery', NULL, 2, 0, 0, 0, 0, 2, 1, 1),
(9, 'body', NULL, 1, 0, 0, 0, 0, 1, 1, 1),

(11, 'gallery', NULL, 2, 0, 0, 0, 0, 2, 2, 1),
(6, 'background', NULL, 4, 0, 0, 0, 0, 4, 2, 1),
(10, 'body', NULL, 1, 0, 0, 0, 0, 1, 2, 1),
(13, 'video', NULL, 3, 0, 0, 0, 0, 3, 2, 1),

(1, 'body', NULL, 1, 0, 0, 0, 0, 1, 3, 1),
(5, 'video', NULL, 3, 0, 0, 0, 0, 3, 3, 1),

(7, 'background', NULL, 4, 0, 0, 0, 0, 4, 4, 1),
(8, 'scrolling_pane', NULL, 5, 0, 0, 0, 0, 5, 4, 1),
(14, 'video', NULL, 3, 0, 0, 0, 0, 3, 4, 1),
(25, 'gallery', NULL, 2, 0, 0, 0, 0, 5, 4, 1),

(16, 'mini_article', NULL, 6, 0, 0, 0, 0, 6, 5, 1),
(31, 'background', NULL, 4, 0, 0, 0, 0, 5, 5, 1),

(15, 'background', NULL, 4, 0, 0, 0, 0, 4, 6, 1),
(17, 'slide', NULL, 7, 0, 0, 0, 0, 7, 6, 1),

(18, 'body', NULL, 1, 0, 0, 0, 0, 1, 7, 1),
(19, 'video', NULL, 3, 0, 0, 0, 0, 4, 7, 1),
(26, 'background', NULL, 4, 0, 0, 0, 0, 2, 7, 1),
(42, 'advert', NULL, 11, 0, 0, 0, 0, 3, 7, 1),

(20, 'mini_article', NULL, 6, 0, 0, 0, 0, 6, 8, 1),
(32, 'background', NULL, 4, 0, 0, 0, 0, 5, 8, 1),

(21, 'background', NULL, 4, 0, 0, 0, 0, 0, 9, 1),
(22, 'video', NULL, 3, 0, 0, 0, 0, 0, 9, 1),
(23, 'overlay', NULL, 8, 0, 0, 0, 0, 0, 9, 1),

-- Touchable article with fixed illustration (A006)
(27, 'body', NULL, 1, 0, 0, 0, 0, 1, 10, 1),
(28, 'background', NULL, 4, 0, 0, 0, 0, 2, 10, 1),
(60, 'video', NULL, 3, 0, 0, 0, 0, 3, 10, 1),

(29, 'background', NULL, 4, 0, 0, 0, 0, 0, 11, 1),
(30, 'mini_article', NULL, 6, 0, 0, 0, 0, 0, 11, 1),

(33, 'background', NULL, 4, 0, 0, 0, 0, 2, 12, 1),
(34, 'body', NULL, 1, 0, 0, 0, 0, 2, 12, 1),
(35, 'video', NULL, 3, 0, 0, 0, 0, 3, 12, 1),
(36, 'sound', NULL, 9, 0, 0, 0, 0, 4, 12, 1),

(37, 'mini_article', NULL, 6, 0, 0, 0, 0, 0, 13, 1),

(41, 'body', NULL, 1, 0, 0, 0, 0, 1, 14, 1),
(40, 'html', NULL, 10, 0, 0, 0, 0, 2, 14, 1),

(38, 'background',    NULL, 4, 0, 0, 0, 0, 1, 15, 1),
(39, 'drag_and_drop', NULL, 12, 0, 0, 0, 0, 2, 15, 1),

(43, 'body', NULL, 1, 0, 0, 0, 0, 0, 16, 1),
(44, 'background', NULL, 4, 0, 0, 0, 0, 0, 16, 1),
(45, 'mini_article', NULL, 6, 0, 0, 0, 0, 0, 16, 1),

(46, 'background', NULL, 4, 0, 0, 0, 0, 1, 17, 1),
(47, 'scrolling_pane', NULL, 5, 0, 0, 0, 0, 2, 17, 1),
(48, 'gallery', NULL, 2, 0, 0, 0, 0, 3, 17, 1),
(49, 'popup', NULL, 13, 0, 0, 0, 0, 4, 17, 1),
(50, 'video', NULL, 3, 0, 0, 0, 0, 5, 17, 1),

(51, 'html5', NULL, 14, 0, 0, 0, 0, 1, 18, 1),

-- Games template
(52, 'background', NULL, 4, 0, 0, 0, 0, 1, 19, 1),
(53, 'games', NULL, 15, 0, 0, 0, 0, 1, 19, 1),

-- Scrolling page (horizontal) (P005)
(54, 'background', NULL, 4, 0, 0, 0, 0, 4, 20, 1),
(55, 'scrolling_pane', NULL, 5, 0, 0, 0, 0, 5, 20, 1),
(56, 'video', NULL, 3, 0, 0, 0, 0, 3, 20, 1),
(57, 'gallery', NULL, 2, 0, 0, 0, 0, 5, 20, 1),

(58, 'background', NULL, 4, 0, 0, 0, 0, 1, 21, 1),
(59, '3d', NULL, 16, 0, 0, 0, 0, 2, 21, 1)
;