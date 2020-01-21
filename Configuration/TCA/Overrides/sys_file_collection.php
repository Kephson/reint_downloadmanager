<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
(function ($table) {
    /* add sorting for file collections */
    $newSysCategoryColumns = array(
        'sorting' => array(
            'label' => 'sorting',
            'config' => array(
                'type' => 'passthrough'
            )
        ),
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, $newSysCategoryColumns);

    /* order the file collections default by the sorting field */
    $GLOBALS['TCA'][$table]['ctrl']['default_sortby'] = 'ORDER BY sorting';
    $GLOBALS['TCA'][$table]['ctrl']['sortby'] = 'sorting';
})('sys_file_collection');
