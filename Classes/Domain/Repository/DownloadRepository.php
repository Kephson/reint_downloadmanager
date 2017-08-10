<?php

namespace RENOLIT\ReintDownloadmanager\Domain\Repository;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Ephraim Härer <ephraim.haerer@renolit.com>, RENOLIT SE
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

/**
 * The repository for download statistics
 */
class DownloadRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

	/**
	 * find one download by sys_file_uid
	 * 
	 * @param array $uid
	 */
	public function getOneBySysFileUid($uid)
	{

		$query = $this->createQuery();
		$query->matching(
			$query->equals('sys_file_uid', $uid)
		);
		return $query->execute()->getFirst();
	}

	/**
	 * find the top ten downloads ordered by the counter
	 * 
	 */
	public function findTopDownloadList($limit = 10)
	{

		$query = $this->createQuery();
		$query->setLimit($limit);
		$query->setOrderings(
			array(
				'downloads' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
			)
		);
		return $query->execute();
	}

	/**
	 * return all entries without page id specified
	 */
	public function findAllWithoutPid()
	{
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		return $query->execute();
	}
}
