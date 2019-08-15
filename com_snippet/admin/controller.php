<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

class SnippetController extends BaseController
{

	function display($cachable = false, $urlparams = array())
	{
		$this->default_view = 'items';
		parent::display($cachable, $urlparams);
		return $this;
	}

	public function getAjax()
	{
		$input = Factory::getApplication()->input;
		$model = $this->getModel('ajax');
		$action = $input->getCmd('action');
		$reflection = new ReflectionClass($model);
		$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
		$methodList = array();
		foreach ($methods as $method) {
			$methodList[] = $method->name;
		}
		if (in_array($action, $methodList)) {
			$model->$action();
		}
		exit;
	}
}
