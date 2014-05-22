<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * The template edit controller
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Controller.template
 * @since       2.0
 */
class RedsliderControllerTemplate extends RControllerForm
{
	/**
	 * For edit an template
	 *
	 * @param   int     $key     [description]
	 * @param   string  $urlVar  [description]
	 *
	 * @return void
	 */
	public function edit($key = null, $urlVar = null)
	{
		$itemmodel = RModel::getAdminInstance('Template');

		$item = $itemmodel->getItem();

		$app = JFactory::getApplication();
		$app->setUserState('com_redslider.global.tid', array($item->id));

		return parent::edit($key, $urlVar);
	}
}
