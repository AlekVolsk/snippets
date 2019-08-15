<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

$app = Factory::getApplication();

if ($app->isClient('site')) {
	Session::checkToken('get') or die(Text::_('JINVALID_TOKEN'));
}

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.polyfill', ['event'], 'lt IE 9');
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', ['placement' => 'bottom']);
HTMLHelper::_('bootstrap.popover', '.hasPopover', ['placement' => 'bottom']);
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$searchFilterDesc = $this->filterForm->getFieldAttribute('search', 'description', null, 'filter');
HTMLHelper::_('bootstrap.tooltip', '#filter_search', ['title' => JText::_($searchFilterDesc), 'placement' => 'bottom']);

$function  = $app->input->getCmd('function', 'jSelectSnippet');
$editor    = $app->input->getCmd('editor', '');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$onclick   = $this->escape($function);

if (!empty($editor)) {
	Factory::getDocument()->addScriptOptions('xtd-snippets', ['editor' => $editor]);
	$onclick = "jSelectSnippet";
}
?>
<div class="container-popup">

	<form action="<?php echo Route::_('index.php?option=com_snippet&view=items&layout=modal&tmpl=component&function=' . $function . '&' . Session::getFormToken() . '=1&editor=' . $editor); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">

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
		<table class="table table-striped table-condensed">
			<thead>
				<tr>
					<th width="5%" class="center" style="min-width:55px;"><?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?></th>
					<th><?php echo HTMLHelper::_('searchtools.sort', 'COM_SNIPPET_LIST_NAME', 'name', $listDirn, $listOrder); ?></th>
					<th><?php echo Text::_('COM_SNIPPET_LIST_CONTENT'); ?></th>
					<th width="1%" class="hidden-phone center nowrap"><?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $item) { ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center"><span class="<?php echo 'icon-' . ($this->escape($item->published) ? '' : 'un') . 'publish'; ?>" aria-hidden="true"></span></td>
					<td class="nowrap">
						<a class="select-snippet-link" href="javascript:void(0)" data-function="<?php echo $this->escape($onclick); ?>" data-name="<?php echo $this->escape($item->name); ?>"><?php echo $this->escape($item->name); ?></a>

						<?php if ($item->descript) { ?>
						<div class="small"><?php echo $this->escape($item->descript); ?></div>
						<?php } ?>
					</td>
					<td><?php echo $this->mbCutString(htmlspecialchars(strip_tags($item->content)), 50); ?></td>
					<td class="center hidden-phone"><?php echo (int)$item->id; ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php } ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo HTMLHelper::_('form.token'); ?>

	</form>
</div>

<?php
Factory::getDocument()->addScriptDeclaration("
(function() {
	'use strict';
	window.jSelectSnippet = function (name) {
		var editor, tag;

		if (!Joomla.getOptions('xtd-snippets')) {
			window.parent.jModalClose();
			return false;
		}

		editor = Joomla.getOptions('xtd-snippets').editor;

		tag = '{snippet ' + name + '}';

		if (window.parent.Joomla && window.parent.Joomla.editors && window.parent.Joomla.editors.instances && window.parent.Joomla.editors.instances.hasOwnProperty(editor)) {
			window.parent.Joomla.editors.instances[editor].replaceSelection(tag)
		} else {
			window.parent.jInsertEditorText(tag, editor);
		}

		window.parent.jModalClose();
	};

	document.addEventListener('DOMContentLoaded', function(){
		var elements = document.querySelectorAll('.select-snippet-link');

		for(var i = 0, l = elements.length; l>i; i++) {
			elements[i].addEventListener('click', function (event) {
				event.preventDefault();
				var functionName = event.target.getAttribute('data-function');

				if (functionName === 'jSelectSnippet') {
					window[functionName](event.target.getAttribute('data-name'));
				} else {
					window.parent[functionName](event.target.getAttribute('data-name'));
				}
			})
		}
	});
})();
");