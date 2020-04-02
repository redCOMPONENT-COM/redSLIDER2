<?php
/**
 * @package     RedSlider
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('JPATH_BASE') or die;

/**
 * Product field.
 *
 * @package     RedSLIDER
 * @subpackage  Fields
 * @since       2.0
 */
class JFormFieldModal_Product extends JFormField
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
		$app = Factory::getApplication();

		if (!RedsliderHelper::checkExtension('com_redshop'))
		{
			$app->enqueueMessage(JText::_('PLG_REDSLIDER_SECTION_FORM_INSTALL_COM_REDSHOP_FIRST'), 'Warning');

			return '';
		}

		// Build the script.
		$script   = [];
		$script[] = '	function jSelectProduct(id, title, object) {';
		$script[] = '		document.getElementById("' . $this->id . '_id").value = id;';
		$script[] = '		document.getElementById("' . $this->id . '_name").value = title;';
		$script[] = '		window.parent.jQuery("#product-button-modal-' . $this->id . '").modal("hide");';
		$script[] = '	}';

		$script[] = '	function showProductButtonModal(el) {
				var $el = jQuery(el);
				var src = $el.data("link");
				var id = $el.data("id");
				var iframe = jQuery(\'<iframe/>\', {src : src, style: "width: 100%; height: 500px; border: 0;"});
				var modal  = jQuery(\'#product-button-modal-\' + id);
				modal.find(\'.modal-body\').html(iframe);
				modal.modal(\'show\');

				return false;
			}';

		// Add the script to the document head.
		Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$link  = Route::_(
			'index.php?option=com_redshop&view=product&task=element&tmpl=component&function=jSelectProduct&layout=element',
			false
		);
		$db    = Factory::getDBO();
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
			$title = Text::_('PLG_REDSLIDER_SECTION_PRODUCT_SELECT_PRODUCT');
		}

		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current user display field.

		$html   = array();
		$html[] = '<div class="input-group">';
		$html[] = '<input type="text" class="input-small form-control" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled"/>';
		$html[] = '<div class="input-group-btn"><button class="btn btn-default modalProductAjax "'
			. ' data-link="' . $link . '"'
			. ' data-id="' . $this->id . '"'
			. ' onclick="return showProductButtonModal(this);">'
			. Text::_('PLG_REDSLIDER_SECTION_PRODUCT_SELECT_PRODUCT_BUTTON') . '</button>';
		$html[] = '</div></div>';

		// The active product id field.
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

		$modal = '<div id="product-button-modal-' . $this->id . '" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="' . Text::_('JTOOLBAR_CLOSE') . '">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body"></div>

				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">' . Text::_('JTOOLBAR_CLOSE') . '</button>
				</div>
			</div>
		</div>
	</div>';

		// Move modal at the end of the document
		$app->registerEvent('onAfterRender', function () use ($modal, $app) {
			$app->setBody($app->getBody() . $modal);
		});

		return implode("\n", $html);
	}
}
