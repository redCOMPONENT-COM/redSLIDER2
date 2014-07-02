<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Class RedsliderHelperCpanelIcons
 *
 * @since  2.0
 */
class RedsliderHelperCpanelIcons extends JObject
{
	/**
	 * Protected! Use the getInstance
	 *
	 * @return array $icon_array
	 */
	protected function RedsliderHelperCpanelIcons()
	{
		// Parent Helper Construction
		parent::__construct();
	}

	/**
	 * Some function which was in obscure reddesignhelper class.
	 *
	 * @return array
	 */
	public static function getIconArray()
	{
		$icon_array = array(
				"galleries" => array(
					"link"      => JRoute::_('index.php?option=com_redslider&view=galleries'),
					"icon"   	=> "icon-sitemap",
					"title"     => JText::_('COM_REDSLIDER_CPANEL_GALLERIES_LABEL'),
				),
				"slides" => array(
					"link"      => JRoute::_('index.php?option=com_redslider&view=slides'),
					"icon"      => "icon-file-text",
					"title"     => JText::_('COM_REDSLIDER_CPANEL_SLIDES_LABEL'),
				),
				"templates" => array(
					"link"      => JRoute::_('index.php?option=com_redslider&view=templates'),
					"icon"      => "icon-desktop",
					"title"     => JText::_('COM_REDSLIDER_CPANEL_TEMPLATES_LABEL'),
				)
		);

		return $icon_array;
	}
}
