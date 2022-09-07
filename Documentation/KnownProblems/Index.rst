.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _known-problems:

Known Problems
==============

Please use the `bugtracker system on Github <https://github.com/Kephson/reint_downloadmanager/issues>`_ and report every bug you will find.
I will fix the problems as soon as possible and send you feedback.


.. important::

   My plugins appears as **INVALID VALUE ("reintdownloadmanager_reintdlm")** after upgrade to version >= 3.2.0

  - Please check the upgrade wizard "EXT:reint_downloadmanager - Migrate Plugins to Content elements" in "Admin Tools -> Upgrade" to migrate the plugins to content elements.
  - To remove the Switchable Controller Actions (future ready) the content elements have to be migrated


.. important::

   Flexforms are "broken" after upgrade to version >= 3.1.0

  - Please check the upgrade wizard "EXT:reint_downloadmanager - Migrate Flexforms" in "Admin Tools -> Upgrade" to fix current flexforms.
  - New action was introduced to download the files, the upgrade wizard will migrate the current flexform values to the new values


