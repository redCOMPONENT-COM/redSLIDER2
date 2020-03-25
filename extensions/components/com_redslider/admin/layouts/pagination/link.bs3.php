<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;

extract($displayData);

$item = $data;

switch ((string) $item->text)
{
	// Check for "Start" item
	case Text::_('JLIB_HTML_START'):
		$display = '&laquo;';
		break;

	// Check for "Prev" item
	case $item->text == Text::_('JPREV'):
		$display = '&lsaquo;';
		break;

	// Check for "Next" item
	case Text::_('JNEXT'):
		$display = '&rsaquo;';
		break;

	// Check for "End" item
	case Text::_('JLIB_HTML_END'):
		$display = '&raquo;';
		break;

	default:
		$display = $item->text;
		break;
}

if ($active)
{
	if ($item->base > 0)
	{
		$limit = 'limitstart.value=' . $item->base;
	}
	else
	{
		$limit = 'limitstart.value=0';
	}

	$cssClasses = array();

	$title = '';

	$onClick = "document."
		. $item->formName
		. "."
		. $item->prefix
		. $limit
		. "; Joomla.submitform(document.forms['" . $item->formName . "'].task.value, document.forms['" . $item->formName . "']);return false;";

	if (!is_numeric($item->text))
	{
		$title = ' title="' . $item->text . '" ';
	}
}

$liClasses = array('page');

if (!$active)
{
	$liClasses[] = (property_exists($item, 'active') && $item->active) ? 'active' : 'disabled';
}

?>
<?php if ($active) : ?>
	<li class="<?php echo implode(' ', $liClasses); ?>">

		<a <?php if ($cssClasses) : ?>class="<?php echo implode(' ', $cssClasses); ?>"<?php
		   endif; ?> <?php echo $title; ?> href="<?php echo $item->link; ?>#" onclick="<?php echo $onClick; ?>">
			<?php echo $display; ?>
		</a>
	</li>
<?php else : ?>
	<li class="<?php echo implode(' ', $liClasses); ?>">
		<a href="#" onclick="return false;"><?php echo $display; ?></a>
	</li>
<?php endif;
