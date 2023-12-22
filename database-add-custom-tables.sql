
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
