<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

(static function ($extKey = 'reint_downloadmanager', $iconIdentifier = 'reint-dm-icon') {
    /***************
     * Make the extension configuration accessible
     */
    $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    );
    $dmManagerPackageConfiguration = $extensionConfiguration->get($extKey);

    $extensionName = 'RENOLIT.' . $extKey;
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionName,
        'Reintdlm',
        [
            'Manager' => 'list, topdownloads, empty, filesearch',
        ],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionName,
        'DmList',
        [
            'Manager' => 'list',
        ],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionName,
        'DmTopdownloads',
        [
            'Manager' => 'topdownloads',
        ],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionName,
        'DmFilesearch',
        [
            'Manager' => 'filesearch',
        ],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/cache/frontend/class.t3lib_cache_frontend_variablefrontend.php']['set'][$extKey] =
        \RENOLIT\ReintDownloadmanager\Hooks\SetPageCacheHook::class . '->set';

    /***************
     * Register Icons
     */
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'reint-dm-icon',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:' . $extKey . '/ext_icon.svg']
    );

    /* add a default pageTS if allowed in extension configuration */
    if (!(bool)$dmManagerPackageConfiguration['disableDefaultPageTs']) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extKey . '/Configuration/TsConfig/Default.tsconfig">');
    }
})();
