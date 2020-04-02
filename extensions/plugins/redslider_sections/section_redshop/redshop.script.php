<?php
/**
 * @package    RedSLIDER.Installer
 *
 * @copyright  Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
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
class PlgRedslider_SectionsSection_RedshopInstallerScript extends Com_RedcoreInstallerScript
{
	/**
	 * @var string
	 */
	private $section = 'SECTION_REDSHOP';

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

				$comExists = RedsliderHelper::checkExtension('com_redshop');

				$db = JFactory::getDbo();

				// Add Include path
				JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_redslider/tables');

				/*
				 * Insert demo template for redSHOP section
				 */
				$templateTable            = JTable::getInstance('Template', 'RedsliderTable', array('ignore_request' => true));
				$templateTable->id        = null;
				$templateTable->title     = 'Template redSHOP';
				$templateTable->section   = $this->section;
				$templateTable->published = $comExists ? 1 : 0;
				$templateTable->content   = '<div class="eachSlide">
											<div class="prod-show">
												<div class="slideImg">{product_image}</div>
												<div class="slidePrice">
													<img src="media/com_redslider/images/prod_price.png" border="0" />
													<h3>{product_price}</h3>
												</div>
										 	</div>
											<div class="prod-detail">
												<div class="slideTitle">
												<h3>{product_name}</h3>
											</div>
											<div class="slideText">{product_short_description}</div>
												<div class="slideAttribute">{attribute_template:attributes}</div>
												<div class="slideForm">{form_addtocart:add_to_cart2}</div>
											</div>
										 </div>';
				$templateTable->store();
				$templateId = (int) $templateTable->id;

				// Prepare params for demo redSHOP slide
				$slideParams = array(
					'product_id'       => 1,
					'background_image' => 'images/stories/redslider/bg_redshop_slider.png',
					'slide_class'      => 'redshop_slide'
				);

				$slideParams = new JRegistry($slideParams);

				/*
				 * Insert demo slide for redSHOP section
				 */
				$slideTable              = JTable::getInstance('Slide', 'RedsliderTable', array('ignore_request' => true));
				$slideTable->gallery_id  = 1;
				$slideTable->template_id = $templateId;
				$slideTable->title       = 'Sample redSHOP';
				$slideTable->section     = 'SECTION_REDSHOP';
				$slideTable->published   = $comExists ? 1 : 0;
				$slideTable->params      = $slideParams->toString();
				$slideTable->store();

				unset($templateTable, $slideTable);

				// Set this plugin published
				$query = $db->getQuery(true);

				$query->update($db->qn("#__extensions"))
					->set($db->qn('enabled') . ' = 1')
					->where(
						$db->qn('element') . ' = ' . $db->q('section_redshop')
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
		$db->setQuery($query);
		$db->execute();

		// Remove all templates which belong to this section
		$query = $db->getQuery(true)
			->delete($db->qn('#__redslider_templates'))
			->where($db->qn('section') . '=' . $db->q($this->section));
		$db->setQuery($query);
		$db->execute();

		parent::uninstall($parent);
	}
}
