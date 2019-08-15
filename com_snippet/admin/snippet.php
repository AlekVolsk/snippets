<?php defined( '_JEXEC' ) or die;
/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Access\Exception\NotAllowed;

if (!Factory::getUser()->authorise('core.manage', 'com_snippet')) {
	throw new NotAllowed(Text::_('JERROR_ALERTNOAUTHOR'), 403);
}

$controller = BaseController::getInstance('snippet');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
