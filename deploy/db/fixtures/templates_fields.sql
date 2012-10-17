INSERT INTO `template` (`id`, `title`, `description`, `has_right_connector`, `has_left_connector`, `has_top_connector`, `has_bottom_connector`, `engine_version`, `weight`) VALUES
(7, 'cover_page', 'Cover page (P003)', 1, 0, 0, 0, 1, 0),

(1, 'basic_article', 'Basic Article (A001)', 1, 1, 0, 0, 1, 1),
(3, 'simple_page', 'Simple page (P001)', 1, 1, 1, 1, 1, 1),

(10, 'fixed_illustration_article_touchable', 'Touchable article with fixed illustration (A006)', 1, 1, 1, 1, 1, 2),

(5, 'sliders_based_mini_articles_horizontal', 'Sliders based mini-articles (horizontal)(A003)', 1, 1, 1, 1, 1, 3),
(8, 'sliders_based_mini_articles_vertical', 'Sliders based mini-articles (vertical) (A004)', 1, 1, 1, 1, 1, 3),
(13, 'sliders_based_mini_articles_top', 'Sliders based mini-articles (top) (A008)', 1, 1, 1, 1, 1, 3),

(4, 'scrolling_page', 'Scrolling page (vertical) (P002)', 1, 1, 1, 1, 1, 4),
(20, 'scrolling_page_horizontal', 'Scrolling page (horizontal) (P005)', 1, 1, 1, 1, 1, 4),

(6, 'slideshow', 'Slideshow  (S001)', 1, 1, 1, 1, 1, 5),

(11, 'interactives_bullets', 'Interactives bullets (A007)', 1, 1, 1, 1, 1, 6),
(16, 'flash_bullet_interactive', 'Interactive bullet with flash (A010)', 1, 1, 1, 1, 1, 6),
(15, 'drag_and_drop', 'Drag and drop with mini-articles (A009)', 1, 1, 1, 1, 2, 6),
(17, 'gallery_flash_bullet_interactive', 'Gallery with flash bullet interactive (A011)', 1, 1, 1, 1, 1, 6),

(14, 'html_page', 'HTML page (P004)', 1, 1, 1, 1, 1, 7),
(18, 'html5', 'HTML5 (A012)', 1, 1, 1, 1, 1, 7),
(19, 'games', 'Games (A013)', 1, 1, 0, 0, 2, 7),

(21, '3d', '3D (A014)', 1, 1, 1, 1, 1, 8)
;

INSERT INTO `field_type` (`id`, `title`) VALUES
(1, 'body'),
(2, 'gallery'),
(3, 'video'),
(4, 'background'),
(5, 'scrolling_pane'),
(6, 'mini_article'),
(7, 'slide'),
(9, 'sound'),
(10, 'html'),
(11, 'advert'),
(12, 'drag_and_drop'),
(13, 'popup'),
(14, 'html5'),
(15, 'games'),
(16, '3d')
;

INSERT INTO `field` (`name`, `description`, `field_type`, `min`, `max`, `max_width`, `max_height`, `weight`, `template`, `engine_version`) VALUES
-- Basic Article (A001)
('video', NULL, 3, 0, 0, 0, 0, 3, 1, 1),
('gallery', NULL, 2, 0, 0, 0, 0, 2, 1, 1),
('body', NULL, 1, 0, 0, 0, 0, 1, 1, 1),

-- Simple page (P001)
('body', NULL, 1, 0, 0, 0, 0, 1, 3, 1),
('video', NULL, 3, 0, 0, 0, 0, 3, 3, 1),

-- Scrolling page (vertical) (P002)
('background', NULL, 4, 0, 0, 0, 0, 4, 4, 1),
('scrolling_pane', NULL, 5, 0, 0, 0, 0, 5, 4, 1),
('video', NULL, 3, 0, 0, 0, 0, 3, 4, 1),
('gallery', NULL, 2, 0, 0, 0, 0, 5, 4, 1),

