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

JHTML::Script('fetchscript.js', 'components/com_redshop/assets/js/', false);
JHTML::Script('attribute.js', 'components/com_redshop/assets/js/', false);
JHTML::Script('common.js', 'components/com_redshop/assets/js/', false);

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
	 * Get section name by section Id
	 *
	 * @param   string  $sectionId  Section's ID
	 *
	 * @return  string
	 */
	public function getSectionNameById($sectionId)
	{
		if ($sectionId === $this->sectionId)
		{
			return $this->sectionName;
		}
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
					"{product_short_description}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_PRODUCT_SHORT_DESCRIPTION_DESC"),
					"{product_description}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_PRODUCT_DESCRIPTION_DESC"),
					"{attribute_template:<em>template</em>}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_PRODUCT_ATTRIBUTE_DESC"),
					"{form_addtocart:<em>template</em>}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_ADDTOCART_BUTTON_DESC"),
					"{product_thumb_image|<em>width</em>|<em>height</em>}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_PRODUCT_THUMB_IMAGE_DESC"),
					"{product_thumb_image_link}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_PRODUCT_THUMB_IMAGE_LINK_DESC"),
					"{product_image|<em>width</em>|<em>height</em>}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_PRODUCT_IMAGE_DESC"),
					"{product_image_link}" => JText::_("COM_REDSLIDER_TAG_REDSHOP_PRODUCT_IMAGE_LINK_DESC"),
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
		if ($sectionId === $this->sectionId)
		{
			$return = false;

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

			return $return;
		}
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
		if ($sectionId === $this->sectionId)
		{
			$return = false;

			$app = JFactory::getApplication();

			if ($app->isAdmin())
			{
				$view->addTemplatePath(__DIR__ . '/tmpl/');
				$return = $view->loadTemplate('redshop');
			}

			return $return;
		}
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

	/**
	 * Prepare content for slide show in module
	 *
	 * @param   string  $content  Template Content
	 * @param   object  $slide    Slide result object
	 *
	 * @return  string  $content  repaced content
	 */
	public function onPrepareTemplateContent($content, $slide)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/redshop.cfg.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/configuration.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_redslider/helpers/helper.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/template.php';
		require_once JPATH_SITE . '/components/com_redshop/helpers/product.php';
		require_once JPATH_ROOT . '/components/com_redshop/helpers/redshop.js.php';
		require_once JPATH_SITE . '/components/com_redshop/helpers/extra_field.php';

		// Load stylesheet for each section
		$css = 'redslider.' . JString::strtolower($this->sectionId) . '.css';
		RHelperAsset::load($css, 'redslider_sections/' . JString::strtolower($this->sectionId));

		$Redconfiguration = new Redconfiguration;
		$Redconfiguration->defineDynamicVars();

		if ($slide->section === $this->sectionId)
		{
			if (RedsliderHelperHelper::checkExtension($this->extensionName))
			{
				$user = JFactory::getUser();
				$params = new JRegistry($slide->params);
				$productHelper   = new producthelper;
				$redTemplate = new Redtemplate;
				$extraField = new extraField;

				$product = new stdClass;
				$product->id = (int) $params->get('product_id', '0');
				$product->background = JString::trim($params->get('background_image', ''));
				$product->slideClass = JString::trim($params->get('redshop_slide_class', 'redshop_slide'));
				$product->folder = '/components/com_redshop/assets/images/product/';

				$product->instance = $productHelper->getProductById($product->id);
				$product->prices = $productHelper->getProductNetPrice($product->id, $user->id);
				$product->template = $redTemplate->getTemplate("product", $product->instance->product_template);

				{
					$product->template = $product->template[0];
				}

				$temp = $productHelper->getProductUserfieldFromTemplate($product->template->template_desc);

				$product->fields = new stdClass;
				$product->fields->template = $temp[0];
				$product->fields->data = $temp[1];
				$product->totalFields = count($product->fields->data);

				$product->children = $productHelper->getChildProduct($product->id);
				$product->isChild = (bool) count($product->children);

				$product->accessories = $productHelper->getProductAccessory(0, $product->id);

				if ($product->isChild)
				{
					$product->attributes = array();
				}
				else
				{
					$attributes_set = array();

					if ($product->instance->attribute_set_id > 0)
					{
						$attributes_set = $productHelper->getProductAttribute(0, $product->instance->attribute_set_id, 0, 1);
					}

					$product->attributes = $productHelper->getProductAttribute($product->id);
					$product->attributes = array_merge($product->attributes, $attributes_set);
				}

				$product->totalAttributes = count($product->attributes);
				$product->totalAccessories = count($product->accessories);

				// Repalce tags

				if (preg_match_all('/{product_name[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							$content = JString::str_ireplace($match[0], $product->instance->product_name, $content);
						}
					}
				}

				if (preg_match_all('/{product_short_description[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							$content = JString::str_ireplace($match[0], $product->instance->product_s_desc, $content);
						}
					}
				}

				if (preg_match_all('/{product_description[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							$content = JString::str_ireplace($match[0], $product->instance->product_desc, $content);
						}
					}
				}

				if (preg_match_all('/{product_price[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							$price = '';

							if (isset($product->prices['product_price']))
							{
								$price = $productHelper->getProductFormattedPrice($product->prices['product_price']);
							}

							$content = JString::str_ireplace($match[0], $price, $content);
						}
					}
				}

				if (preg_match_all('/{product_image[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							if (isset($product->instance->product_full_image) && $product->instance->product_full_image)
							{
								$middleMan = strip_tags($match[0]);
								$middleMan = JString::str_ireplace('{', '', $middleMan);
								$middleMan = JString::str_ireplace('}', '', $middleMan);
								$middleMan = explode('|', $middleMan);

								$replaceString = '<img src="' . JURI::base() . $product->folder . $product->instance->product_full_image . '" ';

								if (isset($middleMan[1]) && is_numeric($middleMan[1]))
								{
									$replaceString .= 'width="' . $middleMan[1] . '" ';
								}

								if (isset($middleMan[2]) && is_numeric($middleMan[2]))
								{
									$replaceString .= 'height="' . $middleMan[2] . '" ';
								}

								$replaceString .= '/>';
							}
							else
							{
								$replaceString = '';
							}

							$content = JString::str_ireplace($match[0], $replaceString, $content);
						}
					}
				}

				if (preg_match_all('/{product_image_link[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							if (isset($product->instance->product_full_image) && $product->instance->product_full_image)
							{
								$replaceString = JURI::base() . $product->folder . $product->instance->product_full_image;
							}
							else
							{
								$replaceString = '';
							}

							$content = JString::str_ireplace($match[0], $replaceString, $content);
						}
					}
				}

				if (preg_match_all('/{product_thumb_image[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							if (isset($product->instance->product_thumb_image) && $product->instance->product_thumb_image)
							{
								$middleMan = strip_tags($match[0]);
								$middleMan = JString::str_ireplace('{', '', $middleMan);
								$middleMan = JString::str_ireplace('}', '', $middleMan);
								$middleMan = explode('|', $middleMan);

								$replaceString = '<img src="' . JURI::base() . $product->folder . $product->instance->product_thumb_image . '" ';

								if (isset($middleMan[1]) && is_numeric($middleMan[1]))
								{
									$replaceString .= 'width="' . $middleMan[1] . '" ';
								}

								if (isset($middleMan[2]) && is_numeric($middleMan[2]))
								{
									$replaceString .= 'height="' . $middleMan[2] . '" ';
								}

								$replaceString .= '/>';
							}
							else
							{
								$replaceString = '';
							}

							$content = JString::str_ireplace($match[0], $replaceString, $content);
						}
					}
				}

				if (preg_match_all('/{product_thumb_image_link[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							if (isset($product->instance->product_thumb_image) && $product->instance->product_thumb_image)
							{
								$replaceString = JURI::base() . $product->folder . $product->instance->product_thumb_image;
							}
							else
							{
								$replaceString = '';
							}

							$content = JString::str_ireplace($match[0], $replaceString, $content);
						}
					}
				}

				if (preg_match_all('/{form_addtocart:[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							$template = strip_tags($match[0]);
							$replaceString = $productHelper->replaceCartTemplate(
													$product->id,
													0,
													0,
													0,
													$template,
													$product->isChild,
													$product->fields->data,
													$product->totalAttributes,
													$product->totalAccessories,
													$product->totalFields
												);

							$content = JString::str_ireplace($match[0], $replaceString, $content);
						}
					}
				}

				if (preg_match_all('/{attribute_template:[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							$template = strip_tags($match[0]);

							// Checking for child products
							$childproduct = $productHelper->getChildProduct($product->instance->product_id);

							if (count($childproduct) > 0)
							{
								if (PURCHASE_PARENT_WITH_CHILD == 1)
								{
									$isChilds       = false;
									$attributes_set = array();

									if ($product->instance->attribute_set_id > 0)
									{
										$attributes_set = $productHelper->getProductAttribute(0, $product->instance->attribute_set_id, 0, 1);
									}

									$attributes = $productHelper->getProductAttribute($product->instance->product_id);
									$attributes = array_merge($attributes, $attributes_set);
								}
								else
								{
									$isChilds   = true;
									$attributes = array();
								}
							}
							else
							{
								$isChilds       = false;
								$attributes_set = array();

								if ($product->instance->attribute_set_id > 0)
								{
									$attributes_set = $productHelper->getProductAttribute(0, $product->instance->attribute_set_id, 0, 1);
								}

								$attributes = $productHelper->getProductAttribute($product->instance->product_id);
								$attributes = array_merge($attributes, $attributes_set);
							}

							$attribute_template = $productHelper->getAttributeTemplate($template);

							$totalatt = count($attributes);
							$template = $productHelper->replaceAttributeData($product->instance->product_id, 0, 0, $attributes, $template, $attribute_template, $isChilds);

							$content = JString::str_ireplace($match[0], $template, $content);
						}
					}
				}

				return $content;
			}
		}
	}
}
