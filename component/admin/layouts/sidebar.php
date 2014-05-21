<?php
/**
 * @package     RedSLIDER
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */


defined('JPATH_REDCORE') or die;

$active = null;
$data = $displayData;

if (isset($data['active']))
{
	$active = $data['active'];
}

$sidebars = array(
	array('view' => 'galleries', 'icon' => 'icon-folder-open', 'text' => JText::_('COM_REDSLIDER_SIDEBAR_GALLERIES')),
	array('view' => 'sliders', 'icon' => 'icon-file', 'text' => JText::_('COM_REDSLIDER_SIDEBAR_SLIDERS')),
	array('view' => 'templates', 'icon' => 'icon-hdd', 'text' => JText::_('COM_REDSLIDER_SIDEBAR_TEMPLATES'))
);

?>
<ul class="nav nav-tabs nav-stacked">
	<?php foreach ($sidebars as $sidebar) : ?>
	<li>
		<?php $link = JRoute::_('index.php?option=com_redslider&view=' . $sidebar['view']); ?>
		<?php $class = ($active === $sidebar['view']) ? 'active' : ''; ?>
		<a href="<?php echo $link; ?>" class="<?php echo $class ?>">
			<i class="<?php echo $sidebar['icon']; ?>"></i>
			<?php echo $sidebar['text']; ?>
		</a>
	</li>
	<?php endforeach; ?>
</ul>
