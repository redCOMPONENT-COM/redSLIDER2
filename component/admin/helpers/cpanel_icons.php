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
			"redslider" => array(
				"galleries" => array(
					"name"      => "galleries",
					"icon"      => "icon-folder-open",
					"oldIcon"   => "redslider_categories48.png",
					"title"     => "GALLERIES",
					'cpanelDisplay' => true,
				),
				"sliders" => array(
					"name"      => "sliders",
					"icon"      => "icon-file",
					"oldIcon"   => "redslider_items48.png",
					"title"     => "SLIDERS",
					'cpanelDisplay' => true,
				),
				"templates" => array(
					"name"      => "templates",
					"icon"      => "icon-hdd",
					"oldIcon"   => "redslider_templates48.png",
					"title"     => "TEMPLATES",
					'cpanelDisplay' => true,
				)
			)
		);

		return $icon_array;
	}
}
