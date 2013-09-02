INSERT INTO  `task_type` (`id`, `class`) VALUES (NULL ,  'AM_Task_Worker_Notification_Sender_Boxcar');
UPDATE `task_type` SET `class` = 'AM_Task_Worker_Notification_Planner_Apple' WHERE `id` = 1;
UPDATE `task_type` SET `class` = 'AM_Task_Worker_Notification_Feedback_Apple' WHERE `id` = 2;
UPDATE `task_type` SET `class` = 'AM_Task_Worker_Notification_Sender_Apple' WHERE `id` = 3;
