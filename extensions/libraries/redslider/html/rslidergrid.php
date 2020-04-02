<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Html
 *
 * @copyright   Copyright (C) 2016 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;

require_once JPATH_REDCORE . '/html/rgrid.php';

/**
 * Utility class for creating HTML Grids.
 *
 * @package     RedITEM.Libraries
 * @subpackage  Html
 * @since       3.0
 */
abstract class JHtmlRslidergrid extends JHtmlRgrid
{
	/**
	 * Returns an action on a grid
	 *
	 * @param   integer       $index           The row index
	 * @param   string        $task            The task to fire
	 * @param   string|array  $prefix          An optional task prefix or an array of options
	 * @param   string        $text            An optional text to display
	 * @param   string        $activeTitle     An optional active tooltip to display if $enable is true
	 * @param   string        $inactiveTitle   An optional inactive tooltip to display if $enable is true
	 * @param   boolean       $tip             An optional setting for tooltip
	 * @param   string        $activeClass     An optional active HTML class
	 * @param   string        $inactiveClass   An optional inactive HTML class
	 * @param   boolean       $enabled         An optional setting for access control on the action.
	 * @param   boolean       $translate       An optional setting for translation.
	 * @param   string        $checkbox	       An optional prefix for checkboxes.
	 * @param   string        $formId          An optional form id
	 * @param   string        $buttonClass     An optional button class
	 *
	 * @return  string         The Html code
	 */
	public static function action($index, $task, $prefix = '', $text = '', $activeTitle = '', $inactiveTitle = '',
		$tip = false, $activeClass = '',$inactiveClass = '',
		$enabled = true, $translate = true, $checkbox = 'cb', $formId = 'adminForm', $buttonClass = ''
	)
	{
		if (is_array($prefix))
		{
			$options       = $prefix;
			$activeTitle   = array_key_exists('active_title', $options) ? $options['active_title'] : $activeTitle;
			$inactiveTitle = array_key_exists('inactive_title', $options) ? $options['inactive_title'] : $inactiveTitle;
			$tip           = array_key_exists('tip', $options) ? $options['tip'] : $tip;
			$activeClass   = array_key_exists('active_class', $options) ? $options['active_class'] : $activeClass;
			$inactiveClass = array_key_exists('inactive_class', $options) ? $options['inactive_class'] : $inactiveClass;
			$enabled       = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$translate     = array_key_exists('translate', $options) ? $options['translate'] : $translate;
			$checkbox      = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$formId        = array_key_exists('formId', $options) ? $options['formId'] : $formId;
			$buttonClass   = array_key_exists('buttonClass', $options) ? $options['buttonClass'] : $buttonClass;
			$prefix        = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}

		if ($tip)
		{
			HTMLHelper::_('rbootstrap.tooltip');
		}

		if ($enabled)
		{
			// Prepare the class.
			if ($activeClass === 'plus')
			{
				$buttonClass = 'published';
			}

			elseif ($activeClass === 'minus')
			{
				$buttonClass = 'unpublished';
			}

			$buttonClass .= $tip ? ' hasTooltip' : '';

			$html[] = '<a class="btn btn-default btn-small btn-sm ' . $buttonClass . '"';
			$html[] = ' href="javascript:void(0);" onclick="return listItemTaskForm(\'' . $checkbox . $index . '\',\''
				. $prefix . $task . '\',\'' . $formId . '\')"';
			$html[] = ' title="' . addslashes(htmlspecialchars($translate ? Text::_($activeTitle) : $activeTitle, ENT_COMPAT, 'UTF-8')) . '">';
			$html[] = '<i class="icon-' . $activeClass . '">';
			$html[] = '</i>';
			$html[] = '</a>';
		}
		else
		{
			$html[] = '<a class="btn btn-default btn-micro disabled jgrid ' . $buttonClass . ' ' . ($tip ? 'hasTooltip' : '') . '" ';
			$html[] = ' title="' . addslashes(htmlspecialchars($translate ? Text::_($inactiveTitle) : $inactiveTitle, ENT_COMPAT, 'UTF-8')) . '">';

			if ($activeClass == "protected")
			{
				$html[] = '<i class="icon-lock"></i>';
			}
			else
			{
				$html[] = '<i class="icon-' . $inactiveClass . '"></i>';
			}

			$html[] = '</a>';
		}

		return implode($html);
	}

