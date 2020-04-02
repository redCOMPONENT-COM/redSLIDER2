<?php
/**
 * @package     Aesir.Backend
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2017 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

extract($displayData);

$toolbar = $view->getToolbar();
?>
<div class="row">
	<div class="col-xs-12 col-sm-5 col-md-3">
		<h1><?php echo $view->getTitle() ?></h1>
	</div>
	<div class="col-xs-12 col-sm-7 col-md-9">
		<?php if ($toolbar instanceof RToolbar) : ?>
			<div class="header-toolbar pull-right">
				<?php echo $toolbar->render() ?>
			</div>
		<?php endif; ?>
	</div>
</div>
<div class="row-fluid message-sys" id="message-sys"></div>
