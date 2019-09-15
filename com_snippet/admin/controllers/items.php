<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filter\OutputFilter;
use Symfony\Component\Yaml\Exception\RuntimeException;

class SnippetControllerItems extends AdminController
{
	function __construct($config = [])
	{
		parent::__construct($config);
	}

	public function getModel($name = 'Item', $prefix = 'SnippetModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function export()
	{
		$app = Factory::getApplication();

		if (!Session::checkToken()) {
			throw new RuntimeException(Text::_('JINVALID_TOKEN'), 500);
		}

		$uri = Factory::getURI();

		$type = strtolower($app->input->getString('impex', ''));
		if (!$type) {
			$this->setRedirect(Route::_($uri));
			$app->enqueueMessage(Text::_('COM_SNIPPET_EXPORT_TYPEERROR'), 'error');
			return false;
		}
		
		$model = $this->getModel('Items');
		$data = $model->getExpotData();
		if (!$data) {
			$this->setRedirect(Route::_($uri));
			$app->enqueueMessage(Text::_('COM_SNIPPET_EXPORT_NODATA'), 'warning');
			return false;
		}
		$tmp = [];
		foreach ($data as $item) {
			$tmp[$item->name] = ['content' => $item->content, 'descript' => $item->descript];
		}
		$data = $tmp;
		unset($tmp);

		$prm = ComponentHelper::getParams('com_snippet');
		$isSave = $prm->get('filesave', false);
		$saveOnTemp = true;
		if ($isSave) {
			$path = $prm->get('folderpath', '');
			if ($path) {
				$path = Path::clean(JPATH_ROOT . '/' . $path);
			}
			$saveOnTemp = !is_dir($path);
		}
		if ($saveOnTemp) {
            $config = Factory::getConfig();
            $path = Path::clean($config->get('tmp_path'));
		}

		PluginHelper::importPlugin('snippetimpex');
		$dispatcher = \JEventDispatcher::getInstance();
		$result = $dispatcher->trigger('onSnippetDataExport', [$type, $path, $data]);
		$resultCount = 0;
		if ($result) {
			foreach ($result as $item) {
				if (gettype($item) === 'object' && isset($item->message) && $item->message != '') {
					$this->setRedirect(Route::_($uri));
					if ($item->result && $item->file) {
						$app->enqueueMessage(Text::sprintf('COM_SNIPPET_EXPORT_RESULT', $item->type, $item->message, str_replace('\\', '/', $item->file)));
					} else {
						$app->enqueueMessage(Text::sprintf('COM_SNIPPET_EXPORT_RESULT_ERROR', $item->type, $item->message), 'error');
					}
				}
				$resultCount++;
			}
		}
		if (!$resultCount) {
			$this->setRedirect(Route::_($uri));
			$app->enqueueMessage(Text::sprintf('COM_SNIPPET_EXPORT_RESULT_NO_PLUGINS', $type), 'error');
		}
	}

	public function import()
	{
		$app = Factory::getApplication();

		if (!Session::checkToken()) {
			throw new RuntimeException(Text::_('JINVALID_TOKEN'), 500);
		}

		$uri = Factory::getURI();

		$file = \JRequest::getVar('file', null, 'files', 'array');
		if (!(bool) ini_get('file_uploads')) {
			$this->setRedirect(Route::_($uri));
			$app->enqueueMessage(Text::_('COM_SNIPPET_IMPORT_ERROR_NO_LOAD'), 'warning');
			return false;
		}

		if (!is_array($file)) {
			$this->setRedirect(Route::_($uri));
			$app->enqueueMessage(Text::_('COM_SNIPPET_IMPORT_FORM_ERROR_INPUT'), 'warning');
			return false;
		}
		
		if ($file['error'] || $file['size'] < 1) {
			$this->setRedirect(Route::_($uri));
			$app->enqueueMessage(Text::_('COM_SNIPPET_IMPORT_ERROR_FILEEMPTY'), 'warning');
			return false;
		}
		
		$config = Factory::getConfig();
		$tmpSrc = $file['tmp_name'];
		$filename = $file['name'];
		$tmpfilename = Path::clean($config->get('tmp_path') . '/' . $filename);
		File::upload($tmpSrc, $tmpfilename);
		if (!file_exists($tmpfilename)) {
			$this->setRedirect(Route::_($uri));
			$app->enqueueMessage(Text::_('COM_SNIPPET_IMPORT_ERROR_UPLOAD'), 'warning');
			return false;
		}

		PluginHelper::importPlugin('snippetimpex');
		$dispatcher = \JEventDispatcher::getInstance();
		$result = $dispatcher->trigger('onSnippetDataImport', [$tmpfilename]);
		$this->setRedirect(Route::_($uri));
		if ($result) {
			$resultCount = 0;
			foreach ($result as $item) {
				if (gettype($item) === 'object' && isset($item->message) && $item->message != '') {
					if ($item->result && isset($item->data) && is_array($item->data)) {
						if ($item->data) {
							$counter = $this->saveData($item->data);
							if ($counter['c']) {
								$app->enqueueMessage(Text::sprintf('COM_SNIPPET_IMPORT_RESULT', $item->type, $counter['c'], $counter['i'], $counter['u']));
							} else {
								$app->enqueueMessage(Text::sprintf('COM_SNIPPET_IMPORT_RESULT_EMPTY', $item->type), 'error');
							}
						} else {
							$app->enqueueMessage(Text::sprintf('COM_SNIPPET_IMPORT_RESULT_EMPTY', $item->type), 'error');
						}
					} else {
						$app->enqueueMessage(Text::sprintf('COM_SNIPPET_IMPORT_RESULT_ERROR', $item->type, $item->message), 'error');
					}
				}
				$resultCount++;
			}
		}
		if (!$resultCount) {
			$app->enqueueMessage(Text::sprintf('COM_SNIPPET_IMPORT_RESULT_NO_PLUGINS', $filename), 'error');
		}
		unlink($tmpfilename);
	}

	public function dwnl()
	{
		if (!Session::checkToken()) {
			throw new RuntimeException(Text::_('JINVALID_TOKEN'), 500);
		}
		
		$uri = Factory::getURI();

		$prm = ComponentHelper::getParams('com_snippet');
		$saveOnTemp = $prm->get('filesave', false);
		
		$file = Path::clean(Factory::getApplication()->input->getString('dwnlfile', ''));
		
		$this->setRedirect(Route::_($uri));
		
		if (file_exists($file)) {
			if ($this->file_force_download($file) && $saveOnTemp) {
				unlink($file);
				exit;
			}											
		}
	}

    private function file_force_download($file)
    {
        set_time_limit(0);
        if (file_exists($file)) {
            while (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Description: File Transfer');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            return (bool) readfile($file);
        } else {
            return false;
        }
	}
	
	private function saveData($data)
	{
		$db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('`id`, `name`')
            ->from('`#__snippets`');
        $tmp = $db->setQuery($query)->loadObjectList();
        $names = [];
        if ($tmp) {
            foreach ($tmp as $item) {
                $names[$item->name] = $item->id;
            }
        }
        $dbKeys = array_keys($names);
		
		$i = 0;
        $u = 0;
		foreach ($data as $key => $value) {
			$key = str_replace('-', '_', OutputFilter::stringURLSafe($key));
			if ($dbKeys && in_array($key, $dbKeys)) {
                $rec = new stdClass();
                $rec->id = $names[$key];
                $rec->name = $key;
				if (!is_array($value)) {
					$rec->content = $value;
					$rec->descript = '';
				} else {
					$rec->content = $value['content'];
					$rec->descript = $value['descript'];
				}
                $db->updateObject('#__snippets', $rec, 'id');
                unset($rec);
                $u++;
            } else {
                $rec = new stdClass();
                $rec->name = $key;
				if (!is_array($value)) {
					$rec->content = $value;
					$rec->descript = '';
				} else {
					$rec->content = $value['content'];
					$rec->descript = $value['descript'];
				}
                $rec->published = 1;
                $db->insertObject('#__snippets', $rec);
                unset($rec);
                $i++;
            }
		}

		return ['c' => ($i + $u), 'i' => $i, 'u' => $u];
	}
}
