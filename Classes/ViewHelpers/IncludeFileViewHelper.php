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
        $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
        if (!$pageRenderer) {
            return;
        }

        if (TYPO3_MODE === 'FE') {
            $this->arguments['path'] = $GLOBALS['TSFE']->tmpl->getFileName($this->arguments['path']);
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
