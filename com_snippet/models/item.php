<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filter\OutputFilter;

class SnippetModelItem extends AdminModel
{
	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm('com_snippet.item', 'item', ['control' => 'jform', 'load_data' => $loadData]);
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	public function getTable($type = 'snippet_list', $prefix = 'Table', $config = [])
	{
		return Table::getInstance($type, $prefix, $config);
	}

	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_snippet.edit.item.data', []);
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	protected function canDelete($record)
	{
		if (!empty($record->id)) {
			return Factory::getUser()->authorise('core.manage', 'com_snippet');
		}
	}

	protected function canEditState($record)
	{
		if (!empty($record->id)) {
			return Factory::getUser()->authorise('core.manage', 'com_snippet');
		} else {
			return parent::canEditState('com_snippet');
		}
	}
	
	public function publish(&$pks, $value = 1)
	{
		return parent::publish($pks, $value);
	}

	public function save($data)
	{
		$data['name'] = str_replace('-', '_', OutputFilter::stringURLSafe($data['name']));
		return parent::save($data);
	}
}
