<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Slide
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

$isNew = true;
$user  = JFactory::getUser();
JPluginHelper::importPlugin('redslider_sections');
$dispatcher = RFactory::getDispatcher();

if ($this->item->id)
{
	$isNew = false;
}
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        // Disable click function on btn-group
        jQuery(".btn-group").each(function (index) {
            if (jQuery(this).hasClass("disabled")) {
                jQuery(this).find("label").off("click");
            }
        });
    });
</script>
<form enctype="multipart/form-data"
      action="index.php?option=com_redslider&task=slide.edit&id=<?php echo $this->item->id; ?>"
      method="post" name="adminForm" class="form-validate" id="adminForm">
    <div class="box-body">
    <div class="row">
        <div class="col-md-5">
	        <?php echo $this->form->renderField('section') ?>
	        <?php echo $this->form->renderField('gallery_id') ?>
	        <?php echo $this->form->renderField('template_id') ?>
	        <?php echo $this->form->renderField('title') ?>
	        <?php echo $this->form->renderField('alias') ?>
	        <?php echo $this->form->renderField('created_date') ?>
	        <?php echo $this->form->renderField('created_by') ?>
	        <?php echo $this->form->renderField('modified_date') ?>
	        <?php echo $this->form->renderField('modified_by') ?>
	        <?php echo $this->form->renderField('publish_up') ?>
	        <?php echo $this->form->renderField('publish_down') ?>
			<?php echo $this->form->getInput('checked_out'); ?>
			<?php echo $this->form->getInput('checked_out_time'); ?>
        </div>
        <div class="col-md-7">
	        <?php echo $this->form->renderField('published') ?>
	        <?php echo $this->form->renderField('language') ?>

			<?php // Load template dynamically from plugin ?>
			<?php if ($user->authorise('core.create', 'com_redslider') && $user->authorise('core.edit', 'com_redslider') && $user->authorise('core.edit.state', 'com_redslider')): ?>
				<?php if ($this->sectionId): ?>
					<?php $sectionTemplates = $dispatcher->trigger('onSlidePrepareTemplate', array($this, $this->sectionId)); ?>
					<?php if (count($sectionTemplates)): ?>
						<?php foreach ($sectionTemplates as $template): ?>
							<?php echo $template ?>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
        </div>
    </div>
    </div>
	<?php echo $this->form->getInput('id'); ?>
    <input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>
</form>
