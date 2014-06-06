<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;

JLoader::import('joomla.form.formfield');
JFormHelper::loadFieldClass('list');

/**
 * RedSLIDER slide ordering
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Field.RLTemplateLst
 *
 * @since       2.0
 */
class JFormFieldRLSlideOrdering extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'RLSlideOrdering';

	protected $gallery = 0;

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 * 
	 * @return  string  The field input markup.
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		$query = $db->getQuery(true);

		$query->select($db->qn('a.id') . ' AS ' . $db->qn('value') . ', ' . $db->qn('a.title') . ' AS ' . $db->qn('text'))
			->from($db->qn('#__redslider_slides', 'a'))
			->where($db->qn('a.published') . ' >= 0 AND ' . $db->qn('a.gallery_id') . ' = ' . $this->gallery)
			->order($db->qn('ordering') . ' ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		$options = array_merge(
			array(array('value' => '-1', 'text' => JText::_('COM_REDSLIDER_SLIDE_ORDERING_VALUE_FIRST'))),
			$options,
			array(array('value' => '-2', 'text' => JText::_('COM_REDSLIDER_SLIDE_ORDERING_VALUE_LAST')))
		);

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

	/**
	 * Method to get the field input markup
	 *
	 * @return  string  The field input markup
	 */
	protected function getInput()
	{
		if ($this->form->getValue('gallery_id', 0) == 0)
		{
			return '<span class="readonly">' . JText::_('COM_REDSLIDER_SLIDE_SELECT_GALLERY_FIRST') . '</span>';
		}
		else
		{
			$this->gallery = $this->form->getValue('gallery_id', 0);

			return parent::getInput();
		}
	}
}
