<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Entry point
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Aesir\Core\Helper\AssetHelper;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

$redcoreLoader = JPATH_LIBRARIES . '/redcore/bootstrap.php';

if (!file_exists($redcoreLoader) || !JPluginHelper::isEnabled('system', 'redcore'))
{
	throw new Exception(JText::_('COM_REDSLIDER_REDCORE_INIT_FAILED'), 404);
}

// Bootstraps redCORE
RBootstrap::bootstrap();
RHtmlMedia::setFramework('bootstrap3');

$app    = Factory::getApplication();
$jInput = $app->input;

// Register component prefix
JLoader::registerPrefix('Redslider', __DIR__);

// Load redSLIDER Library
JLoader::import('redslider.library');

$controller = $jInput->get('view', 'cpanel');

// Set the controller page
if (!file_exists(JPATH_COMPONENT . '/controllers/' . $controller . '.php'))
{
	$controller = 'redslider';
	$jInput->set('view', 'cpanel');
}

$user        = Factory::getUser();
$task        = $jInput->get('task', '');
$layout      = $jInput->get('layout', '');
$showbuttons = $jInput->get('showbuttons', '0');
$showall     = $jInput->get('showall', '0');

AssetHelper::load('redslider.backend.min.css');

$controller	= JControllerLegacy::getInstance('Redslider');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

AssetHelper::load('chosen.destroy.min.js', 'com_redslider');
AssetHelper::load('select2.min.css', 'com_redslider');
AssetHelper::load('select2.min.js', 'com_redslider');

Factory::getDocument()
	->addScriptDeclaration(
	"(function ($) {
	$(document).ready(function () {
		$('select').chosenDestroy().select2();
	});
})(jQuery);"
);
