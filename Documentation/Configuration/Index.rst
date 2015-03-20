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
				templateRootPath = {$plugin.tx_reintdownloadmanager.view.templateRootPath}
				partialRootPath = {$plugin.tx_reintdownloadmanager.view.partialRootPath}
				layoutRootPath = {$plugin.tx_reintdownloadmanager.view.layoutRootPath}
			}
			# configure a default storage pid here
			persistence {
				storagePid = {$plugin.tx_reintdownloadmanager.persistence.storagePid}
			}
			# configure settings for the extension here
			settings {
				# use redirecting to the file instead of a download header for all files
				redirecttofile = {$plugin.tx_reintdownloadmanager.settings.redirecttofile}
			}
		}

		plugin.tx_reintdownloadmanager._CSS_DEFAULT_STYLE (

		)



With the setting

**plugin.tx_reintdownloadmanager.settings.redirecttofile = 1**

it is possible to redirect to the file after tracking the download, so the browsers default behaviour is used for the file.
The default setting is false, so all files will be downloaded automatically.