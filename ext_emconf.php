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
    'version' => '3.0.5',
    'category' => 'plugin',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.99.99',
            'php' => '7.0.0-7.3.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'state' => 'stable',
    'uploadfolder' => false,
    'clearCacheOnLoad' => false,
    'author' => 'Ephraim Härer',
    'author_email' => 'ephraim.haerer@renolit.com',
    'author_company' => 'https://www.renolit.com',
    'autoload' => [
        'psr-4' => [
            'RENOLIT\\ReintDownloadmanager\\' => 'Classes'
        ],
    ],
];
