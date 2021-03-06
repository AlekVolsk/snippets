<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.Plugin
 * @subpackage  snippetimpex.csv
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filesystem\Path;

class plgSnippetimpexCsv extends CMSPlugin
{
    protected $autoloadLanguage = true;

    public function onSnippetDataExport($type, $path, $data)
    {
        $out = new stdClass();
        $out->type = 'csv';
        $out->result = false;
        $out->message = '';

        if ($type !== $out->type) {
            return $out;
        }

        $lang = Factory::getLanguage();
        $lang->load('plg_snippetimpex_csv', JPATH_ADMINISTRATOR);

        $file = Path::clean($path . '/snippet_' . HTMLHelper::_('date', time(), 'Y-m-d_H-i-s') . '.csv');

        $this->setCSV($file, $data, $this->params->get('delimiter', ';'), $this->params->get('bom', true));
        if (file_exists($file)) {
            $out->result = true;
            $out->file = $file;
            $out->message = Text::sprintf('PLG_SNIPPETIMPEX_CSV_FILESAVED', str_replace(JPATH_ROOT, '', $file));
        } else {
            $out->message = Text::sprintf('PLG_SNIPPETIMPEX_CSV_ERROR_OUT', str_replace(JPATH_ROOT, '', $file));
        }
        return $out;
    }

    public function onSnippetDataImport($file)
    {
        $out = new stdClass();
        $out->type = 'csv';
        $out->result = false;
        $out->message = '';

        $mimesCSV = [
            'text/plain',
            'text/csv',
            'text/x-csv',
            'application/csv',
            'application/x-csv'
        ];

        $mime = mime_content_type($file);
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (!in_array($mime, $mimesCSV) || $ext !== 'csv') {
            return $out;
        }

        $lang = Factory::getLanguage();
        $lang->load('plg_snippetimpex_csv', JPATH_ADMINISTRATOR);

        $data = $this->parseCsvFile($file);
        if (!$data) {
            $out->message = Text::_('PLG_SNIPPETIMPEX_CSV_UNKNOWN_CSV_DELIMITER');
            return $out;
        }

        $out->result = true;
        $out->data = $data;
        $out->message = Text::_('PLG_SNIPPETIMPEX_CSV_IMPORT_SUCCESS');
        return $out;
    }

    private function parseCsvFile($filename, $colDelimiter = '', $rowDelimiter = '')
    {
        if (!file_exists($filename)) {
            return false;
        }

        ini_set('auto_detect_line_endings', true);

        $cont = trim(file_get_contents($filename));
        
        // A special hack for Russian bastards who do not know how to save files in the correct encoding
        $cont = strpos($cont, 'я') !== false ? $cont : mb_convert_encoding($cont, 'UTF-8', 'CP1251');

        if (!$rowDelimiter) {
            $rowDelimiter = "\r\n";
            if (false === strpos($cont, "\r\n")) {
                $rowDelimiter = "\n";
            }
        }

        $lines = explode($rowDelimiter, trim($cont));
        $lines = array_filter($lines);
        $lines = array_map('trim', $lines);

        if (!$colDelimiter) {
            $lines30 = array_slice($lines, 0, 30);

            foreach ($lines30 as $line) {
                if (!strpos($line, ',')) {
                    $colDelimiter = ';';
                }
                if (!strpos($line, ';')) {
                    $colDelimiter = ',';
                }
                if ($colDelimiter) {
                    break;
                }
            }

            if ($colDelimiter === '') {
                $delim_counts = [';' => [], ',' => []];
                foreach ($lines as $line) {
                    $delim_counts[','][] = substr_count($line, ',');
                    $delim_counts[';'][] = substr_count($line, ';');
                }
                $delim_counts = array_map('array_filter', $delim_counts);
                $delim_counts = array_map('array_count_values', $delim_counts);
                $delim_counts = array_map('max', $delim_counts);
                if ($delim_counts[';'] === $delim_counts[',']) {
                    return false;
                }
                $colDelimiter = array_search(max($delim_counts), $delim_counts);
            }
        }

        $data = [];
        $out = [];
        foreach ($lines as $key => $line) {
            $data[] = str_getcsv($line, $colDelimiter);
            unset($lines[$key]);
        }
        foreach ($data as $item) {
            if (count($item) > 3) {
                $out[$item[0]] = ['content' => $item[1], 'descript' => $item[2]];
            } else {
                switch (count($item)) {
                    case 3:
                        $out[$item[0]] = ['content' => $item[1], 'descript' => $item[2]];
                        break;
                    case 2:
                        $out[$item[0]] = $item[1];
                        break;
                    case 1:
                        $out[$item[0]] = '';
                        break;
                    case 0:
                        break;
                }
            }
        }

        return $out;
    }

    private function setCSV($file, $data, $delimiter = ';', $bom = false)
    {
        if (($handle = fopen($file, 'w')) !== false) {
            if ($bom) {
                fwrite($handle, "\xEF\xBB\xBF");
            }
            foreach ($data as $key => $item) {
                if (!is_array($item)) {
                    $item = [$key, $item];
                } else {
                    $item = [$key, $item['content'], $item['descript']];
                }
                fputcsv($handle, $item, $delimiter);
            }
            fclose($handle);
        }
    }
}
