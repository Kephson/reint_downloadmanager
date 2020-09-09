<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
(static function ($table) {
    /* add columns for file collections */
    $newSysCategoryColumns = array(
        'sorting' => array(
            'label' => 'sorting',
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        'description' => [
            'l10n_mode' => 'prefixLangTitle',
            'exclude' => true,
            'label' => 'LLL:EXT:reint_downloadmanager/Resources/Private/Language/locallang_db.xlf:sys_file_collection.description',
            'config' => [
                'type' => 'text',
                'cols' => 20,
                'rows' => 5,
                'eval' => 'null',
                'placeholder' => '__row|uid_local|metadata|description',
                'mode' => 'useOrOverridePlaceholder',
                'default' => null,
            ]
        ],
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, $newSysCategoryColumns);

    /* order the file collections default by the sorting field */
    $GLOBALS['TCA'][$table]['ctrl']['default_sortby'] = 'ORDER BY sorting';
    $GLOBALS['TCA'][$table]['ctrl']['sortby'] = 'sorting';
})('sys_file_collection');
