
ALTER TABLE `amsd_cycling_banner` DROP COLUMN IF EXISTS `img`;
ALTER TABLE `amsd_cycling_banner` DROP COLUMN IF EXISTS `url`;
ALTER TABLE `amsd_cycling_banner` ADD `focused_img` text DEFAULT NULL;
ALTER TABLE `amsd_cycling_banner` ADD `button_one` text DEFAULT NULL;
ALTER TABLE `amsd_cycling_banner` ADD `button_two` text DEFAULT NULL;
ALTER TABLE `amsd_cycling_banner` ADD `button_one_icon` text DEFAULT NULL;
ALTER TABLE `amsd_cycling_banner` ADD `button_two_icon` text DEFAULT NULL;

ALTER TABLE `amsd_standard` DROP COLUMN IF EXISTS `img`;
ALTER TABLE `amsd_standard` ADD `focused_img` text DEFAULT NULL;

ALTER TABLE `amsd_standard_with_categories` DROP COLUMN IF EXISTS `img`;
ALTER TABLE `amsd_standard_with_categories` ADD `focused_img` text DEFAULT NULL;

UPDATE `core_developer_settings` SET `value`='/assets/css/cms.css' WHERE `setting`='contentsCSS';
UPDATE `core_developer_settings` SET `value`='1' WHERE `setting`='stylesMenuV2';
UPDATE `core_developer_settings` SET `value`='[{
    \"title\": \"Heading 1\",
    \"class\": \"heading\",
    \"element\": \"div\"  
},{
    \"title\": \"Heading 2\",
    \"class\": \"heading heading-2\",
    \"element\": \"div\"  
},{
    \"title\": \"Heading 3\",
    \"class\": \"heading heading-3\",
    \"element\": \"div\"  
},{
    \"title\": \"Divider\",
    \"class\": \"text-divider\",
    \"element\": \"div\"  
},{
    \"title\": \"Call Out Text\",
    \"class\": \"call-out-text\",
    \"element\": \"div\"
}]' WHERE `setting`='ckeditorStyles';


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


CREATE TABLE IF NOT EXISTS `amsd_popup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `block` int(11) unsigned NOT NULL DEFAULT 0,
  `active` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `html` text DEFAULT NULL,
  `pos` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci ROW_FORMAT=DYNAMIC;







/* --- Below is just formatting for future use ---  */





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
