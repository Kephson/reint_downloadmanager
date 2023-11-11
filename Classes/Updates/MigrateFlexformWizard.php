<?php

namespace RENOLIT\ReintDownloadmanager\Updates;

use Doctrine\DBAL\Exception as DbalException;
use PDO;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('migrateFlexformWizard')]
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
        'empty' => '<value index="vDEF">Manager-&gt;empty</value>',
        'list' => '<value index="vDEF">Manager-&gt;list</value>',
        'topdownloads' => '<value index="vDEF">Manager-&gt;topdownloads</value>',
        'filesearch' => '<value index="vDEF">Manager-&gt;filesearch</value>',
    ];
    /**
     * @var string[]
     */
    protected $replace = [
        'empty' => '<value index="vDEF">Manager-&gt;empty;Manager-&gt;download</value>',
        'list' => '<value index="vDEF">Manager-&gt;list;Manager-&gt;download</value>',
        'topdownloads' => '<value index="vDEF">Manager-&gt;topdownloads;Manager-&gt;download</value>',
        'filesearch' => '<value index="vDEF">Manager-&gt;filesearch;Manager-&gt;download</value>',
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
     * @throws DbalException
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
                    ->executeQuery();
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
     * @throws DbalException
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
     * @throws DbalException
     */
    protected function getEntriesToMigrate(bool $singleEntry = true): array|bool
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        $queryBuilder->getRestrictions()->removeAll();

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
            return (bool)$queryBuilder->executeQuery()->fetchOne();
        }
        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }
}
