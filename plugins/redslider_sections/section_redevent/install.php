<?php
/**
 * @package    RedSLIDER.Installer
 *
 * @copyright  Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Script file of redSLIDER plugins
 *
 * @package  RedSLIDER.Installer
 *
 * @since    2.0
 */
class PlgRedslider_SectionsSection_RedeventInstallerScript
{
	/**
	 * Plugin install
	 *
	 * @param   object  $parent  [description]
	 *
	 * @return  void
	 */
	public function install($parent)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$css = dirname(__FILE__) . '/redslider.section_redevent.css';
		var_dump($css);die;
		if (file_exists($css))
		{
			JFile::copy($css, JURI::base() . '/media/mod_redslider/redslider.section_redevent.css');
		}

		$parent::install();
	}
}
