<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
/**
 * RedSLIDER gallery Model
 *
 * @package     RedSLIDER.Component
 * @subpackage  Models.gallery
 * @since       2.0.0
 *
 */
class RedsliderModelGallery extends RModelAdmin
{
	/**
	 * Method to get the row form.
	 *
	 * @param   int  $pk  Primary key
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 *
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		$app = JFactory::getApplication();
		$item = parent::getItem($pk);

		return $item;
	}
}
