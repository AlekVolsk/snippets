<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.Plugin
 * @subpackage  snippetimpex.json
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filesystem\Path;

class plgSnippetimpexJson extends CMSPlugin
{
    protected $autoloadLanguage = true;

    public function onSnippetDataExport($type, $path, $data)
    {
        $out = new stdClass();
        $out->type = 'json';
        $out->result = false;
        $out->message = '';

        if ($type !== $out->type) {
            return $out;
        }

        $lang = Factory::getLanguage();
        $lang->load('plg_snippetimpex_json', JPATH_ADMINISTRATOR);

        $file = Path::clean($path . '/snippet_' . HTMLHelper::_('date', time(), 'Y-m-d_H-i-s') . '.json');

        $data = json_encode($data, JSON_FORCE_OBJECT);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $out->message = Text::sprintf('PLG_SNIPPETIMPEX_JSON_ERROR_DATA', json_last_error_msg());
        }

        file_put_contents($file, $data);
        if (file_exists($file)) {
            $out->result = true;
            $out->file = $file;
            $out->message = Text::sprintf('PLG_SNIPPETIMPEX_JSON_FILESAVED', str_replace(JPATH_ROOT, '', $file));
        } else {
            $out->message = Text::sprintf('PLG_SNIPPETIMPEX_JSON_ERROR_OUT', str_replace(JPATH_ROOT, '', $file));
        }
        return $out;
    }

    public function onSnippetDataImport($file)
    {
        $out = new stdClass();
        $out->type = 'json';
        $out->result = false;
        $out->message = '';

        $mimesjson = [
            'text/plain',
            'text/json',
            'text/x-json',
            'text/x-javascript',
            'application/json',
            'application/x-json',
            'application/x-javascript'
        ];

        $mime = mime_content_type($file);
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (!in_array($mime, $mimesjson) || $ext !== 'json') {
            return $out;
        }

        $lang = Factory::getLanguage();
        $lang->load('plg_snippetimpex_json', JPATH_ADMINISTRATOR);

        $data = file_get_contents($file);
        if (!$data) {
            $out->message = Text::_('PLG_SNIPPETIMPEX_JSON_UNKNOWN_JSON_DELIMITER');
            return $out;
        }

        $data = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $out->message = Text::_('PLG_SNIPPETIMPEX_JSON_ERROR_DATA', json_last_error_msg());
            return $out;
        }
        
        if (!$data) {
            $out->message = Text::_('PLG_SNIPPETIMPEX_JSON_ERROR_DATA', 'data empty');
            return $out;
        }
        
        $out->result = true;
        $out->data = $data;
        $out->message = Text::_('PLG_SNIPPETIMPEX_JSON_IMPORT_SUCCESS');
        return $out;
    }
}
