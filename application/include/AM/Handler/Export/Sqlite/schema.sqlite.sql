BEGIN TRANSACTION;

CREATE TABLE `page`
(
       id INTEGER NOT NULL,
       title TEXT NOT NULL,
       horisontal_page_id INTEGER NOT NULL DEFAULT "0",
       template INTEGER NOT NULL DEFAULT "0" ,
       machine_name TEXT,
       color TEXT,
       PRIMARY KEY(id)
);

CREATE TABLE `page_imposition`
(
       id INTEGER NOT NULL,
       page_id INTEGER NOT NULL,
       is_linked_to INTEGER NOT NULL,
       position_type TEXT NOT NULL,
       PRIMARY KEY(id)
);

CREATE TABLE `element`
(
       id INTEGER NOT NULL,
       page_id INTEGER NOT NULL,
       element_type_name TEXT NOT NULL,
       weight INTEGER NOT NULL,
       content_text  BLOB,
       PRIMARY KEY(id)
);

CREATE TABLE `element_data`
(
       id INTEGER NOT NULL,
       element_id INTEGER NOT NULL,
       type TEXT NOT NULL,
       value TEXT NOT NULL,
       position_id  INTEGER,
       PRIMARY KEY(id)
);

CREATE TABLE `element_data_position`
(
       id INTEGER,
       start_x INTEGER,
       start_y INTEGER,
       end_x INTEGER,
       end_y INTEGER,
       PRIMARY KEY(id)
);

CREATE TABLE `menu`
(
       id INTEGER NOT NULL,
       title TEXT,
       firstpage_id INTEGER,
       description TEXT,
       thumb_stripe TEXT,
       thumb_summary TEXT,
       color TEXT,
       PRIMARY KEY(id)
);

CREATE TABLE `page_horisontal`
(
       id INTEGER NOT NULL,
       name TEXT,
       resource TEXT,
       PRIMARY KEY(id)
);

CREATE TABLE `game_crossword`
(
       id INTEGER NOT NULL,
       title TEXT,
       grid_width INTEGER,
       grid_height INTEGER,
       PRIMARY KEY(id)
);

CREATE TABLE `game_crossword_word`
(
       id INTEGER NOT NULL,
       game INTEGER NOT NULL,
       answer TEXT NOT NULL,
       question TEXT NOT NULL,
       length INTEGER DEFAULT NULL,
       direction  TEXT NOT NULL,
       start_from INTEGER NOT NULL,
       PRIMARY KEY(id)
);

COMMIT;
