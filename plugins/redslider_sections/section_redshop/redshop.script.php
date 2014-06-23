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
class PlgRedslider_SectionsSection_RedshopInstallerScript extends Com_RedcoreInstallerScript
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
		 * Insert demo template for redSHOP section
		 */
		$templateTable = JTable::getInstance('Template', 'RedsliderTable', array('ignore_request' => true));
		$templateTable->id = null;
		$templateTable->title = 'Template redSHOP';
		$templateTable->section = 'SECTION_redSHOP';
		$templateTable->published = 1;
		$templateTable->content = '<div class="eachSlide">\r\n<div class="slideImg">\r\n<h3>{product_image|300|200}</h3>\r\n</div>\r\n<div class="slidePrice">\r\n<h3>{product_price}</h3>\r\n</div>\r\n<div class="slideTitle">\r\n<h3>{product_name}</h3>\r\n</div>\r\n<div class="slideText">{product_short_description}</div>\r\n<div class="slideForm">{form_addtocart:add_to_cart2}</div>\r\n</div>';
		$templateTable->store();
		$templateId = (int) $templateTable->id;
		/*
		 * Insert demo slide for redSHOP section
		 */
		$slideTable = JTable::getInstance('Slide', 'RedsliderTable', array('ignore_request' => true));
		$slideTable->gallery_id = 1;
		$slideTable->template_id = $templateId;
		$slideTable->title = 'Sample redSHOP';
		$slideTable->section = 'SECTION_REDSHOP';
		$slideTable->published = 1;
		$slideTable->params = '{"product_id":"1","background_image":"images\\/joomla_logo_black.jpg","redshop_slide_class":"redshop_slide"}';
		$slideTable->store();

		unset($templateTable);
		unset($slideTable);

		return true;
	}
}
