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

// Check if we need to load component's CSS or not
$useOwnCSS = JComponentHelper::getParams('com_redslider')->get('use_own_css', '0');

RHelperAsset::load('redslider.min.js', 'com_redslider');

if (!$useOwnCSS)
{
	RHelperAsset::load('redslider.min.css', 'com_redslider');
}


// Main slider settings
$opt = array();

// Thumbnails slider settings
$optThumb = array();

// Get slides
$galleryId      = (int) $params->get('gallery_id', 0);
$slides         = ModredSLIDERHelper::getSlides($galleryId);

// Get params
$class          = $params->get('slider_class', 'flexslider');
$layout         = $params->get('layouts', 'rstyle1');
$slideControl   = (bool) $params->get('slide_control', true);
$pager          = (bool) $params->get('pager', true);
$slideThumbnail = (bool) $params->get('slide_thumbnail', false);
$thumbControl   = (bool) $params->get('thumb_control', false);
$effect         = $params->get('effect_type', 'slide');
$autoPlay       = (bool) $params->get('auto_play', true);
$pauseOnHover   = (bool) $params->get('pause_on_hover', true);
$speed          = (int) $params->get('slideshow_speed', 7000);
$duration       = (int) $params->get('animation_duration', 600);
$thumbNums      = (int) $params->get('thumb_nums', 3);
$thumbWidth      = (int) $params->get('thumb_width', 150);

if ($slideThumbnail && $thumbNums > 0)
{
	$opt = array(
		'animation'         => $effect,
		'slideshow'         => $autoPlay,
		'pauseOnHover'      => $pauseOnHover,
		'slideshowSpeed'    => $speed,
		'animationDuration' => $duration,
		'directionNav'      => $slideControl,
		'controlNav'        => false,
	);

	$optThumb = array(
		'animation'    => $effect,
		'slideshow'    => false,
		'maxItems'     => $thumbNums,
		'controlNav'   => false,
		'directionNav' => false,
		'itemWidth' => $thumbWidth,
		'asNavFor'     => '.' . $class . '> .slider'
	);
}
else
{
	$opt = array(
		'animation'         => $effect,
		'slideshow'         => $autoPlay,
		'pauseOnHover'      => $pauseOnHover,
		'slideshowSpeed'    => $speed,
		'animationDuration' => $duration,
		'directionNav'      => $slideControl,
		'controlNav'        => $pager,
	);
}

// Initialize the slider with settings
JHtml::_('rjquery.flexslider', '.' . $class . '> .slider', $opt);

// Initialize the thumbnails control
JHtml::_('rjquery.flexslider', '.' . $class . '> .carousel', $optThumb);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$displayType     = $params->get('display', 0);
$class           = $moduleclass_sfx . ' ' . $class . ' ' . $layout;

require JModuleHelper::getLayoutPath('mod_redslider', $params->get('layout', 'default'));
