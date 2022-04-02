<?php

namespace RENOLIT\ReintDownloadmanager\Updates;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use PDO;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class MigrateFlexformWizard implements UpgradeWizardInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $table = 'tt_content';

    /**
     * @var string[]
     */
    protected $search = [
        'empty' => '<numIndex index="1">Manager-&gt;empty</numIndex>',
        'list' => '<numIndex index="1">Manager-&gt;list</numIndex>',
        'topdownloads' => '<numIndex index="1">Manager-&gt;topdownloads</numIndex>',
        'filesearch' => '<numIndex index="1">Manager-&gt;filesearch</numIndex>',
    ];
    /**
     * @var string[]
     */
    protected $replace = [
        'empty' => '<numIndex index="1">Manager-&gt;empty,Manager-&gt;download</numIndex>',
        'list' => '<numIndex index="1">Manager-&gt;list,Manager-&gt;download</numIndex>',
        'topdownloads' => '<numIndex index="1">Manager-&gt;topdownloads,Manager-&gt;download</numIndex>',
        'filesearch' => '<numIndex index="1">Manager-&gt;filesearch,Manager-&gt;download</numIndex>',
    ];

    /**
     * @param OutputInterface $output
     * @return void
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    /**
     * Return the identifier for this wizard
     * This should be the same string as used in the ext_localconf class registration
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'reintDownloadmanager_migrateFlexformWizard';
    }

    /**
     * Return the speaking name of this wizard
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'EXT:reint_downloadmanager - Migrate Flexforms';
    }

    /**
     * Return the description for this wizard
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Migrate all Flexforms in plugins to newest version';
    }

    /**
     * Execute the update
     *
     * Called when a wizard reports that an update is necessary
     *
     * @return bool
     * @throws DBALException
     * @throws Exception
     */
    public function executeUpdate(): bool
    {
        $entries = $this->getEntriesToMigrate(false);
        if (count($entries) > 0) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);

            foreach ($entries as $entry) {
                $queryBuilder
                    ->update($this->table, 't')
                    ->where(
                        $queryBuilder->expr()->eq('t.uid', $queryBuilder->createNamedParameter($entry['uid'], PDO::PARAM_INT))
                    )
                    ->set('t.pi_flexform', str_replace($this->search, $this->replace, $entry['pi_flexform']))
                    ->executeStatement();
            }
        }

        return true;
    }

    /**
     * Is an update necessary?
     *
     * Is used to determine whether a wizard needs to be run.
     * Check if data for migration exists.
     *
     * @return bool Whether an update is required (TRUE) or not (FALSE)
     * @throws DBALException
     * @throws Exception
     */
    public function updateNecessary(): bool
    {
        return (bool)$this->getEntriesToMigrate();
    }

    /**
     * Returns an array of class names of prerequisite classes
     *
     * This way a wizard can define dependencies like "database up-to-date" or
     * "reference index updated"
     *
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    /**
     * @param bool $singleEntry
     * @return array<string,mixed>|bool
     * @throws DBALException
     * @throws Exception
     */
    protected function getEntriesToMigrate($singleEntry = true)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($this->table)
            ->createQueryBuilder();

        $queryBuilder->select('uid', 'pi_flexform')
            ->from($this->table)
            ->orWhere(
                $queryBuilder->expr()->like('pi_flexform', $queryBuilder->createNamedParameter('%' . $this->search['empty'] . '%')),
                $queryBuilder->expr()->like('pi_flexform', $queryBuilder->createNamedParameter('%' . $this->search['list'] . '%')),
                $queryBuilder->expr()->like('pi_flexform', $queryBuilder->createNamedParameter('%' . $this->search['topdownloads'] . '%')),
                $queryBuilder->expr()->like('pi_flexform', $queryBuilder->createNamedParameter('%' . $this->search['filesearch'] . '%'))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('list_type', $queryBuilder->createNamedParameter('reintdownloadmanager_reintdlm'))
            );

        if ($singleEntry) {
            return (bool)$queryBuilder->execute()->fetchOne();
        }
        return (bool)$queryBuilder->execute()->fetchAssociative();
    }
}
