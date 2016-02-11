<?php

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
