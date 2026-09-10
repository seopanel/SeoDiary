--
-- version 1.1.0 changes
--

--
-- version 1.3.0 changes: due-date reminder emails
--

ALTER TABLE `sd_seo_diary` ADD COLUMN `last_reminder_date` date DEFAULT NULL;

INSERT IGNORE INTO `sd_settings` (`id`, `set_label`, `set_name`, `set_val`, `set_type`, `display`) VALUES
(2, 'Send due-date reminder emails', 'SD_ENABLE_DUE_REMINDERS', '1', 'bool', 1);

INSERT IGNORE INTO `texts` ( `category`, `label`, `content`) VALUES
('seodiary', 'SD_ENABLE_DUE_REMINDERS', 'Send due-date reminder emails'),
('seodiary', 'Task Overdue', 'Task Overdue'),
('seodiary', 'Task Due Today', 'Task Due Today'),
('seodiary', 'Task Due Tomorrow', 'Task Due Tomorrow'),
('seodiary', 'Project', 'Project');

--
-- version 1.5.0 changes: rename "About Us" menu link to "Features & Support"
--

INSERT IGNORE INTO `texts` ( `category`, `label`, `content`) VALUES
('seodiary', 'Features & Support', 'Features & Support');

