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
 * RedSLIDER Slide Model
 *
 * @package     RedSLIDER.Component
 * @subpackage  Models.Slide
 * @since       2.0.0
 *
 */
class RedsliderModelSlide extends RModelAdmin
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

	/**
	 * Method to get the row form.
	 *
	 * @param   array  $idArray   id's of rows to be reordered
	 * @param   array  $lftArray  lft values of rows to be reordered
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 *
	 * @since	1.6
	 */
	public function saveorder($idArray = null, $lftArray = null)
	{
		// Get an instance of the table object.
		$table = $this->getTable();

		if (!$table->saveorder($idArray, $lftArray))
		{
			$this->setError($table->getError());

			return false;
		}

		return true;
	}
}
