.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _developer:

Developer Corner
================

Target group: **Developers**

.. _developer-realurl:

RealUrl example configuration
--------------------

Example realurl configuration you could use:

::
		$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['dlmanager'] = array(
			array(
				'GETvar' => 'tx_reintdownloadmanager_reintdlm[downloaduid]',
			),
		);
