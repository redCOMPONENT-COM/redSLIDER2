<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

?>
<div id="rcCpanel-main-container" class="row">
	<div class="col-md-8 rcCpanelMainIcons">
		<?php $iconsRow = array_chunk($this->iconArray, 6); ?>
		<?php foreach ($iconsRow as $row) : ?>
		<p></p>
		<div class="row">
			<?php foreach ($row as $icon) : ?>
			<div class="col-md-2 col-sm-3 col-xs-6">
                <?php $target = ''; ?>
                <?php if (isset($icon['target']) && $icon['target']): ?>
                    <?php $target = 'target="' . $icon['target'] . '"'; ?>
                <?php endif; ?>
				<a class="rcCpanelIcons" href="<?php echo Route::_($icon['link']); ?>" <?php echo $target; ?>>
					<div class="text-center">
						<span class="dashboard-icon-link-icon">
							<i class="<?php echo $icon['icon']; ?> icon-5x"></i>
						</span>
					</div>
					<div class="text-center">
						<p class="dashboard-icon-link-text">
							<strong><?php echo $icon['text']; ?></strong>
						</p>
					</div>
				</a>
			</div>
			<?php endforeach; ?>
		</div>
		<?php endforeach; ?>
	</div>
	<div class="col-md-4 rcCpanelSideIcons">
		<div class="well">
			<div class="pull-right">
				<strong class="row-title">
					<?php echo Text::_('COM_REDSLIDER_VERSION'); ?>
				</strong>
				<span class="badge badge-success" title="<?php echo JText::_('COM_REDSLIDER_VERSION'); ?>">
					<?php echo $this->redsliderversion; ?>
				</span>
			</div>
			<p class="clearfix"></p>
			<table class="table table-striped adminlist">
			<tr>
				<td>
					<strong><?php echo Text::_('COM_REDSLIDER_GALLERIES'); ?></strong>
				</td>
				<td>
					<span class="badge"><?php echo $this->stats->galleries; ?></span>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo Text::_('COM_REDSLIDER_SLIDES'); ?></strong>
				</td>
				<td>
					<span class="badge"><?php echo $this->stats->slides; ?></span>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo Text::_('COM_REDSLIDER_TEMPLATES'); ?></strong>
				</td>
				<td>
					<span class="badge"><?php echo $this->stats->templates; ?></span>
				</td>
			</tr>
		</table>
		</div>
	</div>
</div>
