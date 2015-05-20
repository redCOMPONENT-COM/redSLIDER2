<?php
/**
 * @package     RedSLIDER
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

// Load the modal behavior script.
JHtml::_('behavior.modal', 'a.modalRedshopProductAjax');
$link	= JRoute::_('index.php?option=com_redshop&view=product&task=element&tmpl=component&function=jSelectProduct', false);

$name  = $displayData['name'];
$id    = $displayData['id'];
$products = $displayData['products'];
?>

<script type="text/javascript">

	var index = '<?php echo count($products)?>';

	function addProduct()
	{
		var id = '<?php echo $id ?>' + index;
		var str = "<div class='media' id='" + id + "' style='width:100%'>";
		str += "<div class='pull-left'>";
		str += "<p>";
		str += "<input type='text' class='input-small' id='" + id + "_name' value='' disabled='disabled'/>";
		str += "<a class='btn modalRedshopProductAjax' onclick='javascript:setActiveModal(\"" + id + "\")' title='<?php echo JText::_('PLG_REDSLIDER_SECTION_PRODUCT_SELECT_PRODUCT_BUTTON')?>'";
		str += "href='<?php echo $link?>&amp;<?php echo JSession::getFormToken()?>=1' rel='{handler: \"iframe\", size: {x: 800, y: 450}}'>";
		str += "<?php echo JText::_('PLG_REDSLIDER_SECTION_PRODUCT_SELECT_PRODUCT_BUTTON') ?>";
		str += "</a>";
		str += "<input type='hidden' id='" + id + "_id' class='required modal-value' name='<?php echo $name?>[]' value='' />";
		str += "</p>";
		str += "</div>";
		str += "<div class='media-body'>";
		str += "<a class='btn btn-danger' href='javascript:void(0);' onClick='javascript:removeProduct(\"" + id + "\");'>";
		str += "<i class='icon-remove'></i> <?php echo JText::_('PLG_REDSLIDER_SECTION_PRODUCT_REMOVE_PRODUCT'); ?>";
		str += "</a>";
		str += "</div>";
		str += "</div>";

		jQuery(".modal_product").append(str);

		index++;
		// Re-init modal popup
		SqueezeBox.initialize({});
		SqueezeBox.assign($$('a.modalRedshopProductAjax'), {
			parse: 'rel'
		});
	}

	function setActiveModal(id)
	{
		document.getElementById('<?php echo $id?>_active_modal').value = id;
	}

	function removeProduct(id)
	{
		var obj = document.getElementById(id);
		jQuery(obj).remove();
	}

	function jSelectProduct(id, title, object) {
		(function($){
			var active = $('#<?php echo $id?>_active_modal').val();
			$('#' + active + '_id').val(id);
			$('#' + active + '_name').val(title);
			SqueezeBox.close();
		})(jQuery);
	}
</script>

<div class="modal_product">
	<p>
		<a class="btn btn-primary" href="javascript:void(0);" onClick="javascript:addProduct()">
			<i class="icon-plus"></i>
			<?php echo JText::_('PLG_REDSLIDER_SECTION_PRODUCT_ADD_MORE_PRODUCT'); ?>
		</a>
	</p>
	<?php if (empty($products)): ?>
		<div class="media" id="<?php echo $id; ?>" style="width:100%">
			<div class="pull-left">
				<input type="text" class="input-small" id="<?php echo $id; ?>_name" value="" disabled="disabled"/>
				<a class="btn modalRedshopProductAjax" title="<?php echo JText::_('PLG_REDSLIDER_SECTION_PRODUCT_SELECT_PRODUCT_BUTTON')?>"
					onClick="javascript:setActiveModal('<?php echo $id?>')"
					href="<?php echo $link ?>&amp;<?php echo JSession::getFormToken()?>=1"
					rel="{handler: 'iframe', size: {x: 800, y: 450}}">
					<?php echo JText::_('PLG_REDSLIDER_SECTION_PRODUCT_SELECT_PRODUCT_BUTTON') ?>
				</a>
				<input type="hidden" id="<?php echo $id?>_id" class="required modal-value" name="<?php echo $name?>[]" value="" />
			</div>
		</div>
	<?php else: ?>
		<?php foreach($products as $index => $product): ?>
			<div class="media" id="<?php echo $id . $index; ?>" style="width:100%">
				<div class="pull-left">
					<input type="text" class="input-small" id="<?php echo $id . $index; ?>_name" value="<?php echo $product->product_name?>" disabled="disabled"/>
					<a class="btn modalRedshopProductAjax" title="<?php echo JText::_('PLG_REDSLIDER_SECTION_PRODUCT_SELECT_PRODUCT_BUTTON')?>"
						onClick="javascript:setActiveModal('<?php echo $id . $index?>')"
						href="<?php echo $link ?>&amp;<?php echo JSession::getFormToken()?>=1"
						rel="{handler: 'iframe', size: {x: 800, y: 450}}">
						<?php echo JText::_('PLG_REDSLIDER_SECTION_PRODUCT_SELECT_PRODUCT_BUTTON') ?>
					</a>
					<input type="hidden" id="<?php echo $id . $index ?>_id" class="required modal-value" name="<?php echo $name?>[]" value="<?php echo $product->product_id; ?>" />
				</div>
				<div class="media-body">
					<?php if ($index > 0): ?>
					<a class="btn btn-danger" href="javascript:void(0)" onclick="javascript:removeProduct('<?php echo $id . $index; ?>')">
						<i class="icon-remove"></i>
						<?php echo JText::_('PLG_REDSLIDER_SECTION_PRODUCT_REMOVE_PRODUCT'); ?>
					</a>
					<?php endif;?>
				</div>
			</div>
		<?php endforeach;?>
	<?php endif;?>
</div>
<input type="hidden" id="<?php echo $id ?>_active_modal" value="" />
