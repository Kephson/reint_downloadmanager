.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _changelog:

ChangeLog
=========

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

