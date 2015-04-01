<?php

namespace RENOLIT\ReintDownloadmanager\ViewHelpers\Format;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Ephraim Härer <ephraim.haerer@renolit.com>, RENOLIT SE
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * Formats string with all alphabetic characters converted to lowercase.
 * @see http://www.php.net/manual/en/function.strtolower.php
 *
 * = Examples =
 *
 * <code title="Defaults">
 * <f:format.strtolower>This is an example</f:format.number>
 * </code>
 * <output>
 * this is an example
 * </output>
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class StrtolowerViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Format the string with strtolower()
	 *
	 * @return string The formatted string
	 * @author Ephraim Härer <ephraim.haerer@renolit.com>
	 * @api
	 */
	public function render() {
		return strtolower($this->renderChildren());
	}

}
