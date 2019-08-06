<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.core');
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', array('placement' => 'bottom'));
HTMLHelper::_('bootstrap.popover', '.hasPopover', array('placement' => 'bottom'));
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>

<div id="collapseUploadFile" class="collapse">
	<div style="background:#F9F9F9;border:1px solid #e5e5e5;border-radius:4px;padding:10px 20px;margin:0 0 20px 5px;">
		<form action="<?php echo Route::_(Uri::base().'index.php?option=com_snippet&view=items'); ?>" method="post" name="uploadForm" id="uploadFormUsers" class="form-inline" enctype="multipart/form-data">
			<h4><?php echo Text::_('COM_SNIPPET_IMPORT_TITLE'); ?></h4>
			<p><?php echo Text::sprintf('COM_SNIPPET_IMPORT_DESC', $this->plugins); ?></p>
			<hr>
			<div>
				<input type="file" id="upload-file" name="file" /> <button class="btn btn-primary" id="upload-files-submit"><i class="icon-upload icon-white"></i> <?php echo Text::_('JTOOLBAR_UPLOAD'); ?></button>
				<input type="hidden" name="task" value="items.import" />
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>

<form action="<?php echo Route::_('index.php?option=com_snippet&view=items'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">

		<?php
		if (empty($this->items)) {
			if ($this->allCount != 0) {
				echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
		?>
		<div class="alert alert-no-items">
			<?php echo Text::_('COM_SNIPPET_DATA_EMPTY_FROM_FILTER'); ?>
		</div>
		<?php
			} else {
		?>
		<div class="alert alert-no-items">
			<?php echo Text::_('COM_SNIPPET_DATA_EMPTY'); ?>
		</div>
		<?php
			}
		} else {
			echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
		?>
		
		<table class="table table-striped">
			<thead>
				<tr>
					<th width="1%" class="hidden-phone center"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
					<th width="5%" class="center" style="min-width:55px;"><?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?></th>
					<th><?php echo HTMLHelper::_('searchtools.sort', 'COM_SNIPPET_LIST_NAME', 'name', $listDirn, $listOrder); ?></th>
					<th><?php echo Text::_('COM_SNIPPET_LIST_CONTENT'); ?></th>
					<th width="1%" class="hidden-phone center nowrap"><?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->items as $i => $item) { ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center hidden-phone"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
					<td class="center"><?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'items.', $this->canDo, 'cb', null, null); ?></td>
					<td class="nowrap">
						<?php if ($this->canDo) { ?>
						<a href="<?php echo Route::_('index.php?option=com_snippet&task=item.edit&id=' . $item->id); ?>">
						<?php } ?>
						<?php echo $this->escape($item->name); ?>
						<?php if ($this->canDo) { ?>
						</a>
						<?php } ?>
					</td>
					<td><?php echo $this->mbCutString(htmlspecialchars(strip_tags($item->content)), 100); ?></td>
					<td class="center hidden-phone"><?php echo (int)$item->id; ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		
		<?php echo $this->pagination->getListFooter(); ?>
		
		<?php } ?>
		
		<input type="hidden" name="impex" value="" />
		<input type="hidden" name="dwnlfile" value="" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo HTMLHelper::_('form.token'); ?>
		
	</div>
</form>
