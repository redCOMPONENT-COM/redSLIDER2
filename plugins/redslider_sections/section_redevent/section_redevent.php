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
 * Plugins RedSLIDER section redevent
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Redevent extends JPlugin
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
		$this->sectionId = "SECTION_REDEVENT";
		$this->sectionName = JText::_('PLG_SECTION_REDEVENT_NAME');
		$this->extensionName = "com_redevent";
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
			$tags = array(
						'[event_description]' => '<br>',
						'[event_title]' => '<br>',
						'[price]' => '<br>',
						'[credits]' => '<br>',
						'[code]' => '<br>',
						'[redform]' => '<br>',
						'[inputname]' => '<br>',
						'[inputemail]' => '<br>',
						'[submit]' => '<br>',
						'[event_info_text]' => '<br>',
						'[time]' => '<br>',
						'[date]' => '<br>',
						'[duration]' => '<br>',
						'[venue]' => '<br>',
						'[city]' => '<br>',
						'[eventplaces]' => '<br>',
						'[waitinglistplaces]' => '<br>',
						'[eventplacesleft]' => '<br>',
						'[waitinglistplacesleft]' => '<br>',
						'[paymentrequest]' => '<br>',
						'[paymentrequestlink]' => ''
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
					$return = $form->loadFile('fields_redevent', false);
				}
				else
				{
					$app->enqueueMessage(JText::_('PLG_REDSLIDER_SECTION_EVENT_INSTALL_COM_REDSHOP_FIRST'), $this->msgLevel);
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
				$view->setLayout('default');
				$return = $view->loadTemplate('redevent');
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
		require_once JPATH_LIBRARIES . '/redevent/tags/tags.php';
		require_once JPATH_LIBRARIES . '/redevent/helper/helper.php';
		require_once JPATH_LIBRARIES . '/redevent/helper/attachment.php';
		require_once JPATH_LIBRARIES . '/redevent/helper/output.php';
		require_once JPATH_LIBRARIES . '/redform/core/model/form.php';
		require_once JPATH_LIBRARIES . '/redform/core/core.php';

		if ($slide->section === $this->sectionId)
		{
			$params = new JRegistry($slide->params);

			$eventId = (int) $params->get('event_id', 0);
			$tags = new RedeventTags;
			$tags->setEventId($eventId);
			$content = $tags->ReplaceTags($content);

			// Adding background image to redEVENT slide
			$html  = '<div class=\'' . $article->slideClass . '\' style=\'background-image:url("' . JURI::base() . $event->image . '")\';>';
			$html .= $content;
			$html .= '</div>';

			$content = $html;

			return $content;
		}
	}
}
