<?php
/**
 * @package     RedSLIDER.Frontend
 * @subpackage  mod_redslider
 *
 * @copyright   Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHtml::_('rjquery.flexslider');

echo '<div class="flexslider">';

if (count($slides))
{
	echo '<ul class="slides">';

	foreach ($slides as $slide)
	{
		if (isset($slide->template_content))
		{
			echo '<li>';
			echo $slide->template_content;
			echo '</li>';
		}
	}

	echo '<li>Some Test</li>';
	echo '</ul>';
}

echo '</div>';
