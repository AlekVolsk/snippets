<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.Plugin
 * @subpackage  Editors-xtd.snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Factory;

class plgEditorsXtdSnippetInstallerScript
{
    private $element = 'snippet';

    function postflight($type, $parent)
    {
        if (strtolower($type) === 'uninstall') {
            return true;
        }

        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->update('`#__extensions`')
            ->set('`enabled`=1')
            ->where('`type` = ' . $db->quote('plugin'))
            ->where('`folder` = ' . $db->quote($parent->manifest->attributes()->group[0]))
            ->where('`element` = ' . $db->quote($this->element));
        $db->setQuery($query)->execute();
    }
}
