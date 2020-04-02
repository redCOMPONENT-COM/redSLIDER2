<?php
/**
 * @package     RedSlider
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Redslider\Plugin\AbstractRedsliderSection;

defined('_JEXEC') or die;

JLoader::import('redslider.library');

/**
 * Plugins RedSLIDER section redSHOP
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Redshop extends AbstractRedsliderSection
{
	/**
	 * @var string
	 */
	protected $sectionId = 'SECTION_REDSHOP';

	/**
	 * @var string
	 */
	protected $extensionName = 'com_redshop';

	protected $formName = 'fields_redshop';

	protected $templateName = 'redshop';

	/**
	 * Constructor - note in Joomla 2.5 PHP4.x is no longer supported so we can use this.
	 *
	 * @param   object $subject  The object to observe
	 * @param   array  $config   An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->sectionName = Text::_('PLG_SECTION_REDSHOP_NAME');
	}

	/**
	 * Get section's tags name
	 *
	 * @param   string  $sectionId  Section's ID
	 *
	 * @return  array
	 * @throws  Exception
	 */
	public function getTagNames($sectionId)
	{
		if ($sectionId === $this->sectionId)
		{
			$app = Factory::getApplication();

			// Check if component redSHOP is not installed
			if (!RedsliderHelper::checkExtension($this->extensionName))
			{
				$app->enqueueMessage(Text::_('PLG_REDSLIDER_SECTION_FORM_INSTALL_COM_REDSHOP_FIRST'), $this->msgLevel);
			}

			$tags = array(
				'{product_name}'                                       => Text::_('COM_REDSLIDER_TAG_REDSHOP_PRODUCT_NAME_DESC'),
				'{product_link}'                                       => Text::_('COM_REDSLIDER_TAG_REDSHOP_PRODUCT_LINK_DESC'),
				'{product_short_description}'                          => Text::_('COM_REDSLIDER_TAG_REDSHOP_PRODUCT_SHORT_DESCRIPTION_DESC'),
				'{product_description}'                                => Text::_('COM_REDSLIDER_TAG_REDSHOP_PRODUCT_DESCRIPTION_DESC'),
				'{attribute_template:<em>template</em>}'               => Text::_('COM_REDSLIDER_TAG_REDSHOP_PRODUCT_ATTRIBUTE_DESC'),
				'{form_addtocart:<em>template</em>}'                   => Text::_('COM_REDSLIDER_TAG_REDSHOP_ADDTOCART_BUTTON_DESC'),
				'{product_thumb_image|<em>width</em>|<em>height</em>}' => Text::_('COM_REDSLIDER_TAG_REDSHOP_PRODUCT_THUMB_IMAGE_DESC'),
				'{product_thumb_image_link}'                           => Text::_('COM_REDSLIDER_TAG_REDSHOP_PRODUCT_THUMB_IMAGE_LINK_DESC'),
				'{product_image|<em>width</em>|<em>height</em>}'       => Text::_('COM_REDSLIDER_TAG_REDSHOP_PRODUCT_IMAGE_DESC'),
				'{product_image_link}'                                 => Text::_('COM_REDSLIDER_TAG_REDSHOP_PRODUCT_IMAGE_LINK_DESC'),
				'{product_price}'                                      => Text::_('COM_REDSLIDER_TAG_REDSHOP_PRODUCT_PRICE_DESC'),
				'{redshop_caption}'                                    => Text::_('COM_REDSLIDER_TAG_REDSHOP_CAPTION_DESC'),
				'{redshop_description}'                                => Text::_('COM_REDSLIDER_TAG_REDSHOP_DESCRIPTION_DESC')
			);

			return $tags;
		}

		return array();
	}

	/**
	 * Prepare content for slide show in module
	 *
	 * @param   string $content Template Content
	 * @param   object $slide   Slide result object
	 *
	 * @return  string  $content  repaced content
	 */
	public function onPrepareTemplateContent($content, $slide)
	{
		if ($slide->section !== $this->sectionId)
		{
			return '';
		}

		// Load redSHOP language file
		Factory::getLanguage()->load('com_redshop');

		// Check if we need to load component's CSS or not
		$useOwnCSS = ComponentHelper::getParams('com_redslider')->get('use_own_css', '0');

		if (RedsliderHelper::checkExtension($this->extensionName))
		{
			// Load redSHOP's javascripts
			HTMLHelper::_('redshopjquery.framework');
			HTMLHelper::script('com_redshop/redbox.js', false, true);
			HTMLHelper::script('com_redshop/attribute.js', false, true);
			HTMLHelper::script('com_redshop/common.js', false, true);

			// Load stylesheet for each section
			$css = 'redslider.' . StringHelper::strtolower($this->sectionId) . '.min.css';

			if (!$useOwnCSS)
			{
				RHelperAsset::load($css, 'redslider_sections/' . StringHelper::strtolower($this->sectionId));
			}

			$redConfiguration = new Redconfiguration;
			$redConfiguration->defineDynamicVars();

			$user          = Factory::getUser();
			$params        = new Registry($slide->params);
			$productHelper = new producthelper;
			$rsHelper      = new redhelper;
			$redTemplate   = new Redtemplate;
			$extraField    = new extraField;

			$product              = new stdClass;
			$product->id          = (int) $params->get('product_id', '0');
			$product->caption     = $params->get('caption');
			$product->description = $params->get('description');
			$product->background  = StringHelper::trim($params->get('background_image', ''));
			$product->slideClass  = StringHelper::trim($params->get('redshop_slide_class', 'redshop_slide'));
			$product->folder      = '/components/com_redshop/assets/images/product/';

			$product->instance = $productHelper->getProductById($product->id);

			if (isset($product->instance))
			{
				$product->prices   = $productHelper->getProductNetPrice($product->id, $user->id);
				$product->template = $redTemplate->getTemplate("product", $product->instance->product_template);

				{
					$product->template = $product->template[0];
				}

				$temp = $productHelper->getProductUserfieldFromTemplate($product->template->template_desc);

				$product->fields           = new stdClass;
				$product->fields->template = $temp[0];
				$product->fields->data     = $temp[1];
				$product->totalFields      = count($product->fields->data);

				$product->children = $productHelper->getChildProduct($product->id);
				$product->isChild  = (bool) count($product->children);

				$product->accessories = $productHelper->getProductAccessory(0, $product->id);

				if ($product->isChild)
				{
					$product->attributes = array();
				}
				else
				{
					$attributeSets = array();

					if ($product->instance->attribute_set_id > 0)
					{
						$attributeSets = $productHelper->getProductAttribute(0, $product->instance->attribute_set_id, 0, 1);
					}

					$product->attributes = $productHelper->getProductAttribute($product->id);
					$product->attributes = array_merge($product->attributes, $attributeSets);
				}

				$product->totalAttributes  = count($product->attributes);
				$product->totalAccessories = count($product->accessories);

				// Replace tags

				if (preg_match_all('/{redshop_description[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							$content = StringHelper::str_ireplace($match[0], $product->description, $content);
						}
					}
				}

				if (preg_match_all('/{redshop_caption[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							$content = StringHelper::str_ireplace($match[0], $product->caption, $content);
						}
					}
				}

				if (preg_match_all('/{product_name[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							$content = StringHelper::str_ireplace($match[0], $product->instance->product_name, $content);
						}
					}
				}

				if (preg_match_all('/{product_link[^}]*}/i', $content, $matches) > 0)
				{
					$menuInformation = $productHelper->getMenuInformation(0, 0, '', 'product&pid=' . $product->instance->product_id);
					$categoryId      = $productHelper->getCategoryProduct($product->instance->product_id);

					if (count($menuInformation) > 0)
					{
						$productItemId = $menuInformation->id;
					}
					else
					{
						$productItemId = $rsHelper->getItemid($product->product_id);
					}

					$link = Route::_(
						'index.php?option=com_redshop&view=product&pid=' .
						$product->instance->product_id . '&cid=' . $categoryId . '&Itemid=' . $productItemId
					);

					foreach ($matches as $match)
					{
						if (count($match))
						{
							$content = StringHelper::str_ireplace($match[0], $link, $content);
						}
					}
				}

				if (preg_match_all('/{product_short_description[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							$content = StringHelper::str_ireplace($match[0], $product->instance->product_s_desc, $content);
						}
					}
				}

				if (preg_match_all('/{product_description[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							$content = StringHelper::str_ireplace($match[0], $product->instance->product_desc, $content);
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

							$content = StringHelper::str_ireplace($match[0], $price, $content);
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
								$middleMan = StringHelper::str_ireplace('{', '', $middleMan);
								$middleMan = StringHelper::str_ireplace('}', '', $middleMan);
								$middleMan = explode('|', $middleMan);

								$replaceString = '<img src="' . Uri::base() . $product->folder . $product->instance->product_full_image . '" ';

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

							$content = StringHelper::str_ireplace($match[0], $replaceString, $content);
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
								$replaceString = Uri::base() . $product->folder . $product->instance->product_full_image;
							}
							else
							{
								$replaceString = '';
							}

							$content = StringHelper::str_ireplace($match[0], $replaceString, $content);
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
								$middleMan = StringHelper::str_ireplace('{', '', $middleMan);
								$middleMan = StringHelper::str_ireplace('}', '', $middleMan);
								$middleMan = explode('|', $middleMan);

								$replaceString = '<img src="' . Uri::base() . $product->folder . $product->instance->product_thumb_image . '" ';

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

							$content = StringHelper::str_ireplace($match[0], $replaceString, $content);
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
								$replaceString = Uri::base() . $product->folder . $product->instance->product_thumb_image;
							}
							else
							{
								$replaceString = '';
							}

							$content = StringHelper::str_ireplace($match[0], $replaceString, $content);
						}
					}
				}

				if (preg_match_all('/{form_addtocart:[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							$template      = strip_tags($match[0]);
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

							$content = StringHelper::str_ireplace($match[0], $replaceString, $content);
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
									$isChild       = false;
									$attributeSets = array();

									if ($product->instance->attribute_set_id > 0)
									{
										$attributeSets = $productHelper->getProductAttribute(0, $product->instance->attribute_set_id, 0, 1);
									}

									$attributes = $productHelper->getProductAttribute($product->instance->product_id);
									$attributes = array_merge($attributes, $attributeSets);
								}
								else
								{
									$isChild    = true;
									$attributes = array();
								}
							}
							else
							{
								$isChild       = false;
								$attributeSets = array();

								if ($product->instance->attribute_set_id > 0)
								{
									$attributeSets = $productHelper->getProductAttribute(0, $product->instance->attribute_set_id, 0, 1);
								}

								$attributes = $productHelper->getProductAttribute($product->instance->product_id);
								$attributes = array_merge($attributes, $attributeSets);
							}

							$attributeTemplate = $productHelper->getAttributeTemplate($template);
							$template          = $productHelper->replaceAttributeData(
								$product->instance->product_id, 0, 0, $attributes, $template, $attributeTemplate, $isChild
							);

							$content = StringHelper::str_ireplace($match[0], $template, $content);
						}
					}
				}
			}

			return $content;
		}
	}
}
