<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
if (!defined('TYPO3')) {
    die('Access denied.');
}

(static function () {
    /* table for download counter */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_reintdownloadmanager_domain_model_download');
})();
