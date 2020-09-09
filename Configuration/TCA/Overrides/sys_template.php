<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
(static function ($extKey) {
    /* add typoscript file */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($extKey,
        'Configuration/TypoScript',
        'Download manager');
})('reint_downloadmanager');
