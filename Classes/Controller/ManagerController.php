<?php

namespace RENOLIT\ReintDownloadmanager\Controller;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Ephraim Härer <ephraim.haerer@renolit.com>, RENOLIT SE
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
use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
	 * initialize the controller
	 *
	 * @return void
	 */
	protected function initializeAction() {
		parent::initializeAction();
		
		//fallback to current pid if no storagePid is defined
		$configuration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		if( empty($configuration['persistence']['storagePid']) ) {
			$currentPid = array();
			$currentPid['persistence']['storagePid'] = $GLOBALS["TSFE"]->id;
			$this->configurationManager->setConfiguration(array_merge($configuration, $currentPid));
		}
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
	 * action list
	 * displays a list with the defined file collections
	 *
	 * @return void
	 */
	public function listAction() {

		// check if there is a file download request
		$this->checkFileDownloadRequest();
		
		
		$collections = array();

		if( isset($this->settings['lbpid']) && !empty($this->settings['lbpid']) ) {

			$uids = explode(',', $this->settings['lbpid']);
			
			// Get all existing collections
			foreach( $uids as $uid ) {
				$collections[] = $this->fileCollectionRepository->findByUid($uid);
			}

			// Load the records in each file collection
			foreach( $collections as $c ) {
				$c->loadContents();
				// load and set description which is not loaded by default
				$c->setDescription($this->getSysFileCollectionData($c->getIdentifier()));
			}
		}

		$this->view->assign('filecollections', $collections);
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
		
		if( isset($this->settings['topdnum']) && (int) $this->settings['topdnum'] > 0 ){
			$files = $this->downloadRepository->findTopDownloadList((int) $this->settings['topdnum']);
		}else {
			$files = $this->downloadRepository->findTopDownloadList();
		}
		
		$filesArray = array();
		$index = 1;
		
		if( is_object($files) ){
			foreach($files as $f ){
				
				$fileRepository = $this->objectManager->get('\\TYPO3\\CMS\\Core\\Resource\\FileRepository');
				$file = $fileRepository->findByUid($f->getSysFileUid());
				if( is_object($file) ){
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
	 * gets additional data for file collections
	 * TODO: why is the field "webdescription" not loaded?
	 *
	 * @return string
	 */
	protected function getSysFileCollectionData( $uid, $fieldname = 'webdescription' ) {

		$table = 'sys_file_collection';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
				'*', $table, 'uid = ' . $uid, '', ''
		);
		if( isset($res[$fieldname]) ) {
			return $res[$fieldname];
		}
		else {
			return '';
		}
	}
	
	/**
	 * check if there is a file download request
	 * 
	 */
	protected function checkFileDownloadRequest(){
		// download file and exit
		if( $this->request->hasArgument('downloaduid') ) {
			$this->setDownload();
			exit;
		}
	}

	/**
	 * sends a file to download if download param is set
	 *
	 * @return void
	 */
	protected function setDownload() {

		if( $this->request->hasArgument('downloaduid') ) {

			$recordUid = (int) $this->request->getArgument('downloaduid');

			$fileRepository = $this->objectManager->get('\\TYPO3\\CMS\\Core\\Resource\\ResourceFactory');
			$file = $fileRepository->getFileObject($recordUid);
			$publicUri = $file->getPublicUrl();
			$fileName = $file->getName();

			if( is_file($publicUri) ) {
				// update counter or set new
				$this->updateUserSessionDownloads($recordUid);
				$this->downloadFile($publicUri, $fileName);
			}
		}
		
	}

	/**
	 * stores the download of a file in the user session
	 * 
	 * @param integer $recordUid
	 */
	protected function updateUserSessionDownloads( $recordUid ) {

		$countEntry = $this->downloadRepository->getOneBySysFileUid($recordUid);
		//DebuggerUtility::var_dump($countEntry);

		$sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'reint_downloadmanager');
		//DebuggerUtility::var_dump($sessionData);

		$newEntry = FALSE;
		if( !$countEntry ) {
			$countEntry = $this->objectManager->get('\\RENOLIT\\ReintDownloadmanager\\Domain\\Model\\Download');
			$countEntry->setSysFileUid($recordUid);
			$countEntry->setDownloads(0);
			$newEntry = TRUE;
		}

		// check session for user downloads
		if( !empty($sessionData) && isset($sessionData['downloads']) ) {
			$data = explode(',', $sessionData['downloads']);
			// check if download is not set in session then update counter
			if( !in_array($recordUid, $data) && !empty($data) ) {
				$sessionData['downloads'] .= ',' . $recordUid;
				$countEntry->setDownloads($countEntry->getDownloads() + 1);
			}
		}
		else {
			$sessionData = array(
				'downloads' => $recordUid,
			);
			$countEntry->setDownloads($countEntry->getDownloads() + 1);
		}

		if( $newEntry ) {
			$this->downloadRepository->add($countEntry);
		}
		else {
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
	 * @param string $publicUri
	 * @param string $fileName
	 */
	protected function downloadFile( $publicUri, $fileName ) {

		if( is_file($publicUri) ) {

			$fileLen = filesize($publicUri);
			$ext = strtolower(substr(strrchr($fileName, '.'), 1));

			switch( $ext ) {

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
					$cType = 'application/force-download';
					break;
			}

			$headers = array(
				'Pragma' => 'public',
				'Expires' => 0,
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'public',
				'Content-Description' => 'File Transfer',
				'Content-Type' => $cType,
				'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
				'Content-Transfer-Encoding' => 'binary',
				'Content-Length' => $fileLen
			);

			foreach( $headers as $header => $data ) {
				$this->response->setHeader($header, $data);
			}
			$this->response->sendHeaders();
			@readfile($publicUri);
		}
		exit;
	}

}
