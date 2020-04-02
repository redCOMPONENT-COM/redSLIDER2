<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2016 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

$data = $displayData;

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();

if (is_array($data['options']))
{
	$data['options'] = new Registry($data['options']);
}

$searchField = 'filter_' . $data['options']->get('searchField', 'search');

// Load the form filters
$filters = $data['view']->filterForm->getGroup('filter');
?>
<?php if ($filters) : ?>
	<?php foreach ($filters as $fieldName => $field) : ?>
		<?php if ($fieldName != $searchField) : ?>
			<div class="js-stools-field-filter col-xs-12 col-sm-12 col-md-6 col-lg-3">
				<?php echo $field->input; ?>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif;
