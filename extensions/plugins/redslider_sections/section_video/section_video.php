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
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Redslider\Plugin\AbstractRedsliderSection;

defined('_JEXEC') or die;

JLoader::import('redslider.library');

/**
 * Plugins RedSLIDER section video
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Video extends AbstractRedsliderSection
{
	/**
	 * @var string
	 */
	protected $sectionId = 'SECTION_VIDEO';

	/**
	 * @var string
	 */
	protected $formName = 'fields_video';

	/**
	 * @var string
	 */
	protected $templateName = 'video';

	/**
	 * Constructor - note in Joomla 2.5 PHP4.x is no longer supported so we can use this.
	 *
	 * @param   object  $subject  The object to observe
	 * @param   array   $config   An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->sectionName = Text::_('PLG_SECTION_VIDEO_NAME');
	}

	/**
	 * Get section's tags name
	 *
	 * @param   string  $sectionId  Section's ID
	 *
	 * @return  array
	 */
	public function getTagNames($sectionId)
	{
		// TODO: Local video - waiting for opinion
		if ($sectionId === $this->sectionId)
		{
			$tags = array(
				'{youtube}' => Text::_('COM_REDSLIDER_TAG_VIDEO_YOUTUBE_DESC'),
				'{vimeo}'   => Text::_('COM_REDSLIDER_TAG_VIDEO_VIMEO_DESC'),
				'{other}'   => Text::_('COM_REDSLIDER_TAG_VIDEO_OTHER_DESC'),
				'{caption}' => Text::_('COM_REDSLIDER_TAG_VIDEO_CAPTION_DESC')
			);

			return $tags;
		}
	}

	/**
	 * Add template of section to template slide
	 *
	 * @param   object  $view       JView object
	 * @param   string  $sectionId  Section's id
	 *
	 * @return  boolean
	 * @throws  Exception
	 */
	public function onSlidePrepareTemplate($view, $sectionId)
	{
		if ($sectionId !== $this->sectionId
			|| !Factory::getApplication()->isClient('administrator'))
		{
			return false;
		}

		// TODO: Local video - waiting for opinion
		$fields = $view->form->getGroup('params');

		if (!empty($fields))
		{
			foreach ($fields as $field)
			{
				if (StringHelper::strpos($field->id, 'jform_params_vimeo') !== false)
				{
					$view->outputFields['COM_REDSLIDER_SECTION_VIDEO_PANE_VIMEO'][] = $field;
				}
				elseif (StringHelper::strpos($field->id, 'jform_params_youtube') !== false)
				{
					$view->outputFields['COM_REDSLIDER_SECTION_VIDEO_PANE_YOUTUBE'][] = $field;
				}
				elseif (StringHelper::strpos($field->id, 'jform_params_other') !== false)
				{
					$view->outputFields['COM_REDSLIDER_SECTION_VIDEO_PANE_OTHER'][] = $field;
				}
				elseif (StringHelper::strpos($field->id, 'jform_params_local') !== false)
				{
					$view->outsizeFields['COM_REDSLIDER_SECTION_VIDEO_PANE_LOCAL'][] = $field;
				}
				else
				{
					$view->basicFields[] = $field;
				}
			}
		}

		return parent::onSlidePrepareTemplate($view, $sectionId);
	}

	/**
	 * Prepare content for slide show in module
	 *
	 * @param   string  $content  Template Content
	 * @param   object  $slide    Slide result object
	 *
	 * @return  string  $content  Replaced content
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

		$params  = new Registry($slide->params);
		$matches = array();

		// Replace video caption
		if (preg_match_all('/{caption[^}]*}/i', $content, $matches) > 0)
		{
			foreach ($matches as $match)
			{
				if (count($match))
				{
					$content = StringHelper::str_ireplace($match[0], $slide->title, $content);
				}
			}
		}

		// Case Vimeo
		if (preg_match_all('/{vimeo[^}]*}/i', $content, $matches) > 0)
		{
			$vimeo           = new stdClass;
			$vimeo->id       = $params->get('vimeo_id');
			$vimeo->width    = $params->get('vimeo_width');
			$vimeo->height   = $params->get('vimeo_height');
			$vimeo->portrait = $params->get('vimeo_portrait');
			$vimeo->title    = $params->get('vimeo_title');
			$vimeo->byline   = $params->get('vimeo_byline');
			$vimeo->autoplay = $params->get('vimeo_autoplay');
			$vimeo->loop     = $params->get('vimeo_loop');
			$vimeo->color    = $params->get('vimeo_color');
			$vimeo->color    = StringHelper::str_ireplace('#', '', $vimeo->color);

			$replaceString = '';

			if (isset($vimeo->id) && StringHelper::trim($vimeo->id))
			{
				$replaceString .= '<iframe ';
				$replaceString .= 'src="//player.vimeo.com/video/' . StringHelper::trim($vimeo->id) . '?color=' . StringHelper::trim($vimeo->color);

				if ($vimeo->loop)
				{
					$replaceString .= '&amp;loop=1';
				}

				if ($vimeo->autoplay)
				{
					$replaceString .= '&amp;autoplay=1';
				}

				if (!$vimeo->byline)
				{
					$replaceString .= '&amp;byline=0';
				}

				if (!$vimeo->title)
				{
					$replaceString .= '&amp;title=0';
				}

				if (!$vimeo->portrait)
				{
					$replaceString .= '&amp;portrait=0';
				}

				$replaceString .= '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
			}

			foreach ($matches as $match)
			{
				if (count($match))
				{
					$content = StringHelper::str_ireplace($match[0], $replaceString, $content);
				}
			}
		}

		// Case Youtube
		if (preg_match_all('/{youtube[^}]*}/i', $content, $matches) > 0)
		{
			$youtube                  = new stdClass;
			$youtube->id              = $params->get('youtube_id');
			$youtube->width           = $params->get('youtube_width');
			$youtube->height          = $params->get('youtube_height');
			$youtube->suggested       = $params->get('youtube_suggested');
			$youtube->privacy_enhance = $params->get('youtube_privacy_enhanced');

			$replaceString = '';

			if (isset($youtube->id) && StringHelper::trim($youtube->id))
			{
				$replaceString .= '<iframe ';

				if (!is_numeric($youtube->width))
				{
					$replaceString .= 'width="560" ';
				}
				else
				{
					$replaceString .= 'width="' . StringHelper::trim($youtube->width) . '" ';
				}

				if (!is_numeric($youtube->height))
				{
					$replaceString .= 'height="315" ';
				}
				else
				{
					$replaceString .= 'height="' . StringHelper::trim($youtube->height) . '" ';
				}

				if ($youtube->privacy_enhance)
				{
					$replaceString .= 'src="//www.youtube-nocookie.com/embed/' . StringHelper::trim($youtube->id) . ' ';
				}
				else
				{
					$replaceString .= 'src="//www.youtube.com/embed/' . StringHelper::trim($youtube->id);
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
					$content = StringHelper::str_ireplace($match[0], $replaceString, $content);
				}
			}
		}

		// Case Local Video
		if (preg_match_all('/{local[^}]*}/i', $content, $matches) > 0)
		{
			$local         = new stdClass;
			$local->media  = $params->get('local_media');
			$local->width  = $params->get('local_width');
			$local->height = $params->get('local_height');

			// TODO: Waiting opinion what video player will be used to stream local video from media manager
		}

		// Case Other Iframe Video Embed
		if (preg_match_all('/{other[^}]*}/i', $content, $matches) > 0)
		{
			$other         = new stdClass;
			$other->iframe = $params->get('other_iframe', '');

			foreach ($matches as $match)
			{
				if (count($match))
				{
					$content = StringHelper::str_ireplace($match[0], $other->iframe, $content);
				}
			}
		}

		return $content;
	}
}
