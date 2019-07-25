<?php
/* * *************************************************************
 * Extension Manager/Repository config file for ext "reint_downloadmanager".
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 * ************************************************************* */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Download manager',
    'description' => 'A simple download manager with different views of file collections as downloadable lists.',
    'category' => 'plugin',
    'version' => '2.0.3',
    'state' => 'stable',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearcacheonload' => false,
    'author' => 'Ephraim Härer',
    'author_email' => 'ephraim.haerer@renolit.com',
    'author_company' => 'https://www.renolit.com',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99',
            'php' => '7.0.0-7.2.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'RENOLIT\\ReintDownloadmanager\\' => 'Classes'
        ],
    ],
];
