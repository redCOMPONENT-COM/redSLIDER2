<?php
/**
 * @package     RedSlider
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('redcore.bootstrap');

/**
 * Plugins RedSLIDER section redevent
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Redform extends JPlugin
{
	/**
	 * @var string
	 */
	private $sectionId = 'SECTION_REDFORM';

	/**
	 * @var string
	 */
	private $sectionName;

	/**
	 * @var string
	 */
	private $extensionName = 'com_redform';

	/**
	 * @var string
	 */
	private $msgLevel = 'Warning';

	/**
	 * @var boolean
	 */
	private $noTemplate = true;

	/**
	 * Constructor - note in Joomla 2.5 PHP4.x is no longer supported so we can use this.
	 *
	 * @param   object $subject The object to observe
	 * @param   array  $config  An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		$this->sectionName = JText::_('PLG_SECTION_REDFORM_NAME');
	}

	/**
	 * Get section name
	 *
	 * @return  object
	 */
	public function getSectionName()
	{
		$section       = new stdClass;
		$section->id   = $this->sectionId;
		$section->name = $this->sectionName;

		return $section;
	}

	/**
	 * Get section name by section Id
	 *
	 * @param   string $sectionId Section's ID
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
	 * Add forms fields of section to slide view
	 *
	 * @param   mixed  $form      joomla form object
	 * @param   string $sectionId section's id
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
				if (RedsliderHelper::checkExtension($this->extensionName))
				{
					JForm::addFormPath(__DIR__ . '/forms/');
					$return = $form->loadFile('fields_redform', false);
				}
				else
				{
					$app->enqueueMessage(JText::_('PLG_REDSLIDER_SECTION_FORM_INSTALL_COM_REDFORM_FIRST'), $this->msgLevel);
				}
			}

			return $return;
		}
	}

	/**
	 * Add template of section to template slide
	 *
	 * @param   object $view      JView object
	 * @param   string $sectionId section's id
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
				$view->setLayout('default');
				$return = $view->loadTemplate('redform');
			}

			return $return;
		}
	}

	/**
	 * Event on store a slide
	 *
	 * @param   object $jtable JTable object
	 * @param   object $jinput JForm data
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
