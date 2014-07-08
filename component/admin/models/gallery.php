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

	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed               A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = parent::getForm($data, $loadData);
		$user = JFactory::getUser();

		if (!$user->authorise('core.admin', 'com_redslider'))
		{
			foreach ($form->getGroup('params') as $field)
			{
				$fieldName	= $field->getAttribute('name');
				$fieldClass	= $field->class . ' disabled';

				$form->setFieldAttribute($fieldName, 'readonly', true, 'params');
				$form->setFieldAttribute($fieldName, 'class', $fieldClass, 'params');
			}
		}

		return $form;
	}
}
