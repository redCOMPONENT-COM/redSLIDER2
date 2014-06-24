<?php
/**
 * @package     RedSLIDER.Frontend
 * @subpackage  mod_redslider
 *
 * @copyright   Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

?>

<div id="redSLIDER2" class="<?php echo $moduleclass_sfx.$class.$layout ?>" >

<div class="slider" >

<?php if (count($slides)): ?>

	<ul class="slides">

		<?php foreach ($slides as $slide): ?>

			<?php if (isset($slide->template_content)): ?>

				<li><?php echo $slide->template_content ?></li>

			<?php endif ?>

		<?php endforeach ?>

	</ul>

	<?php endif ?>

</div>

<?php if (count($slides) && $slideThumbnail): ?>

<div class="carousel" >

	<ul class="slides">

		<?php foreach ($slides as $slide): ?>

			<?php $params = new JRegistry($slide->params); ?>

			<?php $thumbnail = $params->get('background_image'); ?>

				<li><img src="<?php echo JURI::base() . $thumbnail ?>" /></li>

		<?php endforeach ?>

	</ul>

</div>

<?php endif ?>

</div>