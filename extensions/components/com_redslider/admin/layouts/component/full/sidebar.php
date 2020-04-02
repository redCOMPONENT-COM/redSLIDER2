<?php
/**
 * @package     Aesir.Backend
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2017 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

?>
<!-- sidebar: style can be found in sidebar.less -->
<section class="sidebar">
	<?php echo RLayoutHelper::render('component.full.sidebar.menu', $displayData); ?>
</section>
