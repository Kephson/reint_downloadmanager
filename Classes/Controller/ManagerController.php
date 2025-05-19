<?php

namespace RENOLIT\ReintDownloadmanager\Controller;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017-2023 Ephraim Härer <ephraim.haerer@renolit.com>, RENOLIT SE
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

use BeechIt\FalSecuredownload\Security\CheckPermissions;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use RENOLIT\ReintDownloadmanager\Domain\Model\Download;
use RENOLIT\ReintDownloadmanager\Domain\Repository\DownloadRepository;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileCollectionRepository;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * ManagerController
 */
class ManagerController extends ActionController
{

    /**
     * feUserFileAccess
     *
     * @var bool
     */
    protected bool $feUserFileAccess = true;

    /**
     * persistenceManager
     *
     * @var PersistenceManager
     */
    protected PersistenceManager $persistenceManager;

    /**
     * downloadRepository
     *
     * @var ?DownloadRepository
     */
    protected ?DownloadRepository $downloadRepository = null;

    /**
     * @var FileCollectionRepository
     */
    protected FileCollectionRepository $fileCollectionRepository;

    /**
     * @var FileRepository
     */
    protected FileRepository $fileRepository;

    /**
     * Collections ids to display
     *
     * @var array
     */
    protected array $collectionIds = [];

    /**
     * The loaded collections to display
     *
     * @var array
     */
    protected array $collections = [];

    /**
     * The collection search strings
     *
     * @var array
     */
    protected array $collectionSearchStrings = [];

    /**
     * default TypoScript configuration
     *
     * @var array
     */
    protected array $defaultTsConfig = [
        'includedefaultjs' => 1,
    ];


    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * initialize the controller
     *
     * @return void
     */
    protected function initializeAction(): void
    {
        parent::initializeAction();

        /* fallback to current pid or settings from FlexForm if no storagePid is defined */
        $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        if (empty($configuration['persistence']['storagePid'])) {
            if (isset($this->settings['dfolder']) && $this->settings['dfolder'] > 0) {
                $storagePids = $this->settings['dfolder'];
            } else {
                $pageArguments = $this->request->getAttribute('routing');
                $storagePids = $pageArguments->getPageId();
            }
            $configuration['persistence']['storagePid'] = $storagePids;
            $this->configurationManager->setConfiguration($configuration);
        }

        /* check settings for default JavaScript */
        if (isset($this->settings['includedefaultjs'])) {
            $this->defaultTsConfig['includedefaultjs'] = (int)$this->settings['includedefaultjs'];
        }

        /* other settings */
        if (isset($this->settings['topdtitle'])) {
            $this->defaultTsConfig['topdtitle'] = $this->settings['topdtitle'];
        }
        if (isset($this->settings['searchplaceholder'])) {
            $this->defaultTsConfig['searchplaceholder'] = $this->settings['searchplaceholder'];
        }
    }

    public function injectDownloadRepository(
        DownloadRepository $downloadRepository
    ): void
    {
        $this->downloadRepository = $downloadRepository;
    }

    public function injectFileCollectionRepository(
        FileCollectionRepository $fileCollectionRepository
    ): void
    {
        $this->fileCollectionRepository = $fileCollectionRepository;
    }

    public function injectFileRepository(FileRepository $fileRepository): void
    {
        $this->fileRepository = $fileRepository;
    }

    public function injectPersistenceManager(
        PersistenceManager $persistenceManager
    ): void
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @return string
     */
    protected function getUrlExtParam(): string
    {
        return strtolower('tx_' . $this->request->getControllerExtensionName() . '_' . $this->request->getPluginName());
    }

    /**
     * action list
     * displays a list with the defined file collections
     *
     * @return ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        /* check if there is a file download request */
        if ($this->request->hasArgument('downloaduid')) {
            return $this->checkFileDownloadRequest();
        }

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

