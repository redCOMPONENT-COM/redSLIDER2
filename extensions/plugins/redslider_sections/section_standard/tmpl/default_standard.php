<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Slide
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

?>
<?php foreach ($this->form->getGroup('params') as $field) : ?>
	<?php if ($field->type == 'Spacer') : ?>
		<?php if (!$firstSpacer) : ?>
			<hr />
		<?php else : ?>
			<?php $firstSpacer = false; ?>
		<?php endif; ?>
		<?php echo $field->label; ?>
	<?php elseif ($field->hidden) : ?>
		<?php echo $field->input; ?>
	<?php else : ?>
		<?php echo $field->renderField() ?>
	<?php endif; ?>
<?php endforeach; ?>
