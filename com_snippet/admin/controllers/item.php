<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;

class SnippetControllerItem extends FormController
{
	function __construct($config = [])
	{
		$this->view_list = 'items';
		parent::__construct($config);
	}

	protected function allowEdit($data = [], $key = 'id')
	{
		return Factory::getUser()->authorise('core.manage', 'com_snippet');
	}
}
