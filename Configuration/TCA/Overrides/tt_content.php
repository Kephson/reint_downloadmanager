<?php

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

(static function ($extKey) {
    $extensionConfiguration = GeneralUtility::makeInstance(
        ExtensionConfiguration::class
    );
    $dmManagerPackageConfiguration = $extensionConfiguration->get($extKey);

    if (isset($dmManagerPackageConfiguration['disableDefaultPlugin']) && !(bool)$dmManagerPackageConfiguration['disableDefaultPlugin']) {
        /* register extbase plugin */
        $rdPluginSignature = ExtensionUtility::registerPlugin(
            $extKey, 'Reintdlm', 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:plugin_label',
            'EXT:' . $extKey . '/Resources/Public/Images/download_icon.png'
        );
        /* load default Flexform for default download manager plugin */
        ExtensionManagementUtility::addToAllTCAtypes('tt_content', '--div--;Configuration,pi_flexform,', $rdPluginSignature, 'after:subheader');
        ExtensionManagementUtility::addPiFlexFormValue('*', 'FILE:EXT:' . $extKey . '/Configuration/FlexForms/FlexForm.xml', $rdPluginSignature);
    }
})('reint_downloadmanager');
