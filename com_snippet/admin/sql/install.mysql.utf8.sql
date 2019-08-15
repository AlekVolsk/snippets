/*
 * @package     Joomla.component
 * @subpackage  com_snippet
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

CREATE TABLE IF NOT EXISTS `#__snippets` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`published` INT NOT NULL,
	`name` VARCHAR(100) NOT NULL,
	`content` TEXT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
