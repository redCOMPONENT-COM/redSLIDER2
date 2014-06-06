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

<div class="<?php echo $class ?>">

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
