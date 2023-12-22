
/* Check block IDs after importing data */
INSERT INTO `amsd_strings` (`id`, `block`, `key`, `value`, `config`, `pos`) VALUES
  (1, 1, 'Logo', NULL, 'photo', 0),
  (2, 1, 'Logo on Scroll', NULL, 'photo', 1),
  (3, 1, 'Logo in Footer', NULL, 'photo', 2),
  (4, 1, 'Nav Button Icon', NULL, 'font_awesome', 0),
  (5, 2, 'Default Page Banner Photo', NULL, 'focused_img', 1),
  (6, 2, 'Email Address', NULL, NULL, 2),
  (7, 2, 'Phone Number', NULL, NULL, 3),
  (8, 2, 'Address', NULL, 'textarea', 4),
  (9, 2, 'Hours', NULL, 'textarea', 5),
  (10, 2, 'Social Media Links', NULL, 'social_media_links', 6),
  (11, 2, 'Footer Links', '[{"title":"Accessibility Statement","url":""},{"title":"Privacy Policy","url":""}]', 'sorted_list', 99999);



CREATE TABLE IF NOT EXISTS `amsd_alert_bar` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `block` int(11) unsigned NOT NULL DEFAULT 0,
  `active` int(11) unsigned DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `textarea` text DEFAULT NULL,
  `button` text DEFAULT NULL,
  `pos` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci ROW_FORMAT=DYNAMIC;


/* Check block IDs after importing data */
INSERT INTO `amsd_alert_bar` (`id`, `block`, `active`, `icon`, `textarea`, `button`, `pos`) VALUES
  (1, 3, 1, 'fas fa-triangle-exclamation', 'Lorem ipsum dolor sit amet, eam everti tractatos cu, ea vis brute ullamcorper, nominavi probatus posidonium cu his.', '{"text":"Learn More","url":""}', 0);






CREATE TABLE IF NOT EXISTS `amsd_popup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `block` int(11) unsigned NOT NULL DEFAULT 0,
  `active` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `html` text DEFAULT NULL,
  `pos` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci ROW_FORMAT=DYNAMIC;



/* Check block IDs after importing data */
INSERT INTO `amsd_popup` (`id`, `block`, `active`, `title`, `html`, `pos`) VALUES
  (1, 4, 1, 'Test', '<div class="heading">Lightbox <span style="color:#63a34a;">Heading</span></div>\n\n<p>The Rural Energy for America Program provides guaranteed loan financing and grant funding to agricultural producers and rural small businesses to purchase or install renewable energy systems or make energy efficiency improvements.</p>\n\n<p>&nbsp;</p>\n\n<p><a class="cms-btn cms-btn-primary" data-btn-text="Button Text" href="#">Button Text</a></p>\n', 0);






CREATE TABLE IF NOT EXISTS `amsd_cycling_banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `block` int(11) unsigned NOT NULL DEFAULT 0,
  `focused_img` text DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `pos` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


CREATE TABLE IF NOT EXISTS `amsd_standard` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `block` int(11) unsigned NOT NULL DEFAULT 0,
  `focused_img` text DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `sub_title` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `preview_text` text DEFAULT NULL,
  `html` text DEFAULT NULL,
  `pos` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


CREATE TABLE IF NOT EXISTS `amsd_standard_with_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `block` int(11) unsigned NOT NULL DEFAULT 0,
  `category` int(11) unsigned DEFAULT NULL,
  `focused_img` text DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `sub_title` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `preview_text` text DEFAULT NULL,
  `html` text DEFAULT NULL,
  `pos` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
