<?php

namespace RENOLIT\ReintDownloadmanager\ViewHelpers;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * ViewHelper to output an object property or and array element with key
 * Based on: http://typo3.3.n7.nabble.com/Fluid-Variable-zusammbauen-array-key-td193.html
 *
 * # Example: Basic example
 * <code>
 * {r:disp(obj:'{obj}',prop:'{prop}')}
 * </code>
 * <output>
 * This will include the file provided by {settings} in the header
 * </output>
 *
 * @package TYPO3
 * @subpackage reint_downloadmanager
 */
class SimpleDisplayViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Output and object element with property or
	 * an array element with the given key
	 *
	 * @param object $obj object or array
	 * @param string $prop property or key
	 * @return void
	 */
	public function render($obj, $prop) {
		if (is_object($obj)) {
			return $obj->$prop;
		} elseif (is_array($obj)) {
			if (array_key_exists($prop, $obj)) {
				return $obj[$prop];
			}
		}
		return NULL;
	}

}
