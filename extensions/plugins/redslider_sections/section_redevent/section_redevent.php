<?php
/**
 * @package     RedSlider
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Redslider\Plugin\AbstractRedsliderSection;

defined('_JEXEC') or die;

JLoader::import('redslider.library');

/**
 * Plugins RedSLIDER section redevent
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Redevent extends AbstractRedsliderSection
{
	/**
	 * @var string
	 */
	protected $sectionId = 'SECTION_REDEVENT';

	/**
	 * @var string
	 */
	protected $extensionName = 'com_redevent';

	protected $formName = 'fields_redevent';

	protected $templateName = 'redevent';

	/**
	 * Constructor - note in Joomla 2.5 PHP4.x is no longer supported so we can use this.
	 *
	 * @param   object $subject The object to observe
	 * @param   array  $config  An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->sectionName = Text::_('PLG_SECTION_REDEVENT_NAME');
	}

	/**
	 * Get section's tags name
	 *
	 * @param   string  $sectionId  Section's ID
	 *
	 * @return  array
	 */
	public function getTagNamesa($sectionId)
	{
		if ($sectionId === $this->sectionId)
		{
			$tags = array(
				'[event_description]'     => '<br>',
				'[event_title]'           => '<br>',
				'[price]'                 => '<br>',
				'[credits]'               => '<br>',
				'[code]'                  => '<br>',
				'[redform]'               => '<br>',
				'[inputname]'             => '<br>',
				'[inputemail]'            => '<br>',
				'[submit]'                => '<br>',
				'[event_info_text]'       => '<br>',
				'[time]'                  => '<br>',
				'[date]'                  => '<br>',
				'[duration]'              => '<br>',
				'[venue]'                 => '<br>',
				'[city]'                  => '<br>',
				'[eventplaces]'           => '<br>',
				'[waitinglistplaces]'     => '<br>',
				'[eventplacesleft]'       => '<br>',
				'[waitinglistplacesleft]' => '<br>',
				'[paymentrequest]'        => '<br>',
				'[paymentrequestlink]'    => ''
			);

			return $tags;
		}

		return array();
	}

	/**
	 * Prepare content for slide show in module
	 *
	 * @param   string $content Template Content
	 * @param   object $slide   Slide result object
	 *
	 * @return  string  $content  repaced content
	 */
	public function onPrepareTemplateContenta($content, $slide)
	{
		// Check if we need to load component's CSS or not
		$useOwnCSS = ComponentHelper::getParams('com_redslider')->get('use_own_css', '0');

		// Load redEVENT & redFORM language file
		Factory::getLanguage()->load('com_redevent');
		Factory::getLanguage()->load('com_redform');

		if ($slide->section === $this->sectionId)
		{
			if (RedsliderHelper::checkExtension($this->extensionName))
			{
				require_once JPATH_LIBRARIES . '/redevent/tags/tags.php';
				require_once JPATH_LIBRARIES . '/redevent/tags/parsed.php';
				require_once JPATH_LIBRARIES . '/redevent/helper/helper.php';
				require_once JPATH_LIBRARIES . '/redevent/helper/attachment.php';
				require_once JPATH_LIBRARIES . '/redevent/helper/output.php';
				require_once JPATH_LIBRARIES . '/redevent/user/acl.php';
				require_once JPATH_LIBRARIES . '/redform/core/model/form.php';
				require_once JPATH_LIBRARIES . '/redform/core/core.php';

				// Load stylesheet for each section
				$css = 'redslider.' . StringHelper::strtolower($this->sectionId) . '.min.css';

				if (!$useOwnCSS)
				{
					RHelperAsset::load($css, 'redslider_sections/' . StringHelper::strtolower($this->sectionId));
				}

				$params  = new Registry($slide->params);
				$eventId = (int) $params->get('event_id', 0);
				$tags    = new RedeventTags;
				$tags->setEventId($eventId);
				$content = $tags->ReplaceTags($content);
			}

			return $content;
		}
	}
}
