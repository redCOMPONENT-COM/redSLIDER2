<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2016 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

use Aesir\Core\Helper\AssetHelper;

/**
 * Layout variables
 * ---------------------
 * @var   array    $displayData          All available data.
 * @var   array    $attribs              All attributes.
 * @var   string   $attributes           Attributes in string format.
 * @var   array    $options              All association data for specific editor.
 * @var   string   $id                   DOM ID of this field.
 * @var   string   $name                 DOM Name of this field.
 * @var   string   $value                Value of field.
 * @var   string   $default              Default value.
 * @var   string   $editorType           Type of editor.
 * @var   int      $limit                Limit characters.
 * @var   boolean  $isLimitGuideEnabled  Limit characters.
 */
extract($displayData);

$limit = !$limit ? false : (int) $limit;
unset($attribs['class']);

HTMLHelper::_('jquery.framework');
AssetHelper::load('reditor.js', 'com_redslider');

$attribs['class'] = (isset($attribs['class']) ? trim($attribs['class']) : '') . ' reditem_customfield_editor' . ($readonly ? '_disabled' : '');
$attribs['id']    = isset($attribs['id']) ? $attribs['id'] : $id . '_editor';

$editorOptions = array_intersect_key(
	$displayData,
	array_flip(array('id', 'limit', 'value', 'options', 'autoSize', 'editorType'))
);
?>
<?php if (!$readonly): ?>
	<script type="text/javascript">
        (function ($) {
            $(function () {
                slider_editor.editor.init(
                    '<?php echo $id ?>',
					<?php echo json_encode($editorOptions); ?>
                );

                var modalClose = window.jModalClose;
                window.jModalClose = function() {
                    modalClose();

                    $('div[id^="editor-button-modal-"]').each(function () {
                        var modal = $(this);
                        modal.find('.modal-body').html('');
                        modal.modal('hide');
                    });
                };
            });
        })(jQuery);
	</script>
<?php endif ?>
	<div <?php echo \Joomla\Utilities\ArrayHelper::toString($attribs);?>>

		<?php if ($readonly): ?>
			<div class="well well-sm">
				<?php echo trim($editorContent) == '' ? '<br />' : $editorContent; ?>
			</div>
		<?php else: ?>
			<?php echo $editorContent; ?>
		<?php endif ?>
	</div>
<?php if (!$readonly): ?>
	<div id="editor-button-modal-<?php echo $attribs['id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo Text::_('JTOOLBAR_CLOSE') ?>">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title"></h4>
				</div>

				<div class="modal-body"></div>

				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo Text::_('JTOOLBAR_CLOSE') ?></button>
				</div>
			</div>
		</div>
	</div>

	<div class="clearfix"></div>

	<?php if ($limit && $isLimitGuideEnabled): ?>
		<div class="text-right"><span id="<?php echo $id ?>_current_chars"></span> /
			<strong><?php echo $limit ?></strong>
		</div>
	<?php endif;
endif;
