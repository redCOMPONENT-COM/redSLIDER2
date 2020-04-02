<?php
/**
 * @package     Aesir.Backend
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2017 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Aesir\Core\Version\Version;

?>
<div class="pull-right hidden-xs">
  <b>Version</b> <?php echo Version::get('com_redslider') ?>
</div>

<strong>
  Copyright &copy;
  <?= JFactory::getDate()->format('Y') ?>
  <a href="https://redweb.dk/">redSLIDER 2</a>.
</strong>

All rights reserved.
