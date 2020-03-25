<?php
/**
 * @package     RedSlider
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Redslider\Plugin\AbstractRedsliderSection;

defined('_JEXEC') or die;

JLoader::import('redslider.library');

/**
 * Plugins RedSLIDER section redevent
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Redform extends AbstractRedsliderSection
{
	/**
	 * @var string
	 */
	protected $sectionId = 'SECTION_REDFORM';

	/**
	 * @var string
	 */
	protected $sectionName;

	/**
	 * @var string
	 */
	protected $extensionName = 'com_redform';

	/**
	 * @var boolean
	 */
	protected $noTemplate = true;

	protected $formName = 'fields_redform';

	protected $templateName = 'redform';

	/**
	 * Constructor - note in Joomla 2.5 PHP4.x is no longer supported so we can use this.
	 *
	 * @param   object $subject The object to observe
	 * @param   array  $config  An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->sectionName = JText::_('PLG_SECTION_REDFORM_NAME');
	}

	/**
	 * Get section's tags name
	 *
	 * @param   string $sectionId Section's ID
	 *
	 * @return  void/array
	 */
	public function getTagNames($sectionId)
	{
		if ($sectionId === $this->sectionId)
		{
			$tags = array(
				"{redform}<em>form_id</em>{/redform}" => JText::_("COM_REDSLIDER_TAG_REDFORM_REDFORM_DESC"),
				"{redform_title}"                     => JText::_("COM_REDSLIDER_TAG_REDFORM_TITLE_DESC")
			);

			return $tags;
		}
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
		// Load redFORM language file
		JFactory::getLanguage()->load('com_redform');

		// Check if we need to load component's CSS or not
		$useOwnCSS = JComponentHelper::getParams('com_redslider')->get('use_own_css', '0');

		if ($slide->section === $this->sectionId)
		{
			if (RedsliderHelper::checkExtension($this->extensionName))
			{
				// Load stylesheet for each section
				$css = 'redslider.' . JString::strtolower($this->sectionId) . '.min.css';

				if (!$useOwnCSS)
				{
					RHelperAsset::load($css, 'redslider_sections/' . JString::strtolower($this->sectionId));
				}

				$matches = array();

				if (preg_match_all('/{redform_title[^}]*}/i', $content, $matches) > 0)
				{
					foreach ($matches as $match)
					{
						if (count($match))
						{
							$content = JString::str_ireplace($match[0], $slide->title, $content);
						}
					}
				}
			}

			return $content;
		}
	}
}
