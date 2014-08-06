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
class PlgRedslider_SectionsSection_RedformInstallerScript extends Com_RedcoreInstallerScript
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

		return true;
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param   object  $type    type of change (install, update or discover_install)
	 * @param   object  $parent  class calling this method
	 *
	 * @return  boolean
	 */
	public function postflight($type, $parent)
	{
		parent::postflight($type, $parent);

		// Migrating demo data
		if ($type === 'install')
		{
			$db					= JFactory::getDbo();
			$user				= JFactory::getUser();
			$query 				= $db->getQuery();
			$currentDate		= JFactory::getDate();
			$helperPath = JPATH_ADMINISTRATOR . '/components/com_redslider/helpers/helper.php';

			$db					= JFactory::getDbo();
			$user				= JFactory::getUser();
			$query 				= $db->getQuery();
			$currentDate		= JFactory::getDate();

			if (JFile::exists($helperPath))
			{
				require_once $helperPath;

				$comExists = RedsliderHelperHelper::checkExtension('redform');

				// Add Include path
				JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_redslider/tables');
				/*
				 * Insert demo template for redFORM section
				 */
				$templateTable = JTable::getInstance('Template', 'RedsliderTable', array('ignore_request' => true));
				$templateTable->id = null;
				$templateTable->title = 'Template redFORM';
				$templateTable->section = 'SECTION_REDFORM';
				$templateTable->content = '<div class="cont-side">
												<table width="100%" cellspacing="5px" cellpadding="10px" align="center">
													<tbody>
														<tr>
															<td colspan="2" align="left">
																<h3>{redform_title}</h3>
															</td>
														</tr>
														<tr>
															<td><input class="textin" type="text" /></td>
															<td><input class="textin" type="text" /></td>
														</tr>
														<tr>
															<td colspan="2"><input class="textfield" type="text" /></td>
														</tr>
														<tr>
															<td colspan="2" align="left">{redform}1{/redform}</td>
														</tr>
													</tbody>
												</table>
											</div>
											<div class="bg-redform"><img src="images/stories/redslider/redform-backg.png" alt="" /></div>';
				$templateTable->published = $comExists? 1 : 0;
				$templateTable->store();
				$templateId = (int) $templateTable->id;

				// Prepare params for demo redFORM slide
				$slideParams = array(
					"form_id" => 1,
					"background_image" => "images/stories/redslider/bg_redform.png",
					"slide_class" => "reform_slide"
				);

				$slideParams = new JRegistry($slideParams);

				/*
				 * Insert demo slide for redFORM section
				 */
				$slideTable = JTable::getInstance('Slide', 'RedsliderTable', array('ignore_request' => true));
				$slideTable->gallery_id = 1;
				$slideTable->template_id = $templateId;
				$slideTable->title = 'Sample redFORM';
				$slideTable->section = 'SECTION_REDFORM';

				$slideTable->published = $comExists? 1 : 0;
				$slideTable->params = $slideParams->toString();
				$slideTable->store();

				unset($templateTable);
				unset($slideTable);

				// Set this plugin published
				$query = $db->getQuery(true);

				$query->update($db->qn("#__extensions"))
					->set($db->qn('enabled') . ' = 1')
					->where($db->qn('element') . ' = ' . $db->q('section_redform') . ' AND ' . $db->qn('folder') . ' = ' . $db->q('redslider_sections'));
				$db->setQuery($query);
				$db->execute();
			}
		}

		return true;
	}
}
