.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _changelog:

ChangeLog
=========

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

