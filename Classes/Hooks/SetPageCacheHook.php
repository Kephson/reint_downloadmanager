<?php

namespace RENOLIT\ReintDownloadmanager\Hooks;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017-2023 Ephraim Härer <ephraim.haerer@renolit.com>, RENOLIT SE
 *  (c) 2018 Benjamin Franzke <bfr@qbus.de>
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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Cache\Backend\RedisBackend;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class SetPageCacheHook
{

    /**
     * @param array $params
     * @param FrontendInterface $frontend
     */
    public function set(array &$params, FrontendInterface $frontend): void
    {
        if ($frontend->getIdentifier() !== 'cache_pages') {
            return;
        }

        $request = $this->getRequest();
        $extParams = $request->getParsedBody()['tx_reintdownloadmanager_reintdlm'] ?? $request->getQueryParams()['tx_reintdownloadmanager_reintdlm'] ?? null;

        if (isset($params['variable']['temp_content']) && $params['variable']['temp_content'] && is_array($extParams) && isset($extParams['downloaduid'])) {
            /* We can't prevent temp_content ('Page is being generated') from going into cache.
             * But lifetime '-1' will immediately invalidate the temporary cache entry,
             * which is enough, so that it is never used. */
            $params['lifetime'] = -1;

            if ($frontend->getBackend() instanceof RedisBackend) {
                /* The redis backend does not allow lifetime of -1, use 1 as a workaround.
                 * That means the temporary record will be stored to cache, but as we set the
                 * 'variable' to false, it is interpreted as unset in TSFE:
                 * https://github.com/TYPO3/TYPO3.CMS/blob/8.5.1/typo3/sysext/frontend/Classes/Controller/TypoScriptFrontendController.php#L2352 */
                $params['lifetime'] = 1;
                /* We may move `'variable' = false` out this if in a non-bugfix release,
                 * but for now we leave it here to make sure we do not break things. */
                $params['variable'] = false;
            }
        }
    }

    /**
     * @return ServerRequestInterface
     */
    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
