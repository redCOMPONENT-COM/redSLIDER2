<?php
/**
 * RedSLIDER Library file.
 * Including this file into your application will make redSLIDER available to use.
 *
 * @package    RedSLIDER.Library
 * @copyright  Copyright (C) 2020 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

namespace Redslider\Plugin;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;

jimport('redcore.bootstrap');

require_once JPATH_ADMINISTRATOR . '/components/com_redslider/helpers/helper.php';

PluginHelper::importPlugin('redslider_addons');

/**
 * Class AbstractRedsliderSection
 * @package Redslider
 * @since  __DEPLOY_VERSION__
 */
abstract class AbstractRedsliderSection extends CMSPlugin
{
	/**
	 * @var boolean
	 */
	protected $autoloadLanguage = true;

	/**
	 * @var string
	 */
	protected $sectionName;

	/**
	 * @var string
	 */
	protected $sectionId;

	/**
	 * @var string
	 */
	protected $formName;

	/**
	 * @var string
	 */
	protected $templateName;

	/**
	 * @var string
	 */
	protected $extensionName;

	/**
	 * @var string
	 */
	protected $msgLevel = 'warning';

	/**
	 * Get section name
	 *
	 * @return  object
	 * @since  __DEPLOY_VERSION__
	 */
	public function getSectionName()
	{
		$section       = new \stdClass;
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
	 * @since  __DEPLOY_VERSION__
	 */
	public function getSectionNameById($sectionId)
	{
		if ($sectionId === $this->sectionId)
		{
			return $this->sectionName;
		}
	}

	/**
	 * @param   Form   $form  Form
	 * @param   array  $data  Data
	 *
	 * @return boolean
	 * @since  __DEPLOY_VERSION__
	 * @throws \Exception
	 */
	public function onContentPrepareForm(Form $form, $data = [])
	{
		$data = (array) $data;

		if ($form->getName() !== 'com_redslider.edit.slide.slide'
			|| empty($data['section'])
			|| $data['section'] !== $this->sectionId)
		{
			return true;
		}

		if (!empty($this->extensionName)
			&& !\RedsliderHelper::checkExtension($this->extensionName))
		{
			Factory::getApplication()
				->enqueueMessage(
					Text::_('PLG_REDSLIDER_SECTION_FORM_INSTALL_' . strtoupper($this->extensionName) . '_FIRST'),
					$this->msgLevel
				);

			return true;
		}

		Form::addFormPath(JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name . '/forms/');
		$form->loadFile($this->formName, false);

		Factory::getApplication()
			->triggerEvent('onRedSliderAfterContentPrepareForm', [$form, $data]);

		return true;
	}

	/**
	 * Add template of section to template slide
	 *
	 * @param   object  $view       JView object
	 * @param   string  $sectionId  section's id
	 *
	 * @return boolean
	 * @since  __DEPLOY_VERSION__
	 */
	public function onSlidePrepareTemplate($view, $sectionId)
	{
		if ($sectionId === $this->sectionId)
		{
			$return = false;

			$app = Factory::getApplication();

			if ($app->isAdmin())
			{
				$view->addTemplatePath(JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name . '/tmpl/');
				$return = $view->loadTemplate($this->templateName);
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
	 * @since  __DEPLOY_VERSION__
	 */
	public function onSlideStore($jtable, $jinput)
	{
		return true;
	}
}
