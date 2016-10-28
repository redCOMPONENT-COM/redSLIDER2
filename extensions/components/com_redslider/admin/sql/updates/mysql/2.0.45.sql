
ALTER TABLE `#__redslider_slides`
	ADD `language` CHAR(7) NOT NULL DEFAULT '' AFTER `checked_out_time`;

ALTER TABLE `#__redslider_slides`
	ADD INDEX `#__rslider_idx_slide_lang` (`language` ASC);
