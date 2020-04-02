<?php
/**
 * @package     Aesir.DAM
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2017 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;

extract($displayData);

?><div class="modal select-assets-modal fade" id="select-assets-modal" tabindex="-1" role="dialog" style="display: none">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
					<?php echo Text::_('PLG_REDSLIDER_ADDONS_DAM_ASSET_SELECT_ASSETS') ?>
					<i>(<span class="assets-count">0</span> <?php echo Text::_('PLG_REDSLIDER_ADDONS_DAM_ASSET_TOTAL') ?>)</i>
					<div class="pull-right">
						<button type="button" class="btn btn-info asset-upload uploadSwitch switchButtonDAM">
							<?php echo Text::_('PLG_REDSLIDER_ADDONS_DAM_ASSET_UPLOAD') ?>
						</button>
						<button type="button" class="btn btn-info asset-list uploadSwitch hide switchButtonDAM">
							<?php echo Text::_('PLG_REDSLIDER_ADDONS_DAM_ASSET_SHOW_LIST') ?>
						</button>
					</div>
				</h4>
			</div>
			<div class="modal-body">
				<div class="selectDAM uploadSwitch">
					<div class="well">
						<?php foreach (array_chunk($form->getGroup('filter'), 3) as $fields) : ?>
							<div class="row">
								<?php foreach ($fields as $field) : ?>
									<div class="col-md-4">
										<?php echo $field->renderField() ?>
									</div>
								<?php endforeach ?>
							</div>
						<?php endforeach ?>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="thumbnails-pagination pull-right"></div>
						</div>
					</div>
					<div class="row">
						<div class="thumbnails-wrapper col-xs-9"></div>
						<div class="selected-attribs col-xs-3">
							<div class="asset-modal-buttons">
								<button type="button" class="btn btn-success asset-save">
									<?php echo Text::_('JSAVE') ?>
								</button>
								<button type="button" class="btn btn-danger asset-remove">
									<?php echo Text::_('PLG_REDSLIDER_ADDONS_DAM_ASSET_DESELECT') ?>
								</button>
							</div>
							<div class="selected-attribs-fields"></div>
						</div>
					</div>
				</div>
				<div class="uploadDAM uploadSwitch hide">
					<form enctype="multipart/form-data"
						  action="<?php echo Route::_('index.php?option=com_aesir_dam') ?>"
						  method="post"
						  name="adminFormUpload"
						  class="dropzone"
						  id="aesir-field-asset-dropzone"
						  data-duplicates-url="<?php echo Route::_('index.php?option=com_aesir_dam&task=upload.ajaxDuplicates') ?>"
					>
						<div class="row">
							<?php foreach (array_chunk($form->getGroup('upload'), 3) as $fields) :
								foreach ($fields as $field) : ?>
									<div class="col-md-4">
										<?php echo $field->renderField() ?>
									</div>
								<?php endforeach;
							endforeach ?>
						</div>
						<div class="clearfix"></div>
						<div class="fallback">
							<input name="file" type="file" multiple />
						</div>
						<input type="hidden" name="task" value="upload.ajaxUpload"/>
						<input type="hidden" name="<?php echo Session::getFormToken() ?>" id="sessionToken" value="1">
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade file-exists-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close close-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
					<?php echo Text::_('COM_AESIR_DAM_UPLOAD_FILE_ALREADY_EXISTS') ?>
					<span class="file-exists-name"></span>
				</h4>
			</div>
			<div class="modal-body">
				<p class="file-exists-text"></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default close-modal" data-dismiss="modal"><?php echo Text::_('JTOOLBAR_CANCEL') ?></button>
				<button id="overwrite-file" type="button" class="btn btn-primary"><?php echo Text::_('COM_AESIR_DAM_UPLOAD_OVERWRITE') ?></button>
			</div>
		</div>
	</div>
</div>
