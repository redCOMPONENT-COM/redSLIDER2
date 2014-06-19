--
-- Dumping data for table `#__redslider_galleries`
--

INSERT INTO `#__redslider_galleries` (`id`, `title`, `alias`, `access`, `ordering`, `published`, `checked_out`, `checked_out_time`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Gallery', '', 1, 0, 1, NULL, '0000-00-00 00:00:00', NULL, NULL, NULL, 2014);

-- --------------------------------------------------------

--
-- Dumping data for table `#__redslider_slides`
--

INSERT INTO `#__redslider_slides` (`id`, `title`, `alias`, `gallery_id`, `template_id`, `section`, `published`, `ordering`, `checked_out`, `checked_out_time`, `params`) VALUES
(1, 'Sample Article', '', 1, 1, 'SECTION_ARTICLE', 1, 2, NULL, '0000-00-00 00:00:00', '{"article_id":"1","background_image":"images\\/joomla_black.gif","article_slide_class":"article_slide"}'),
(2, 'Sample Standard', '', 1, 2, 'SECTION_STANDARD', 1, 3, NULL, '0000-00-00 00:00:00', '{"background_image":"images\\/joomla_green.gif","caption":"Sample Standard","description":"<p>Sample Standard<\\/p>","link":"#","linktext":"Sample Standard","suffix_class":"standard_slide"}'),
(3, 'Sample Video', '', 1, 3, 'SECTION_VIDEO', 1, 5, NULL, '0000-00-00 00:00:00', '{"vimeo_id":"","vimeo_width":"500","vimeo_height":"281","vimeo_portrait":"0","vimeo_title":"0","vimeo_byline":"0","vimeo_autoplay":"0","vimeo_loop":"0","vimeo_color":"#FFFFFF","youtube_id":"niVbODz4Dnw","youtube_width":"500","youtube_height":"315","youtube_suggested":"0","youtube_privacy_enhanced":"0","other_iframe":""}');

-- --------------------------------------------------------

--
-- Dumping data for table `#__redslider_templates`
--

INSERT INTO `#__redslider_templates` (`id`, `title`, `alias`, `section`, `content`, `published`, `ordering`, `checked_out`, `checked_out_time`) VALUES
(1, 'Template Article', '', 'SECTION_ARTICLE', '<div class="eachSlide">\r\n<div class="slideTitle">\r\n<h3>{article_title}</h3>\r\n</div>\r\n<div class="slideText">{article_introtext|limit}</div>\r\n</div>', 1, 0, NULL, '0000-00-00 00:00:00'),
(2, 'Template Standard', '', 'SECTION_STANDARD', '<div class="eachSlide">\r\n<div class="slideTitle">\r\n<h3><a href="{standard_link}">{standard_linktext}</a></h3>\r\n</div>\r\n<div class="slideText">{standard_description}</div>\r\n</div>', 1, 0, NULL, '0000-00-00 00:00:00'),
(3, 'Template Video - Youtube', '', 'SECTION_VIDEO', '<div class="eachSlide">{youtube}</div>', 1, 0, NULL, '0000-00-00 00:00:00');

