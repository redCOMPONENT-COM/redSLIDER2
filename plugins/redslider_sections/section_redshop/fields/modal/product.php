<?php
/**
 * @package     RedSlider
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * Modal Product field.
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
		$app = JFactory::getApplication();

		if (!RedsliderHelper::checkExtension('com_redshop'))
		{
			$app->enqueueMessage(JText::_('PLG_REDSLIDER_SECTION_REDSHOP_INSTALL_COM_REDSHOP_FIRST'), 'Warning');

			return '';
		}

		require_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/redshop.cfg.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/configuration.php';
		JLoader::import('redshop.library');

		foreach ($this->value as $id)
		{
			$product = RedshopHelperProduct::getProductById($id);
			$products[] = $product;
		}

		$layoutData = array(
			'id'    => $this->id,
			'name'  => $this->name,
			'products' => $products
		);

		return RLayoutHelper::render('fields.product', $layoutData, null, array('component' => 'com_redslider'));
	}
}
