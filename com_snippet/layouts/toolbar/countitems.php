<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Language\Text;

echo 
    '<span style="display:inline-block;padding:0 15px;font-size:12px;line-height:25.5px;border:1px solid #d6e9c6;border-radius:3px;background-color:#dff0d8;color:#3c763d;">' . 
    Text::sprintf('COM_SNIPPET_COUNT_ITEMS_VIEW', $displayData['tCount'], $displayData['aCount']) . 
    '</span>';
