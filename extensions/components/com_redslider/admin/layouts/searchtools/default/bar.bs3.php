<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2016 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

$data = $displayData;

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();

if (is_array($data['options']))
{
	$data['options'] = new Registry($data['options']);
}

$filters            = $data['view']->filterForm->getGroup('filter');
$numberOfFilters    = count($filters);
$countActiveFilters = property_exists($data['view'], 'activeFilters') ? count($data['view']->activeFilters) : 0;

// In items view, number activeFilters is reduced 1 because "filter.type_id" which is set on model, is not existed on filter form
if ($countActiveFilters && !empty($data['view']->activeFilters['type_id']) && $data['view']->getName() == "items")
{
	$countActiveFilters--;
}

// Options
$searchField  = 'filter_' . $data['options']->get('searchField', 'search');
$filterButton = $data['options']->get('filterButton', true) && ($numberOfFilters > 0 && empty($filters[$searchField]) || $numberOfFilters > 1 && !empty($filters[$searchField]));
$searchButton = $data['options']->get('searchButton', true) && !empty($filters[$searchField]);
?>
<?php if ($searchButton || $filterButton) : ?>
	<div class="stools-filter-bar row">

		<?php if ($searchButton) : ?>
			<div class="col-xs-8 col-sm-7 col-md-8 col-lg-4">
				<label for="filter_search" class="sr-only">
					<?php echo Text::_('LIB_REDCORE_FILTER_SEARCH_DESC'); ?>
				</label>
				<div class="input-group stools-search-group">
					<?php echo $filters[$searchField]->input; ?>
					<span class="input-group-btn">
						<button type="submit" class="lc-button-search btn btn-default" title="<?php echo RHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
							<i class="fa fa-search"></i>
						</button>
					</span>
				</div>
			</div>
		<?php endif; ?>

		<div class="col-xs-4 col-sm-5 col-md-4 stools-filter-buttons">
			<?php if ($filterButton) : ?>
				<button type="button" class="test-aesir-filter-button btn btn-default js-stools-btn-filter">
					<i class="fa fa-filter"></i> <?php echo Text::_('COM_REDSLIDER_SEARCHTOOLS_FILTER') ?>

					<?php if ($countActiveFilters) : ?>
						<sup><span class="label label-primary"><?php echo $countActiveFilters ?></span></sup>
					<?php endif ?>
				</button>
			<?php endif; ?>

			<button type="button" class="btn btn-default js-stools-btn-clear">
				<i class="fa fa-close"></i> <?php echo Text::_('JSEARCH_FILTER_CLEAR') ?>
			</button>
		</div>
	</div>
<?php endif;
