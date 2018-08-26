<?php

namespace RENOLIT\ReintDownloadmanager\ViewHelpers;

/**
 * This file is part of the TYPO3 CMS project.
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 * The TYPO3 project - inspiring people to share!
 */

/**
 * ViewHelper to include a css/js file
 * adapted from extension news
 * http://typo3.org/extensions/repository/view/news
 * # Example: Basic example
 * <code>
 * <r:includeFile path="{settings.cssFile}" />
 * </code>
 * <output>
 * This will include the file provided by {settings} in the header
 * </output>
 *
 * @package TYPO3
 * @subpackage reint_downloadmanager
 */
class IncludeFileViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * Include a CSS/JS file
     *
     * @param string $path Path to the CSS/JS file which should be included
     * @param string $name Name of the file
     * @param boolean $compress Define if file should be compressed
     * @return void
     */
    public function render($path, $name = '', $compress = false)
    {
        // Retrieve pagerenderer instance
        $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
        if (!$pageRenderer) {
            return;
        }

        if (TYPO3_MODE === 'FE') {
            $path = $GLOBALS['TSFE']->tmpl->getFileName($path);
            if ($name === '') {
                $name = 'dmfile' . strtolower(basename($path));
            }
            if (strtolower(substr($path, -3)) === '.js') {
                // JS
                $pageRenderer->addJsFooterLibrary($name, $path, false, $compress, false, '', true);
            } elseif (strtolower(substr($path, -4)) === '.css') {
                // CSS
                $pageRenderer->addCssFile($path, 'stylesheet', 'all', '', $compress);
            }
        } else {
            if (strtolower(substr($path, -3)) === '.js') {
                // JS
                $pageRenderer->addJsFile($path, null, $compress);
            } elseif (strtolower(substr($path, -4)) === '.css') {
                // CSS
                $pageRenderer->addCssFile($path, 'stylesheet', 'all', '', $compress);
            }
        }
    }
}
