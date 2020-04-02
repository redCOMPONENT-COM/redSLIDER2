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
<?php foreach ($this->basicFields as $field) : ?>
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
<br>
<ul class="nav nav-tabs" id="videoTab">
	<?php if (count($this->outputFields)): ?>
		<?php $first = true; ?>
		<?php foreach ($this->outputFields as $fkey => $fobject): ?>
			<li <?php echo $first?'class="active"':''; $first = false; ?>>
				<a href="#<?php echo $fkey ?>" data-toggle="tab"><strong><?php echo JText::_($fkey); ?></strong></a>
			</li>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>
<div class="tab-content">
	<?php if (count($this->outputFields)): ?>
		<?php $first = true; ?>
		<?php foreach ($this->outputFields as $fkey => $fobject): ?>
			<div class="tab-pane<?php echo $first ? " active": ""; $first = false; ?>" id="<?php echo $fkey ?>">
			<?php foreach ($fobject as $field) : ?>
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
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
