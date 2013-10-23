ALTER TABLE  `page` ADD  `root_page` BOOLEAN NULL DEFAULT NULL AFTER  `pdf_page`;
UPDATE page SET root_page = IF (template = 7, 1, 0);
