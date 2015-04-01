<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
		'RENOLIT.' . $_EXTKEY, 'Reintdlm', array(
	'Manager' => 'list, topdownloads, empty, filesearch',
		),
		// non-cacheable actions
		array(
	'Manager' => '',
		)
);
