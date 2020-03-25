<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2016 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$data = $displayData;

$metatitle = RHtml::tooltipText(Text::_($data->tip ? $data->tip : $data->title), Text::_('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN'), 0);
RHtml::_('rbootstrap.tooltip');

$classes = array('js-stools-column-order hasTooltip ordering', strtolower($data->direction));

if ($data->order == $data->selected)
{
	$classes[] = 'active';
}
?>
<a href="#"
   onclick="return false;"
   class="<?php echo implode(' ', $classes); ?>"
   data-order="<?php echo $data->order; ?>"
   data-direction="<?php echo strtoupper($data->direction); ?>"
   data-name="<?php echo Text::_($data->title); ?>"
   title="<?php echo $metatitle; ?>">

	<?php if (!empty($data->icon)) : ?>
		<i class="<?php echo $data->icon; ?>"></i>
	<?php endif; ?>

	<?php if (!empty($data->title)) : ?>
		<?php echo Text::_($data->title); ?>
	<?php endif; ?>
</a>
