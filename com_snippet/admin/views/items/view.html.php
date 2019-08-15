<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Symfony\Component\Yaml\Exception\RuntimeException;

class SnippetViewItems extends HtmlView
{
	public $items;
	public $pagination;
	public $state;
	public $canDo;
	public $allCount;

	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->allCount = $this->get('ListCount');

		if (count($errors = $this->get('Errors'))) {
			throw new RuntimeException(implode("\n", $errors), 500);
		}

		if ($this->getLayout() !== 'modal') {
			ToolBarHelper::title(Text::_('COM_SNIPPET'), 'puzzle');

			$this->canDo = ContentHelper::getActions('com_snippet')->get('core.manage');
			if ($this->canDo) {
				ToolBarHelper::addNew('item.add');
				if (count($this->items) > 0) {
					ToolBarHelper::editList('item.edit');
					ToolbarHelper::publish('items.publish', 'JTOOLBAR_PUBLISH', true);
					ToolbarHelper::unpublish('items.unpublish', 'JTOOLBAR_UNPUBLISH', true);
					ToolBarHelper::deleteList('COM_SNIPPET_DELETE_QUERY_STRING', 'items.delete', 'JTOOLBAR_DELETE');
				}

				$plugins = PluginHelper::getPlugin('snippetimpex', '');
				if ($plugins) {
					$layout = new JLayoutFile('toolbar.impex');
					ToolBar::getInstance('toolbar')->appendButton('Custom', $layout->render(['plugins' => $plugins, 'cnt' => count($this->items)]), 'impex');
					$tmp = [];
					foreach ($plugins as $plugin) {
						$tmp[] = strtolower($plugin->name);
					}
					$this->plugins = implode(', ', $tmp);
					unset($tmp);
				} else {
					$layout = new JLayoutFile('toolbar.noplugins');
					ToolBar::getInstance('toolbar')->appendButton('Custom', $layout->render([]), 'impex');
				}
			}
			
			ToolbarHelper::divider();
		}

		$layout = new JLayoutFile('toolbar.countitems');
		ToolBar::getInstance('toolbar')->appendButton('Custom', $layout->render(['tCount' => count($this->items), 'aCount' => $this->allCount]), 'options');
		
		if (ContentHelper::getActions('com_snippet')->get('core.admin')) {
			ToolbarHelper::divider();
			ToolBarHelper::preferences('com_snippet');
		}

		parent::display($tpl);
	}

	protected function mbCutString($str, $length, $postfix = '…', $encoding = 'UTF-8')
	{
		if (mb_strlen($str, $encoding) <= $length) {
			return $str;
		}
		$tmp = mb_substr($str, 0, $length, $encoding);
		return mb_substr($tmp, 0, mb_strripos($tmp, ' ', 0, $encoding), $encoding) . $postfix;
	}
}