	/**
	 * Returns a state on a grid
	 *
	 * @param   array         $states     array of value/state. Each state is an array of the form
	 *                                    (task, text, title,html active class, HTML inactive class)
	 *                                    or ('task'=>task, 'text'=>text, 'active_title'=>active title,
	 *                                    'inactive_title'=>inactive title, 'tip'=>boolean, 'active_class'=>html active class,
	 *                                    'inactive_class'=>html inactive class)
	 * @param   integer       $value      The state value.
	 * @param   integer       $index      The row index
	 * @param   string|array  $prefix     An optional task prefix or an array of options
	 * @param   boolean       $enabled    An optional setting for access control on the action.
	 * @param   boolean       $translate  An optional setting for translation.
	 * @param   string        $checkbox   An optional prefix for checkboxes.
	 * @param   string        $formId     An optional form id
	 *
	 * @return  string       The Html code
	 */
	public static function state($states, $value, $index, $prefix = '',
		$enabled = true, $translate = true, $checkbox = 'cb', $formId = 'adminForm'
	)
	{
		if (is_array($prefix))
		{
			$options   = $prefix;
			$enabled   = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$translate = array_key_exists('translate', $options) ? $options['translate'] : $translate;
			$checkbox  = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix    = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}

		$state         = ArrayHelper::getValue($states, (int) $value, $states[0]);
		$task          = array_key_exists('task', $state) ? $state['task'] : $state[0];
		$text          = array_key_exists('text', $state) ? $state['text'] : (array_key_exists(1, $state) ? $state[1] : '');
		$activeTitle   = array_key_exists('active_title', $state) ? $state['active_title'] : (array_key_exists(2, $state) ? $state[2] : '');
		$inactiveTitle = array_key_exists('inactive_title', $state) ? $state['inactive_title'] : (array_key_exists(3, $state) ? $state[3] : '');
		$tip           = array_key_exists('tip', $state) ? $state['tip'] : (array_key_exists(4, $state) ? $state[4] : false);
		$activeClass   = array_key_exists('active_class', $state) ? $state['active_class'] : (array_key_exists(5, $state) ? $state[5] : '');
		$inactiveClass = array_key_exists('inactive_class', $state) ? $state['inactive_class'] : (array_key_exists(6, $state) ? $state[6] : '');

		return self::action(
			$index, $task, $prefix, $text, $activeTitle, $inactiveTitle, $tip,
			$activeClass, $inactiveClass, $enabled, $translate, $checkbox, $formId
		);
	}

