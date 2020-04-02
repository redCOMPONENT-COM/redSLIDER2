<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2005 - 2020 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Form\Field\EditorField;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;

/**
 * RedSLIDER editor
 *
 * @package     RedSLIDER.Backend
 *
 * @since       2.0.0
 */
class JFormFieldRLEditor extends EditorField
{
	/**
	 * The form field type.
	 *
	 * @var   string
	 * @since 2.0.0
	 */
	public $type = 'RLEditor';

	/**
	 * @var array
	 * since 2.0.0
	 */
	protected $layoutData = [];

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 * @since  2.0.0
	 */
	protected $layout = 'joomla.form.field.rleditor';

	/**
	 * Method to get the textarea field input markup.
	 * Use the rows and columns attributes to specify the dimensions of the area.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   2.0.0
	 */
	protected function getInput()
	{
		// Trim the trailing line in the layout file
		return rtrim($this->getRenderer($this->layout)
			->render($this->getLayoutData()), PHP_EOL);
	}

	/**
	 * Get the layout paths.
	 *
	 * @return  array
	 * @since   2.0.0
	 */
	protected function getLayoutPaths()
	{
		return array_merge(parent::getLayoutPaths(), [JPATH_LIBRARIES . '/redslider/layouts']);
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
		if (empty($this->layoutData))
		{
			$this->layoutData = parent::getLayoutData();
			$readonly         = $this->readonly || $this->disabled;

			$this->layoutData['attribs'] = [
				'class' => $this->layoutData['class']
			];

			if (!empty($this->disabled) && $this->disabled)
			{
				$this->layoutData['attribs']['disabled'] = 'disabled';
			}

			if (!empty($this->readonly) && $this->readonly)
			{
				$this->layoutData['attribs']['readonly'] = 'readonly';
			}

			$this->layoutData['isLimitGuideEnabled'] = $this->isLimitGuideEnabled;
			$this->layoutData['limit']               = $this->limit;

			$editorOptions = array(
				'syntax' => (string) $this->element['syntax']
			);

			// Note that regular editor plugins layout don't support the parameter attribute, you might need to
			// template override this
			if ($this->required)
			{
				$editorOptions['required'] = true;
			}

			if (!$readonly)
			{
				$editor                            = $this->getEditor();
				$this->layoutData['editor']        = $editor;
				$this->layoutData['editorContent'] = $editor->display(
					$this->name, $this->value, "100%", null, null, 10,
					true, $this->id, null, null, $editorOptions
				);
			}
			else
			{
				$this->layoutData['editorContent'] = $this->value;
			}

			$this->layoutData['readonly']   = $readonly;
			$this->layoutData['editorType'] = $this->editorType;
			$this->layoutData['options']    = array('pagebreak', 'readmore');
			$this->layoutData['autoSize']   = $this->autoSize;
			$this->layoutData['attributes'] = ArrayHelper::toString($this->layoutData['attribs']);
		}

		return $this->layoutData;
	}
}
