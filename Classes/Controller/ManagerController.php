<?php

namespace RENOLIT\ReintDownloadmanager\Controller;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017-2020 Ephraim Härer <ephraim.haerer@renolit.com>, RENOLIT SE
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

use \RENOLIT\ReintDownloadmanager\Domain\Model\Download;
use \RENOLIT\ReintDownloadmanager\Domain\Repository\DownloadRepository;
use \TYPO3\CMS\Core\Collection\RecordCollectionRepository;
use \TYPO3\CMS\Core\Context\Context;
use \TYPO3\CMS\Core\Database\Query\QueryBuilder;
use \TYPO3\CMS\Core\Messaging\FlashMessage;
use \TYPO3\CMS\Core\Resource\File;
use \TYPO3\CMS\Core\Resource\FileCollectionRepository;
use \TYPO3\CMS\Core\Resource\FileRepository;
use \TYPO3\CMS\Core\Resource\ResourceFactory;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Core\Database\ConnectionPool;
use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * ManagerController
 */
class ManagerController extends ActionController
{

    /**
     * feUserFileAccess
     *
     * @var boolean
     */
    protected $feUserFileAccess = true;

    /**
     * persistenceManager
     *
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * downloadRepository
     *
     * @var DownloadRepository
     */
    protected $downloadRepository = null;

    /**
     * @var RecordCollectionRepository
     */
    protected $collectionRepository;

    /**
     * @var FileCollectionRepository
     */
    protected $fileCollectionRepository;

    /**
     * @var FileRepository
     */
    protected $fileRepository;

    /**
     * Collections ids to display
     *
     * @var array
     */
    protected $collectionIds = array();

    /**
     * The loaded collections to display
     *
     * @var array
     */
    protected $collections = array();

    /**
     * The collection search strings
     *
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
    protected function initializeAction()
    {
        parent::initializeAction();

        /* fallback to current pid if no storagePid is defined */
        $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        if (empty($configuration['persistence']['storagePid'])) {
            $currentPid = array();
            $currentPid['persistence']['storagePid'] = $GLOBALS['TSFE']->id;
            $this->configurationManager->setConfiguration(array_merge($configuration, $currentPid));
        }