        return $this->htmlResponse();
    }

    /**
     * action topdownloads
     * shows a list of the top downloads
     *
     * @return ResponseInterface
     * @throws NoSuchArgumentException
     */
    public function topdownloadsAction(): ResponseInterface
    {
        /* check if there is a file download request */
        if ($this->request->hasArgument('downloaduid')) {
            return $this->checkFileDownloadRequest('topdownloads');
        }

        /* remove old and deleted files */
        $this->cleanupTopDownloads();

        $storagePids = [];
        if (isset($this->settings['dfolder']) && !empty($this->settings['dfolder'])) {
            $storagePids = explode(',', $this->settings['dfolder']);
        }
        if (isset($this->settings['topdnum']) && (int)$this->settings['topdnum'] > 0) {
            $files = $this->downloadRepository->findTopDownloadList($storagePids, (int)$this->settings['topdnum']);
        } else {
            $files = $this->downloadRepository->findTopDownloadList($storagePids);
        }

        $filesArray = [];
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

        return $this->htmlResponse();
    }

    /**
     * action filesearch
     * displays a search field for the defined file collections
     *
     * @return ResponseInterface
     */
    public function filesearchAction(): ResponseInterface
    {
        /* check if there is a file download request */
        if ($this->request->hasArgument('downloaduid')) {
            return $this->checkFileDownloadRequest('filesearch');
        }

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

        return $this->htmlResponse();
    }

    /**
     * action empty
     * nothing selected in flexform
     *
     * @return ResponseInterface
     */
    public function emptyAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * check if there is a file download request
     * @param string $action
     *
     * @return ResponseInterface
     */
    protected function checkFileDownloadRequest(string $action = 'list'): ResponseInterface
    {
        /* download file and exit */
        $arguments = [
            'downloaduid' => $this->request->getArgument('downloaduid'),
            'actionfrom' => $action,
        ];
        $uri = $this->uriBuilder->uriFor('download', $arguments);
        return $this->responseFactory->createResponse(307)->withHeader('Location', $uri);
    }

    /**
     * cleanup the top download table if file was deleted
     */
    protected function cleanupTopDownloads(): void
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
    protected function writeCollectionTitleSearchfield(): void
    {
        if (is_array($this->collections) && !empty($this->collections)) {
            foreach ($this->collections as $key => $col) {
                $searchItems = [];
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
    protected function loadCollectionsFromDb(): void
    {
        /* check if there are any collections */
        if (count($this->collectionIds) > 0) {
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
    protected function loadCollectionsFromFlexform(): bool
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
    protected function getCollectionsFromPages(array $pageIds): void
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
                        $queryBuilder->expr()->or(
                            $queryBuilder->expr()->eq('sys_language_uid', $languageAspect->getId()),
                            $queryBuilder->expr()->eq('sys_language_uid', 0)
                        )
                    )->orderBy('sorting')->executeQuery()->fetchAllAssociative();
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
     * gets additional data for file collection
     *
     * @param int $uid
     * @param string $fieldname
     * @return string
     * @throws Exception
     */
    protected function getSysFileCollectionData(int $uid, string $fieldname = 'description_frontend'): string
    {
        $table = 'sys_file_collection';
        /** @var $queryBuilder QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $res = $queryBuilder->select('*')->from($table)
            ->where($queryBuilder->expr()->eq('uid', $uid))
            ->executeQuery()->fetchAllAssociative();
        return $res[0][$fieldname] ?? '';
    }

    /**
     * return the private path to the file if storage is not public
     *
     * @param File $file
     * @return string
     */
    protected function getPrivateUrlForNonPublic(File $file): string
    {
        $storageConfiguration = $file->getStorage()->getConfiguration();
        $storageBasePath = $storageConfiguration['basePath'];
        return $storageBasePath . $file->getIdentifier();
    }

    /**
     * sets the flashmessage for not found file
     */
    protected function setFileNotFound(): void
    {
        $errorFlashMessage = LocalizationUtility::translate('fileNotFound',
            $this->request->getControllerExtensionKey());
        $this->writeFlashMessage($errorFlashMessage);
    }

    /**
     * sets the flashmessage for not found file
     */
    protected function setFileNoAccess(): void
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
    protected function writeFlashMessage(string $errorFlashMessage): void
    {
        $errorFlashMessageObject = new FlashMessage(
            $errorFlashMessage, '', ContextualFeedbackSeverity::ERROR
        );
        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $messageQueue->addMessage($errorFlashMessageObject);
    }

    /**
     * @param int $uid
     * @return bool
     * @throws Exception
     */
    protected function isFileAvailable(int $uid): bool
    {
        $table = 'sys_file';
        /** @var $queryBuilder QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $existingFileRecord = $queryBuilder->select('uid')->from($table)
            ->where($queryBuilder->expr()->eq('uid', $uid))
            ->executeQuery()->fetchOne();
        if ($uid === $existingFileRecord) {
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
    protected function checkPublicUriForParams(string $publicUri, bool $fileModDate = false): string
    {
        /* do not add timestamp if EXT:reint_file_timestamp is installed */
        if (ExtensionManagementUtility::isLoaded('reint_file_timestamp')) {
            $uri = $publicUri;
        } elseif (stripos($publicUri, '?') > 0 && $fileModDate) {
            $uri = $publicUri . '&v=' . $fileModDate;
        } elseif (stripos($publicUri, '?') === false && $fileModDate) {
            $uri = $publicUri . '?v=' . $fileModDate;
        } else {
            $uri = $publicUri;
        }

        return $uri;
    }

    /**
     * stores the download of a file in the user session
     *
     * @param int $recordUid
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    protected function updateUserSessionDownloads(int $recordUid): void
    {
        $countEntry = $this->downloadRepository->getOneBySysFileUid($recordUid);

        $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'reint_downloadmanager');

        $newEntry = false;
        if (!$countEntry) {
            $countEntry = GeneralUtility::makeInstance(Download::class);
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
            $sessionData = [
                'downloads' => $recordUid,
            ];
            $countEntry->setDownloads($countEntry->getDownloads() + 1);
        }

        if ($newEntry) {
            $this->downloadRepository->add($countEntry);
        } else {
            $this->downloadRepository->update($countEntry);
        }

        /* persist the database updates because of exit() in download function */
        $this->persistenceManager->persistAll();

        /*$sessionData = []; to reset session */
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'reint_downloadmanager', $sessionData);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
    }

    /**
     * sends a file to download if download param is set
     *
     * @return ResponseInterface
     * @throws Exception
     * @throws FileDoesNotExistException
     * @throws IllegalObjectTypeException
     * @throws PropagateResponseException
     * @throws UnknownObjectException
     */
    protected function downloadAction(): ResponseInterface
    {
        if ($this->request->hasArgument('downloaduid') && $this->request->hasArgument('actionfrom')) {
            $returnToAction = $this->request->getArgument('actionfrom');
            $recordUid = (int)$this->request->getArgument('downloaduid');

            $this->loadCollectionsFromFlexform();
            $this->loadCollectionsFromDb();
            $files = [];
            foreach ($this->collections as $collection) {
                /** @var FileReference $fileReference */
                foreach ($collection as $fileReference) {
                    $fUid = $fileReference->getOriginalFile()->getUid();
                    $files[$fUid] = $fUid;
                }
            }
            if (!in_array($recordUid, $files)) {
                $this->setFileNotFound();
                return $this->redirect('list');
            }

            $publicUri = '';
            $fileName = '';
            $fileModDate = '';

            if (($recordUid > 0) && $this->isFileAvailable($recordUid)) {
                /** @var $fileRepository ResourceFactory */
                $fileRepository = GeneralUtility::makeInstance(ResourceFactory::class);
                $file = $fileRepository->getFileObject($recordUid);

                $privateUri = '';
                if (is_object($file)) {
                    $publicUri = $file->getPublicUrl();
                    $fileName = $file->getName();
                    $fileModDate = $file->getProperty('tstamp');
                    if (!$file->getStorage()->isPublic() && ExtensionManagementUtility::isLoaded('fal_securedownload')) {
                        /** @var $checkPermissions CheckPermissions */
                        $checkPermissions = GeneralUtility::makeInstance(CheckPermissions::class);
                        $this->feUserFileAccess = $checkPermissions->checkFileAccessForCurrentFeUser($file);
                    }
                    $privateUri = $this->getPrivateUrlForNonPublic($file);
                } else {
                    $this->setFileNotFound();
                    return $this->redirect($returnToAction);
                }
                if (!$file->isMissing() && is_file($privateUri) && $this->feUserFileAccess) {
                    /* update counter or set new */
                    $this->updateUserSessionDownloads($recordUid);
                    return $this->downloadFile($privateUri, $fileName, $publicUri, $fileModDate);
                } else {
                    if (!$this->feUserFileAccess) {
                        $this->setFileNoAccess();
                        return $this->redirect($returnToAction);
                    } else {
                        $this->setFileNotFound();
                        return $this->redirect($returnToAction);
                    }
                }
            } else {
                $this->setFileNotFound();
                return $this->redirect('list');
            }
        }
        return $this->responseFactory->createResponse();
    }

    /**
     * set download headers and download a file
     * @see https://brot.krue.ml/extbase-controller-action-responses-in-typo3
     *
     * @param string $privateUri
     * @param string $fileName
     * @param string $publicUri
     * @param bool $fileModDate
     *
     * @return ResponseInterface
     * @throws PropagateResponseException
     */
    protected function downloadFile(string $privateUri, string $fileName, string $publicUri, bool $fileModDate = true): ResponseInterface
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
            return $this->redirectToUri($fullPublicUri);
        } else {
            if (is_file($privateUri)) {
                $fileLen = (string)filesize($privateUri);
                $ext = strtolower(substr(strrchr($fileName, '.'), 1));
                $invalidChars = ['<', '>', '?', '"', ':', '|', '\\', '/', '*', '&'];
                $fileNameValid = str_replace($invalidChars, '', $fileName);

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

                $response = $this->responseFactory->createResponse()
                    ->withHeader('Content-Type', $cType)
                    ->withHeader('Pragma', 'public')
                    ->withHeader('Expires', '-1')
                    ->withHeader('Cache-Control', 'public')
                    ->withHeader('Content-Disposition', 'attachment; filename="' . $fileNameValid . '"')
                    ->withHeader('Content-Length', $fileLen)
                    ->withBody($this->streamFactory->createStreamFromFile($privateUri));
                throw new PropagateResponseException($response, 200);
            }
        }

        return $this->responseFactory->createResponse();
    }
}
