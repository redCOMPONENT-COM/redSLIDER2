<?php
/**
 * RedSLIDER Library file.
 * Including this file into your application will make redSLIDER available to use.
 *
 * @package    RedSLIDER.Library
 * @copyright  Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('JPATH_PLATFORM') or die;

// Define redSLIDER Library Folder Path
define('JPATH_REDSLIDER_LIBRARY', __DIR__);

// Load redSLIDER Library
JLoader::import('redcore.bootstrap');

// Bootstraps redCORE
RBootstrap::bootstrap();

// Register library prefix
RLoader::registerPrefix('Redslider', JPATH_REDSLIDER_LIBRARY);

// Make available the redSLIDER fields
JFormHelper::addFieldPath(JPATH_REDSLIDER_LIBRARY . '/form/fields');

// Make available the redSLIDER form rules
JFormHelper::addRulePath(JPATH_REDSLIDER_LIBRARY . '/form/rules');

HTMLHelper::addIncludePath(__DIR__ . '/html');
RHtml::addIncludePath(__DIR__ . '/html');

JLoader::registerNamespace('Redslider', __DIR__ . '/src', false, false, 'psr4');
