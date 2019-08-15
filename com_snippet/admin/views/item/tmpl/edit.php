<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidation');

Factory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function (task) {
		if (task == 'item.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			msg_valid = '" . $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')) . "';
			Joomla.JText.load({error:'" . $this->escape(Text::_('ERROR')) . "'});
			Joomla.renderMessages({'error':[msg_valid]});
		}
	}
");
?>
<form action="<?php echo Route::_('index.php?option=com_snippet&layout=edit&id=' . $this->form->getValue('id')); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
	
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span9">
				<div class="form-vertical">
				
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
					</div>

					<div class="control-group">
						<div class="controls"><?php echo $this->form->getLabel('note'); ?></div>
					</div>

					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('content'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('content'); ?></div>
					</div>

					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('descript'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('descript'); ?></div>
					</div>

				</div>
			</div>
			<div class="span3">
				<div class="form-vertical">
					
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('published'); ?></div>
					</div>
					
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
					</div>
					
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo Factory::getApplication()->input->getCmd('return'); ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>