<?php
/**
 * @package     Aesir
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2016 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\CMS\Pagination\Pagination;

extract($displayData);

/**
 * @var  Pagination  $pagination  The pagination object
 */

if ($pagination->pagesTotal > 1) : ?>
	<div class="row list-pagination">
		<div class="col-xs-12">
			<div class="pull-right">
				<?php echo $pagination->getPaginationLinks(null, array('showLimitBox' => false)); ?>
			</div>
		</div>
	</div>
<?php endif;
