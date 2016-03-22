<?php

namespace RENOLIT\ReintDownloadmanager\Controller;

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

use \TYPO3\CMS\Core\Messaging\FlashMessage;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Utility\DebugUtility;
use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * ManagerController
 */
class ManagerController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * persistenceManager
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 * @inject
	 */
	protected $persistenceManager;

	/**
	 * downloadRepository
	 * 
	 * @var \RENOLIT\ReintDownloadmanager\Domain\Repository\DownloadRepository
	 * @inject
	 */
	protected $downloadRepository = NULL;

	/**
	 * @var \TYPO3\CMS\Core\Collection\RecordCollectionRepository
	 * @inject
	 */
	protected $collectionRepository;

	/**
	 * @var \TYPO3\CMS\Core\Resource\FileCollectionRepository
	 * @inject
	 */
	protected $fileCollectionRepository;

	/**
	 * @var \TYPO3\CMS\Core\Resource\FileRepository
	 * @inject
	 */
	protected $fileRepository;

	/**
	 * Collections ids to display
	 * @var array
	 */
	protected $collectionIds = array();

	/**
	 * The loaded collections to display
	 * @var array
	 */
	protected $collections = array();

	/**
	 * The collection search strings
	 * @var array
	 */
	protected $collectionSearchStrings = array();

	/**
	 * default TypoScript configuration
	 * 
	 * @var array
	 */
	protected $defaultTsConfig = array(
		'includedefaultjs' => 1,
		'includedefaultcss' => 1,
	);

	/**
	 * initialize the controller
	 *
	 * @return void
	 */
	protected function initializeAction() {
		parent::initializeAction();

		//fallback to current pid if no storagePid is defined
		$configuration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		if (empty($configuration['persistence']['storagePid'])) {
			$currentPid = array();
			$currentPid['persistence']['storagePid'] = $GLOBALS["TSFE"]->id;
			$this->configurationManager->setConfiguration(array_merge($configuration, $currentPid));
		}

		// check settings for css and js
		if (isset($this->settings['includedefaultjs'])) {
			$this->defaultTsConfig['includedefaultjs'] = (int) $this->settings['includedefaultjs'];
		}
		if (isset($this->settings['includedefaultcss'])) {
			$this->defaultTsConfig['includedefaultcss'] = (int) $this->settings['includedefaultcss'];
		}
		$this->defaultTsConfig['topdtitle'] = $this->settings['topdtitle'];
		$this->defaultTsConfig['searchplaceholder'] = $this->settings['searchplaceholder'];
	}

	/**
	 * action filesearch
	 * displays a search field for the defined file collections
	 *
	 * @return void
	 */
	public function filesearchAction() {

		// check if there is a file download request
		$this->checkFileDownloadRequest();

		// include default config
		$this->view->assign('config', $this->defaultTsConfig);

		// load the configured collections from flexform
		$this->loadCollectionsFromFlexform();

		// load the collections from database
		$this->loadCollectionsFromDb();

		// write the search field for collection titles
		$this->writeCollectionTitleSearchfield();

		// assign headline search strings
		$this->view->assign('collectionSearchStrings', $this->collectionSearchStrings);

		// assign the collections to fluid
		$this->view->assign('filecollections', $this->collections);
	}

	/**
	 * action list
	 * displays a list with the defined file collections
	 *
	 * @return void
	 */
	public function listAction() {

		// check if there is a file download request
		$this->checkFileDownloadRequest();

		// include default config
		$this->view->assign('config', $this->defaultTsConfig);

		// load the configured collections from flexform
		$this->loadCollectionsFromFlexform();

		// load the collections from database
		$this->loadCollectionsFromDb();

		//DebuggerUtility::var_dump($this->collections); die();
		// assign the collections to fluid
		$this->view->assign('filecollections', $this->collections);
	}

	/**
	 * action topdownloads
	 * shows a list of the top downloads
	 *
	 * @return void
	 */
	public function topdownloadsAction() {

		// check if there is a file download request
		$this->checkFileDownloadRequest();

		// include default config
		$this->view->assign('config', $this->defaultTsConfig);

		// remove old and deleted files
		$this->cleanupTopDownloads();

		if (isset($this->settings['topdnum']) && (int) $this->settings['topdnum'] > 0) {
			$files = $this->downloadRepository->findTopDownloadList((int) $this->settings['topdnum']);
		} else {
			$files = $this->downloadRepository->findTopDownloadList();
		}

		$filesArray = array();
		$index = 1;

		if (is_object($files)) {
			foreach ($files as $f) {
				$file = $this->fileRepository->findByUid($f->getSysFileUid());
				if (is_object($file) && !is_null($file)) {
					$file->getContents();
					$filesArray[$index] = $file;
					$index++;
				}
			}
		}
		//DebuggerUtility::var_dump($filesArray);

		$this->view->assign('files', $filesArray);
	}

	/**
	 * cleanup the top download table if file was deleted
	 */
	protected function cleanupTopDownloads() {
		$topdownloads = $this->downloadRepository->findAllWithoutPid();
		foreach ($topdownloads as $d) {
			$fileUid = $d->getSysFileUid();
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('uid', 'sys_file', 'uid=' . $fileUid);
			if (!$res) {
				$this->downloadRepository->remove($d);
			}
		}
		$this->persistenceManager->persistAll();
	}

	/**
	 * action empty
	 * nothing selected in flexform
	 *
	 * @return void
	 */
	public function emptyAction() {
		
	}

	/**
	 * write a search field for each file collection as string
	 * includes the *file titles*, *file extensions* and *file keywords*
	 * 
	 */
	protected function writeCollectionTitleSearchfield() {

		if (is_array($this->collections) && !empty($this->collections)) {
			foreach ($this->collections as $key => $col) {
				$searchItems = array();
				foreach ($col as $file) {
					if (is_object($file)) {
						$file->getContents();

						// check if there is a title set for file
						if (method_exists($file, 'getTitle')) {
							$title = $file->getTitle();
						} else if ($file->hasProperty('title')) {
							$title = $file->getProperty('title');
						} else {
							$title = '';
						}
						// check if there is a filename set for file
						if (method_exists($file, 'getName')) {
							$name = $file->getName();
						} else if ($file->hasProperty('name')) {
							$name = $file->getProperty('name');
						} else {
							$name = '';
						}
						// add title and name to search string if not empty
						if (!empty($title)) {
							$searchItems[] = $title;
						} else if (!empty($name)) {
							$searchItems[] = $name;
						}
						$fileExt = $file->getExtension();
						$fileExtLower = strtolower($fileExt);
						if (!empty($fileExt) && !isset($searchItems[$fileExtLower])) {
							$searchItems[$fileExtLower] = $fileExt;
						}
						// check if there are keywords for the file and add them, too
						if ($file->hasProperty('keywords')) {
							$keywords = $file->getProperty('keywords');
							if (!empty($keywords) && $keywords !== NULL) {
								$searchItems[] = $keywords;
							}
						}
					}
				}
				$searchItemString = implode(' ', $searchItems);
				$this->collectionSearchStrings[$key] = $searchItemString;
			}
		}
		//DebuggerUtility::var_dump($this->collectionSearchStrings);
	}

	/**
	 * load all collections from database
	 * 
	 * @return true
	 */
	protected function loadCollectionsFromDb() {

		// check if there are any collections
		if (count($this->collectionIds) > 0) {
			// Get all existing collections
			foreach ($this->collectionIds as $uid) {
				$this->collections[] = $this->fileCollectionRepository->findByUid($uid);
			}

			// Load the records in each file collection
			foreach ($this->collections as $c) {
				$c->loadContents();
				// load and set description of file collection which is not loaded by default
				$c->setDescription($this->getSysFileCollectionData($c->getIdentifier()));
			}
		}
	}

	/**
	 * load all collection ids from flexform fields
	 * 
	 * @return true
	 */
	protected function loadCollectionsFromFlexform() {
		// check if single collections are set
		if (isset($this->settings['lbpid']) && !empty($this->settings['lbpid'])) {
			$uids = explode(',', $this->settings['lbpid']);
			if (count($uids) > 0) {
				foreach ($uids as $uid) {
					$this->collectionIds[$uid] = $uid;
				}
			}
		}

		// check if a folder or page with collections is set
		if (isset($this->settings['dfolder']) && !empty($this->settings['dfolder'])) {
			$pageids = explode(',', $this->settings['dfolder']);
			$this->getCollectionsFromPages($pageids);
		}
		return true;
	}

	/**
	 * load all collection ids from given pages
	 * 
	 * @param array $pageids
	 */
	protected function getCollectionsFromPages($pageids) {

		$table = 'sys_file_collection';
		if (count($pageids) > 0) {
			foreach ($pageids as $pageid) {
				$fileCollections = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						'*', $table, 'pid = ' . $pageid . ' AND hidden=0 AND deleted=0', '', 'sorting', 1000
				);
				if (count($fileCollections) > 0) {
					foreach ($fileCollections as $col) {
						if (!isset($this->collectionIds[$col['uid']])) {
							$this->collectionIds[$col['uid']] = $col['uid'];
						}
					}
				}
			}
		}
	}

	/**
	 * gets additional data for file collections
	 * TODO: why is the field "webdescription" not loaded?
	 *
	 * @return string
	 */
	protected function getSysFileCollectionData($uid, $fieldname = 'webdescription') {

		$table = 'sys_file_collection';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
				'*', $table, 'uid = ' . $uid, '', ''
		);
		if (isset($res[$fieldname])) {
			return $res[$fieldname];
		} else {
			return '';
		}
	}

	/**
	 * check if there is a file download request
	 * 
	 */
	protected function checkFileDownloadRequest() {
		// download file and exit
		if ($this->request->hasArgument('downloaduid')) {
			$this->setDownload();
		}
	}

	/**
	 * sends a file to download if download param is set
	 *
	 * @return void
	 */
	protected function setDownload() {

		if ($this->request->hasArgument('downloaduid')) {

			$recordUid = (int) $this->request->getArgument('downloaduid');

			$fileRepository = $this->objectManager->get('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');
			$file = $fileRepository->getFileObject($recordUid);

			$privateUri = '';
			if (is_object($file)) {
				$publicUri = $file->getPublicUrl();
				$fileName = $file->getName();
				$fileModDate = $file->getProperty('tstamp');
				$privateUri = urldecode($this->checkPublicUriForParams($publicUri));
			}
			//DebuggerUtility::var_dump($privateUri); die();

			if (is_file($privateUri)) {
				// update counter or set new
				$this->updateUserSessionDownloads($recordUid);
				$this->downloadFile($privateUri, $fileName, $publicUri, $fileModDate);
			}
		}
	}

	/**
	 * checks the public uri for params or extension reint_file_timestamp
	 * 
	 * @param string $publicUri
	 */
	protected function checkPublicUriForParams($publicUri) {

		if (ExtensionManagementUtility::isLoaded('reint_file_timestamp') || stripos($publicUri, '?') !== FALSE) {
			$uriFragments = explode('?', $publicUri);
			if (isset($uriFragments[0]) && !empty($uriFragments[0])) {
				$uri = $uriFragments[0];
			}
		} else {
			$uri = $publicUri;
		}

		return $uri;
	}

	/**
	 * stores the download of a file in the user session
	 * 
	 * @param integer $recordUid
	 */
	protected function updateUserSessionDownloads($recordUid) {

		$countEntry = $this->downloadRepository->getOneBySysFileUid($recordUid);
		//DebuggerUtility::var_dump($countEntry);

		$sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'reint_downloadmanager');
		//DebuggerUtility::var_dump($sessionData);

		$newEntry = FALSE;
		if (!$countEntry) {
			$countEntry = $this->objectManager->get('RENOLIT\ReintDownloadmanager\Domain\Model\Download');
			$countEntry->setSysFileUid($recordUid);
			$countEntry->setDownloads(0);
			$newEntry = TRUE;
		}

		// check session for user downloads
		if (!empty($sessionData) && isset($sessionData['downloads'])) {
			$data = explode(',', $sessionData['downloads']);
			// check if download is not set in session then update counter
			if (!in_array($recordUid, $data) && !empty($data)) {
				$sessionData['downloads'] .= ',' . $recordUid;
				$countEntry->setDownloads($countEntry->getDownloads() + 1);
			}
		} else {
			$sessionData = array(
				'downloads' => $recordUid,
			);
			$countEntry->setDownloads($countEntry->getDownloads() + 1);
		}

		if ($newEntry) {
			$this->downloadRepository->add($countEntry);
		} else {
			$this->downloadRepository->update($countEntry);
		}

		// persist the database updates because of exit() in download function
		$this->persistenceManager->persistAll();


		//$sessionData = array(); // reset session
		$GLOBALS['TSFE']->fe_user->setKey('ses', 'reint_downloadmanager', $sessionData);
		$GLOBALS['TSFE']->fe_user->storeSessionData();
	}

	/**
	 * set download headers and download a file
	 * 
	 * @param string $privateUri
	 * @param string $fileName
	 * @param string $publicUri
	 * @param string $fileModDate
	 */
	protected function downloadFile($privateUri, $fileName, $publicUri, $fileModDate = 1) {

		//DebuggerUtility::var_dump($this->settings); die();
		// check if there is a setting to redirect only to the file
		if (isset($this->settings['redirecttofile']) && (int) $this->settings['redirecttofile'] === 1) {
			// add modification date when set in setup
			if (isset($this->settings['addfiletstamp']) && (int) $this->settings['addfiletstamp'] === 1) {
				$fullPublicUri = GeneralUtility::locationHeaderUrl($this->checkPublicUriForParams($publicUri)) . '?v=' . $fileModDate;
			} else {
				$fullPublicUri = GeneralUtility::locationHeaderUrl($publicUri);
			}
			header('Location: ' . $fullPublicUri);
		} else if (is_file($privateUri)) {

			$fileLen = filesize($privateUri);
			$ext = strtolower(substr(strrchr($fileName, '.'), 1));
			$invalid_chars = array('<', '>', '?', '"', ':', '|', '\\', '/', '*', '&');
			$fileName_valid = str_replace($invalid_chars, '', $fileName);

			switch ($ext) {

				//forbidden filetypes
				case 'inc':
				case 'conf':
				case 'sql':
				case 'cgi':
				case 'htaccess':
				case 'php':
				case 'php3':
				case 'php4':
				case 'php5':
					exit;

				default:
					// should be better than 'application/force-download'
					$cType = 'application/octet-stream';
					break;
			}

			$headers = array(
				'Pragma' => 'public',
				'Expires' => -1,
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'public',
				//'Content-Description' => 'File Transfer', // not in http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
				//'Content-Transfer-Encoding' => 'binary', // not in http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
				'Content-Type' => $cType,
				'Content-Disposition' => 'attachment; filename="' . $fileName_valid . '"',
				'Content-Length' => $fileLen
			);

			//DebuggerUtility::var_dump($headers); die();

			ob_clean(); // set to remove wrong headers which crashed some files (e.g. xls, dot, ...)
			foreach ($headers as $header => $data) {
				$this->response->setHeader($header, $data);
			}
			$this->response->sendHeaders();

			@readfile($privateUri);
		} else {
			//DebuggerUtility::var_dump($privateUri);
		}
		exit();
	}

}
