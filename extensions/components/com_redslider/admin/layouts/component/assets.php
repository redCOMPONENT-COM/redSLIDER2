<?php
/**
 * @package     Aesir.Backend
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2017 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Redslider\Document\Document;

$app = Factory::getApplication();
$doc = Document::getInstance();

RHelperAsset::load('lib/fontawesome4/css/font-awesome.min.css', 'redcore');
RHelperAsset::load('lib/bootstrap3/css/bootstrap.min.css', 'redcore');

$doc->addTopScript(JUri::root(true) . '/media/com_redslider/js/backend.js');

HTMLHelper::_('stylesheet', 'com_redslider/redslider.backend.css', ['relative' => true]);
HTMLHelper::_('script', 'redcore/component.min.js', ['relative' => true]);

// Disable template shit
$doc->disableStylesheet('*administrator/templates/*.css*');
$doc->disableScript('*administrator/templates/*.js*');

// We will apply our own searchtools styles
$doc->disableStylesheet('media/redcore/lib/jquery-searchtools/jquery.searchtools.css');
$doc->disableStylesheet('media/redcore/lib/jquery-searchtools/jquery.searchtools.min.css');
$doc->disableStylesheet('*media/redcore/*chosen*.css');

// Disable redCORE things
$doc->disableScript('*media/redcore/*/jquery.min.js');
$doc->disableScript('*media/redcore/*/jquery-migrate.min.js');
$doc->disableScript('*media/redcore/*/jquery-noconflict.js');
$doc->disableScript('*media/redcore/*/bootstrap.min.js');
$doc->disableScript('*media/redcore/*/bootstrap/js/bootstrap.min.js');

// Disable core things
$doc->disableScript('*media/*/jquery.min.js');
$doc->disableScript('*media/*/jquery-noconflict.js');
$doc->disableScript('*media/*/jquery-migrate.min.js');
$doc->disableScript('*media/*/bootstrap.min.js');
$doc->disableScript('*media/*/mootools-core.js');
$doc->disableScript('*media/*/mootools-more.js');
$doc->disableScript('*media/*/modal.js');
