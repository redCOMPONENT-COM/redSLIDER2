<?php
/**
 * @package     Aesir.Backend
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2017 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$view = Factory::getApplication()->input->get('view');
?><div class="aesir <?=$view?>">
	<div class="wrapper">
		<header class="main-header">
			<?php echo RLayoutHelper::render('component.full.header', array()); ?>
		</header>
		<aside class="main-sidebar">
			<?php echo RLayoutHelper::render('component.full.sidebar', array()); ?>
		</aside>
		<div class="content-wrapper">
			<section class="content-header clearfix">
				<?php echo RLayoutHelper::render('component.full.content.header', $displayData); ?>
			</section>
			<section class="content">
				<?php echo RLayoutHelper::render('component.full.content.body', $displayData); ?>
			</section>
		</div>
		<footer class="main-footer">
			<?php echo RLayoutHelper::render('component.full.content.footer', $displayData); ?>
		</footer>
	</div>
</div>
