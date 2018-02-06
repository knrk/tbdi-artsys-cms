-- ArtSys 1.6.0, 2016-05-08
-- ----------------------------------------------------------------------------
-- - User data cols added
ALTER TABLE `user_data` 
ADD `born_day` INT(2) NULL AFTER `username`, 
ADD `born_month` INT(2) NULL AFTER `born_day`, 
ADD `born_year` INT(4) NULL AFTER `born_month`,
ADD `degree` VARCHAR(20) COLLATE utf8_czech_ci NULL AFTER `email`,
ADD `pass_changed_date` timestamp NULL DEFAULT NULL AFTER `verif_date`,
ADD `forgotten_pass_hash` varchar(60) COLLATE utf8_czech_ci NOT NULL AFTER `pass_changed_date`,
ADD `forgotten_pass_IP` varchar(50) COLLATE utf8_czech_ci NOT NULL AFTER `forgotten_pass_hash`,
ADD `forgotten_pass_date` datetime NOT NULL AFTER `forgotten_pass_IP`;

-- Added active col for users
ALTER TABLE `user` ADD `active` tinyint(1) NOT NULL DEFAULT '1' NULL AFTER `id_currency`;

-- Added house number for address
ALTER TABLE `address` ADD `housenum` VARCHAR(10) NULL AFTER `street`;


