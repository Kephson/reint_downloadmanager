<?php

namespace RENOLIT\ReintDownloadmanager\ViewHelpers;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017-2020 Ephraim Härer <ephraim.haerer@renolit.com>, RENOLIT SE
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

use TYPO3\CMS\Frontend\Resource\FilePathSanitizer;
use \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Page\PageRenderer;

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
class IncludeFileViewHelper extends AbstractViewHelper
{

    /**
     * initialize arguments
     * https://docs.typo3.org/typo3cms/ExtbaseFluidBook/9.5/8-Fluid/8-developing-a-custom-viewhelper.html
     */
    public function initializeArguments()
    {
        $this->registerArgument('path', 'string', 'Path to file', true);
        $this->registerArgument('name', 'string', 'Name of file', false, '');
        $this->registerArgument('compress', 'boolean', 'Compress', false, 'false');
    }

    public function render()
    {
        // Retrieve pagerenderer instance
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        if (!$pageRenderer) {
            return;
        }

        if (TYPO3_MODE === 'FE') {
            $this->arguments['path'] = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize((string)$this->arguments['path']);
            if ($this->arguments['name'] === '') {
                $this->arguments['name'] = 'dmfile' . strtolower(basename($this->arguments['path']));
            }
            if (strtolower(substr($this->arguments['path'], -3)) === '.js') {
                // JS
                $pageRenderer->addJsFooterLibrary($this->arguments['name'], $this->arguments['path'], false,
                    $this->arguments['compress'], false, '', true);
            } elseif (strtolower(substr($this->arguments['path'], -4)) === '.css') {
                // CSS
                $pageRenderer->addCssFile($this->arguments['path'], 'stylesheet', 'all', '',
                    $this->arguments['compress']);
            }
        } else {
            if (strtolower(substr($this->arguments['path'], -3)) === '.js') {
                // JS
                $pageRenderer->addJsFile($this->arguments['path'], null, $this->arguments['compress']);
            } elseif (strtolower(substr($this->arguments['path'], -4)) === '.css') {
                // CSS
                $pageRenderer->addCssFile($this->arguments['path'], 'stylesheet', 'all', '',
                    $this->arguments['compress']);
            }
        }
    }
}
