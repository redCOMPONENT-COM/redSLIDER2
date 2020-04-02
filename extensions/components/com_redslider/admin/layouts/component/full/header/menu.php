<?php
/**
 * @package     Aesir.Backend
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2017 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

extract($displayData);

$logoutUrl = Route::_('index.php?option=com_login&task=logout&' . Session::getFormToken() . '=1');
$user      = Factory::getUser();
?>
<ul class="nav navbar-nav">
	<li>
		<a href="<?php echo Route::_('index.php?option=com_users&task=user.edit&id=' . $user->id) ?>">
			<span class="hidden-xs"><?php echo $user->name ?></span>
		</a>
	</li>
	<li>
		<a href="<?php echo Route::_('index.php') ?>"><i class="fa fa-joomla"></i></a>
	</li>
	<li>
		<a href="<?php echo Uri::root() ?>" target="_blank"><i class="fa fa-external-link"></i></a>
	</li>
	<li>
		<a href="<?php echo $logoutUrl ?>"><i class="fa fa-sign-out"></i></a>
	</li>
</ul>
