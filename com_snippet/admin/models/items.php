<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\MVC\Model\ListModel;

class SnippetModelItems extends ListModel
{
	public function __construct($config = [])
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = ['published', 'name', 'id'];
		}
		parent::__construct($config);
	}

	protected function populateState($ordering = 'name', $direction = 'asc')
	{
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string'));
		$this->setState('filter.published', $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string'));
		parent::populateState($ordering, $direction);
	}

	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$query = $this->getDbo()->getQuery(true)
			->select('`id`, `published`, `name`, `content`, `descript`')
			->from('#__snippets');

		$published = $this->getState('filter.published');
		if ($published !== '') {
			$query->where('`published` = ' . (int) $published);
		}

		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $this->getDbo()->Quote('%' . $this->getDbo()->escape($search, true) . '%');
			$query->where('`name` like ' . $search . ' or `descript` like ' . $search);
		}

		$listOrder = $this->getState('list.ordering', '`name`');
		$listDirn  = $this->getState('list.direction', 'asc');
		$query->order($this->_db->quoteName($listOrder) . ' ' . $this->_db->escape($listDirn));

		return $query;
	}

	public function getListCount()
	{
		$query = $this->getDbo()->getQuery(true)
			->select('count(*)')
			->from('#__snippets');
		return (int) $this->getDbo()->setQuery($query)->loadResult();
	}

	public function getExpotData()
	{
		$query = $this->getDbo()->getQuery(true)
			->select('`name`, `content`, `descript`')
			->from('#__snippets');
		return $this->getDbo()->setQuery($query)->loadObjectList();
	}
}
