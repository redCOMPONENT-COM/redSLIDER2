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
		$helperPath = JPATH_ADMINISTRATOR . '/components/com_redslider/helpers/helper.php';

		if (JFile::exists($helperPath))
		{
			require_once $helperPath;

			$comExists = RedsliderHelperHelper::checkExtension('com_redshop');

			$db					= JFactory::getDbo();
			$user				= JFactory::getUser();
			$query 				= $db->getQuery(true);
			$currentDate		= JFactory::getDate();

			// Add Include path
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_redslider/tables');

			/*
			 * Insert demo template for redSHOP section
			 */
			$templateTable = JTable::getInstance('Template', 'RedsliderTable', array('ignore_request' => true));
			$templateTable->id = null;
			$templateTable->title = 'Template redSHOP';
			$templateTable->section = 'SECTION_REDSHOP';
			$templateTable->published = $comExists ? 1 : 0;
			$templateTable->content = '<div class="eachSlide"><div class="prod-show"><div class="slideImg">{product_image|300|200}</div><div class="slidePrice"><h3>{product_price}</h3></div></div><div class="prod-detail"><div class="slideTitle"><h3>{product_name}</h3></div><div class="slideText">{product_short_description}</div><div class="slideAttribute">{attribute_template:attributes}</div><div class="slideForm">{form_addtocart:add_to_cart2}</div></div></div>';
			$templateTable->store();
			$templateId = (int) $templateTable->id;

			// Prepare params for demo redSHOP slide
			$slideParams = array(
				"product_id" => 1,
				"background_image" => "images/stories/redslider/bg_redshop_slider.png",
				"slide_class" => "redshop_slide"
			);

			$slideParams = new JRegistry($slideParams);

			/*
			 * Insert demo slide for redSHOP section
			 */
			$slideTable = JTable::getInstance('Slide', 'RedsliderTable', array('ignore_request' => true));
			$slideTable->gallery_id = 1;
			$slideTable->template_id = $templateId;
			$slideTable->title = 'Sample redSHOP';
			$slideTable->section = 'SECTION_REDSHOP';
			$slideTable->published = $comExists? 1 : 0;
			$slideTable->params = $slideParams->toString();
			$slideTable->store();

			unset($templateTable);
			unset($slideTable);

			// Set this plugin published
			$query = $db->getQuery(true);

			$query->update($db->qn("#__extensions"))
				->set($db->qn('enabled') . ' = 1')
				->where($db->qn('element') . ' = ' . $db->q('section_redshop') . ' AND ' . $db->qn('folder') . ' = ' . $db->q('redslider_sections'));
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}
}
