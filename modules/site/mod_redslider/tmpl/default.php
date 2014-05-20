<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_relateditems
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHtml::_('rholder.image', '100x100');
?>

<?php if ($displayType) : ?>
	<!-- Display as slider -->
	<?php
	JHtml::_('rjquery.framework');
	RHelperAsset::load('jquery.resize.min.js', 'com_reditem');
	RHelperAsset::load('jquery.bxslider.min.js', 'com_reditem');
	?>
	<script type="text/javascript">
	(function($){
		$(document).ready(function() {
			$("#mod_reditem_relateditems_<?php echo $module->id;?>").bxSlider({
				controls: <?php echo $sliderControls; ?>,
				pager: <?php echo $slidePager; ?>,
				auto: <?php echo $slideAutoPlay; ?>
			});
		});
	})(jQuery);
	</script>
<?php endif; ?>

<div class="mod_reditem_relateditems_wrapper">
<?php if ($items) : ?>
	<?php if ($template) : ?>
	<ul id="mod_reditem_relateditems_<?php echo $module->id; ?>">
		<?php foreach ($items As $item) : ?>
			<?php
			$itemContent = $template->content;
			ReditemTagsHelper::tagReplaceItem($itemContent, $item, 0, $paramItemId);
			ReditemTagsHelper::tagReplaceItemCustomField($itemContent, $item);
			JPluginHelper::importPlugin('content');
			$itemContent = JHtml::_('content.prepare', $itemContent);
			?>
			<li><?php echo $itemContent; ?></li>
		<?php endforeach; ?>
	</ul>
	<?php else : ?>
		<p><?php echo JText::_('MOD_REDITEM_ITEMS_ERROR_TEMPLATE_NOT_FOUND'); ?></p>
	<?php endif; ?>
<?php else : ?>
	<p><?php echo JText::_('MOD_REDITEM_ITEMS_ERROR_NO_ITEMS_FOUND'); ?></p>
<?php endif; ?>
</div>
