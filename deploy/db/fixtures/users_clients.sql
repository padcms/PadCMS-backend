INSERT INTO `client` (`id`, `title`, `deleted`) VALUES
(1, 'Admin', 'no'),
(2, 'User', 'no');

INSERT INTO `user` (`id`, `first_name`, `last_name`, `login`, `password`, `email`, `is_admin`, `client`, `deleted`) VALUES
(1, '', '', 'admin', MD5('password'), 'admin@gmail.com', 1, 1, 'no'),
(2, '', '', 'user', MD5('password'), 'user@gmail.com', 0, 2, 'no');