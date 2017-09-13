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

// add typoscript file
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('reint_downloadmanager', 'Configuration/TypoScript', 'Download manager');