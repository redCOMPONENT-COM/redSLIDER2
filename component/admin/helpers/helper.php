<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Helpers
 *
 * @copyright   Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedSLIDER CustomFields Helper
 *
 * @package     RedSLIDER.Component
 * @subpackage  Helpers.CusomHelper
 * @since       2.0
 *
 */
class RedsliderHelperHelper
{
	/**
	 * Function check is extension installed
	 *
	 * @param   string  $extension  extension's name, ex: com_sample
	 *
	 * @return boolean
	 */
	public static function checkExtension($extension)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->qn('enabled'))
			->from($db->qn('#__extensions'))
			->where($db->qn('name') . ' = ' . $db->q($extension));

		$db->setQuery($query);
		$result = $db->loadObject();

		if (isset($result) && $result->enabled)
		{
			return true;
		}

		return false;
	}
}
