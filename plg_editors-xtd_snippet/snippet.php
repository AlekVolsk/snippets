<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.Plugin
 * @subpackage  Editors-xtd.snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;

class PlgButtonSnippet extends CMSPlugin
{
	protected $autoloadLanguage = true;

	public function onDisplay($name)
	{
		$link = 'index.php?option=com_snippet&amp;view=items&amp;layout=modal&amp;tmpl=component&amp;' . Session::getFormToken() . '=1&amp;editor=' . $name;

		$button = new CMSObject;
		$button->modal   = true;
		$button->class   = 'btn';
		$button->link    = $link;
		$button->text    = Text::_('PLG_EDITORS-XTD_SNIPPET_BUTTON_TITLE');
		$button->name    = 'puzzle';
		$button->options = "{handler: 'iframe', size: {x: 800, y: 500}}";

		return $button;
	}
}
