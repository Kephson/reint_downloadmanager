<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// register extbase plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY, 'Reintdlm', 'LLL:EXT:reint_downloadmanager/Resources/Private/Language/locallang.xlf:plugin_label', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Images/download_icon.png'
);

// add wizard icon to the "add new record" in backend
if (TYPO3_MODE == "BE") {
	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["ReintDownloadmanagerWizicon"] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Helper/ReintDownloadmanagerWizicon.php';
}

// add typoscript file
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Download manager');

// load flexform for backend config
$pluginSignature = str_replace('_', '', $_EXTKEY) . '_' . 'reintdlm';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/ControllerActions.xml');

// table for download counter
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_reintdownloadmanager_domain_model_download');
