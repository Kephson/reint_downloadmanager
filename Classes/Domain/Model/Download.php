<?php

namespace RENOLIT\ReintDownloadmanager\Domain\Model;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017-2022 Ephraim Härer <ephraim.haerer@renolit.com>, RENOLIT SE
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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Manages the downloads.
 */
class Download extends AbstractEntity
{

    /**
     * sysFileUid
     *
     * @var int
     */
    protected $sysFileUid = 0;

    /**
     * downloads
     *
     * @var int
     */
    protected $downloads = 0;

    /**
     * Returns the sysFileUid
     *
     * @return int $sysFileUid
     */
    public function getSysFileUid()
    {
        return $this->sysFileUid;
    }

    /**
     * Sets the sysFileUid
     *
     * @param int $sysFileUid
     * @return void
     */
    public function setSysFileUid($sysFileUid)
    {
        $this->sysFileUid = $sysFileUid;
    }

    /**
     * Returns the downloads
     *
     * @return int $downloads
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * Sets the downloads
     *
     * @param string $downloads
     * @return void
     */
    public function setDownloads($downloads)
    {
        $this->downloads = $downloads;
    }
}
