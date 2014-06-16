<?php
/**
 * @package     RedSlider
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * Article field.
 *
 * @package     RedSLIDER
 * @subpackage  Fields
 * @since       2.0
 */
class JFormFieldModal_Event extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Modal_Product';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 *
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Check if component redSHOP is not installed
		$app = JFactory::getApplication();

		if (!RedsliderHelperHelper::checkExtension('com_redevent'))
		{
			$app->enqueueMessage(JText::_('PLG_REDSLIDER_SECTION_REDSHOP_INSTALL_COM_REDSHOP_FIRST'), 'Warning');

			return '';
		}

		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modalRedshopProductAjax');

		// Build the script.
		$script = array();
		$script[] = '	function jSelectProduct(id, title, object) {';
		$script[] = '		document.id("' . $this->id . '_id").value = id;';
		$script[] = '		document.id("' . $this->id . '_name").value = title;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$html	= array();
		$link	= JRoute::_('index.php?option=com_redshop&view=product&task=element&tmpl=component&function=jSelectProduct', false);

		$db	= JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->qn('product_name'))
			->from($db->qn('#__redshop_product'))
			->where($db->qn('product_id') . ' = ' . (int) $this->value);

		$db->setQuery($query);
		$title = $db->loadResult();
		$error = $db->getErrorMsg();

		if ($error)
		{
			JError::raiseWarning(500, $error);
		}

		if (empty($title))
		{
			$title = JText::_('PLG_REDSLIDER_SECTION_PRODUCT_SELECT_PRODUCT');
		}

		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current user display field.

		$html = array();
		$html[] = '<div class="input-prepend input-append">';
		$html[] = '<input type="text" class="input-small" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled"/>';
		$html[] = '<a class="btn modalRedshopProductAjax" title="' . JText::_('PLG_REDSLIDER_SECTION_PRODUCT_SELECT_PRODUCT_BUTTON') . '" href="' . $link . '&amp;' . JSession::getFormToken() . '=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">' . JText::_('PLG_REDSLIDER_SECTION_PRODUCT_SELECT_PRODUCT_BUTTON') . '</a>';
		$html[] = '</div>';

		// The active article id field.

		if (0 == (int) $this->value)
		{
			$value = '';
		}
		else
		{
			$value = (int) $this->value;
		}

		// Client side validation
		$class = '';

		if ($this->required)
		{
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="' . $this->id . '_id"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';

		return implode("\n", $html);
	}
}
