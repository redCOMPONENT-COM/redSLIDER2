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
	'filtersHidden'       => false,
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
	<div class="js-stools-container-filters row clearfix">
		<?php echo RLayoutHelper::render('searchtools.default.filters', $data); ?>
	</div>
</div>
