<?php
/**
 * @package     RedSlider
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Aesir\Dam\Entity\Asset;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

JLoader::import('aesir_dam.library');

/**
 * Class PlgRedslider_AddonsAesir_Dam
 * @since 2.0.0
 */
class PlgRedslider_AddonsAesir_Dam extends CMSPlugin
{
	/**
	 * @var boolean
	 */
	protected $autoloadLanguage = true;

	/**
	 * @param   string  $content  Content
	 * @param   object  $slide    Slide
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function onPrepareTemplateContent($content, $slide)
	{
		$params = new Registry($slide->params);

		if (!empty($slide->background))
		{
			$slide->backgroundImagePath = Uri::base() . $slide->background;
			$slide->backgroundImage     = HTMLHelper::image($slide->backgroundImagePath, '');
		}
		else
		{
			$slide->backgroundImagePath = '';
			$slide->backgroundImage     = '';
		}

		if ($params->get('file_source') !== 'dam'
			|| empty($params->get('dam')))
		{
			return;
		}

		$dam = json_decode($params->get('dam'), true);

		if (empty($dam[0]['id']))
		{
			return;
		}

		$dam = Asset::load($dam[0]['id']);

		if (!$dam->isLoaded()
			|| !$dam->isWebImage())
		{
			return;
		}

		$slide->backgroundImage     = $dam->renderImage();
		$slide->backgroundImagePath = $dam->getImageUrlByApproxWidth();
		$slide->dam                 = $dam;
	}

	/**
	 * @param   Form   $form  Form
	 * @param   array  $data  Data
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function onRedSliderAfterContentPrepareForm(Form $form, $data = [])
	{
		Form::addFieldPath(__DIR__ . '/form/field');

		$target = $form->getFieldXml('background_image', 'params');

		if (!$target instanceof SimpleXMLElement)
		{
			return;
		}

		$target->addAttribute('showon', 'file_source:original');

		$field = new SimpleXMLElement('<field />');

		$field->addAttribute('name', 'file_source');
		$field->addAttribute('type', 'list');
		$field->addAttribute('label', 'PLG_REDSLIDER_ADDONS_AESIR_DAM_BACKGROUND_SOURCE');
		$field->addAttribute('default', 'dam');

		$option = $field->addChild('option', 'PLG_REDSLIDER_ADDONS_AESIR_DAM_BACKGROUND_SOURCE_LOCAL');
		$option->addAttribute('value', 'original');

		$option = $field->addChild('option', 'PLG_REDSLIDER_ADDONS_AESIR_DAM_BACKGROUND_SOURCE_DAM');
		$option->addAttribute('value', 'dam');

		$this->simpleXMLInsert($field, $target, 'before');

		$field = new SimpleXMLElement('<field />');

		$field->addAttribute('name', 'dam');
		$field->addAttribute('showon', 'file_source:dam');
		$field->addAttribute('type', 'aesir_dam_asset');
		$field->addAttribute('label', 'COM_REDSLIDER_STANDARD_IMAGE_FILE');
		$field->addAttribute('description', 'COM_REDSLIDER_STANDARD_IMAGE_FILE_DESC');

		$this->simpleXmlInsert($field, $target, 'after');

		if (empty($data['params']['dam'])
			&& empty($data['params']['file_source']))
		{
			$form->setValue('file_source', 'params', 'original');
		}
	}

	/**
	 * @param   SimpleXMLElement  $insert    Insert
	 * @param   SimpleXMLElement  $target    Target
	 * @param   string            $position  Position
	 *
	 * @return void
	 * @since 2.0.0
	 */
	protected function simpleXMLInsert(SimpleXMLElement $insert, SimpleXMLElement $target, $position = 'after')
	{
		$targetDom = dom_import_simplexml($target);
		$insertDom = $targetDom->ownerDocument->importNode(dom_import_simplexml($insert), true);

		if ($position == 'after')
		{
			$targetDom->parentNode->insertBefore($insertDom, $targetDom->nextSibling);
		}
		else
		{
			$targetDom->parentNode->insertBefore($insertDom, $targetDom);
		}
	}
}
