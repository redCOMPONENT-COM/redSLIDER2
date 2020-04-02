<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2016 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$data = $displayData;

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();
$loadFilters     = isset($data['options']['loadFilters']) ? (boolean) $data['options']['loadFilters'] : true;

// Set some basic options
$customOptions = array(
	'filtersHidden'       => true,
	'defaultLimit'        =>
		isset($data['options']['defaultLimit']) ? $data['options']['defaultLimit'] : Factory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#list_fullordering'
);

$data['options'] = array_merge($customOptions, $data['options']);

$formSelector = !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#adminForm';

// Load search tools
RHtml::_('rsearchtools.form', $formSelector, $data['options']);
?>
<div class="js-stools clearfix">
	<div class="row">
		<div class="col-sm-12 col-md-12 col-lg-8" style="padding-left: 0;">
			<div class="col-md-1">
				<div class="js-stools-container-list hidden-xs hidden-sm">
					<?php echo RLayoutHelper::render('searchtools.default.list', $data); ?>
				</div>
			</div>

			<div class="col-md-11">
				<div class="js-stools-container-bar">
					<?php echo RLayoutHelper::render('searchtools.default.bar', $data); ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Filters div -->
	<?php if ($loadFilters)
	:
?>
		<div class="js-stools-container-filters row clearfix">
			<?php echo RLayoutHelper::render('searchtools.default.filters', $data); ?>
		</div>
	<?php endif; ?>
</div>
