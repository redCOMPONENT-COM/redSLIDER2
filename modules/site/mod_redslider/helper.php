<?php
/**
 * @package     RedSLIDER.Frontend
 * @subpackage  mod_redslider
 *
 * @copyright   Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once 'administrator/components/com_redslider/helpers/helper.php';

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
		return RedsliderHelperHelper::getSlides($galleryId);
	}

	/**
	 * Generate HTML of sliders to show in frontend
	 *
	 * @param   object  $slides  result set of slides
	 * @param   string  $class   CSS Class
	 *
	 * @return  string  html
	 */

	public static function getHTML($slides, $class)
	{
		JHtml::_('rjquery.flexslider', '.' . $class);

		$html = array();

		$html[] = '<div class="' . $class . '">';

		if (count($slides))
		{
			$html[] = '<ul class="slides">';

			foreach ($slides as $slide)
			{
				if (isset($slide->template_content))
				{
					$html[] = '<li>' . $slide->template_content . '</li>';
				}
			}

			$html[] = '</ul>';
		}

		return implode("\n", $html);
	}
}
