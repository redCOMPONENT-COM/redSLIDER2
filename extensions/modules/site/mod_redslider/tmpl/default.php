<?php
/**
 * @package     RedSLIDER.Frontend
 * @subpackage  mod_redslider
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

?>
<div id="redslider-<?php echo $module->id ?>" class="redslider2 <?php echo $class; ?>">
    <!-- Main slider -->
    <div class="slider">
		<?php if (count($slides)): ?>
            <ul class="slides">
				<?php foreach ($slides as $slide): ?>
					<?php if (isset($slide->template_content)): ?>
                        <li class="<?php echo $slide->class; ?>">
                            <div class="slide-content"><?php echo $slide->template_content ?></div>
                            <div class="slide-img">
								<?php if (isset($slide->backgroundImage)): ?>
                                    <img src="<?php echo $slide->backgroundImage ?>"/>
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
	<?php if ($thumbNums && $slideThumbnail): ?>
        <div class="carousel">
            <ul class="slides">
				<?php foreach ($slides as $slide): ?>
                    <li><img src="<?php echo $slide->backgroundImage ?>"/></li>
				<?php endforeach ?>
            </ul>
        </div>
	<?php endif ?>
    <!-- End thumbnails slider -->
</div>