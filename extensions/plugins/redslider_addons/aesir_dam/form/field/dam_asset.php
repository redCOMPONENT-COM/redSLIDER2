<?php
/**
 * @package     RedSlider
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('aesir_dam.library');

use Joomla\CMS\Form\FormField;
use Joomla\Utilities\ArrayHelper;

/**
 * Aesir DAM Asset field.
 *
 * @since  2.0.0
 */
class JFormFieldAesir_Dam_Asset extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Aesir_Dam_Asset';

	/**
	 * @var string
	 */
	protected $layout = 'edit';

	/**
	 * Cached array of options.
	 *
	 * @var  array
	 */
	protected static $options = array();

	/**
	 * Get the layout paths.
	 *
	 * @return  array
	 */
	protected function getLayoutPaths()
	{
		return array_merge(parent::getLayoutPaths(), [JPATH_PLUGINS . '/redslider_addons/aesir_dam/layouts']);
	}

	/**
	 * Get the renderer
	 *
	 * @param   string  $layoutId  Id to load
	 *
	 * @return  RLayoutFile
	 *
	 * @since   2.0.0
	 */
	protected function getRenderer($layoutId = 'default')
	{
		$renderer = new RLayoutFile($layoutId);

		$renderer->setDebug($this->isDebugEnabled());

		$layoutPaths = $this->getLayoutPaths();

		if ($layoutPaths)
		{
			$renderer->addIncludePaths($layoutPaths);
		}

		return $renderer;
	}

	/**
	 * Get the data that is going to be passed to the layout
	 *
	 * @return  array
	 * @since   2.0.0
	 */
	protected function getLayoutData()
	{
		$data = parent::getLayoutData();

		$data['attribs']['multiple'] = (int) ($this->multiple === true);
		$data['attributes']          = ArrayHelper::toString($data['attribs']);
		$data['form']                = $this->getFilterForm();

		// Value format is different if loaded from getItem(), or if obtained from session data on save failure
		$value = is_array($this->value) ? json_encode($this->value) : $this->value;

		$data['value'] = empty($value) ? '[]' : $value;

		return $data;
	}

	/**
	 * Gets the form.
	 *
	 * @return  \JForm
	 * @since   2.0.0
	 */
	private function getFilterForm()
	{
		RForm::addFormPath(__DIR__ . '/..');

		return RForm::getInstance('filters_form', 'filters', array('control' => null));
	}

	/**
	 * Method to get the name used for the field input tag.
	 *
	 * @param   string  $fieldName  The field element name.
	 *
	 * @return  string  The name to be used for the field input tag.
	 *
	 * @since   2.0.0
	 */
	protected function getName($fieldName)
	{
		$name = parent::getName($fieldName);

		if (substr($name, -2) == '[]')
		{
			$name = substr($name, 0, -2);
		}

		return $name;
	}
}
