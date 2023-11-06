<?php

namespace RENOLIT\ReintDownloadmanager\ViewHelpers;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017-2023 Ephraim Härer <ephraim.haerer@renolit.com>, RENOLIT SE
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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to output an object property or and array element with key
 * Based on: http://typo3.3.n7.nabble.com/Fluid-Variable-zusammbauen-array-key-td193.html
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
class SimpleDisplayViewHelper extends AbstractViewHelper
{

    /**
     * initialize arguments
     * https://docs.typo3.org/typo3cms/ExtbaseFluidBook/9.5/8-Fluid/8-developing-a-custom-viewhelper.html
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('obj', 'mixed', 'Object or array', false);
        $this->registerArgument('prop', 'string', 'Property or key', false);
    }

    /**
     * Output and object element with property or
     * an array element with the given key
     *
     * @return mixed
     */
    public function render(): mixed
    {
        $obj = $this->arguments['obj'];
        $prop = $this->arguments['prop'];
        if (is_object($obj)) {
            return $obj->$prop;
        } elseif (is_array($obj)) {
            if (array_key_exists($prop, $obj)) {
                return $obj[$prop];
            }
        }
        return null;
    }
}
