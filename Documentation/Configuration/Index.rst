.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _configuration:

Configuration Reference
=======================

Here you will find a short overview over the TypoScript options.

Target group: **Administrators**


.. _configuration-typoscript:

TypoScript Reference
--------------------

Overwrite the template path or the default storage page id and change the default css like you want it:

::

		plugin.tx_reintdownloadmanager {
		  # configure the path for the templates here
		  view {
			templateRootPaths {
			  0 = EXT:reint_downloadmanager/Resources/Private/Templates/
			  1 = {$plugin.tx_reintdownloadmanager.view.templateRootPath}
			}

			partialRootPaths {
			  0 = EXT:reint_downloadmanager/Resources/Private/Partials/
			  1 = {$plugin.tx_reintdownloadmanager.view.partialRootPath}
			}

			layoutRootPaths {
			  0 = EXT:reint_downloadmanager/Resources/Private/Layouts/
			  1 = {$plugin.tx_reintdownloadmanager.view.layoutRootPath}
			}
		  }

		  # configure a default storage pid here
		  persistence {
			storagePid = {$plugin.tx_reintdownloadmanager.persistence.storagePid}
		  }

		  # configure settings for the extension here
		  settings {
			# use redirecting to the file instead of a download header for all files
			redirecttofile = {$plugin.tx_reintdownloadmanager.settings.redirecttofile}
			# add the modification date when redirecting to file
			addfiletstamp = {$plugin.tx_reintdownloadmanager.settings.addfiletstamp}
			# Include JavaScript for file search
			includedefaultjs = {$plugin.tx_reintdownloadmanager.settings.includedefaultjs}
			# Include default CSS
			includedefaultcss = {$plugin.tx_reintdownloadmanager.settings.includedefaultcss}
		  }
		}

		plugin.tx_reintdownloadmanager_dmlist < plugin.tx_reintdownloadmanager
		plugin.tx_reintdownloadmanager_dmlist.templateName = List

		plugin.tx_reintdownloadmanager_dmtopdownloads < plugin.tx_reintdownloadmanager
		plugin.tx_reintdownloadmanager_dmtopdownloads.templateName = Topdownloads

		plugin.tx_reintdownloadmanager_dmfilesearch < plugin.tx_reintdownloadmanager
		plugin.tx_reintdownloadmanager_dmfilesearch.templateName = Filesearch




**plugin.tx_reintdownloadmanager.settings.redirecttofile = 1** (default: 0)

Makes it possible to redirect to the file after tracking the download, so the browsers default behaviour is used for the file.
The default setting is false, so all files will be downloaded automatically.

-----------------------

**plugin.tx_reintdownloadmanager.settings.addfiletstamp = 0** (default: 0)

When redirecting to file (redirecttofile = 1) is set, it is possible to add the change date as param to the file, to reload in browsers like IE when file was changed.

-----------------------

**plugin.tx_reintdownloadmanager.settings.includedefaultjs = 1** (default: 1)

As default a JS file is included which uses jQuery Mobile for a simple search. File could be removed with this option or in the Fluid template.

-----------------------

**plugin.tx_reintdownloadmanager.settings.includedefaultcss = 1** (default: 1)

As default a CSS file is included. File could be removed with this option or in the Fluid template.