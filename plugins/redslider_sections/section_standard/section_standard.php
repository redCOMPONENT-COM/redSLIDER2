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
 * Plugins RedSLIDER section standard
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Standard extends JPlugin
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
		$this->sectionId = "SECTION_STANDARD";
		$this->sectionName = "Standard";
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
			// Do nothing because section standard has got no tag
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
				JForm::addFormPath(__DIR__ . '/forms/');
				$return = $form->loadFile('fields_standard', false);
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
				$return = $view->loadTemplate('standard');
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
		$jform = $jinput->get('jform', null, 'array');
		$files = $jinput->files->get('jform');

		if ($jform['section'] === $this->sectionId)
		{
			if (isset($files['params']))
			{
				$images = $files['params'];

				$imageFolder = JPATH_ROOT . '/media/com_redslider/images/slides/';

				/* if (!JFolder::exists($imageFolder))
				{
					JFolder::create($imageFolder);
				} */

				// Upload and save image
				if ($images['slide_image_file']['name'] != '')
				{
					$images['slide_image_file']['name'] = time() . '_' . RedsliderHelperHelper::replaceSpecial($images['slide_image_file']['name']);
					$itemImageUpload = true;
					$jform['params']['slide_image_file'] = $images['slide_image_file']['name'];
					$jinput->set('jform', $jform);
				}

				if ($itemImageUpload)
				{
					JFile::upload($images['slide_image_file']['tmp_name'], $imageFolder . $images['slide_image_file']['name']);
				}
			}
		}

		return true;
	}
}
