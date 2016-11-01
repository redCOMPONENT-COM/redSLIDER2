<?php
/**
 * @package     RedSLIDER.Frontend
 * @subpackage  mod_redslider
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Module redSLIDER Related Items helper
 *
 * @since  1.0
 */
class ModredSLIDERHelper
{
	/**
	 * Get slides of gallery
	 *
	 * @param   int  $galleryId  Gallery ID
	 *
	 * @return  array of object
	 */
	public static function getSlides($galleryId)
	{
		if (!$galleryId)
		{
			return false;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('s.*')
			->select($db->qn('g.title', 'gallery_title'))
			->select($db->qn('t.title', 'template_title'))
			->select($db->qn('t.content', 'template_content'))
			->from($db->qn('#__redslider_slides', 's'))
			->leftJoin($db->qn('#__redslider_galleries', 'g') . ' ON ' . $db->qn('s.gallery_id') . ' = ' . $db->qn('g.id'))
			->leftJoin($db->qn('#__redslider_templates', 't') . ' ON ' . $db->qn('s.template_id') . ' = ' . $db->qn('t.id'))
			->where($db->qn('s.published') . ' = 1')
			->where($db->qn('s.gallery_id') . ' = ' . $galleryId)
			->order($db->qn('s.ordering') . ' ASC');

		if (JLanguageMultilang::isEnabled())
		{
			$query->where($db->qn('s.language') . ' IN (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		$slides = $db->setQuery($query)->loadObjectList();

		if (!$slides)
		{
			return array();
		}

		$dispatcher = RFactory::getDispatcher();
		JPluginHelper::importPlugin('redslider_sections');

		foreach ($slides as $slide)
		{
			$results = $dispatcher->trigger('onPrepareTemplateContent', array($slide->template_content, &$slide));
			$results = array_values(array_filter($results));
			$templateContent = '';

			if (!empty($results))
			{
				$templateContent = JHtml::_('content.prepare', $results[0]);
			}

			$slide->template_content = $templateContent;

			$params = new Registry($slide->params);

			$slide->background = '';
			$slide->class = '';
			$background = $params->get('background_image');

			if (JFile::exists($background))
			{
				$slide->background = $background;
			}
			else
			{
				$slide->background = 'images/stories/redslider/bg_general.png';
			}

			if ($class = $params->get('slide_class'))
			{
				$slide->class = $class;
			}
		}

		return $slides;
	}
}