	/**
	 * Returns a published state on a grid
	 *
	 * @param   integer       $value         The state value.
	 * @param   integer       $index         The row index
	 * @param   string|array  $prefix        An optional task prefix or an array of options
	 * @param   boolean       $enabled       An optional setting for access control on the action.
	 * @param   string        $checkbox      An optional prefix for checkboxes.
	 * @param   string        $publishUp     An optional start publishing date.
	 * @param   string        $publishDown   An optional finish publishing date.
	 * @param   string        $formId        An optional form id
	 *
	 * @return  string  The Html code
	 */
	public static function splitstate($value, $index, $prefix = '', $enabled = true, $checkbox = 'cb', $publishUp = null, $publishDown = null, $formId = 'adminForm')
	{
		if (is_array($prefix))
		{
			$options  = $prefix;
			$enabled  = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix   = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}

		$states = array(
			1 => array('unpublish', 'LIB_REDITEM_STARTED', '', 'LIB_REDITEM_STARTED', false, 'fa fa-pause', 'fa fa-pause'),
			0 => array('publish', 'LIB_REDITEM_STOPPED', '', 'LIB_REDITEM_STOPPED', false, 'fa fa-play', 'fa fa-play')
		);

		// Special state for dates
		if ($publishUp || $publishDown)
		{
			$nullDate = Factory::getDbo()->getNullDate();
			$nowDate  = Factory::getDate()->toUnix();

			$tz          = new DateTimeZone(Factory::getUser()->getParam('timezone', Factory::getConfig()->get('offset')));
			$publishUp   = ($publishUp != $nullDate) ? Factory::getDate($publishUp, 'UTC')->setTimeZone($tz) : false;
			$publishDown = ($publishDown != $nullDate) ? Factory::getDate($publishDown, 'UTC')->setTimeZone($tz) : false;

			// Create tip text, only we have publish up or down settings
			$tips = array();

			if ($publishUp != $nullDate && $publishUp != false)
			{
				$tips[] = Text::sprintf('JLIB_HTML_PUBLISHED_START', $publishUp->format(Date::$format, true));
			}

			if ($publishDown != $nullDate && $publishDown != false)
			{
				$tips[] = Text::sprintf('JLIB_HTML_PUBLISHED_FINISHED', $publishDown->format(Date::$format, true));
			}

			$tip = empty($tips) ? false : implode('<br/>', $tips);

			// Add tips and special titles
			foreach ($states as $key => $state)
			{
				// Create special titles for published items
				if ($key == 1)
				{
					$states[$key][3] = 'JLIB_HTML_PUBLISHED_ITEM';
					$states[$key][3] = 'JLIB_HTML_PUBLISHED_ITEM';

					if ($publishUp > $nullDate && $nowDate < $publishUp->toUnix())
					{
						$states[$key][2] = 'JLIB_HTML_PUBLISHED_PENDING_ITEM';
						$states[$key][3] = 'JLIB_HTML_PUBLISHED_PENDING_ITEM';
						$states[$key][5] = 'pending';
						$states[$key][6] = 'pending';
					}

					if ($publishDown > $nullDate && $nowDate > $publishDown->toUnix())
					{
						$states[$key][2] = 'JLIB_HTML_PUBLISHED_EXPIRED_ITEM';
						$states[$key][3] = 'JLIB_HTML_PUBLISHED_EXPIRED_ITEM';
						$states[$key][5] = 'expired';
						$states[$key][6] = 'expired';
					}
				}

				// Add tips to titles
				if ($tip)
				{
					$states[$key][1] = Text::_($states[$key][1]);
					$states[$key][2] = Text::_($states[$key][2]) . '::' . $tip;
					$states[$key][3] = Text::_($states[$key][3]) . '::' . $tip;
					$states[$key][4] = true;
				}
			}

			return self::state($states, $value, $index, array('prefix' => $prefix, 'translate' => !$tip), $enabled, true, $checkbox, $formId);
		}

		return self::state($states, $value, $index, $prefix, $enabled, true, $checkbox, $formId);
	}

