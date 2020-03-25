<?php
/**
 * @package     Aesir
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2017 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Aesir\Core\Helper\AssetHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

extract($displayData);

// Initialise once for all DAM fields
if (!class_exists('InitAesirDamAssetModal'))
{
	AssetHelper::load('dropzone.min.js', 'com_aesir_dam');
	AssetHelper::load('dropzone.min.css', 'com_aesir_dam');
	AssetHelper::load('script.js', 'plg_redlider_addons_aesir_dam');
	AssetHelper::load('styles.css', 'plg_redlider_addons_aesir_dam');
	AssetHelper::load('holder.js', 'com_aesir_dam');
	AssetHelper::load('jquery.lazyload.js', 'com_aesir_dam');
	AssetHelper::load('jquery.bootpag.js', 'com_aesir_dam');

	$baseAjaxRoute = Route::_('index.php?option=com_aesir_dam&' . Session::getFormToken() . '=1', false);

	Factory::getDocument()->addScriptDeclaration("
	(function($){
		$(document).ready(function() {
			new DamAssetSelectionModal({
				\$modal: $('#select-assets-modal'),
				\$baseAjaxUrl: '$baseAjaxRoute',
				assetFieldGroup: '.assetFieldGroup',
				selectedAssets: '.selected-assets',
				\$selectedAssetsControl: $('.assets-selected'),
				selectedAssetsControl: '.assets-selected',
				\$selectedAssetsCount: $('.select-assets-btn .selected-assets-count'),
				selectedAssetsCount: '.select-assets-btn .selected-assets-count',
				\$selectedSave: $('#select-assets-modal .asset-save'),
				\$selectedRemove: $('#select-assets-modal .asset-remove'),
				\$uploadButton: $('#select-assets-modal .asset-upload')
			});
		});
	})(jQuery);
"
	);

	/**
	 * @since       1.0.0
	 */
	class InitAesirDamAssetModal
	{
		/**
		 * @var   \Aesir\Layout\LayoutFile
		 * @since 1.0.0
		 */
		public static $layoutObj;

		/**
		 * @return void
		 * @since 1.0.0
		 * @throws Exception
		 */
		public static function loadModal()
		{
			// Put modal to the end of the body
			Factory::getApplication()->appendBody(
				static::$layoutObj->setLayoutId('dam_asset_field_modal')->render()
			);
		}
	}

	InitAesirDamAssetModal::$layoutObj = clone $this;
	InitAesirDamAssetModal::$layoutObj->setData(['form' => $form]);
	Factory::getApplication()->registerEvent('onAfterRender', ['InitAesirDamAssetModal', 'loadModal']);
}

/**
 * Layout variables
 * ==================================
 * @var  string  $id    DOM id of the field.
 * @var  \JForm  $form  The filter form
 * @var  string  $value The JSON encoded value
 * @var  boolean  $disabled  Disabled or not
 * @var  boolean  $readonly  Readonly or not
 */

?>
<div class="form-group assetFieldGroup">
	<button id="select-assets-btn-<?php echo $id ?>"
			type="button"
		<?php if ($disabled || $readonly) : ?>
			class="btn btn-default disabled"
		<?php else: ?>
			class="btn btn-default select-assets-btn" data-toggle="modal" data-target="#select-assets-modal"
		<?php endif ?>
			data-show-selected="0"
			data-damid="<?php echo $id ?>">
		<?php echo Text::_('PLG_REDSLIDER_ADDONS_DAM_ASSET_SELECT_ASSETS') ?>
		<span class="selected-assets-count label label-<?php echo ($disabled || $readonly) ? 'info' : 'success' ?>"></span>
	</button>
	<div class="assets-selected" id="assets-selected-<?php echo $id ?>"></div>
	<input type="hidden"
		   name="<?php echo $name ?>"
		   class="selected-assets"
		   id="selected-assets-<?php echo $id ?>"
		   data-multiple="<?php echo $attribs['multiple'] ?>"
		   value="<?php echo htmlspecialchars($value) ?>"
	<?php echo $disabled ? 'disabled' : '' ?>
	<?php echo $hidden ? 'hidden' : '' ?>
	/>
</div>
