<?php
/**
 * @package     RedSlider
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Redslider\Plugin\AbstractRedsliderSection;

JLoader::import('redslider.library');

/**
 * Plugins RedSLIDER section standard
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Standard extends AbstractRedsliderSection
{
	/**
	 * @var string
	 */
	protected $sectionId = 'SECTION_STANDARD';

	/**
	 * @var string
	 */
	protected $formName = 'fields_standard';

	/**
	 * @var string
	 */
	protected $templateName = 'standard';

	/**
	 * Constructor - note in Joomla 2.5 PHP4.x is no longer supported so we can use this.
	 *
	 * @param   object $subject  The object to observe
	 * @param   array  $config   An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->sectionName = Text::_("PLG_SECTION_STANDARD_NAME");
	}

	/**
	 * Get section's tags name
	 *
	 * @param   string $sectionId Section's ID
	 *
	 * @return  array
	 */
	public function getTagNames($sectionId)
	{
		if ($sectionId === $this->sectionId)
		{
			return array(
				'{standard_description}' => Text::_("COM_REDSLIDER_SECTION_STANDARD_TAG_DESCRIPTION_DESC"),
				'{standard_link}'        => Text::_("COM_REDSLIDER_SECTION_STANDARD_TAG_LINK_DESC"),
				'{standard_linktext}'    => Text::_("COM_REDSLIDER_SECTION_STANDARD_TAG_LINKTEXT_DESC"),
				'{standard_title}'       => Text::_("COM_REDSLIDER_SECTION_STANDARD_TAG_TITLE_DESC"),
				'{standard_caption}'     => Text::_("COM_REDSLIDER_SECTION_STANDARD_TAG_CAPTION_DESC"),
			);
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

		// Check if we need to load component's CSS or not
		$useOwnCSS = ComponentHelper::getParams('com_redslider')->get('use_own_css', '0');

		// Load stylesheet for each section
		$css = 'redslider.' . StringHelper::strtolower($this->sectionId) . '.min.css';

		if (!$useOwnCSS)
		{
			RHelperAsset::load($css, 'redslider_sections/' . StringHelper::strtolower($this->sectionId));
		}

		$params   = new Registry($slide->params);
		$standard = new stdClass;

		$standard->description = $params->get('description', '');
		$standard->link        = $params->get('link', '');
		$standard->linktext    = $params->get('linktext', '');
		$standard->suffixClass = $params->get('suffix_class', 'standard_slide');
		$standard->title       = $slide->title;
		$standard->caption     = $params->get('caption', '');

		if (strpos($content, '{standard_description}') !== false)
		{
			$content = str_replace('{standard_description}', $standard->description, $content);
		}

		if (strpos($content, '{standard_link}') !== false)
		{
			$content = str_replace('{standard_link}', $standard->link, $content);
		}

		if (strpos($content, '{standard_linktext}') !== false)
		{
			$content = str_replace('{standard_linktext}', $standard->linktext, $content);
		}

		if (strpos($content, '{standard_title}') !== false)
		{
			$content = str_replace('{standard_title}', $standard->title, $content);
		}

		if (strpos($content, '{standard_caption}') !== false)
		{
			$content = str_replace('{standard_caption}', $standard->caption, $content);
		}

		return $content;
	}
}