-- ArtSys 1.5.7, 2016-04-19
-- ----------------------------------------------------------------------------
-- Creation of new table: mail_dump
CREATE TABLE IF NOT EXISTS `mail_dump` (
`id` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `from_address` varchar(100) DEFAULT NULL,
  `from_name` varchar(100) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `body` text,
  `alt_body` text,
  `message_id` varchar(50) DEFAULT NULL,
  `message_date` datetime DEFAULT NULL,
  `to_address` varchar(100) DEFAULT NULL,
  `cc_addresses` text,
  `bcc_addresses` text,
  `reply_to_addresses` text,
  `attachments` blob
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `mail_dump` ADD PRIMARY KEY (`id`);
ALTER TABLE `mail_dump` MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT
ALTER TABLE `mail_dump` ADD `mail_type` VARCHAR(50) NULL AFTER `id_user`, ADD INDEX (`mail_type`) ;
ALTER TABLE `mail_dump` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;


-- ArtSys 1.4, 2016-04-02
-- ----------------------------------------------------------------------------
-- - Address_type name is now unique column

UPDATE `address_type` SET `name` = 'residental', `modified_date` = NULL WHERE `address_type`.`id` = 1; 
UPDATE `address_type` SET `name` = 'contact', `modified_date` = NULL WHERE `address_type`.`id` = 2; 
UPDATE `address_type` SET `name` = 'delivery', `modified_date` = NULL WHERE `address_type`.`id` = 3;
INSERT INTO `address_type` (`name`, `description`, `created_by`, `modified_by`, `created_date`, `modified_date`) VALUES ('invoicing', NULL, '', NULL, CURRENT_TIMESTAMP, NULL);
ALTER TABLE `address_type` ADD UNIQUE(`name`);


-- ArtSys 1.32, 2016-03-18
-- ----------------------------------------------------------------------------
-- - Minor update
-- - Added email template model
-- - Module types now have separate directive for adding and updating

CREATE TABLE IF NOT EXISTS `email_template` (
`id` int(10) unsigned NOT NULL,
  `guid` varchar(60) NOT NULL,
  `name` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `body` text NOT NULL,
  `from_name` varchar(60) NOT NULL,
  `from_email` varchar(60) NOT NULL,
  `reply_to_name` varchar(60) NOT NULL,
  `reply_to_email` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `email_template` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `guid` (`guid`);
ALTER TABLE `email_template` MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `email_template` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;

-- Removed write_allowed col from user_group_x_module_type
TABLE `user_group_x_module_type` DROP `write_allowed`;
-- Added add_allowed
ALTER TABLE `user_group_x_module_type` ADD `add_allowed` BOOLEAN NOT NULL DEFAULT TRUE AFTER `read_allowed`;
-- Added update_allowed
ALTER TABLE `user_group_x_module_type` ADD `update_allowed` BOOLEAN NOT NULL DEFAULT TRUE AFTER `add_allowed`;


-- ArtSys 1.31, 2016-03-16
-- ----------------------------------------------------------------------------
-- - Minor update

-- - Added created_by, modified_by, created_date, modified_date to system models
ALTER TABLE `address` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `address_type` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `article` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `article_category` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `attribute` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `attribute_value` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `country` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `currency` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `label` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `meta` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `module` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `module_type` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `node` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `node_type` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `register_value` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `resource` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `rights` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `user_group` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `user_data` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `user_user_group` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `user_group_module_type` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `vat` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;

-- Rename M:N tables
RENAME TABLE `user_user_group` TO `user_x_user_group`;
RENAME TABLE `user_group_module_type` TO `user_group_x_module_type`;



-- ArtSys 1.3, 2016-03-16
-- ----------------------------------------------------------------------------
-- - Major update

-- user_data added id column
TRUNCATE TABLE `user_data`;
ALTER TABLE `user_data` DROP COLUMN `id`;
-- ALTER TABLE `user_data` DROP COLUMN `id_user`;
ALTER TABLE `user_data` ADD `id_user` INT UNSIGNED NOT NULL UNIQUE FIRST;
ALTER TABLE `user_data` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

-- Added created_by, modified_by, created_date and modified_date to tables
ALTER TABLE `user` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ;
ALTER TABLE `login` ADD `created_by` INT UNSIGNED NOT NULL , ADD `modified_by` INT UNSIGNED NULL , ADD `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ADD `modified_date` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ;

-- Label for login title
INSERT INTO `label` (`id`, `key`, `group`, `cs`) VALUES (NULL, 'login_title', 'general', 'Přihlášení');

-- Added user_group table
CREATE TABLE IF NOT EXISTS `user_group` (
`id` int(10) unsigned NOT NULL,
  `id_rights` int(11) NOT NULL DEFAULT '1',
  `name` varchar(60) NOT NULL,
  `description` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `user_group` ADD PRIMARY KEY (`id`), ADD KEY `name` (`name`);
ALTER TABLE `user_group` MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

-- Added user_user_group table
CREATE TABLE IF NOT EXISTS `user_user_group` (
`id` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `id_user_group` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `user_user_group` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id_user` (`id_user`,`id_group`);
ALTER TABLE `user_user_group` MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

-- Added user_group_module_type table
CREATE TABLE IF NOT EXISTS `user_group_module_type` (
`id` int(11) NOT NULL,
  `id_user_group` int(11) NOT NULL,
  `id_module_type` int(11) NOT NULL,
  `read_allowed` tinyint(1) NOT NULL DEFAULT '1',
  `write_allowed` tinyint(1) NOT NULL DEFAULT '1',
  `delete_allowed` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `user_group_module_type` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id_user_group` (`id_user_group`,`id_module_type`);
ALTER TABLE `user_group_module_type` MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;


-- Removed rights col from db_user
ALTER TABLE `user` DROP `rights`;

-- Added module types
INSERT INTO `module_type` (`id`, `name`, `settings`) VALUES ('9', 'error', NULL);
INSERT INTO `module_type` (`id`, `name`, `settings`) VALUES ('10', 'mainpage', NULL);
INSERT INTO `module_type` (`id`, `name`, `settings`) VALUES ('11', 'admin', NULL);
INSERT INTO `module_type` (`id`, `name`, `settings`) VALUES ('12', 'login', NULL);
INSERT INTO `module_type` (`id`, `name`, `settings`) VALUES ('13', 'navigation', NULL);
INSERT INTO `module_type` (`id`, `name`, `settings`) VALUES ('14', 'node', NULL);

-- Added en translation in label db
ALTER TABLE `label` ADD `en` VARCHAR(200) NULL AFTER `cs`;

-- ArtSys 1.2, 2016-02-19
-- ----------------------------------------------------------------------------
-- - Major update


-- ArtSys 1.1, 2015-11-02
-- ----------------------------------------------------------------------------
-- - Major update


-- ArtSys 1.0, 2015-10-02
-- ----------------------------------------------------------------------------
-- - Initial release