	/**
	 * Returns a published state on a grid
	 *
	 * @param   integer       $value         The state value.
	 * @param   integer       $index         The row index
	 * @param   string|array  $prefix        An optional task prefix or an array of options
	 * @param   boolean       $enabled       An optional setting for access control on the action.
	 * @param   string        $checkbox      An optional prefix for checkboxes.
	 * @param   string        $publishUp     An optional start publishing date.
	 * @param   string        $publishDown   An optional finish publishing date.
	 * @param   string        $formId        An optional form id
	 *
	 * @return  string  The Html code
	 */
	public static function published($value, $index, $prefix = '', $enabled = true,
		$checkbox = 'cb', $publishUp = null, $publishDown = null, $formId = 'adminForm'
	)
	{
		if (is_array($prefix))
		{
			$options  = $prefix;
			$enabled  = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix   = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}

		$states = array(
			1 => array('unpublish', 'JPUBLISHED', 'JLIB_HTML_UNPUBLISH_ITEM', 'JPUBLISHED', false, 'fa fa-check-circle', 'fa fa-check-circle'),
			0 => array('publish', 'JUNPUBLISHED', 'JLIB_HTML_PUBLISH_ITEM', 'JUNPUBLISHED', false, 'fa fa-check-circle-o', 'fa fa-check-circle-o'),
			2 => array('unpublish', 'JARCHIVED', 'JLIB_HTML_UNPUBLISH_ITEM', 'JARCHIVED', false, 'fa fa-archive', 'fa fa-archive'),
			-2 => array('publish', 'JTRASHED', 'JLIB_HTML_PUBLISH_ITEM', 'JTRASHED', false, 'fa fa-trash', 'fa fa-trash')
		);

		// Special state for dates
		if ($publishUp || $publishDown)
		{
			$nullDate = Factory::getDbo()->getNullDate();
			$nowDate  = Factory::getDate()->toUnix();

			$tz = new DateTimeZone(Factory::getUser()->getParam('timezone', Factory::getConfig()->get('offset')));

			$publishUp   = ($publishUp != $nullDate) ? Factory::getDate($publishUp, 'UTC')->setTimeZone($tz) : false;
			$publishDown = ($publishDown != $nullDate) ? Factory::getDate($publishDown, 'UTC')->setTimeZone($tz) : false;

			// Create tip text, only we have publish up or down settings
			$tips = array();

			if ($publishUp != $nullDate && $publishUp != false)
			{
				$tips[] = Text::sprintf('JLIB_HTML_PUBLISHED_START', $publishUp->format(Date::$format, true));
			}

			if ($publishDown != $nullDate && $publishDown != false)
			{
				$tips[] = Text::sprintf('JLIB_HTML_PUBLISHED_FINISHED', $publishDown->format(Date::$format, true));
			}

			$tip = empty($tips) ? false : implode('<br/>', $tips);

			// Add tips and special titles
			foreach ($states as $key => $state)
			{
				// Create special titles for published items
				if ($key == 1)
				{
					$states[$key][2] = 'JLIB_HTML_PUBLISHED_ITEM';
					$states[$key][3] = 'JLIB_HTML_PUBLISHED_ITEM';

					if ($publishUp > $nullDate && $nowDate < $publishUp->toUnix())
					{
						$states[$key][2] = 'JLIB_HTML_PUBLISHED_PENDING_ITEM';
						$states[$key][3] = 'JLIB_HTML_PUBLISHED_PENDING_ITEM';
						$states[$key][5] = 'pending';
						$states[$key][6] = 'pending';
					}

					if ($publishDown > $nullDate && $nowDate > $publishDown->toUnix())
					{
						$states[$key][2] = 'JLIB_HTML_PUBLISHED_EXPIRED_ITEM';
						$states[$key][3] = 'JLIB_HTML_PUBLISHED_EXPIRED_ITEM';
						$states[$key][5] = 'expired';
						$states[$key][6] = 'expired';
					}
				}

				// Add tips to titles
				if ($tip)
				{
					$states[$key][1] = Text::_($states[$key][1]);
					$states[$key][2] = Text::_($states[$key][2]) . '::' . $tip;
					$states[$key][3] = Text::_($states[$key][3]) . '::' . $tip;
					$states[$key][4] = true;
				}
			}

			return self::state($states, $value, $index, array('prefix' => $prefix, 'translate' => !$tip), $enabled, true, $checkbox, $formId);
		}

		return self::state($states, $value, $index, $prefix, $enabled, true, $checkbox, $formId);
	}


	/**
	 * Returns a published state on a grid
	 *
	 * @param   integer       $index     The row index
	 * @param   string        $checkbox  An optional prefix for checkboxes.
	 * @param   string        $formId    An optional form id
	 *
	 * @return  string  The Html code
	 */
	public static function splitreset($index, $checkbox = 'cb', $formId = 'adminForm')
	{
		return "<a class=\"btn btn-default btn-sm \" href=\"javascript:void(0);\" onclick=\"return listItemTaskForm('"
		. $checkbox . $index . "','split_tests.reset','$formId')\"><i class=\"fa fa-refresh\"></i></a>";
	}

	/**
	 * Returns a starred state.
	 *
	 * @param   integer  $index         The row index
	 * @param   string   $prefix        The prefix
	 * @param   bool     $starred       Whether starred
	 * @param   bool     $canEditState  Whether the user can edit the state
	 * @param   string   $formId        The form id
	 *
	 * @return  string
	 */
	public static function star($index, $prefix, $starred, $canEditState, $formId = 'adminForm')
	{
		if ($canEditState)
		{
			if ($starred)
			{
				return HTMLHelper::_('rigrid.action', $index, 'unstar', $prefix, '', '', '', false, 'fa fa-star', 'fa fa-star', true, true, 'cb', $formId, 'btn-star');
			}

			else
			{
				return HTMLHelper::_('rigrid.action', $index, 'star', $prefix, '', '', '', false, 'fa fa-star-o', 'fa fa-star-o', true, true, 'cb', $formId, 'btn-star');
			}
		}

		if ($starred)
		{
			return '<span class="btn btn-sm btn-star disabled"><i class="fa fa-star"></i></span>';
		}

		return '<span class="btn btn-sm btn-star disabled"><i class="fa fa-star-o"></i></span>';
	}

