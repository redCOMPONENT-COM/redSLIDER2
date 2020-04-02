<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * The gallery edit controller
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Controller.gallery
 * @since       2.0
 */
class RedsliderControllerGallery extends RControllerForm
{
	/**
	 * For edit an gallery
	 *
	 * @param   int    $key    Gallery key to edit
	 * @param   string $urlVar Url variables
	 *
	 * @return  boolean
	 * @throws  Exception
	 */
	public function edit($key = null, $urlVar = null)
	{
		$itemModel = RModel::getAdminInstance('Gallery');
		$item      = $itemModel->getItem();

		$app = JFactory::getApplication();
		$app->setUserState('com_redslider.global.gid', array($item->id));

		return parent::edit($key, $urlVar);
	}
}
