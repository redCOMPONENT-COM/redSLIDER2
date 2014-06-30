<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Helpers
 *
 * @copyright   Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
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
class RedsliderHelperHelper
{
	/**
	 * Function check is extension installed
	 *
	 * @param   string  $extension  extension's name, ex: com_sample
	 *
	 * @return boolean
	 */
	public static function checkExtension($extension)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->qn('enabled'))
			->from($db->qn('#__extensions'))
			->where($db->qn('name') . ' = ' . $db->q($extension));

		$db->setQuery($query);

		$result = $db->loadObject();

		if (isset($result) && $result->enabled)
		{
			return true;
		}

		return false;
	}

	/**
	 * Get slides of gallery
	 *
	 * @param   int  $galleryId  Gallery ID
	 *
	 * @return  array of object
	 */
	public static function getSlides($galleryId = 0)
	{
		$result = array();

		$slidesModel = RModel::getAdminInstance('Slides', array('ignore_request' => true), 'com_redslider');
		$slidesModel->setState('filter.published', 1);
		$slidesModel->setState('filter.gallery_id', $galleryId);
		$slidesModel->setState('list.ordering', 's.ordering');
		$slidesModel->setState('list.direction', 'asc');

		$slides = $slidesModel->getItems();

		if (count($slides))
		{
			$dispatcher = RFactory::getDispatcher();
			JPluginHelper::importPlugin('redslider_sections');

			foreach ($slides as &$slide)
			{
				$templateModel = RModel::getAdminInstance('Templates', array('ignore_request' => true), 'com_redslider');
				$templateModel->setState('filter.published', 1);
				$templateModel->setState('t.id', $slide->template_id);

				$template = $templateModel->getItems();

				if (count($template))
				{
					$replacedContent = $dispatcher->trigger('onPrepareTemplateContent', array($template[0]->content, &$slide));

					if (count($replacedContent))
					{
						$slide->template_content = JHtml::_('content.prepare', $replacedContent[0]);
					}
				}
			}

			$result = $slides;
		}

		return $result;
	}

	/**
	 * Replace tags for HTML content
	 *
	 * @param   string  $match          tag search string (maybe include HTML tags)
	 * @param   string  $replaceString  replaceString
	 * @param   string  $content        content string
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
}