-- Sliders based mini-articles (horizontal)(A003)
('mini_article', NULL, 6, 0, 0, 0, 0, 6, 5, 1),
('background', NULL, 4, 0, 0, 0, 0, 5, 5, 1),

-- Slideshow  (S001)
('background', NULL, 4, 0, 0, 0, 0, 4, 6, 1),
('slide', NULL, 7, 0, 0, 0, 0, 7, 6, 1),

-- Cover page (P003)
('body', NULL, 1, 0, 0, 0, 0, 1, 7, 1),
('video', NULL, 3, 0, 0, 0, 0, 4, 7, 1),
('background', NULL, 4, 0, 0, 0, 0, 2, 7, 1),
('advert', NULL, 11, 0, 0, 0, 0, 3, 7, 1),

-- Sliders based mini-articles (vertical) (A004)
('mini_article', NULL, 6, 0, 0, 0, 0, 6, 8, 1),
('background', NULL, 4, 0, 0, 0, 0, 5, 8, 1),

-- Touchable article with fixed illustration (A006)
('body', NULL, 1, 0, 0, 0, 0, 1, 10, 1),
('background', NULL, 4, 0, 0, 0, 0, 2, 10, 1),
('gallery', NULL, 2, 0, 0, 0, 0, 3, 10, 1),
('popup', NULL, 13, 0, 0, 0, 0, 5, 10, 1),
('video', NULL, 3, 0, 0, 0, 0, 4, 10, 1),

-- Interactives bullets (A007)
('background', NULL, 4, 0, 0, 0, 0, 0, 11, 1),
('mini_article', NULL, 6, 0, 0, 0, 0, 0, 11, 1),

-- Sliders based mini-articles (top) (A008)
('mini_article', NULL, 6, 0, 0, 0, 0, 0, 13, 1),

-- HTML page (P004)
('body', NULL, 1, 0, 0, 0, 0, 1, 14, 1),
('html', NULL, 10, 0, 0, 0, 0, 2, 14, 1),

-- Drag and drop with mini-articles (A009)
('background',    NULL, 4, 0, 0, 0, 0, 1, 15, 1),
('drag_and_drop', NULL, 12, 0, 0, 0, 0, 2, 15, 1),

-- Interactive bullet with flash (A010)
('body', NULL, 1, 0, 0, 0, 0, 0, 16, 1),
('background', NULL, 4, 0, 0, 0, 0, 0, 16, 1),
('mini_article', NULL, 6, 0, 0, 0, 0, 0, 16, 1),

-- Gallery with flash bullet interactive (A011)
('background', NULL, 4, 0, 0, 0, 0, 1, 17, 1),
('scrolling_pane', NULL, 5, 0, 0, 0, 0, 2, 17, 1),
('gallery', NULL, 2, 0, 0, 0, 0, 3, 17, 1),
('popup', NULL, 13, 0, 0, 0, 0, 4, 17, 1),
('video', NULL, 3, 0, 0, 0, 0, 5, 17, 1),

-- HTML5 (A012)
('html5', NULL, 14, 0, 0, 0, 0, 2, 18, 1),
('body', NULL, 1, 0, 0, 0, 0, 1, 18, 1),

-- Games template
('background', NULL, 4, 0, 0, 0, 0, 1, 19, 1),
('games', NULL, 15, 0, 0, 0, 0, 1, 19, 1),

-- Scrolling page (horizontal) (P005)
('background', NULL, 4, 0, 0, 0, 0, 4, 20, 1),
('scrolling_pane', NULL, 5, 0, 0, 0, 0, 5, 20, 1),
('video', NULL, 3, 0, 0, 0, 0, 3, 20, 1),
('gallery', NULL, 2, 0, 0, 0, 0, 5, 20, 1),

-- 3D (A014)
('background', NULL, 4, 0, 0, 0, 0, 1, 21, 1),
('3d', NULL, 16, 0, 0, 0, 0, 2, 21, 1)
;