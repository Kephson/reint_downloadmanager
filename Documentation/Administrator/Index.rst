.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _admin-manual:

Administrator Manual
====================

Target group: **Administrators**

This extension is easy to install and to configure.
There are no dependencies to other extension, only to TYPO3 core.

The extension suggests to install EXT:fal_securedownload if needed
and EXT:filemetadata to add keywords to files.


.. _admin-installation:

Installation
------------

To install the extension, perform the following steps:

With composer:

#. Install extension via composer with **composer req renolit/reint-downloadmanager**
#. Load the static TypoScript constants and setup to your site extension


.. _admin-updates:

Updates
-------

There are some migrations which should be done after upgrading the extension to a version newer than version 3.1.0.
Have a look at the :ref:`Known Problems section <known-problems>` on how to upgrade.


.. _admin-configuration:

Configuration
-------------

#. Copy the templates (/Resources/Private/Templates/) and layouts (/Resources/Private/Layouts/) from the extension folder to a folder in your own sitepackage if you want to edit them
#. Configure your new template pathes in TypoScript
#. Include the TypoScript of your sitepackage after the extension template
#. Style the output like you want it


.. _admin-faq:

FAQ
---

Is it possible to use my own templates?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
Yes, please copy the templates from the extension to your own folder (e.g. EXT:my_site_package/Resources/Private/Templates/Ext/) and configure the path in the TypoScript of the extension. See also step 1 and 2 in configuration.

What to do if there are no files shown in frontend?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
Please check your storagePid settings in TypoScript or TSconfig or define the folder in the FlexForm in the plugin on the page.


.. _admin-changes:

Important changes
-----------------

.. important::

   Upgrade to version >= 3.1: there are important changes since version 3.0 and later

  - Please compare all the templates between the new version and your local templates
  - Check the upgrade wizard in "Admin Tools -> Upgrade" section and execute them if needed
  - Have a look at the :ref:`Known Problems section <known-problems>` on how to upgrade


