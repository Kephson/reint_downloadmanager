<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// register extbase plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $_EXTKEY, 'Reintdlm', 'LLL:EXT:reint_downloadmanager/Resources/Private/Language/locallang.xlf:plugin_label',
    'EXT:reint_downloadmanager/Resources/Public/Images/download_icon.png'
);

// load flexform for backend config
$pluginSignature = str_replace('_', '', $_EXTKEY) . '_' . 'reintdlm';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature,
    'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/ControllerActions.xml');

// table for download counter
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_reintdownloadmanager_domain_model_download');

/***************
 * Register Icons
 */
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
    'reint-dm-icon',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:reint_downloadmanager/ext_icon.svg']
);

/* add a default pageTS
 * @see https://docs.typo3.org/typo3cms/extensions/fluid_styled_content/7.6/AddingYourOwnContentElements/Index.html
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
mod.wizards.newContentElement.wizardItems.plugins {
	elements {
		plugins_reint_downloadmanager {
			iconIdentifier = reint-dm-icon
			title = LLL:EXT:reint_downloadmanager/Resources/Private/Language/locallang.xlf:plugin_label
			description = LLL:EXT:reint_downloadmanager/Resources/Private/Language/locallang.xlf:plugin_value
			tt_content_defValues {
				CType = list
				list_type = reintdownloadmanager_reintdlm
			}
		}
	}
	show := addToList(plugins_reint_downloadmanager)
}
');
