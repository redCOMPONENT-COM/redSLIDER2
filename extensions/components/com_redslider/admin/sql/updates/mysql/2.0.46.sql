SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `#__redslider_slides` ADD `created_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `params`;
ALTER TABLE `#__redslider_slides` ADD `created_by` INT(11) NULL DEFAULT NULL AFTER `created_date`;
ALTER TABLE `#__redslider_slides` ADD `modified_by` INT(11) NULL DEFAULT NULL AFTER `created_by`;
ALTER TABLE `#__redslider_slides` ADD `modified_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `modified_by`;
ALTER TABLE `#__redslider_slides` ADD `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `modified_date`;
ALTER TABLE `#__redslider_slides` ADD `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `publish_up`;

SET FOREIGN_KEY_CHECKS = 1;