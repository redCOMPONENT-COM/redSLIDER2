<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Redshop configuration model
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Model.Configuration
 * @since       2.0
 */
class RedSliderModelCpanel extends RModelAdmin
{
	/**
	 * Get the current redSLIDER version
	 *
	 * @return  string  The redSLIDER version
	 *
	 * @since   2.0
	 */
	public function getVersion()
	{
		$xmlfile = JPATH_SITE . '/administrator/components/com_redslider/redslider.xml';
		$version = JText::_('COM_REDSLIDER_FILE_NOT_FOUND');

		if (file_exists($xmlfile))
		{
			$data = JApplicationHelper::parseXMLInstallFile($xmlfile);
			$version = $data['version'];
		}

		return $version;
	}

	/**
	 * Get the current redSLIDER version
	 *
	 * @return  string  The redSLIDER version
	 *
	 * @since   2.0.0
	 */
	public function getStats()
	{
		$stats = (object) array();

		return $stats;
	}

	/**
	 * Install demo content
	 *
	 * @return   boolean  Always returns true
	 *
	 * @since	2.0
	 */
	public function demoContentInsert()
	{
		$db				= JFactory::getDbo();
		$user			= JFactory::getUser();
		$currentDate	= JFactory::getDate();

		return true;
	}
}
