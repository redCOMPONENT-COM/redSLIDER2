<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Slide
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('rdropdown.init');
HTMLHelper::_('rbootstrap.tooltip');

$saveOrderLink = 'index.php?option=com_redslider&task=slides.saveOrderAjax&tmpl=component';
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$ordering = ($listOrder == 's.ordering');
$saveOrder = ($listOrder == 's.ordering' && strtolower($listDirn) == 'asc');
$search = $this->state->get('filter.search');

$user   = Factory::getUser();
$userId = $user->id;

if ($saveOrder)
{
    HTMLHelper::_('rsortablelist.sortable', 'table-items', 'adminForm', strtolower($listDirn), $saveOrderLink, false, true);
}

?>
<script type="text/javascript">
	Joomla.submitbutton = function (pressbutton)
	{
		submitbutton(pressbutton);
	}

	submitbutton = function (pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton)
		{
			form.task.value = pressbutton;
		}

		if (pressbutton == 'slides.delete')
		{
			var r = confirm('<?php echo Text::_("COM_REDSLIDER_SLIDES_DELETE")?>');
			if (r == true)    form.submit();
			else return false;
		}
		form.submit();
	}
</script>
<form action="index.php?option=com_redslider&view=slides" class="admin" id="adminForm" method="post" name="adminForm">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'searchField' => 'search',
				'searchFieldSelector' => '#filter_search',
				'limitFieldSelector' => '#list_slides_limit',
				'activeOrder' => $listOrder,
				'activeDirection' => $listDirn
			)
		)
	);
	?>
    <br>
	<?php if (empty($this->items)) : ?>
	<div class="alert alert-info">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<div class="pagination-centered">
			<h3><?php echo Text::_('COM_REDSLIDER_NOTHING_TO_DISPLAY'); ?></h3>
		</div>
	</div>
	<?php else : ?>
        <div class="box">
	<table class="table table-striped" id="table-items">
		<thead>
			<tr>
				<th width="10" align="center">
					<?php echo '#'; ?>
				</th>
				<th width="10">
					<?php if (version_compare(JVERSION, '3.0', 'lt')) : ?>
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					<?php else : ?>
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					<?php endif; ?>
				</th>
				<th width="30" nowrap="nowrap">
					<?php echo HTMLHelper::_('rsearchtools.sort', 'JSTATUS', 's.published', $listDirn, $listOrder); ?>
				</th>
				<?php if ($search == ''): ?>
				<th width="30">
					<?php echo HTMLHelper::_('rsearchtools.sort', '<i class=\'icon-sort\'></i>', 's.ordering', $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>
				<th width="1" align="center">
				</th>
				<th class="title" width="auto">
					<?php echo HTMLHelper::_('rsearchtools.sort', 'COM_REDSLIDER_SLIDE', 's.title', $listDirn, $listOrder); ?>
				</th>
				<th width="20">
					<?php echo Text::_('COM_REDSLIDER_SLIDE_SECTION') ?>
				</th>
				<th class="title" width="auto">
					<?php echo HTMLHelper::_('rsearchtools.sort', 'COM_REDSLIDER_GALLERY', 'gallery_title', $listDirn, $listOrder); ?>
				</th>
				<th class="title" width="auto">
					<?php echo HTMLHelper::_('rsearchtools.sort', 'COM_REDSLIDER_TEMPLATE', 'template_title', $listDirn, $listOrder); ?>
				</th>
				<th class="title" width="auto">
					<?php echo HTMLHelper::_('rsearchtools.sort', 'COM_REDSLIDER_LANGUAGE', 'language_title', $listDirn, $listOrder); ?>
				</th>
				<th class="title" width="auto">
					<?php echo HTMLHelper::_('rsearchtools.sort', 'COM_REDSLIDER_FIELD_PUBLISH_UP_LABEL', 'publish_up', $listDirn, $listOrder); ?>
				</th>
				<th class="title" width="auto">
					<?php echo HTMLHelper::_('rsearchtools.sort', 'COM_REDSLIDER_FIELD_PUBLISH_DOWN_LABEL', 'publish_down', $listDirn, $listOrder); ?>
				</th>
				<th width="10" nowrap="nowrap">
					<?php echo HTMLHelper::_('rsearchtools.sort', 'COM_REDSLIDER_ID', 's.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php $n = count($this->items); ?>
			<?php foreach ($this->items as $i => $row) :
				$canCreate    = $user->authorise('core.create', 'com_redslider');
				$canEdit      = $user->authorise('core.edit', 'com_redslider');
				$canCheckin   = $user->authorise('core.manage', 'com_checkin') || $row->checked_out == $userId || $row->checked_out == 0;
				$canEditOwn   = $user->authorise('core.edit.own', 'com_redslider');
				$canEditState = $user->authorise('core.edit.state', 'com_redslider');
				$canChange    = $canEditState && $canCheckin;
				$editor       = JFactory::getUser($row->checked_out);
				$orderkey     = array_search($row->id, $this->ordering[0]);
				?>

				<tr>
					<td><?php echo $this->pagination->getRowOffset($i); ?></td>
					<td><?php echo HTMLHelper::_('grid.id', $i, $row->id); ?></td>
					<td>
						<?php if ($canEditState): ?>
							<?php echo HTMLHelper::_('rslidergrid.published', $row->published, $i, 'slides.', true, 'cb'); ?>
						<?php else: ?>
							<?php if ($row->published) : ?>
								<a class="btn btn-small disabled"><i class="icon-ok-sign icon-green"></i></a>
							<?php else : ?>
								<a class="btn btn-small disabled"><i class="icon-remove-sign icon-red"></i></a>
							<?php endif; ?>
						<?php endif; ?>
					</td>
					<?php if ($search == ''): ?>
					<td class="order nowrap center">
						<span class="sortable-handler hasTooltip <?php echo ($saveOrder) ? '' : 'inactive'; ?>">
						<i class="icon-move"></i>
						</span>
						<input type="text" style="display:none" name="order[]" value="<?php echo $orderkey + 1;?>" class="text-area-order" />
					</td>
					<?php endif; ?>
					<td>
						<?php if ($row->checked_out) : ?>
							<?php echo HTMLHelper::_('rslidergrid.checkedout', $i, $editor->name, $row->checked_out_time, 'slides.', $canCheckin) ?>
						<?php endif; ?>
					</td>
					<td>
						<?php if ($canEdit) : ?>
							<?php echo HTMLHelper::_('link', 'index.php?option=com_redslider&task=slide.edit&id=' . $row->id, $row->title) ?>
						<?php else : ?>
							<?php echo $this->escape($row->title); ?>
						<?php endif; ?>
					</td>
					<td><?php echo Text::_('PLG_' . $row->section . '_NAME') ?></td>
					<td><?php echo $row->gallery_title ?></td>
					<td><?php echo $row->template_title ?></td>
					<td>
						<?php if ($row->language == '*'): ?>
							<?php $language = Text::alt('JALL', 'language'); ?>
						<?php else:?>
							<?php $language = $row->language_title ? $this->escape($row->language_title) : Text::_('JUNDEFINED'); ?>
						<?php endif;?>
						<small><?php echo $language ?></small>
					</td>
					<td><?php echo $row->publish_up; ?></td>
					<td><?php echo $row->publish_down; ?></td>
					<td>
						<?php echo $row->id; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
    </div>
        <?php echo RLayoutHelper::render('list.pagination', ['pagination' => $this->pagination]) ?>
	<?php endif; ?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
