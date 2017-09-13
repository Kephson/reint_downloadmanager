<?php
/* * *************************************************************
 * Extension Manager/Repository config file for ext "reint_downloadmanager".
 *
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 * ************************************************************* */

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Download manager',
	'description' => 'A simple download manager with different views of file collections as downloadable lists.',
	'category' => 'plugin',
	'version' => '1.6.1',
	'state' => 'stable',
	'uploadfolder' => false,
	'createDirs' => '',
	'clearcacheonload' => false,
	'author' => 'Ephraim Härer',
	'author_email' => 'ephraim.haerer@renolit.com',
	'author_company' => 'www.renolit.com',
	'constraints' =>
	array(
		'depends' =>
		array(
			'typo3' => '7.6.0-8.99.99',
			'php' => '5.5.0-7.1.99',
		),
		'conflicts' =>
		array(
		),
		'suggests' =>
		array(
		),
	),
);

