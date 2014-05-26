<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Slide
 *
 * @copyright   Copyright (C) 2014 redCOMPONENi.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->form->getGroup('params') as $field) : ?>
<div class="control-group">
	<?php if ($field->type == 'Spacer') : ?>
		<?php if (!$firstSpacer) : ?>
			<hr />
		<?php else : ?>
			<?php $firstSpacer = false; ?>
		<?php endif; ?>
		<?php echo $field->label; ?>
	<?php elseif ($field->hidden) : ?>
		<?php echo $field->input; ?>
	<?php elseif ($field->name == 'jform[params][slide_image_file]') : ?>
	<div class="control-label">
		<?php echo $field->label; ?>
	</div>
	<div class="controls">
		<?php echo $field->input; ?>
		<?php if (isset($this->item->params['slide_image_file'])): ?>
			<?php $img_src = JURI::root() . 'media/com_redslider/images/slides/' . $this->item->params['slide_image_file'] ?>
			<img style="max-width: 300px; max-height: 300px; margin-right: 20px;" class="preview_img img-polaroid pull-left" src="<?php echo $img_src; ?>" />
		<?php endif; ?>
	</div>
	<?php else : ?>
	<div class="control-label">
		<?php echo $field->label; ?>
	</div>
	<div class="controls">
		<?php echo $field->input; ?>
	</div>
	<?php endif; ?>
</div>
<?php endforeach; ?>
