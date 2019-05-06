<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'RENOLIT.' . $_EXTKEY, 'Reintdlm', [
    'Manager' => 'list, topdownloads, empty, filesearch',
],
    // non-cacheable actions
    [
        'Manager' => '',
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/cache/frontend/class.t3lib_cache_frontend_variablefrontend.php']['set'][$_EXTKEY] =
    \RENOLIT\ReintDownloadmanager\Hooks\SetPageCacheHook::class . '->set';
