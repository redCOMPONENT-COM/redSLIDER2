<?php
/**
 * @package     Aesir.Backend
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2017 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_redslider/helpers/cpanel_icons.php';

$input      = Factory::getApplication()->input;
$option     = $input->get('option', '');
$activeView = $input->get('view', 'setup');
$return     = base64_encode(Uri::current());
$configLink = 'index.php?option=com_config&view=component&component=com_redslider&path=&return=' . $return;

$icons = [
	[
		'header' => JText::_('COM_REDSLIDER_MENU'),
		'icons' => RedsliderHelpersCpanel_Icons::getIconArray(),
	]
];
?>
<ul class="sidebar-menu">
	<?php foreach ($icons as $group) : ?>

		<?php if (empty($group)) continue ?>

		<li class="header"><?php echo strtoupper($group['header']) ?></li>

		<?php foreach ($group['icons'] as $icon): ?>
			<?php $class = ($activeView === $icon['view']) ? 'active' : '' ?>
			<?php $badge = isset($icon['badge']) ? $icon['badge'] : 'badge-default' ?>

			<li class="<?php echo $class ?>">
				<a href="<?php echo $icon['link'] ?>">
					<i class="<?php echo $icon['icon'] ?>"></i>&nbsp;
					<span><?php echo $icon['text'] ?></span>

					<?php if (!empty($stats[$icon['view']])) : ?>
						<span class="badge <?php echo $badge ?> pull-right">
							<?php echo $stats{$icon['view']} ?>
						</span>
					<?php endif ?>
				</a>
			</li>
		<?php endforeach; ?>
	<?php endforeach ?>
</ul>
