<?php
/**
 * @package    RedSLIDER.Installer
 *
 * @copyright  Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Registry\Registry;

defined('_JEXEC') or die;

// Find redCORE installer to use it as base system
if (!class_exists('Com_RedcoreInstallerScript'))
{
	$searchPaths = array(
		// Install
		dirname(__FILE__) . '/redCORE',
		// Discover install
		JPATH_ADMINISTRATOR . '/components/com_redcore'
	);

	$redcoreInstaller = JPath::find($searchPaths, 'install.php');

	if ($redcoreInstaller)
	{
		require_once $redcoreInstaller;
	}
}

/**
 * Script file of redSLIDER plugins
 *
 * @package  RedSLIDER.Installer
 *
 * @since    2.0
 */
class PlgRedslider_SectionsSection_RedeventInstallerScript extends Com_RedcoreInstallerScript
{
	/**
	 * @var string
	 */
	private $section = 'SECTION_REDEVENT';

	/**
	 * Method to install the component
	 *
	 * @param   object $parent Class calling this method
	 *
	 * @return  boolean          True on success
	 */
	public function installOrUpdate($parent)
	{
		parent::installOrUpdate($parent);

		return true;
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param   object $type   type of change (install, update or discover_install)
	 * @param   object $parent class calling this method
	 *
	 * @return  boolean
	 */
	public function postflight($type, $parent)
	{
		parent::postflight($type, $parent);

		// Migrating demo data
		if ($type === 'install')
		{
			$helperPath = JPATH_ADMINISTRATOR . '/components/com_redslider/helpers/helper.php';

			if (JFile::exists($helperPath))
			{
				require_once $helperPath;

				$comExists = RedsliderHelper::checkExtension('com_redevent');
				$db        = JFactory::getDbo();

				// Add Include path
				JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_redslider/tables');

				// Insert demo template for redEVENT section
				/** @var RedsliderTableTemplate $templateTable */
				$templateTable            = JTable::getInstance('Template', 'RedsliderTable', array('ignore_request' => true));
				$templateTable->id        = null;
				$templateTable->title     = 'Template redEVENT';
				$templateTable->section   = 'SECTION_REDEVENT';
				$templateTable->published = $comExists ? 1 : 0;
				$templateTable->content   = '<div class="eachSlide">
					<div class="slideTitle"><h3>[event_title]</h3></div>
					<div class="slideText">[event_description]</div></div>';
				$templateTable->store();
				$templateId = (int) $templateTable->id;

				// Prepare params for redEVENT slide

				$slideParams = array(
					'event_id'             => 1,
					'background_image'     => 'images/stories/redslider/redevent_slider.jpg',
					'redevent_slide_class' => 'redevent_slide'
				);

				$slideParams = new Registry($slideParams);

				/*
				 * Insert demo slide for redEVENT section
				 */
				/** @var RedsliderTableSlide $templateTable */
				$slideTable              = JTable::getInstance('Slide', 'RedsliderTable', array('ignore_request' => true));
				$slideTable->gallery_id  = 1;
				$slideTable->template_id = $templateId;
				$slideTable->title       = 'Sample redEVENT';
				$slideTable->section     = 'SECTION_REDEVENT';
				$slideTable->published   = $comExists ? 1 : 0;
				$slideTable->params      = $slideParams->toString();
				$slideTable->store();

				unset($templateTable, $slideTable);

				// Set this plugin published
				$query = $db->getQuery(true);

				$query->update($db->qn('#__extensions'))
					->set($db->qn('enabled') . ' = 1')
					->where(
						$db->qn('element') . ' = ' . $db->q('section_redevent')
						. ' AND ' . $db->qn('folder') . ' = ' . $db->q('redslider_sections')
					);
				$db->setQuery($query)->execute();
			}
		}

		return true;
	}

	/**
	 * method to uninstall the component
	 *
	 * @param   JInstallerAdapter  $parent  Class calling this method
	 *
	 * @return  void
	 *
	 * @throws  RuntimeException
	 */
	public function uninstall($parent)
	{
		$db = JFactory::getDbo();

		// Remove all slides which belong to this section
		$query = $db->getQuery(true)
			->delete($db->qn('#__redslider_slides'))
			->where($db->qn('section') . '=' . $db->q($this->section));
		$db->setQuery($query)->execute();

		// Remove all templates which belong to this section
		$query->clear()
			->delete($db->qn('#__redslider_templates'))
			->where($db->qn('section') . '=' . $db->q($this->section));
		$db->setQuery($query)->execute();

		parent::uninstall($parent);
	}
}
