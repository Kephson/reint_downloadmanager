<?php

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Ephraim Härer <ephraim.haerer@renolit.com>, RENOLIT SE
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class that adds the wizard icon.
 */
class ReintDownloadmanagerWizicon {

	/**
	 * Processing the wizard items array
	 *
	 * @param array $wizardItems : The wizard items
	 * @return Modified array with wizard items
	 */
	function proc($wizardItems) {

		$wizardItems['plugins_reint_downloadmanager'] = array(
			'icon' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('reint_downloadmanager') . 'Resources/Public/Images/wizard.png',
			'title' => LocalizationUtility::translate('plugin_label', 'reint_downloadmanager'),
			'description' => LocalizationUtility::translate('plugin_value', 'reint_downloadmanager'),
			'params' => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=reintdownloadmanager_reintdlm'
		);

		return $wizardItems;
	}

}
