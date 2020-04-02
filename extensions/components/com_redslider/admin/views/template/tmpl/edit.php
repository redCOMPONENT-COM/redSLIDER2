<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Language\Text;

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
		jQuery('.tab-pane .btn-tag').on('click', function (e) {
			var $button = jQuery(this);
			var tag = $button.html().trim().replace(/<em>|<\/em>/g, "");
			var cm = jQuery('.CodeMirror')[0].CodeMirror;
			var doc = cm.getDoc();
			var cursor = doc.getCursor();
			var pos = {
				line: cursor.line,
				ch: cursor.ch
			}
			doc.replaceRange(tag, pos);
			cm.focus();
			doc.setSelection(pos, {line: cursor.line, ch: cursor.ch + tag.length});
		});
	});
</script>
<form enctype="multipart/form-data"
	action="index.php?option=com_redslider&task=template.edit&id=<?php echo $this->item->id; ?>"
	method="post" name="adminForm" class="form-validate" id="adminForm">
    <div class="box-body">
        <div class="row">
            <div class="col-md-7">
	            <?php echo $this->form->renderField('section') ?>
	            <?php echo $this->form->renderField('title') ?>
	            <?php echo $this->form->renderField('alias') ?>
	            <?php echo $this->form->renderField('published') ?>
	            <?php echo $this->form->renderField('content') ?>
            </div>
            <div class="col-md-5">
		            <?php if (count($this->templateTags)): ?>
                    <div class='template_tags'>
                        <div class='well'>
                            <div class="accordion" id="accordion_tag_default">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_tag_default" href="#collapseOne">
							            <?php echo Text::_('COM_REDSLIDER_TEMPLATE_TAGS_DEFAULT_LBL'); ?>
                                    </a>
                                </div>
                                <div id="collapseOne" class="accordion-body collapse in">
                                    <div class="accordion-inner">
                                        <div class="tab-pane" id="tag_related">
                                            <ul>
									            <?php foreach ($this->templateTags as $tag => $tagDesc) : ?>
                                                    <li class="block">
                                                        <button type="button" class="btn-tag btn btn-small"><?php echo $tag ?></button>&nbsp;&nbsp;<?php echo $tagDesc ?>
                                                    </li>
									            <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
            </div>
        </div>
    </div>
	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
