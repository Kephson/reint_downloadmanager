<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

(static function ($extKey) {
    $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    );
    $dmManagerPackageConfiguration = $extensionConfiguration->get($extKey);

    if (!(bool)$dmManagerPackageConfiguration['disableDefaultPlugin']) {
        /* load default Flexform for default download manager plugin */
        $rdPluginSignature = str_replace('_', '', $extKey) . '_' . 'reintdlm';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$rdPluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($rdPluginSignature,
            'FILE:EXT:' . $extKey . '/Configuration/FlexForms/FlexForm.xml');

        /* register extbase plugin */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            $extKey, 'Reintdlm', 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:plugin_label',
            'EXT:' . $extKey . '/Resources/Public/Images/download_icon.png'
        );
    }
})('reint_downloadmanager');
