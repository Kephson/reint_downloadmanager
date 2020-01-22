<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

(function ($extKey = 'reint_downloadmanager', $iconIdentifier = 'reint-dm-icon') {
    $extensionName = 'RENOLIT.' . $extKey;
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionName,
        'Reintdlm',
        [
            'Manager' => 'list, topdownloads, empty, filesearch',
        ],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionName,
        'DmList',
        [
            'Manager' => 'list',
        ],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionName,
        'DmTopdownloads',
        [
            'Manager' => 'topdownloads',
        ],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $extensionName,
        'DmFilesearch',
        [
            'Manager' => 'filesearch',
        ],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/cache/frontend/class.t3lib_cache_frontend_variablefrontend.php']['set'][$extKey] =
        \RENOLIT\ReintDownloadmanager\Hooks\SetPageCacheHook::class . '->set';

    /***************
     * Register Icons
     */
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'reint-dm-icon',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:' . $extKey . '/ext_icon.svg']
    );

    /* add a default pageTS */
    /* @see https://docs.typo3.org/typo3cms/extensions/fluid_styled_content/7.6/AddingYourOwnContentElements/Index.html */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
    mod {
        wizards.newContentElement.wizardItems {
            plugins {
                elements {
                    plugins_' . $extKey . ' {
                        iconIdentifier = ' . $iconIdentifier . '
                        title = LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:plugin_label
                        description = LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:plugin_value
                        tt_content_defValues {
                            CType = list
                            list_type = reintdownloadmanager_reintdlm
                        }
                    }
                }
                show := addToList(plugins_' . $extKey . ')
            }
            common {
                elements {
                    reintdownloadmanager_dmlist {
                        iconIdentifier = ' . $iconIdentifier . '
                        title = LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:celem1_ce
                        description = LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:celem1_ce_desc
                        tt_content_defValues {
                            CType = reintdownloadmanager_dmlist
                        }
                    }
                }
                elements {
                    reintdownloadmanager_dmtopdownloads {
                        iconIdentifier = ' . $iconIdentifier . '
                        title = LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:celem2_ce
                        description = LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:celem2_ce_desc
                        tt_content_defValues {
                            CType = reintdownloadmanager_dmtopdownloads
                        }
                    }
                }
                elements {
                    reintdownloadmanager_dmfilesearch {
                        iconIdentifier = ' . $iconIdentifier . '
                        title = LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:celem3_ce
                        description = LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:celem3_ce_desc
                        tt_content_defValues {
                            CType = reintdownloadmanager_dmfilesearch
                        }
                    }
                }
                show := addToList(reintdownloadmanager_dmlist,reintdownloadmanager_dmtopdownloads,reintdownloadmanager_dmfilesearch)
            }
        }
        web_layout.tt_content.preview.reintdownloadmanager_dmlist = EXT:' . $extKey . '/Resources/Private/Templates/CEPreview/List.html
        web_layout.tt_content.preview.reintdownloadmanager_dmtopdownloads = EXT:' . $extKey . '/Resources/Private/Templates/CEPreview/Topdownloads.html
        web_layout.tt_content.preview.reintdownloadmanager_dmfilesearch = EXT:' . $extKey . '/Resources/Private/Templates/CEPreview/Filesearch.html
    }
    ');
})();
