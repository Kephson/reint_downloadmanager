.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _changelog:

ChangeLog
=========

- **09.01.2025** -> 4.0.1

  - [BUGFIX] fixed downloads when creating response object
  - thanks to @Bunnyfield

-----------------------

- **05.11.2023** -> 4.0.0

  - initial release for TYPO3 12.4
  - migrated plugins, wizards, etc for TYPO3 12

-----------------------

- **15.09.2022** -> 3.2.4

  - updated documentation with migration information
  - updated fallback for storagePid in top downloads view

-----------------------

- **14.09.2022** -> 3.2.3

  - removed unused field in Flexform of top downloads
  - updated fallback for storagePid to FlexForm values if there is no storage Pid in TSconfig or TypoScript setup defined

-----------------------

- **13.09.2022** -> 3.2.2

  - updated backend layout templates to fix view in TYPO3 v10

-----------------------

- **12.09.2022** -> 3.2.1

  - improved migration wizard to migrate plugin to content element

-----------------------

- **07.09.2022** -> 3.2.0

  - [bugfix] fixed problems with PHP 8.0 and 8.1
  - [bugfix] updated Flexforms and fixed some bugs
  - [feature] updated all templates to Bootstrap 5
  - [feature] removed jQuery mobile and did the search with Vanilla JavaScript
  - cleaned coding and removed old code
  - disabled plugin by default (switchable controller actions)
  - enable only content elements for different views by default
  - updated documentation to latest version with screens of TYPO3 11.5

-----------------------

- **05.04.2022** -> 3.1.1

  - fixed redirect to file with correct function

-----------------------

- **02.04.2022** -> 3.1.0

  - added FlexForm upgrade wizard to migrate controller actions to new config
  - moved download part to own action
  - IMPORTANT: check the upgrade wizard in "Admin Tools -> Upgrade" to fix current flexforms!

-----------------------

- **30.03.2022** -> master

  - removed breaking https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/11.0/Breaking-91909-SysCollectionDatabaseTablesMovedIntoExternalExtension.html
  - fixed output of @readfile

-----------------------

- **15.03.2022** -> master

  - first basic release for TYPO3 11.5 in master

-----------------------

- **26.08.2021** -> master

  - migrated TCA and updated classes
  - new release is TYPO3 10 only

-----------------------

- **21.02.2021** -> 3.0.6

  - added description field for sys_file_collection

-----------------------

- **05.05.2020** -> 3.0.4, 3.0.5

  - bugfix update, fixes #18
  - bugfix update, fixes #13
  - fixed wrong loading of collection description

-----------------------

- **05.05.2020** -> 3.0.3

  - bugfix update, fixes #16
  - fixed wrong loading of collections from FlexForm
  - please update if using version 3+

-----------------------

- **23.01.2020** -> 3.0.2

  - pushed content object data to fluid templates

-----------------------

- **22.01.2020** -> 3.0.1

  - added own plugins for each action to include it easier for editors
  - refactored all coding, removed deprecated functions

-----------------------

- **22.01.2020** -> 3.0.0

  - new release for TYPO3 >= 9.5

-----------------------

- **25.07.2019** -> 2.0.3

  - added translations handling of file collection when using multiple translations

-----------------------

- **25.07.2019** -> 2.0.2

  - updated ViewHelpers and removed depraction
  - fixed wrong getting of arguments in ViewHelper

-----------------------

- **06.05.2019** -> 2.0.0

  - added example for slug configuration
  - updated flexform to allow 999 pages or collections
  - cleaned up coding
  - new release for TYPO3 8.7 and 9.5

-----------------------

- **26.08.2018** -> 1.8.0

  - added example for realurl configuration
  - added Hook for "Page is beeing generated" message if download should start
  - please clear all caches or reinstall extension!

-----------------------

- **22.10.2017** -> 1.7.0

  - added support for use with non-public storage folders
  - added compatibility with fal_securedownload
  - allow downloads from storages outside of webroot
  - code cleanup

-----------------------

