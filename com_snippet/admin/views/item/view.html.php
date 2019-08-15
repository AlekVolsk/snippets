<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Symfony\Component\Yaml\Exception\RuntimeException;

class SnippetViewItem extends HtmlView
{
	public $form;
	public $item;

	public function display($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');

		if (count($errors = $this->get('Errors'))) {
			throw new RuntimeException(implode("\n", $errors), 500);
		}

		$isNew = $this->item->id == 0;
		ToolBarHelper::title(Text::_('COM_SNIPPET_ITEM_TITLE_' . ($isNew ? 'ADD' : 'MOD')), 'puzzle');
		Factory::getApplication()->input->set('hidemainmenu', true);

		$canDo = ContentHelper::getActions('com_snippet');
		if ($canDo->get('core.manage')) {
			ToolBarHelper::apply('item.apply');
			ToolBarHelper::save('item.save');
			ToolBarHelper::save2new('item.save2new');
			ToolBarHelper::save2new('item.save2copy', 'COM_SNIPPET_ITEM_SAVE_TO_COPY');
		}
		if ($isNew) {
			ToolBarHelper::cancel('item.cancel');
		} else {
			ToolBarHelper::cancel('item.cancel', 'JTOOLBAR_CLOSE');
		}

		parent::display($tpl);
	}
}