	/**
	 * Returns an ignored state.
	 *
	 * @param   integer  $index         The row index
	 * @param   string   $prefix        The prefix
	 * @param   bool     $ignored       Whether ignored
	 * @param   bool     $canEditState  Whether the user can edit the state
	 * @param   string   $formId        The form id
	 *
	 * @return  string
	 */
	public static function ignore($index, $prefix, $ignored, $canEditState, $formId = 'adminForm')
	{
		if ($canEditState)
		{
			if ($ignored)
			{
				return HTMLHelper::_('rigrid.action', $index, 'unignore', $prefix, '', '', '', false, 'fa fa-plus-square-o', 'fa fa-plus-square-o', true, true, 'cb', $formId, 'lc-unignore');
			}
			else
			{
				return HTMLHelper::_('rigrid.action', $index, 'ignore', $prefix, '', '', '', false, 'fa fa-minus-square-o', 'fa fa-minus-square-o', true, true, 'cb', $formId, 'lc-ignore');
			}
		}

		if ($ignored)
		{
			return '<span class="btn btn-sm btn-default disabled"><i class="fa fa-plus-square-o"></i></span>';
		}

		return '<span class="btn btn-sm btn-default disabled"><i class="fa fa-minus-square-o"></i></span>';
	}

	/**
	 * Returns a read state.
	 *
	 * @param   integer  $index         The row index
	 * @param   string   $prefix        The prefix
	 * @param   bool     $read          Whether read
	 * @param   bool     $canEditState  Whether the user can edit the state
	 * @param   string   $formId        The form id
	 *
	 * @return  string
	 */
	public static function read($index, $prefix, $read, $canEditState, $formId = 'adminForm')
	{
		if ($canEditState)
		{
			if ($read)
			{
				return HTMLHelper::_('rigrid.action', $index, 'unread', $prefix, '', '', '', false, 'fa fa-eye', 'fa fa-eye', true, true, 'cb', $formId);
			}

			else
			{
				return HTMLHelper::_('rigrid.action', $index, 'read', $prefix, '', '', '', false, 'fa fa-eye-slash', 'fa fa-eye-slash', true, true, 'cb', $formId);
			}
		}

		if ($read)
		{
			return '<span class="btn btn-sm btn-default disabled"><i class="fa fa-eye"></i></span>';
		}

		return '<span class="btn btn-sm btn-default disabled"><i class="fa fa-eye-slash"></i></span>';
	}

	/**
	 * Returns a checked-out icon
	 *
	 * @param   integer       $index       The row index.
	 * @param   string        $editorName  The name of the editor.
	 * @param   string        $time        The time that the object was checked out.
	 * @param   string|array  $prefix      An optional task prefix or an array of options
	 * @param   boolean       $enabled     True to enable the action.
	 * @param   string        $checkbox    An optional prefix for checkboxes.
	 * @param   string        $formId      An optional form id
	 *
	 * @return  string  The required HTML.
	 */
	public static function checkedout($index, $editorName, $time, $prefix = '', $enabled = false, $checkbox = 'cb', $formId = 'adminForm')
	{
		if (is_array($prefix))
		{
			$options  = $prefix;
			$enabled  = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix   = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}

		$text          = addslashes(htmlspecialchars($editorName, ENT_COMPAT, 'UTF-8'));
		$date          = addslashes(htmlspecialchars(HTMLHelper::_('date', $time, Text::_('DATE_FORMAT_LC')), ENT_COMPAT, 'UTF-8'));
		$time          = addslashes(htmlspecialchars(HTMLHelper::_('date', $time, 'H:i'), ENT_COMPAT, 'UTF-8'));
		$activeTitle   = Text::_('JLIB_HTML_CHECKIN') . '::' . $text . '<br />' . $date . '<br />' . $time;
		$inactiveTitle = Text::_('JLIB_HTML_CHECKED_OUT') . '::' . $text . '<br />' . $date . '<br />' . $time;

		return self::action(
			$index, 'checkin', $prefix, Text::_('JLIB_HTML_CHECKED_OUT'), $activeTitle, $inactiveTitle, true, 'fa fa-unlock',
			'fa fa-unlock', $enabled, false, $checkbox, $formId
		);
	}
}
