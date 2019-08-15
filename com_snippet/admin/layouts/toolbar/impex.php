<?php defined('_JEXEC') or die;
/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Joomla\CMS\Language\Text;

?>
<div class="btn-group">
    <button class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-loop"></i>&nbsp;<?php echo Text::_('COM_SNIPPET_TOOLBAR_IMPEX_TITLE'); ?>&nbsp;&nbsp;<span class="caret" style="margin-top:10px;"></span></button>
    <ul class="dropdown-menu">
        <li><a href="javascript:void(0);" data-toggle="collapse" data-target="#collapseUploadFile"><?php echo Text::_('COM_SNIPPET_TOOLBAR_IMPORT_TITLE'); ?></a></li>
        <?php if ($displayData['cnt'] > 0) { ?>
        <li class="divider"></li>
        <?php foreach ($displayData['plugins'] as $plugin) { ?>
        <li><a href="javascript:void(0);" onclick="document.querySelector('input[name=\'impex\']').value='<?php echo $plugin->name; ?>';Joomla.submitbutton('items.export');"><?php echo Text::sprintf('COM_SNIPPET_TOOLBAR_EXPORT_TITLE', ucfirst(strtolower($plugin->name))); ?></a></li>
        <?php }} ?>
    </ul>
</div>
