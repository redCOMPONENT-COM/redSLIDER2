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
}
