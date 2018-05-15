<?php
/**
 * @package     RedSlider
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Registry\Registry;

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('redcore.bootstrap');

/**
 * Plugins RedSLIDER section redevent
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Redevent extends JPlugin
{
	/**
	 * @var string
	 */
	private $sectionId = 'SECTION_REDEVENT';

	/**
	 * @var string
	 */
	private $sectionName;

	/**
	 * @var string
	 */
	private $extensionName = 'com_redevent';

	/**
	 * @var string
	 */
	private $msgLevel = 'Warning';

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

		$this->sectionName = JText::_('PLG_SECTION_REDEVENT_NAME');
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
	 * @return  array
	 */
	public function getTagNames($sectionId)
	{
		if ($sectionId === $this->sectionId)
		{
			$tags = array(
				'[event_description]'     => '<br>',
				'[event_title]'           => '<br>',
				'[price]'                 => '<br>',
				'[credits]'               => '<br>',
				'[code]'                  => '<br>',
				'[redform]'               => '<br>',
				'[inputname]'             => '<br>',
				'[inputemail]'            => '<br>',
				'[submit]'                => '<br>',
				'[event_info_text]'       => '<br>',
				'[time]'                  => '<br>',
				'[date]'                  => '<br>',
				'[duration]'              => '<br>',
				'[venue]'                 => '<br>',
				'[city]'                  => '<br>',
				'[eventplaces]'           => '<br>',
				'[waitinglistplaces]'     => '<br>',
				'[eventplacesleft]'       => '<br>',
				'[waitinglistplacesleft]' => '<br>',
				'[paymentrequest]'        => '<br>',
				'[paymentrequestlink]'    => ''
			);

			return $tags;
		}

		return array();
	}

	/**
	 * Add forms fields of section to slide view
	 *
	 * @param   mixed  $form      joomla form object
	 * @param   string $sectionId section's id
	 *
	 * @return  boolean
	 * @throws  Exception
	 */
	public function onSlidePrepareForm($form, $sectionId)
	{
		if ($sectionId !== $this->sectionId)
		{
			return false;
		}

		$app    = JFactory::getApplication();
		$return = false;

		if ($app->isAdmin())
		{
			if (RedsliderHelper::checkExtension($this->extensionName))
			{
				JForm::addFormPath(__DIR__ . '/forms/');
				$return = $form->loadFile('fields_redevent', false);
			}
			else
			{
				$app->enqueueMessage(JText::_('PLG_REDSLIDER_SECTION_EVENT_INSTALL_COM_REDSHOP_FIRST'), $this->msgLevel);
			}
		}

		return $return;
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
		if ($sectionId === $this->sectionId)
		{
			$return = false;

			$app = JFactory::getApplication();

			if ($app->isAdmin())
			{
				$view->addTemplatePath(__DIR__ . '/tmpl/');
				$view->setLayout('default');
				$return = $view->loadTemplate('redevent');
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
		// Check if we need to load component's CSS or not
		$useOwnCSS = JComponentHelper::getParams('com_redslider')->get('use_own_css', '0');

		// Load redEVENT & redFORM language file
		JFactory::getLanguage()->load('com_redevent');
		JFactory::getLanguage()->load('com_redform');

		if ($slide->section === $this->sectionId)
		{
			if (RedsliderHelper::checkExtension($this->extensionName))
			{
				require_once JPATH_LIBRARIES . '/redevent/tags/tags.php';
				require_once JPATH_LIBRARIES . '/redevent/tags/parsed.php';
				require_once JPATH_LIBRARIES . '/redevent/helper/helper.php';
				require_once JPATH_LIBRARIES . '/redevent/helper/attachment.php';
				require_once JPATH_LIBRARIES . '/redevent/helper/output.php';
				require_once JPATH_LIBRARIES . '/redevent/user/acl.php';
				require_once JPATH_LIBRARIES . '/redform/core/model/form.php';
				require_once JPATH_LIBRARIES . '/redform/core/core.php';

				// Load stylesheet for each section
				$css = 'redslider.' . JString::strtolower($this->sectionId) . '.min.css';

				if (!$useOwnCSS)
				{
					RHelperAsset::load($css, 'redslider_sections/' . JString::strtolower($this->sectionId));
				}

				$params  = new Registry($slide->params);
				$eventId = (int) $params->get('event_id', 0);
				$tags    = new RedeventTags;
				$tags->setEventId($eventId);
				$content = $tags->ReplaceTags($content);
			}

			return $content;
		}
	}
}
