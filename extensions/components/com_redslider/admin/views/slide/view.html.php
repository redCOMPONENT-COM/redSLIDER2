<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_redslider/helpers/helper.php';

/**
 * Slide edit view
 *
 * @package     RedSLIDER.Backend
 * @subpackage  View
 * @since       2.0.0
 */
class RedsliderViewSlide extends RedsliderView
{
	/**
	 * @var  boolean
	 */
	protected $displaySidebar = false;

	public $basicFields = [];

	public $outputFields = [];

	/**
	 * Display the slide edit page
	 *
	 * @param   string $tpl The template file to use
	 *
	 * @return  void
	 * @throws  Exception
	 *
	 * @since   2.0.0
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$this->form      = $this->get('Form');
		$this->item      = $this->get('Item');
		$this->tags      = $this->get('Tags');
		$this->sectionId = $app->getUserState('com_redslider.global.slide.section', '');

		// Display the slide
		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		return JText::_('COM_REDSLIDER_SLIDE');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @todo    We have setup ACL requirements for redSLIDER
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$group = new RToolbarButtonGroup;

		$save         = RToolbarBuilder::createSaveButton('slide.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('slide.save');
		$saveAndNew   = RToolbarBuilder::createSaveAndNewButton('slide.save2new');
		$save2Copy    = RToolbarBuilder::createSaveAsCopyButton('slide.save2copy');

		$group->addButton($save)
			->addButton($saveAndClose)
			->addButton($saveAndNew)
			->addButton($save2Copy);

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('slide.cancel');
		}
		else
		{
			$cancel = RToolbarBuilder::createCloseButton('slide.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
