<?php
/**
 * @package     RedSLIDER.Frontend
 * @subpackage  mod_redslider
 *
 * @copyright   Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$redcoreLoader = JPATH_LIBRARIES . '/redcore/bootstrap.php';

if (!file_exists($redcoreLoader) || !JPluginHelper::isEnabled('system', 'redcore'))
{
	throw new Exception(JText::_('COM_REDSLIDER_REDCORE_INIT_FAILED'), 404);
}

// Bootstraps redCORE
RBootstrap::bootstrap();

require_once JPATH_SITE . '/modules/mod_redslider/helper.php';

// Get params

$galleryId = (int) $params->get('gallery_id', 0);
$class = $params->get('slider_class', 'flexslider');
$slideWidth = (int) $params->get('slide_width', 756);
$slideWidth .= "px";
$slideHeight = (int) $params->get('slide_height', 420);
$slideHeight .= "px";
$smoothHeight = $params->get('smooth_height', false);
$slideControl = (bool) $params->get('slide_control', true);
$pager = (bool) $params->get('pager', true);
$slideThumbnail = $params->get('slide_thumbnail', false);
$thumbWidth = (int) $params->get('thumb_width', 150);
$thumbHeight = (int) $params->get('thumb_height', 100);
$thumbControl = (bool) $params->get('thumb_control', false);
$thumbNums = (int) $params->get('thumb_nums', 3);
$effect = $params->get('effect_type', 'slide');
$autoPlay = (bool) $params->get('auto_play', true);
$pauseOnHover = $params->get('pause_on_hover', true);
$speed = $params->get('slideshow_speed', 7000);
$duration = $params->get('animation_duration', 600);
$slides = ModredSLIDERHelper::getSlides($gallery_id);

if ($slideThumbnail)
{
	$opt = array(
		'animation' => $effect,
		'slideshow' => $autoPlay,
		'pauseOnHover' => $pauseOnHover,
		'slideshowSpeed' => $speed,
		'animationDuration' => $duration,
		'directionNav' => false,
		'maxItems' => $thumbNums,
		'controlNav' => $slideControl,
		'sync'	=> '.' . $class . '> .carousel'
	);

	$optThumb = array(
		'animation' => $effect,
		'slideshow' => false,
		'maxItems' => $thumbNums,
		'itemWidth' => $thumbWidth,
		'controlNav' => $thumbControl,
		'asNavFor'	=> '.' . $class . '> .slider'
	);
}
else
{
	$opt = array(
		'animation' => $effect,
		'slideshow' => $autoPlay,
		'pauseOnHover' => $pauseOnHover,
		'slideshowSpeed' => $speed,
		'animationDuration' => $duration,
		'directionNav' => $slideControl,
		'controlNav' => $pager,
	);
}


// Initialize the slider with settings
JHtml::_('rjquery.flexslider', '.' . $class . '> .slider', $opt);

// Initialize the thumbnails control
JHtml::_('rjquery.flexslider', '.' . $class . '> .carousel', $optThumb);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$displayType = $params->get('display', 0);

require JModuleHelper::getLayoutPath('mod_redslider', $params->get('layout', 'default'));
