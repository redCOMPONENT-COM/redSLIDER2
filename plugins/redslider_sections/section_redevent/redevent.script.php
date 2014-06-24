<?php
/**
 * @package    RedSLIDER.Installer
 *
 * @copyright  Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

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

	if ($redcoreInstaller = JPath::find($searchPaths, 'install.php'))
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
	 * Method to install the component
	 *
	 * @param   object  $parent  Class calling this method
	 *
	 * @return  boolean          True on success
	 */
	public function installOrUpdate($parent)
	{
		parent::installOrUpdate($parent);

		$this->plugin_install();

		return true;
	}

	/**
	 * Plugin install
	 *
	 * @return  boolean
	 */
	public function plugin_install()
	{
		$db					= JFactory::getDbo();
		$user				= JFactory::getUser();
		$query 				= $db->getQuery();
		$currentDate		= JFactory::getDate();

		// Add Include path
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_redslider/tables');
		/*
		 * Insert demo template for redEVENT section
		 */
		$templateTable = JTable::getInstance('Template', 'RedsliderTable', array('ignore_request' => true));
		$templateTable->id = null;
		$templateTable->title = 'Template redEVENT';
		$templateTable->section = 'SECTION_REDEVENT';
		$templateTable->published = 1;
		$templateTable->content = "<div>[event_title]<div><div>[event_description]<div>";
		$templateTable->store();
		$templateId = (int) $templateTable->id;
		/*
		 * Insert demo slide for redEVENT section
		 */
		$slideTable = JTable::getInstance('Slide', 'RedsliderTable', array('ignore_request' => true));
		$slideTable->gallery_id = 1;
		$slideTable->template_id = $templateId;
		$slideTable->title = 'Sample redEVENT';
		$slideTable->section = 'SECTION_REDEVENT';
		$slideTable->published = 1;
		$slideTable->params = '{"event_id":"1","background_image":"images\/joomla_black.gif","redevent_slide_class":"redevent_slide"}';
		$slideTable->store();

		unset($templateTable);
		unset($slideTable);

		return true;
	}
}