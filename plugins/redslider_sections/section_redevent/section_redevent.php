<?php
/**
 * @package     RedSlider
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('redcore.bootstrap');

/**
 * Plugins RedSLIDER section redevent
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Redevent extends JPlugin
{
	private $sectionId;

	private $sectionName;

	/**
	 * Constructor - note in Joomla 2.5 PHP4.x is no longer supported so we can use this.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		$this->sectionId = "SECTION_REDEVENT";
		$this->sectionName = "redEVENT";
	}

	/**
	 * Get section name
	 *
	 * @return  array
	 */
	public function getSectionName()
	{
		$section = new stdClass;
		$section->id = $this->sectionId;
		$section->name = $this->sectionName;

		return $section;
	}

	/**
	 * Get section's tags name
	 *
	 * @param   string  $sectionId  Section's ID
	 *
	 * @return  void/array
	 */
	public function getTagNames($sectionId)
	{
		if ($sectionId === $this->sectionId)
		{
			$tags = array(
					"{event_name}" => JText::_("COM_REDSLIDER_TAG_REDEVENT_TITLE_DESC"),
					"{event_description}" => JText::_("COM_REDSLIDER_TAG_REDEVENT_DESCRIPTION_DESC"),
					"{event_button}" => JText::_("COM_REDSLIDER_TAG_REDEVENT_BUTTON_DESC"),
					"{session_title}" => JText::_("COM_REDSLIDER_TAG_REDEVENT_SECTION_TITLE_DESC"),
					"{session_description}" => JText::_("COM_REDSLIDER_TAG_REDEVENT_SECTION_DESCRIPTION_DESC"),
				);

			return $tags;
		}
	}
}