        /* check settings for css and js */
        if (isset($this->settings['includedefaultjs'])) {
            $this->defaultTsConfig['includedefaultjs'] = (int)$this->settings['includedefaultjs'];
        }
        if (isset($this->settings['includedefaultcss'])) {
            $this->defaultTsConfig['includedefaultcss'] = (int)$this->settings['includedefaultcss'];
        }
        $this->defaultTsConfig['topdtitle'] = $this->settings['topdtitle'];
        $this->defaultTsConfig['searchplaceholder'] = $this->settings['searchplaceholder'];
    }

    public function injectCollectionRepository(
        RecordCollectionRepository $collectionRepository
    ) {
        $this->collectionRepository = $collectionRepository;
    }

    public function injectDownloadRepository(
        DownloadRepository $downloadRepository
    ) {
        $this->downloadRepository = $downloadRepository;
    }

    public function injectFileCollectionRepository(
        FileCollectionRepository $fileCollectionRepository
    ) {
        $this->fileCollectionRepository = $fileCollectionRepository;
    }

    public function injectFileRepository(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function injectPersistenceManager(
        PersistenceManager $persistenceManager
    ) {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @return string
     */
    protected function getUrlExtParam()
    {
        return strtolower('tx_' . $this->request->getControllerExtensionName() . '_' . $this->request->getPluginName());
    }

    /**
     * action list
     * displays a list with the defined file collections
     *
     * @return void
     */
    public function listAction()
    {
        /* check if there is a file download request */
        $this->checkFileDownloadRequest();

        /* load the configured collections from flexform */
        $this->loadCollectionsFromFlexform();

        /* load the collections from database */
        $this->loadCollectionsFromDb();

        $contentObject = $this->configurationManager->getContentObject()->data;

        /* assign the data to fluid */
        $this->view->assignMultiple(
            [
                'config' => $this->defaultTsConfig,
                'fileCollections' => $this->collections,
                'extAdditionalParams' => $this->getUrlExtParam(),
                'contentobj' => $contentObject,
            ]
        );
    }

    /**
     * action topdownloads
     * shows a list of the top downloads
     *
     * @return void
     */
    public function topdownloadsAction()
    {
        /* check if there is a file download request */
        $this->checkFileDownloadRequest();

        /* remove old and deleted files */
        $this->cleanupTopDownloads();

        if (isset($this->settings['topdnum']) && (int)$this->settings['topdnum'] > 0) {
            $files = $this->downloadRepository->findTopDownloadList((int)$this->settings['topdnum']);
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

        $contentObject = $this->configurationManager->getContentObject()->data;

        /* assign the data to fluid */
        $this->view->assignMultiple(
            [
                'config' => $this->defaultTsConfig,
                'files' => $filesArray,
                'extAdditionalParams' => $this->getUrlExtParam(),
                'contentobj' => $contentObject,
            ]
        );
    }

    /**
     * action filesearch
     * displays a search field for the defined file collections
     *
     * @return void
     */
    public function filesearchAction()
    {
        /* check if there is a file download request */
        $this->checkFileDownloadRequest();

        /* load the configured collections from flexform */
        $this->loadCollectionsFromFlexform();

        /* load the collections from database */
        $this->loadCollectionsFromDb();

        /* write the search field for collection titles */
        $this->writeCollectionTitleSearchfield();

        $contentObject = $this->configurationManager->getContentObject()->data;

        /* assign the data to fluid */
        $this->view->assignMultiple(
            [
                'config' => $this->defaultTsConfig,
                'collectionSearchStrings' => $this->collectionSearchStrings,
                'fileCollections' => $this->collections,
                'extAdditionalParams' => $this->getUrlExtParam(),
                'contentobj' => $contentObject,
            ]
        );
    }

    /**
     * action empty
     * nothing selected in flexform
     *
     * @return void
     */
    public function emptyAction()
    {

    }

    /**
     * cleanup the top download table if file was deleted
     */
    protected function cleanupTopDownloads()
    {
        $topdownloads = $this->downloadRepository->findAllWithoutPid();
        /** @var $queryBuilder QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file');
        foreach ($topdownloads as $d) {
            $fileUid = $d->getSysFileUid();
            $res = $queryBuilder->select('uid')->from('sys_file')->where($queryBuilder->expr()->eq('uid',
                $fileUid))->execute()->fetch();
            if (!$res) {
                $this->downloadRepository->remove($d);
            }
        }
        $this->persistenceManager->persistAll();
    }

    /**
     * write a search field for each file collection as string
     * includes the *file titles*, *file extensions* and *file keywords*
     */
    protected function writeCollectionTitleSearchfield()
    {

        if (is_array($this->collections) && !empty($this->collections)) {
            foreach ($this->collections as $key => $col) {
                $searchItems = array();
                foreach ($col as $file) {
                    if (is_object($file)) {
                        $file->getContents();

                        /* check if there is a title set for file */
                        if (method_exists($file, 'getTitle')) {
                            $title = $file->getTitle();
                        } else {
                            if ($file->hasProperty('title')) {
                                $title = $file->getProperty('title');
                            } else {
                                $title = '';
                            }
                        }
                        /* check if there is a filename set for file */
                        if (method_exists($file, 'getName')) {
                            $name = $file->getName();
                        } else {
                            if ($file->hasProperty('name')) {
                                $name = $file->getProperty('name');
                            } else {
                                $name = '';
                            }
                        }
                        /* add title and name to search string if not empty */
                        if (!empty($title)) {
                            $searchItems[] = $title;
                        } else {
                            if (!empty($name)) {
                                $searchItems[] = $name;
                            }
                        }
                        $fileExt = $file->getExtension();
                        $fileExtLower = strtolower($fileExt);
                        if (!empty($fileExt) && !isset($searchItems[$fileExtLower])) {
                            $searchItems[$fileExtLower] = $fileExt;
                        }
                        /* check if there are keywords for the file and add them, too */
                        if ($file->hasProperty('keywords')) {
                            $keywords = $file->getProperty('keywords');
                            if (!empty($keywords) && $keywords !== null) {
                                $searchItems[] = $keywords;
                            }
                        }
                    }
                }
                $searchItemString = implode(' ', $searchItems);
                $this->collectionSearchStrings[$key] = $searchItemString;
            }
        }
    }

    /**
     * load all collections from database
     *
     * @return void
     * @throws
     */
    protected function loadCollectionsFromDb()
    {

        /* check if there are any collections */
        if (count($this->collectionIds) > 0) {
            //$this->fileCollectionRepository->
            /* Get all existing collections */
            foreach ($this->collectionIds as $uid) {
                $this->collections[] = $this->fileCollectionRepository->findByUid($uid);
            }

            /* Load the records in each file collection */
            foreach ($this->collections as $c) {
                $c->loadContents();
                /* load and set description of file collection which is not loaded by default */
                $c->setDescription($this->getSysFileCollectionData($c->getIdentifier()));
            }
        }
    }

    /**
     * load all collection ids from flexform fields
     *
     * @return true
     */
    protected function loadCollectionsFromFlexform()
    {
        /* check if single collections are set */
        if (isset($this->settings['lbpid']) && !empty($this->settings['lbpid'])) {
            $uids = explode(',', $this->settings['lbpid']);
            if (count($uids) > 0) {
                foreach ($uids as $uid) {
                    $this->collectionIds[$uid] = $uid;
                }
            }
        }

        /* check if a folder or page with collections is set */
        if (isset($this->settings['dfolder']) && !empty($this->settings['dfolder'])) {
            $pageids = explode(',', $this->settings['dfolder']);
            $this->getCollectionsFromPages($pageids);
        }
        return true;
    }

    /**
     * load all collection ids from given pages
     *
     * @param array $pageIds
     * @throws
     */
    protected function getCollectionsFromPages($pageIds)
    {
        $table = 'sys_file_collection';
        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        if (count($pageIds) > 0) {
            /** @var $queryBuilder QueryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
            foreach ($pageIds as $pageId) {
                /* load all file collections in default language and current language if set */
                $fileCollections = $queryBuilder->select('*')->from($table)
                    ->where($queryBuilder->expr()->eq('pid', $pageId),
                        $queryBuilder->expr()->eq('hidden', 0),
                        $queryBuilder->expr()->eq('deleted', 0),
                        $queryBuilder->expr()->orX(
                            $queryBuilder->expr()->eq('sys_language_uid', $languageAspect->getId()),
                            $queryBuilder->expr()->eq('sys_language_uid', 0)
                        )
                    )->orderBy('sorting')->execute()->fetchAll();
                if (count($fileCollections) > 0) {
                    foreach ($fileCollections as $col) {
                        if (!isset($this->collectionIds[$col['uid']])) {
                            /* check if there is a parent translation and remove it to get only the translated file collection */
                            if (isset($col['l10n_parent']) && $col['l10n_parent'] > 0 && isset($this->collectionIds[$col['l10n_parent']])) {
                                unset($this->collectionIds[$col['l10n_parent']]);
                            }
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
     * @param integer $uid
     * @param string $fieldname
     * @return string
     */
    protected function getSysFileCollectionData($uid, $fieldname = 'webdescription')
    {
        $table = 'sys_file_collection';
        /** @var $queryBuilder QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $res = $queryBuilder->select('*')->from($table)
            ->where($queryBuilder->expr()->eq('uid', $uid))
            ->execute()->fetch();
        if (isset($res[$fieldname])) {
            return $res[$fieldname];
        } else {
            return '';
        }
    }

    /**
     * check if there is a file download request
     */
    protected function checkFileDownloadRequest()
    {
        /* download file and exit */
        if ($this->request->hasArgument('downloaduid')) {
            $this->setDownload();
        }
    }

    /**
     * sends a file to download if download param is set
     *
     * @return void
     * @throws
     */
    protected function setDownload()
    {

        if ($this->request->hasArgument('downloaduid')) {

            $recordUid = (int)$this->request->getArgument('downloaduid');
            $publicUri = '';
            $fileName = '';
            $fileModDate = '';

            if (($recordUid > 0) && $this->isFileAvailable($recordUid)) {
                /* @var $fileRepository ResourceFactory */
                $fileRepository = $this->objectManager->get(ResourceFactory::class);
                /* @var $file File */
                $file = $fileRepository->getFileObject($recordUid);

                $privateUri = '';
                if (is_object($file)) {
                    $publicUri = $file->getPublicUrl();
                    $fileName = $file->getName();
                    $fileModDate = $file->getProperty('tstamp');
                    if (!$file->getStorage()->isPublic() && ExtensionManagementUtility::isLoaded('fal_securedownload')) {
                        /* @var $checkPermissions \BeechIt\FalSecuredownload\Security\CheckPermissions */
                        $checkPermissions = GeneralUtility::makeInstance(\BeechIt\FalSecuredownload\Security\CheckPermissions::class);
                        $this->feUserFileAccess = $checkPermissions->checkFileAccessForCurrentFeUser($file);
                    }
                    $privateUri = $this->getPrivateUrlForNonPublic($file);
                } else {
                    $this->setFileNotFound();
                }
                if (!$file->isMissing() && is_file($privateUri) && $this->feUserFileAccess) {
                    /* update counter or set new */
                    $this->updateUserSessionDownloads($recordUid);
                    $this->downloadFile($privateUri, $fileName, $publicUri, $fileModDate);
                } else {
                    if (!$this->feUserFileAccess) {
                        $this->setFileNoAccess();
                    } else {
                        $this->setFileNotFound();
                    }
                }
            } else {
                $this->setFileNotFound();
            }
        }
    }

    /**
     * return the private path to the file if storage is not public
     *
     * @param File $file
     * @return string
     */
    protected function getPrivateUrlForNonPublic(File $file)
    {
        $storageConfiguration = $file->getStorage()->getConfiguration();
        $storageBasePath = $storageConfiguration['basePath'];
        return $storageBasePath . $file->getIdentifier();
    }

    /**
     * sets the flashmessage for not found file
     */
    protected function setFileNotFound()
    {
        $errorFlashMessage = LocalizationUtility::translate('fileNotFound',
            $this->request->getControllerExtensionKey());
        $this->writeFlashMessage($errorFlashMessage);
    }

    /**
     * sets the flashmessage for not found file
     */
    protected function setFileNoAccess()
    {
        $errorFlashMessage = LocalizationUtility::translate('fileNoAccess',
            $this->request->getControllerExtensionKey());
        $this->writeFlashMessage($errorFlashMessage);
    }

    /**
     * write the flash messages to flash message queue
     *
     * @param string $errorFlashMessage
     * @return void
     * @throws
     */
    protected function writeFlashMessage($errorFlashMessage)
    {
        $errorFlashMessageObject = new FlashMessage(
            $errorFlashMessage, '', FlashMessage::ERROR
        );
        $this->controllerContext->getFlashMessageQueue()->enqueue($errorFlashMessageObject);
    }

    /**
     * @param integer $uid
     * @return boolean
     */
    protected function isFileAvailable($uid)
    {
        $table = 'sys_file';
        /** @var $queryBuilder QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $existingFileRecord = $queryBuilder->select('uid')->from($table)
            ->where($queryBuilder->expr()->eq('uid', $uid))
            ->execute()->fetch();
        if (is_array($existingFileRecord)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * checks the public uri for params or extension reint_file_timestamp
     *
     * @param string $publicUri
     * @param bool $fileModDate
     * @return string
     */
    protected function checkPublicUriForParams($publicUri, $fileModDate = false)
    {

        if (ExtensionManagementUtility::isLoaded('reint_file_timestamp') || stripos($publicUri, '?') !== false) {
            $uri = $publicUri . '&v=' . $fileModDate;
        } else {
            if ($fileModDate) {
                $uri = $publicUri . '?v=' . $fileModDate;
            } else {
                $uri = $publicUri;
            }
        }

        return $uri;
    }

    /**
     * stores the download of a file in the user session
     *
     * @param integer $recordUid
     * @throws
     */
    protected function updateUserSessionDownloads($recordUid)
    {
        $countEntry = $this->downloadRepository->getOneBySysFileUid($recordUid);

        $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'reint_downloadmanager');

        $newEntry = false;
        if (!$countEntry) {
            $countEntry = $this->objectManager->get(Download::class);
            $countEntry->setSysFileUid($recordUid);
            $countEntry->setDownloads(0);
            $newEntry = true;
        }

        /* check session for user downloads */
        if (!empty($sessionData) && isset($sessionData['downloads'])) {
            $data = explode(',', $sessionData['downloads']);
            /* check if download is not set in session then update counter */
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

        /* persist the database updates because of exit() in download function */
        $this->persistenceManager->persistAll();

        /*$sessionData = array(); to reset session */
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'reint_downloadmanager', $sessionData);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
    }

    /**
     * set download headers and download a file
     *
     * @param string $privateUri
     * @param string $fileName
     * @param string $publicUri
     * @param bool $fileModDate
     */
    protected function downloadFile($privateUri, $fileName, $publicUri, $fileModDate = true)
    {
        /* check if there is a setting to redirect only to the file */
        if (isset($this->settings['redirecttofile']) && (int)$this->settings['redirecttofile'] === 1) {
            /* add modification date when set in setup */
            if (isset($this->settings['addfiletstamp']) && (int)$this->settings['addfiletstamp'] === 1) {
                $fullPublicUri = GeneralUtility::locationHeaderUrl($this->checkPublicUriForParams($publicUri,
                    $fileModDate));
            } else {
                $fullPublicUri = GeneralUtility::locationHeaderUrl($publicUri);
            }
            header('Location: ' . $fullPublicUri);
        } else {
            if (is_file($privateUri)) {

                $fileLen = filesize($privateUri);
                $ext = strtolower(substr(strrchr($fileName, '.'), 1));
                $invalid_chars = array('<', '>', '?', '"', ':', '|', '\\', '/', '*', '&');
                $fileName_valid = str_replace($invalid_chars, '', $fileName);

                switch ($ext) {

                    /* forbidden filetypes */
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
                        /* should be better than 'application/force-download' */
                        $cType = 'application/octet-stream';
                        break;
                }

                $headers = array(
                    'Pragma' => 'public',
                    'Expires' => -1,
                    'Cache-Control' => 'public',
                    'Content-Type' => $cType,
                    'Content-Disposition' => 'attachment; filename="' . $fileName_valid . '"',
                    'Content-Length' => $fileLen
                );

                /* set to remove wrong headers which crashed some files (e.g. xls, dot, ...) */
                ob_clean();
                foreach ($headers as $header => $data) {
                    $this->response->setHeader($header, $data);
                }
                $this->response->sendHeaders();

                @readfile($privateUri);
            }
        }
        exit();
    }
}
