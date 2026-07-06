<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(static function ($extKey) {
    /* add TypoScript file */
    ExtensionManagementUtility::addStaticFile($extKey,
        'Configuration/TypoScript',
        'Download manager');
})('reint_downloadmanager');
