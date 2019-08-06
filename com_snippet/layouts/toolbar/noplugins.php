<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Language\Text;

echo '<button class="btn btn-small" onclick="Joomla.renderMessages({\'error\':[\'' . Text::_('COM_SNIPPET_TOOLBAR_NOIMPEX_MESSAGE') . '\']});"><i class="icon-loop"></i> ' . Text::_('COM_SNIPPET_TOOLBAR_IMPEX_TITLE') . '</button>';
