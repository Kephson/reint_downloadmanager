<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// register extbase plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		$_EXTKEY, 'Reintdlm', 'LLL:EXT:reint_downloadmanager/Resources/Private/Language/locallang.xlf:plugin_label', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/download_icon.png'
);

// add wizard icon to the "add new record" in backend
if (TYPO3_MODE == "BE") {
	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["ReintDownloadmanagerWizicon"] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Helper/ReintDownloadmanagerWizicon.php';
}

// add typoscript file
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Download manager');

// load flexform for backend config
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages';
$pluginSignature = str_replace('_', '', $_EXTKEY) . '_' . 'reintdlm';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/Flexforms/ControllerActions.xml');

// table for download counter
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_reintdownloadmanager_domain_model_download', 'EXT:reint_downloadmanager/Resources/Private/Language/locallang_csh_tx_reintdownloadmanager_domain_model_download.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_reintdownloadmanager_domain_model_download');
$GLOBALS['TCA']['tx_reintdownloadmanager_domain_model_download'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:reint_downloadmanager/Resources/Private/Language/locallang_db.xlf:tx_reintdownloadmanager_domain_model_download',
		'label' => 'sys_file_uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'sys_file_uid',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Download.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/tx_reintdownloadmanager_domain_model_download.png'
	),
);

// add sorting for file collections
$newSysCategoryColumns = array(
	'sorting' => array(
		'label' => 'sorting',
		'config' => array(
			'type' => 'passthrough'
		)
	),
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file_collection', $newSysCategoryColumns);

// order the file collections default by the sorting field
$GLOBALS['TCA']['sys_file_collection']['ctrl']['default_sortby'] = 'ORDER BY sorting';
$GLOBALS['TCA']['sys_file_collection']['ctrl']['sortby'] = 'sorting';
