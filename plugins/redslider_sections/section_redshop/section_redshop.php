<?php
/**
 * @package     RedSlider
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('redcore.bootstrap');

/**
 * Plugins RedSLIDER section redSHOP
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Redshop extends JPlugin
{
	private $sectionId;

	private $sectionName;

	/**
	 * Constructor - note in Joomla 2.5 PHP4.x is no longer supported so we can use this.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		$this->sectionId = "SECTION_REDSHOP";
		$this->sectionName = "redSHOP";
	}

	/**
	 * Get section name
	 *
	 * @return  array
	 */
	public function getSectionName()
	{
		$section = new stdClass;
		$section->id = $this->sectionId;
		$section->name = $this->sectionName;

		return $section;
	}

	/**
	 * Get section's tags name
	 *
	 * @param   string  $sectionId  Section's ID
	 *
	 * @return  void/array
	 */
	public function getTagNames($sectionId)
	{
		if ($sectionId === $this->sectionId)
		{
			$tags = array(
					"{product_name}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_PRODUCT_NAME_DESC"),
					"{product_description}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_PRODUCT_DESCRIPTION_DESC"),
					"{product_attribute}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_PRODUCT_ATTRIBUTE_DESC"),
					"{product_quantity}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_PRODUCT_QUANTITY_DESC"),
					"{addtocart_button}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_ADDTOCART_BUTTON_DESC"),
					"{product_image}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_PRODUCT_IMAGE_DESC"),
					"{product_price}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_PRODUCT_PRICE_DESC"),
				);

			return $tags;
		}
	}
}
