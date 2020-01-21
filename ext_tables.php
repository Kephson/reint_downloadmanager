<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

(function () {
    /* table for download counter */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_reintdownloadmanager_domain_model_download');
})();
