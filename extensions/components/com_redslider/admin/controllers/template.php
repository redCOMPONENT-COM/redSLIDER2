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
	 * @param   int    $key    [description]
	 * @param   string $urlVar [description]
	 *
	 * @return  boolean
	 * @throws  Exception
	 */
	public function edit($key = null, $urlVar = null)
	{
		$app       = JFactory::getApplication();
		$itemModel = RModel::getAdminInstance('Template');

		$item = $itemModel->getItem();
		$app->setUserState('com_redslider.global.template.section', $item->section);

		$app = JFactory::getApplication();
		$app->setUserState('com_redslider.global.tid', array($item->id));

		return parent::edit($key, $urlVar);
	}

	/**
	 * Function set Section
	 *
	 * @return  void
	 * @throws  Exception
	 */
	public function setSection()
	{
		$app      = JFactory::getApplication();
		$recordId = $app->input->get('id', 0, 'int');
		$data     = $app->input->get('jform', array(), 'array');

		$app->setUserState('com_redslider.edit.template.data', $data);
		$app->setUserState('com_redslider.global.template.section', $data['section']);

		$redirect = JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false);

		$this->setRedirect($redirect);
	}
}
