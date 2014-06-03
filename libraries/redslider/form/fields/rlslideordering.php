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
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text');
		$query->from('#__redslider_slides AS a');

		$query->where('a.published >= 0');

		$query->order('a.lft ASC');

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
		$options = $this->getOptions();

		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		return JHTML::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
	}
}
