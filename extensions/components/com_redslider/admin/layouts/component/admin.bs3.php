<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2017 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

$app    = Factory::getApplication();
$input  = $app->input;
$option = $input->get('option');

// Hack for config view which calls from redCORE
if ($option !== 'com_redslider')
{
	RLayoutHelper::$defaultBasePath = '';

	echo RLayoutHelper::render('component.admin', $displayData);
	return;
}

extract($displayData);

$data = $displayData;

// Get the toolbar early to store buttons as view property and render them in the template
$toolbar = $view->getToolbar();

$content = $view->loadTemplate($tpl);

$displayData['content'] = $content;

/**
 * Handle raw format
 */
$format = $input->getString('format');

if ('raw' === $format)
{
	/** @var RView $view */
	$view = $data['view'];

	if (!$view instanceof RViewBase)
	{
		throw new InvalidArgumentException(
			sprintf(
				'Invalid view %s specified for the component layout',
				get_class($view)
			)
		);
	}

	$toolbar = $view->getToolbar();

	// Get the view render.
	return $content;
}

$templateComponent = 'component' === $input->get('tmpl');
$input->set('tmpl', 'component');
$input->set('redcore', true);

echo RLayoutHelper::render('component.assets');

// For Joomla! 2.5 we will add bootstrap alert messages
if (version_compare(JVERSION, '3.0', '<') && Factory::getApplication()->isAdmin())
{
	// Require the message renderer as it doesn't respect the naming convention.
	$messageRendererPath = JPATH_LIBRARIES . '/redcore/joomla/document/renderer/message.php';

	if (file_exists($messageRendererPath))
	{
		require_once $messageRendererPath;
	}
}

// Do we have to display the sidebar ?
$displaySidebar = false;

if (isset($data['sidebar_display']))
{
	$displaySidebar = (bool) $data['sidebar_display'];
}

$sidebarLayout = '';

// The sidebar layout name.
if ($displaySidebar)
{
	if (!isset($data['sidebar_layout']))
	{
		throw new InvalidArgumentException('No sidebar layout specified in the component layout.');
	}

	$sidebarLayout = $data['sidebar_layout'];
}

$sidebarData = array();

if (isset($data['sidebar_data']))
{
	$sidebarData = $data['sidebar_data'];
}

// Do we have to display the topbar ?
$displayTopbar = false;

if (isset($data['topbar_display']))
{
	$displayTopbar = (bool) $data['topbar_display'];
}

$topbarLayout = '';

// The topbar layout name.
if ($displayTopbar)
{
	if (!isset($data['topbar_layout']))
	{
		throw new InvalidArgumentException('No topbar layout specified in the component layout.');
	}

	$topbarLayout = $data['topbar_layout'];
}

$topbarData = array();

if (isset($displayTopbar))
{
	$topbarData = $data;
}

// The view to render.
if (!isset($data['view']))
{
	throw new InvalidArgumentException('No view specified in the component layout.');
}

/** @var RView $view */
$view = $data['view'];

if (!$view instanceof RViewBase)
{
	throw new InvalidArgumentException(
		sprintf(
			'Invalid view %s specified for the component layout',
			get_class($view)
		)
	);
}

if ($content instanceof Exception)
{
	return $content;
}
?>
<script type="text/javascript">
	jQuery(document).ready(function () {

		<?php if ($input->getBool('disable_topbar') || $input->getBool('hidemainmenu')) : ?>
		jQuery('.topbar').addClass('opacity-70');
		jQuery('.topbar button').prop('disabled', true);
		jQuery('.topbar a').attr('disabled', true).attr('href', '#').addClass('disabled');
		<?php endif; ?>

		<?php if ($input->getBool('disable_sidebar') || $input->getBool('hidemainmenu')) : ?>
		jQuery('.sidebar').addClass('opacity-70');
		jQuery('.sidebar button').prop('disabled', true);
		jQuery('.sidebar a').attr('disabled', true).attr('href', '#').addClass('disabled');
		<?php endif; ?>
	});
</script>
<?php if ($view->getLayout() === 'modal') : ?>
	<div class="row-fluid redcore">
		<section id="component">
			<div class="row-fluid message-sys"  id="message-sys"></div>
			<div class="row-fluid">
				<?php echo $content ?>
			</div>
		</section>
	</div>
<?php elseif ($templateComponent) : ?>
	<div class="container-fluid redcore">
		<div class="span12 content">
			<section id="component">
				<div class="row-fluid">
					<h1><?php echo $view->getTitle() ?></h1>
				</div>
				<div class="row-fluid message-sys" id="message-sys"></div>
				<hr/>
				<div class="row-fluid">
					<?php echo $content ?>
				</div>
			</section>
		</div>
	</div>
<?php
else : ?>
	<?php echo RLayoutHelper::render('component.full', $displayData); ?>
<?php endif;
