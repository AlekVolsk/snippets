<?php defined('_JEXEC') or die('Restricted access');
/*
 * @package     Joomla.Plugin
 * @subpackage  system.snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;

class plgSystemSnippet extends CMSPlugin
{
    public function onAfterRender()
    {
        if (Factory::getDocument()->getType() != 'html') {
            return false;
        }

        $app = Factory::getApplication();

        if ($app->isClient('administrator')) {
            return false;
        }

        $html = $app->getBody();

        if (strpos($html, '</head>') !== false) {
            list($head, $content) = explode('</head>', $html, 2);
        } else {
            $content = $html;
        }

        if (empty($content)) {
            return false;
        }

        if (strpos($content, '{snippet ') === false) {
            return false;
        }

        $pattern = '#\{snippet (\w+)(.*?)}#i';
        if (preg_match_all($pattern, $content, $matches)) {

            foreach ($matches[0] as $j => $match) {
                $tmp = explode($match, $content, 2);
                $before = strtolower(reset($tmp));
                $before = preg_replace('#\s+#', ' ', $before);

                if (strpos($before, '<textarea') !== false) {
                    $tmp = explode('<textarea', $before);
                    $textarea = end($tmp);
                    if (!empty($textarea) && strpos($textarea, '</textarea>') === false) {
                        continue;
                    }
                }

                $snippet_id = $matches[1][$j];
                $content = str_replace($matches[0][$j], $this->getSnippet($snippet_id), $content);
            }

            $html = isset($head) ? ($head . '</head>' . $content) : $content;

            $app->setBody($html);
        }
    }

	protected function getSnippet($snippet)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('`content`')
			->from('`#__snippets`')
			->where('`published` = 1')
			->where($snippet && (int)$snippet > 0 ? '`id` = ' . (int)$snippet : '`name` = ' . $db->quote($snippet));
		$content = $db->setQuery($query)->loadResult();
		return $content;
	}
}
