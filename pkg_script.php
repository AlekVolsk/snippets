<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.Package
 * @subpackage  snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\CMS\Language\Text;
use Joomla\CMS\FileSystem\Path;
use Joomla\CMS\FileSystem\Folder;

class pkg_snippetInstallerScript
{
    private $minPhpVersion = '5.6.0';
    private $name = '«Snippet Package»';

    function preflight($type, $parent)
    {
        if (strtolower($type) === 'uninstall') {
            return true;
        }

        $msg = '';
        $ver = new Version();

        $minJoomlaVersion = $parent->manifest->attributes()->version[0];
        if (version_compare($ver->getShortVersion(), $minJoomlaVersion, 'lt')) {
            $msg .= Text::sprintf('PKG_SNIPPET_JOOMLA_COMPATIBLE', $this->name, $minJoomlaVersion);
        }

        if (version_compare(phpversion(), $this->minPhpVersion, 'lt')) {
            $msg .= Text::sprintf('PKG_SNIPPET_PHP_COMPATIBLE', $this->name, $this->minPhpVersion);
        }

        if ($msg) {
            Factory::getApplication()->enqueueMessage($msg, 'error');
            return false;
        }

        Folder::create(Path::clean(JPATH_ROOT . '/files'), 0755);
    }
}
