<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Helpers
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedSLIDER CustomFields Helper
 *
 * @package     RedSLIDER.Component
 * @subpackage  Helpers.CusomHelper
 * @since       2.0
 *
 */
class RedsliderHelper
{
	/**
	 * Cached galleries
	 *
	 * @var  array
	 *
	 * @since  2.0.44
	 */
	public static $galleries = array();

	/**
	 * Cached templates
	 *
	 * @var  array
	 *
	 * @since  2.0.44
	 */
	public static $templates = array();

	/**
	 * Function check is extension installed
	 *
	 * @param   string $extension extension's name, ex: com_sample
	 *
	 * @return boolean
	 */
	public static function checkExtension($extension)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->qn('enabled'))
			->from($db->qn('#__extensions'))
			->where($db->qn('name') . ' = ' . $db->q($extension));

		$db->setQuery($query);

		$result = $db->loadObject();

		return $result && $result->enabled;
	}

	/**
	 * Get slides of gallery
	 *
	 * @param   int $galleryId Gallery ID
	 *
	 * @return  array of object
	 */
	public static function getSlides($galleryId = 0)
	{
		if (!$galleryId)
		{
			return array();
		}

		if (isset(static::$galleries[$galleryId]))
		{
			return static::$galleries[$galleryId];
		}

		$slidesModel = RModel::getAdminInstance('Slides', array('ignore_request' => true), 'com_redslider');
		$slidesModel->setState('filter.published', 1);
		$slidesModel->setState('filter.gallery_id', $galleryId);
		$slidesModel->setState('list.ordering', 's.ordering');
		$slidesModel->setState('list.direction', 'asc');
		$slides = $slidesModel->getItems();

		if (empty($slides))
		{
			static::$galleries[$galleryId] = array();

			return static::$galleries[$galleryId];
		}

		$dispatcher = RFactory::getDispatcher();
		JPluginHelper::importPlugin('redslider_sections');

		foreach ($slides as &$slide)
		{
			$templateId = $slide->template_id;

			if (!isset(static::$templates[$templateId]))
			{
				$templateModel                  = RModel::getAdminInstance('Template', array('ignore_request' => true), 'com_redslider');
				static::$templates[$templateId] = $templateModel->getItem($templateId);
			}

			$template = static::$templates[$templateId];

			if (is_null($template) || $template->published != '1')
			{
				continue;
			}

			$replacedContent = $dispatcher->trigger('onPrepareTemplateContent', array($template->content, &$slide));

			if (count($replacedContent))
			{
				$slide->template_content = JHtml::_('content.prepare', $replacedContent[0]);
			}
		}

		static::$galleries[$galleryId] = $slides;

		return static::$galleries[$galleryId];
	}

	/**
	 * Replace tags for HTML content
	 *
	 * @param   string $match         tag search string (maybe include HTML tags)
	 * @param   string $replaceString replaceString
	 * @param   string $content       content string
	 *
	 * @return  string  $content
	 */
	public static function replaceTagsHTML($match, $replaceString, $content)
	{
		$middleMan = strip_tags($match);

		$middleMan = JString::str_ireplace("{", "", $middleMan);
		$middleMan = JString::str_ireplace("}", "", $middleMan);
		$middleMan = explode("|", $middleMan);

		if (count($middleMan) > 1)
		{
			if (is_numeric($middleMan[1]))
			{
				$limit = (int) $middleMan[1];

				$replaceString = JHtml::_('string.truncate', $replaceString, $limit, false, false);
			}
		}

		$content = JString::str_ireplace($match, $replaceString, $content);

		return $content;
	}

	/**
	 * Method for get extension
	 *
	 * @param   string $element Element name of extension (ex: com_reditem)
	 * @param   string $type    Type of extension (component, plugin, module)
	 *
	 * @return  boolean/object  Extension of object. False otherwise.
	 */
	public static function getExtension($element, $type = 'component')
	{
		if (empty($element))
		{
			return false;
		}

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->qn(array('e.extension_id', 'e.name', 'e.enabled')))
			->from($db->qn('#__extensions', 'e'))
			->where($db->qn('e.type') . ' = ' . $db->quote($type))
			->where($db->qn('e.element') . ' = ' . $db->quote($element));
		$db->setQuery($query);

		$extension = $db->loadObject();

		if (!$extension)
		{
			return false;
		}

		return $extension;
	}
}
