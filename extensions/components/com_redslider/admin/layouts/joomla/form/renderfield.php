<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
/**
 * Layout variables
 * ---------------------
 * 	$options         : (array)  Optional parameters
 * 	$label           : (string) The html code for the label (not required if $options['hiddenLabel'] is true)
 * 	$input           : (string) The input field html code
 */

if (!empty($displayData['options']['showonEnabled']))
{
	HTMLHelper::_('jquery.framework');
	HTMLHelper::_('script', 'jui/cms.js', false, true);
}

$class = empty($displayData['options']['class']) ? "" : " " . $displayData['options']['class'];
$rel   = empty($displayData['options']['rel']) ? "" : " " . $displayData['options']['rel'];
?>
<?php if (!empty($displayData['label']) || !empty($displayData['input'])) : ?>
	<div class="form-group <?php echo $class; ?>"<?php echo $rel; ?>>

		<?php if (empty($displayData['options']['hiddenLabel'])) : ?>
			<?php echo $displayData['label']; ?>
		<?php endif; ?>
		<?php echo $displayData['input']; ?>
	</div>
<?php endif;
