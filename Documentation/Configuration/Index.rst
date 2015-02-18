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
		view {
			# cat=plugin.tx_reintdownloadmanager/file; type=string; label=Path to template root (FE)
			templateRootPath = EXT:reint_downloadmanager/Resources/Private/Templates/
			# cat=plugin.tx_reintdownloadmanager/file; type=string; label=Path to template partials (FE)
			partialRootPath = EXT:reint_downloadmanager/Resources/Private/Partials/
			# cat=plugin.tx_reintdownloadmanager/file; type=string; label=Path to template layouts (FE)
			layoutRootPath = EXT:reint_downloadmanager/Resources/Private/Layouts/
		}
		persistence {
			# cat=plugin.tx_reintdownloadmanager//a; type=string; label=Default storage PID
			storagePid =
		}
	}

	plugin.tx_reintdownloadmanager._CSS_DEFAULT_STYLE (

	)

