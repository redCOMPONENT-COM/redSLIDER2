<?php
/**
 * @package     RedSLIDER
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_REDCORE') or die;

JLoader::import('helper', JPATH_COMPONENT . '/helpers');

$active = null;
$data = $displayData;

if (isset($data['active']))
{
	$active = $data['active'];
}

$sidebars = array(
	array('view' => 'cpanel', 'icon' => 'icon-home', 'text' => JText::_('COM_REDSLIDER_SIDEBAR_DASHBOARD')),
	array('view' => 'galleries', 'icon' => 'icon-sitemap', 'text' => JText::_('COM_REDSLIDER_SIDEBAR_GALLERIES')),
	array('view' => 'slides', 'icon' => 'icon-file-text', 'text' => JText::_('COM_REDSLIDER_SIDEBAR_SLIDES')),
	array('view' => 'templates', 'icon' => 'icon-desktop', 'text' => JText::_('COM_REDSLIDER_SIDEBAR_TEMPLATES'))
);

// Check redSLIDER Category Fields component
$categoryFields = RedsliderHelperHelper::getExtension('com_redslidercategoryfields');

?>

<ul class="nav nav-pills nav-stacked redslider-sidebar">
	<li class="nav-header"><?php echo JText::_('COM_REDSLIDER_SIDEBAR_CPANEL'); ?></li>
	<?php foreach ($sidebars as $sidebar) : ?>
		<?php $class = ($active === $sidebar['view']) ? 'active' : ''; ?>
		<li class="<?php echo $class; ?>">
			<?php $link = JRoute::_('index.php?option=com_redslider&view=' . $sidebar['view']); ?>
			<a href="<?php echo $link; ?>">
				<i class="<?php echo $sidebar['icon']; ?>"></i>
				<?php echo $sidebar['text']; ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
