<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use RENOLIT\ReintDownloadmanager\Controller\ManagerController;
use RENOLIT\ReintDownloadmanager\Hooks\SetPageCacheHook;

if (!defined('TYPO3')) {
    die('Access denied.');
}

(static function ($extKey = 'reint_downloadmanager') {
    /***************
     * Make the extension configuration accessible
     */
    $extensionConfiguration = GeneralUtility::makeInstance(
        ExtensionConfiguration::class
    );
    $dmManagerPackageConfiguration = $extensionConfiguration->get($extKey);

    ExtensionUtility::configurePlugin(
        $extKey,
        'DmList',
        [
            ManagerController::class => 'list, download',
        ],
        [
            ManagerController::class => 'download',
        ]
    );
    ExtensionUtility::configurePlugin(
        $extKey,
        'DmTopdownloads',
        [
            ManagerController::class => 'topdownloads, download',
        ],
        [
            ManagerController::class => 'download',
        ]
    );
    ExtensionUtility::configurePlugin(
        $extKey,
        'DmFilesearch',
        [
            ManagerController::class => 'filesearch, download',
        ],
        [
            ManagerController::class => 'download',
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/cache/frontend/class.t3lib_cache_frontend_variablefrontend.php']['set'][$extKey] =
        SetPageCacheHook::class . '->set';

})();
