<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
(function (
    $tablename = 'tt_content',
    $contentType = 'reintdownloadmanager_dmfilesearch',
    $iconName = 'reint-dm-icon',
    $extKey = 'reint_downloadmanager'
) {
    \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA'][$tablename], [
        'ctrl' => [
            'typeicon_classes' => [
                $contentType => $iconName,
            ],
        ],
        'types' => [
            $contentType => [
                'showitem' => implode(',', [
                    '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general',
                    '--palette--;;general',
                    'pi_flexform',
                    '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;;frames,--palette--;;appearanceLinks,',
                    '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,',
                    '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                      --palette--;;hidden,
                      --palette--;;access,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
                         categories,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                         rowDescription,
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,'
                ]),
            ],
        ],
        'columns' => [
            'pi_flexform' => [
                'config' => [
                    'ds' => [
                        '*,' . $contentType => 'FILE:EXT:' . $extKey . '/Configuration/FlexForms/ContentElements/FileSearch.xml',
                    ],
                ],
            ],
        ],
    ]);

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
        $tablename,
        'CType',
        [
            'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:celem3_ce',
            $contentType,
            $iconName,
        ],
        'textmedia',
        'after'
    );
})();
