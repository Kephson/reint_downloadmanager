<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
if (!defined('TYPO3')) {
    die('Access denied.');
}

(static function ($extKey = 'reint_downloadmanager') {
    /***************
     * Make the extension configuration accessible
     */
    $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    );
    $dmManagerPackageConfiguration = $extensionConfiguration->get($extKey);

    if (isset($dmManagerPackageConfiguration['disableDefaultPlugin']) && !(bool)$dmManagerPackageConfiguration['disableDefaultPlugin']) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            $extKey,
            'Reintdlm',
            [
                \RENOLIT\ReintDownloadmanager\Controller\ManagerController::class => 'list, topdownloads, empty, filesearch, download',
            ],
            [
                \RENOLIT\ReintDownloadmanager\Controller\ManagerController::class => 'download',
            ]
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extKey . '/Configuration/TsConfig/Plugin.tsconfig">');
    }
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extKey,
        'DmList',
        [
            \RENOLIT\ReintDownloadmanager\Controller\ManagerController::class => 'list, download',
        ],
        [
            \RENOLIT\ReintDownloadmanager\Controller\ManagerController::class => 'download',
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extKey,
        'DmTopdownloads',
        [
            \RENOLIT\ReintDownloadmanager\Controller\ManagerController::class => 'topdownloads, download',
        ],
        [
            \RENOLIT\ReintDownloadmanager\Controller\ManagerController::class => 'download',
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extKey,
        'DmFilesearch',
        [
            \RENOLIT\ReintDownloadmanager\Controller\ManagerController::class => 'filesearch, download',
        ],
        [
            \RENOLIT\ReintDownloadmanager\Controller\ManagerController::class => 'download',
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/cache/frontend/class.t3lib_cache_frontend_variablefrontend.php']['set'][$extKey] =
        \RENOLIT\ReintDownloadmanager\Hooks\SetPageCacheHook::class . '->set';

    /* add a default pageTS if allowed in extension configuration */
    if (isset($dmManagerPackageConfiguration['disableDefaultPageTs']) && !(bool)$dmManagerPackageConfiguration['disableDefaultPageTs']) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extKey . '/Configuration/TsConfig/Default.tsconfig">');
    }

})();
