<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Welcome View
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Views
 * @since       2.0
 */
class RedsliderViewWelcome extends RedsliderView
{
	/**
	 * Do we have to display a sidebar ?
	 *
	 * @var  boolean
	 */
	protected $displaySidebar = false;

	/**
	 * Display the welcome page
	 *
	 * @param   string $tpl The template file to use
	 *
	 * @return  void
	 * @throws  Exception
	 *
	 * @since   2.0
	 */
	public function display($tpl = null)
	{
		$this->redsliderversion = $this->get('Version');
		$this->installationType = JFactory::getApplication()->input->getString('type', '');

		parent::display($tpl);
	}
}
