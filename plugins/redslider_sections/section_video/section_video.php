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
 * Plugins RedSLIDER section video
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Video extends JPlugin
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
		$this->sectionId = "SECTION_VIDEO";
		$this->sectionName = JText::_("PLG_SECTION_VIDEO_NAME");
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
					"{youtube}" => JText::_("COM_REDSLIDER_TAG_VIDEO_YOUTUBE_DESC"),
					"{vimeo}" => JText::_("COM_REDSLIDER_TAG_VIDEO_VIMEO_DESC"),
					"{local}" => JText::_("COM_REDSLIDER_TAG_VIDEO_LOCAL_DESC"),
					"{other}" => JText::_("COM_REDSLIDER_TAG_VIDEO_OTHER_DESC"),
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
				JForm::addFormPath(__DIR__ . '/forms/');
				$return = $form->loadFile('fields_video', false);
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
				$fields = $view->form->getGroup('params');

				if (count($fields))
				{
					foreach ($fields as $field)
					{
						if (JString::strpos($field->id, "jform_params_vimeo") !== false)
						{
							$view->outputFields["COM_REDSLIDER_SECTION_VIDEO_PANE_VIMEO"][] = $field;
						}
						elseif (JString::strpos($field->id, "jform_params_youtube") !== false)
						{
							$view->outputFields["COM_REDSLIDER_SECTION_VIDEO_PANE_YOUTUBE"][] = $field;
						}
						elseif (JString::strpos($field->id, "jform_params_local") !== false)
						{
							$view->outputFields["COM_REDSLIDER_SECTION_VIDEO_PANE_LOCAL"][] = $field;
						}
						elseif (JString::strpos($field->id, "jform_params_other") !== false)
						{
							$view->outputFields["COM_REDSLIDER_SECTION_VIDEO_PANE_OTHER"][] = $field;
						}
					}
				}

				$view->addTemplatePath(__DIR__ . '/tmpl/');
				$return = $view->loadTemplate('video');
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
		if ($slide->section === $this->sectionId)
		{
			$params = new JRegistry($slide->params);

			$matches = array();

			// Case Vimeo
			if (preg_match_all('/{vimeo[^}]*}/i', $content, $matches) > 0)
			{
				$vimeo = new stdClass;
				$vimeo->id = $params->get('vimeo_id');
				$vimeo->width = $params->get('vimeo_width');
				$vimeo->height = $params->get('vimeo_height');
				$vimeo->portrait = $params->get('vimeo_portrait');
				$vimeo->title = $params->get('vimeo_title');
				$vimeo->byline = $params->get('vimeo_byline');
				$vimeo->autoplay = $params->get('vimeo_autoplay');
				$vimeo->loop = $params->get('vimeo_loop');
				$vimeo->color = $params->get('vimeo_color');
				$vimeo->color = JString::str_ireplace("#", "", $vimeo->color);

				$replaceString = '';

				if (isset($vimeo->id) && (JString::trim($vimeo->id)))
				{
					$replaceString .= '<iframe ';
					$replaceString .= 'src="//player.vimeo.com/video/' . JString::trim($vimeo->id) . '?color=' . JString::trim($vimeo->color);

					if ($vimeo->loop)
					{
						$replaceString .= '&amp;loop=1';
					}

					if ($vimeo->autoplay)
					{
						$replaceString .= '&amp;autoplay=1';
					}

					$replaceString .= '" ';

					if (!is_numeric($vimeo->width))
					{
						$replaceString .= 'width="560" ';
					}
					else
					{
						$replaceString .= 'width="' . JString::trim($vimeo->width) . '" ';
					}

					if (!is_numeric($vimeo->height))
					{
						$replaceString .= 'height="315" ';
					}
					else
					{
						$replaceString .= 'height="' . JString::trim($vimeo->height) . '" ';
					}

					$replaceString .= 'frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
				}

				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = JString::str_ireplace($match[0], $replaceString, $content);
					}
				}
			}
			// Case Youtube
			if (preg_match_all('/{youtube[^}]*}/i', $content, $matches) > 0)
			{
				$youtube = new stdClass;
				$youtube->id = $params->get('youtube_id');
				$youtube->width = $params->get('youtube_width');
				$youtube->height = $params->get('youtube_height');
				$youtube->suggested = $params->get('youtube_suggested');
				$youtube->privacy_enhance = $params->get('youtube_privacy_enhanced');

				$replaceString = '';

				if (isset($youtube->id) && (JString::trim($youtube->id)))
				{
					$replaceString .= '<iframe ';

					if (!is_numeric($youtube->width))
					{
						$replaceString .= 'width="560" ';
					}
					else
					{
						$replaceString .= 'width="' . JString::trim($youtube->width) . '" ';
					}

					if (!is_numeric($youtube->height))
					{
						$replaceString .= 'height="315" ';
					}
					else
					{
						$replaceString .= 'height="' . JString::trim($youtube->height) . '" ';
					}

					if ($youtube->privacy_enhance)
					{
						$replaceString .= 'src="//www.youtube-nocookie.com/embed/' . JString::trim($youtube->id) . ' ';
					}
					else
					{
						$replaceString .= 'src="//www.youtube.com/embed/' . JString::trim($youtube->id) . ' ';
					}

					if (!$youtube->suggested)
					{
						$replaceString .= '?rel=0" ';
					}
					else
					{
						$replaceString .= '" ';
					}

					$replaceString .= 'frameborder="0" allowfullscreen></iframe>';
				}

				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = JString::str_ireplace($match[0], $replaceString, $content);
					}
				}
			}

			// Case Local Video
			if (preg_match_all('/{local[^}]*}/i', $content, $matches) > 0)
			{
				var_dump($matches);
				exit;
			}

			// Case Other Iframe Video Embed
			if (preg_match_all('/{other[^}]*}/i', $content, $matches) > 0)
			{
				var_dump($matches);
				exit;
			}

			return $content;
		}
	}
}
