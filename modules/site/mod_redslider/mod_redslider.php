<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_relateditems
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
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

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$displayType = $params->get('display', 0);

require JModuleHelper::getLayoutPath('mod_redslider', $params->get('layout', 'default'));
