<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * Class RedsliderHelpersCpanel_Icons
 *
 * @since  2.0
 */
class RedsliderHelpersCpanel_Icons extends JObject
{
	/**
	 * Some function which was in obscure reddesignhelper class.
	 *
	 * @return array
	 */
	public static function getIconArray()
	{
		$uri               = JUri::getInstance();
		$return            = base64_encode('index.php' . $uri->toString(array('query')));
		$configurationLink = 'index.php?option=com_redcore&view=config&layout=edit&component=com_redslider&return=' . $return;

		return array(
			[
				'view' => 'galleries',
				'link'  => 'index.php?option=com_redslider&view=galleries',
				'icon'  => 'icon-sitemap',
				'text' => Text::_('COM_REDSLIDER_CPANEL_GALLERIES_LABEL')
			],
			[
				'view' => 'slides',
				'link'  => 'index.php?option=com_redslider&view=slides',
				'icon'  => 'icon-file-text',
				'text' => Text::_('COM_REDSLIDER_CPANEL_SLIDES_LABEL')
			],
			[
				'view' => 'templates',
				'link'  => 'index.php?option=com_redslider&view=templates',
				'icon'  => 'icon-desktop',
				'text' => Text::_('COM_REDSLIDER_CPANEL_TEMPLATES_LABEL')
			],
			[
				'view' => 'configuration',
				'link'  => $configurationLink,
				'icon'  => 'icon-cog',
				'text' => Text::_('COM_REDSLIDER_CPANEL_CONFIGURATION_LABEL')
			]
		);
	}
}
