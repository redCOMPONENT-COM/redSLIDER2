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

require_once JPATH_ADMINISTRATOR . '/components/com_redslider/helpers/helper.php';

/**
 * Plugins RedSLIDER section redSHOP
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Redshop extends JPlugin
{
	private $sectionId;

	private $sectionName;

	private $extensionName;

	private $msgLevel;

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
		$this->sectionName = JText::_('PLG_SECTION_REDSHOP_NAME');
		$this->extensionName = "com_redshop";
		$this->msgLevel = "Warning";
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
			$app = JFactory::getApplication();

			// Check if component redSHOP is not installed
			if (!RedsliderHelperHelper::checkExtension($this->extensionName))
			{
				$app->enqueueMessage(JText::_('PLG_REDSLIDER_SECTION_REDSHOP_INSTALL_COM_REDSHOP_FIRST'), $this->msgLevel);
			}

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

	/**
	 * Add forms fields of section to slide view
	 *
	 * @param   mixed   $form       joomla form object
	 * @param   string  $sectionId  section's id
	 *
	 * @return  boolean
	 */
	public function onSlidePrepareForm($form, $sectionId)
	{
		$return = false;

		if ($sectionId === $this->sectionId)
		{
			$app = JFactory::getApplication();

			if ($app->isAdmin())
			{
				if (RedsliderHelperHelper::checkExtension($this->extensionName))
				{
					JForm::addFormPath(__DIR__ . '/forms/');
					$return = $form->loadFile('fields_redshop', false);
				}
				else
				{
					$app->enqueueMessage(JText::_('PLG_REDSLIDER_SECTION_REDSHOP_INSTALL_COM_REDSHOP_FIRST'), $this->msgLevel);
				}
			}
		}

		return $return;
	}

	/**
	 * Add template of section to template slide
	 *
	 * @param   object  $view       JView object
	 * @param   string  $sectionId  section's id
	 *
	 * @return boolean
	 */
	public function onSlidePrepareTemplate($view, $sectionId)
	{
		$return = false;

		if ($sectionId === $this->sectionId)
		{
			$app = JFactory::getApplication();

			if ($app->isAdmin())
			{
				$view->addTemplatePath(__DIR__ . '/tmpl/');
				$return = $view->loadTemplate('redshop');
			}
		}

		return $return;
	}

	/**
	 * Event on store a slide
	 *
	 * @param   object  $jtable  JTable object
	 * @param   object  $jinput  JForm data
	 *
	 * @return boolean
	 */
	public function onSlideStore($jtable, $jinput)
	{
		return true;
	}
}
