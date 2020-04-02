<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Template
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

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$user      = Factory::getUser();
$userId    = $user->id;

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

		if (pressbutton == 'galleries.delete')
		{
			var r = confirm('<?php echo Text::_("COM_REDSLIDER_GALLERIES_DELETE")?>');
			if (r == true)    form.submit();
			else return false;
		}
		form.submit();
	}
</script>
<form action="index.php?option=com_redslider&view=galleries" class="admin" id="adminForm" method="post" name="adminForm">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'searchField' => 'search',
				'searchFieldSelector' => '#filter_search',
				'limitFieldSelector' => '#list_galleries_limit',
				'activeOrder' => $listOrder,
				'activeDirection' => $listDirn
			)
		)
	);
	?>
    <br>
    <div class="box">
	    <?php if (empty($this->items)) : ?>
            <div class="alert alert-info">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <div class="pagination-centered">
                    <h3><?php echo Text::_('COM_REDSLIDER_NOTHING_TO_DISPLAY'); ?></h3>
                </div>
            </div>
	    <?php else : ?>
        <table class="table table-striped">
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
				    <?php echo HTMLHelper::_('rsearchtools.sort', 'JSTATUS', 'g.published', $listDirn, $listOrder); ?>
                </th>
                <th width="1" align="center">
                </th>
                <th class="title" width="auto">
				    <?php echo HTMLHelper::_('rsearchtools.sort', 'COM_REDSLIDER_GALLERY', 'g.title', $listDirn, $listOrder); ?>
                </th>
                <th width="10" nowrap="nowrap">
				    <?php echo HTMLHelper::_('rsearchtools.sort', 'COM_REDSLIDER_ID', 'g.id', $listDirn, $listOrder); ?>
                </th>
            </tr>
            </thead>
            <tbody>
		    <?php $n = count($this->items); ?>

		    <?php foreach ($this->items as $i => $row) :
			    $canCreate		= $user->authorise('core.create',		'com_redslider');
			    $canEdit		= $user->authorise('core.edit',			'com_redslider');
			    $canCheckin		= $user->authorise('core.manage',		'com_checkin') || $row->checked_out == $userId || $row->checked_out == 0;
			    $canEditOwn		= $user->authorise('core.edit.own',		'com_redslider');
			    $canEditState	= $user->authorise('core.edit.state',		'com_redslider');
			    $canChange		= $canEditState && $canCheckin;
			    $editor 		= Factory::getUser($row->checked_out);
			    ?>
                <tr>
                    <td><?php echo $this->pagination->getRowOffset($i); ?></td>
                    <td><?php echo HTMLHelper::_('grid.id', $i, $row->id); ?></td>
                    <td>
					    <?php if ($canEditState): ?>
						    <?php echo HTMLHelper::_('rslidergrid.published', $row->published, $i, 'galleries.', true, 'cb'); ?>
					    <?php else: ?>
						    <?php if ($row->published) : ?>
                                <a class="btn btn-small disabled"><i class="icon-ok-sign icon-green"></i></a>
						    <?php else : ?>
                                <a class="btn btn-small disabled"><i class="icon-remove-sign icon-red"></i></a>
						    <?php endif; ?>
					    <?php endif; ?>
                    </td>
                    <td>
					    <?php if ($row->checked_out) : ?>
						    <?php
						    echo HTMLHelper::_('rslidergrid.checkedout', $i, $editor->name, $row->checked_out_time, 'galleries.', $canCheckin);
						    ?>
					    <?php endif; ?>
                    </td>
                    <td>
					    <?php if ($canEdit) : ?>
						    <?php echo HTMLHelper::_('link', 'index.php?option=com_redslider&task=gallery.edit&id=' . $row->id, $row->title); ?>
					    <?php else : ?>
						    <?php echo $this->escape($row->title); ?>
					    <?php endif; ?>
                    </td>
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
