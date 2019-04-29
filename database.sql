--
-- Table structure for table `sd_category`
--

CREATE TABLE `sd_category` (
  `id` int(11) NOT NULL,
  `label` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `identifier` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sd_category`
--

INSERT INTO `sd_category` (`id`, `label`, `identifier`, `status`) VALUES
(1, 'Todo', 'todo', 1),
(2, 'Notification', 'notification', 1),
(3, 'Directory submission', 'dir-submission', 1),
(4, 'Newsletter', 'newsletter', 1),
(5, 'Social Media Submission', 'sm-submission', 1),
(6, 'General', 'general', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sd_diary_comments`
--

CREATE TABLE `sd_diary_comments` (
  `id` bigint(20) NOT NULL,
  `diary_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comments` text COLLATE utf8_unicode_ci NOT NULL,
  `updated_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_projects`
--

CREATE TABLE `sd_projects` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_seo_diary`
--

CREATE TABLE `sd_seo_diary` (
  `id` bigint(20) NOT NULL,
  `project_id` int(11) NOT NULL,
  `created_user_id` int(11) NOT NULL,
  `assigned_user_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `creation_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('new','closed','cancelled','inprogress','blocked','feedback') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'new',
  `email_notification` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sd_settings`
--

CREATE TABLE `sd_settings` (
  `id` int(11) NOT NULL,
  `set_label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `set_name` varchar(64) CHARACTER SET latin1 NOT NULL,
  `set_val` text COLLATE utf8_unicode_ci NOT NULL,
  `set_type` enum('small','bool','medium','large','text') CHARACTER SET latin1 DEFAULT 'small',
  `display` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sd_settings`
--

INSERT INTO `sd_settings` (`id`, `set_label`, `set_name`, `set_val`, `set_type`, `display`) VALUES
(1, 'Allow user to access project manager', 'SD_ALLOW_USER_PROJECTS', '0', 'bool', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sd_category`
--
ALTER TABLE `sd_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identifier` (`identifier`);

--
-- Indexes for table `sd_diary_comments`
--
ALTER TABLE `sd_diary_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disary_id_update` (`diary_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sd_projects`
--
ALTER TABLE `sd_projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `website_id` (`website_id`);

--
-- Indexes for table `sd_seo_diary`
--
ALTER TABLE `sd_seo_diary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `diary_project_delete` (`project_id`),
  ADD KEY `assigned_user_id` (`assigned_user_id`),
  ADD KEY `created_user_id` (`created_user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `sd_settings`
--
ALTER TABLE `sd_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `set_name` (`set_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sd_category`
--
ALTER TABLE `sd_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `sd_diary_comments`
--
ALTER TABLE `sd_diary_comments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sd_projects`
--
ALTER TABLE `sd_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sd_seo_diary`
--
ALTER TABLE `sd_seo_diary`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sd_settings`
--
ALTER TABLE `sd_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `sd_diary_comments`
--
ALTER TABLE `sd_diary_comments`
  ADD CONSTRAINT `disary_id_update` FOREIGN KEY (`diary_id`) REFERENCES `sd_seo_diary` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `sd_seo_diary`
--
ALTER TABLE `sd_seo_diary`
  ADD CONSTRAINT `category_id` FOREIGN KEY (`category_id`) REFERENCES `sd_category` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `diary_project_delete` FOREIGN KEY (`project_id`) REFERENCES `sd_projects` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;


ALTER TABLE `sd_projects` ADD CONSTRAINT `sd_project_website_id_delete` FOREIGN KEY ( `website_id` ) REFERENCES `websites` 
(`id`) ON DELETE CASCADE ON UPDATE NO ACTION ;

INSERT INTO `texts` ( `category`, `label`, `content`) VALUES
('seodiary', 'Add your comment here', 'Add your comment here'),
('seodiary', 'Diary already exist', 'Diary already exist'),
('seodiary', 'Edit Diary', 'Edit Diary'),
('seodiary', 'New task is assigned to you in SEO Diary', 'New task is assigned to you in SEO Diary'),
('seodiary', 'Assigned to You', 'Assigned to You'),
('seodiary', 'SD_ALLOW_USER_PROJECTS', 'Allow user to access project manager'),
('seodiary', 'New', 'New'),
('seodiary', 'Closed', 'Closed'),
('seodiary', 'Cancelled', 'Cancelled'),
('seodiary', 'Inprogress', 'Inprogress'),
('seodiary', 'Blocked', 'Blocked'),
('seodiary', 'Feedback', 'Feedback'),
('seodiary', 'Due Date', 'Due Date'),
('seodiary', 'Ascending', 'Ascending'),
('seodiary', 'Descending', 'Descending'),
('seodiary', 'Diary Manager', 'Diary Manager'),
('seodiary', 'Diary Comments', 'Diary Comments'),
('seodiary', 'My Tasks', 'My Tasks'),
('seodiary', 'Projects Manager', 'Projects Manager'),
('seodiary', 'Assignee', 'Assignee'),
('seodiary', 'Sorting', 'Sorting'),
('seodiary', 'New Diary', 'New Diary'),
('seodiary', 'Add Comment', 'Add Comment');
