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
		$slidesModel->setState('list.ordering', 's.id');
		$slidesModel->setState('list.direction', 'desc');

		$slides = $slidesModel->getItems();

		if (count($slides))
		{
			$dispatcher = JDispatcher::getInstance();
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
						$slide->template_content = $replacedContent[0];
					}
				}
			}

			$result = $slides;
		}

		return $result;
	}
}
