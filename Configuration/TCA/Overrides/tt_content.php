<?php
// load flexform for backend config
$rd_pluginSignature = str_replace('_', '', 'reint_downloadmanager') . '_' . 'reintdlm';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$rd_pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($rd_pluginSignature,
    'FILE:EXT:' . 'reint_downloadmanager' . '/Configuration/FlexForms/ControllerActions.xml');
