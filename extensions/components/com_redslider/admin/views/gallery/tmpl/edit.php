<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

$isNew = true;

if ($this->item->id)
{
	$isNew = false;
}

?>
<script type="text/javascript">
	jQuery(document).ready(function()
	{
		// Disable click function on btn-group
		jQuery(".btn-group").each(function(index){
			if (jQuery(this).hasClass('disabled'))
			{
				jQuery(this).find("label").off('click');
			}
		});
	});
</script>
<form enctype="multipart/form-data"
	action="index.php?option=com_redslider&task=gallery.edit&id=<?php echo $this->item->id; ?>"
	method="post" name="adminForm" class="form-validate" id="adminForm">
	<div class="box-body">
		<?php echo $this->form->renderField('title') ?>
		<?php echo $this->form->renderField('alias') ?>
		<?php echo $this->form->renderField('published') ?>
	</div>
	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
