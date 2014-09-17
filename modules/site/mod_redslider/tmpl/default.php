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
<div id="redSLIDER2" class="<?php echo $class; ?>" >
	<!-- Main slider -->
	<div class="slider" >
	<?php if (count($slides)): ?>
		<ul class="slides">
			<?php foreach ($slides as $slide): ?>
				<?php if (isset($slide->template_content)): ?>
				<li class="<?php echo $slide->class; ?>">
					<div class="slide-content" ><?php echo $slide->template_content ?></div>
					<div class="slide-img" >
						<?php if (isset($slide->background)): ?>
							<img src="<?php echo JURI::base() . $slide->background ?>" />
						<?php endif; ?>
					</div>
				</li>
				<?php endif ?>
			<?php endforeach ?>
		</ul>
		<?php endif ?>
	</div>
	<!-- End main slider -->

	<!-- Thumbnails slider -->
	<?php if (count($slides) && $slideThumbnail): ?>
		<div class="carousel" >
			<ul class="slides">
				<?php foreach ($slides as $slide): ?>
					<li><img src="<?php echo JURI::base() . $slide->background ?>" /></li>
				<?php endforeach ?>
			</ul>
		</div>
	<?php endif ?>
	<!-- End thumbnails slider -->
</div>