- **13.09.2017** -> 1.6.1

  - updated dependencies and composer.json
  - fixed icons for TYPO3 8
  - removed class for wizard icon and added default pageTS

-----------------------

- **12.09.2017** -> 1.6.0

  - updated dependencies & icons

-----------------------

- **10.08.2017** -> 1.5.0

  - updated TCA, reformatted code and fixed ViewHelper
  - updated dependencies and added composer.json file

-----------------------

- **27.10.2016** -> 1.4.5

  - fixed flashmessages for TYPO3 >= 7.6

-----------------------

- **22.07.2016** -> 1.4.4

  - changed limits in FlexForm

-----------------------

- **27.06.2016** -> 1.4.3

  - added better failure handling if file could not found

-----------------------

- **22.03.2016** -> 1.4.2

  - added option to overwrite placeholder for searchfield in FlexForm
  - added option to overwrite top downloads title in FlexForm

-----------------------

- **17.03.2016** -> 1.4.1

  - bugfix update for file search string

-----------------------

- **08.03.2016** -> 1.4.0

  - update your fluid templates to make sure all type of file collections work correct (static, categories, folder)!
  - cleanup of coding

-----------------------

- **11.02.2016** -> 1.3.0

  - update of table description file
  - cleanup of coding

-----------------------

- **04.02.2016** -> 1.2.1

  - TCA corrections
  - Icon update

-----------------------

- **02.02.2016** -> 1.2.0

  - code cleanup
  - TypoScript setup updated to templateRootPaths, partialRootPaths and layoutRootPaths

-----------------------

- **29.01.2016** -> 1.1.2

  - code cleanup

-----------------------

- **12.01.2016** -> 1.1.1

  - compatibility: added support for parameters in public file uri
  - bugfix: fixed issues with PHP versions below 5.5, see https://github.com/Kephson/reint_downloadmanager/issues/3 and https://github.com/Kephson/reint_downloadmanager/issues/4
  - set minimum PHP version to 5.4

-----------------------

- **14.11.2015** -> 1.1.0

  - compatibility: added compatibility for TYPO3 CMS 7 LTS

-----------------------

- **07.07.2015** -> 1.0.5

  - bugfix: improved search string for headline of file collection
  - compatibility: added compatibility for TYPO3 CMS 7

-----------------------

- **23.06.2015** -> 1.0.4

  - bugfix update: fixed problem with deleted files in top downloads view

-----------------------

- **21.05.2015** -> 1.0.3

  - bugfix update see: https://github.com/Kephson/reint_downloadmanager/issues/2

-----------------------

- **01.04.2015** -> 1.0.2

  - added a sorting field for file collections, default ordering is now with sorting field

-----------------------

- **31.03.2015** -> 1.0.1

  - added a sorting field for file collections, default ordering is now with sorting field

-----------------------

- **23.03.2015** -> 1.0.0

  - added a search for files with the option to use the keywords of files to find a file by special keyword (based on `jQuery Mobile filterable <http://api.jquerymobile.com/filterable/>`_)
  - added option to select a folder with file collections instead of single file collections or both
  - added simple default JavaScript file and a simple CSS file (could be excluded, see :ref:`configuration documentation <configuration-typoscript>`)
  - added German translations

-----------------------

- **23.03.2015** -> 0.5.4

  - bugfix update, flexform field couldn't be edited by user groups (removed exclude=1)

-----------------------

- **20.03.2015** -> 0.5.3

  - added a viewhelper for stringtolower
  - added option to redirect to the file after tracking the download instead of downloading all files (see :ref:`configuration documentation <configuration-typoscript>`)

-----------------------

- **04.03.2015** -> 0.5.2

  - bugfix release
  - updated download headers
  - added ob_clean() to fix destroyed downloaded files like xls, xlt, dot, docx
  - fixed wrong reading of files with whitespaces
  - removed not valid file name characters

-----------------------

- **19.02.2015** -> 0.5.1

  - changed state to beta

-----------------------

- **19.02.2015** -> 0.5.0

  - initial release of version 0.5.0 on TER
  - list view
  - top downloads

