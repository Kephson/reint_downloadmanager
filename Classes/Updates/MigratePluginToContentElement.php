<?php

namespace RENOLIT\ReintDownloadmanager\Updates;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use PDO;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class MigratePluginToContentElement implements UpgradeWizardInterface
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
        return 'reintDownloadmanager_migratePluginToContentElement';
    }

    /**
     * Return the speaking name of this wizard
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'EXT:reint_downloadmanager - Migrate Plugins to Content elements';
    }

    /**
     * Return the description for this wizard
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Migrate all Plugins to content elements';
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
            $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
            foreach ($entries as $entry) {
                $flexFormData = $flexFormService->convertFlexFormContentToArray($entry['pi_flexform']);
                $migratedEntry = $this->getMigratedData($entry, $flexFormData);
                $queryBuilder
                    ->update($this->table, 't')
                    ->where(
                        $queryBuilder->expr()->eq('t.uid', $queryBuilder->createNamedParameter($migratedEntry['uid'], PDO::PARAM_INT))
                    )
                    ->set('t.list_type', $migratedEntry['list_type'])
                    ->set('t.CType', $migratedEntry['CType'])
                    ->set('t.pi_flexform', $migratedEntry['pi_flexform'])
                    ->execute();
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
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        $queryBuilder->getRestrictions()->removeAll();

        $queryBuilder->select('uid', 'CType', 'list_type', 'pi_flexform')
            ->from($this->table)
            ->andWhere(
                $queryBuilder->expr()->eq('list_type', $queryBuilder->createNamedParameter('reintdownloadmanager_reintdlm')),
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('list'))
            );

        if ($singleEntry) {
            return (bool)$queryBuilder->execute()->fetchOne();
        }
        return $queryBuilder->execute()->fetchAllAssociative();
    }

    /**
     * @param array $oldEntry
     * @param array $flexFormData
     *
     * @return array $newEntry
     */
    protected function getMigratedData($oldEntry, $flexFormData)
    {
        $newEntry = [
            'uid' => $oldEntry['uid'],
            'CType' => '',
            'list_type' => '',
            'pi_flexform' => '',
        ];
        if (isset($flexFormData['switchableControllerActions'], $flexFormData['settings']['lbpid'], $flexFormData['settings']['dfolder']) &&
            ($flexFormData['switchableControllerActions'] === 'Manager->list;Manager->download' ||
                $flexFormData['switchableControllerActions'] === 'Manager->list')) {
            $newEntry['CType'] = 'reintdownloadmanager_dmlist';
            $newEntry['pi_flexform'] = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="element">
            <language index="lDEF">
                <field index="settings.lbpid">
                    <value index="vDEF">' . $flexFormData['settings']['lbpid'] . '</value>
                </field>
                <field index="settings.dfolder">
                    <value index="vDEF">' . $flexFormData['settings']['dfolder'] . '</value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>';
        }
        if (isset($flexFormData['switchableControllerActions'], $flexFormData['settings']['topdnum'], $flexFormData['settings']['topdtitle'],
                $flexFormData['settings']['lbpid'], $flexFormData['settings']['dfolder']) &&
            ($flexFormData['switchableControllerActions'] === 'Manager->topdownloads;Manager->download' ||
                $flexFormData['switchableControllerActions'] === 'Manager->topdownloads')) {
            $newEntry['CType'] = 'reintdownloadmanager_dmtopdownloads';
            $newEntry['pi_flexform'] = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="element">
            <language index="lDEF">
                <field index="settings.topdnum">
                    <value index="vDEF">' . $flexFormData['settings']['topdnum'] . '</value>
                </field>
                <field index="settings.topdtitle">
                    <value index="vDEF">' . $flexFormData['settings']['topdtitle'] . '</value>
                </field>
                <field index="settings.lbpid">
                    <value index="vDEF">' . $flexFormData['settings']['lbpid'] . '</value>
                </field>
                <field index="settings.dfolder">
                    <value index="vDEF">' . $flexFormData['settings']['dfolder'] . '</value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>';
        }
        if (isset($flexFormData['switchableControllerActions'], $flexFormData['settings']['searchplaceholder'],
                $flexFormData['settings']['lbpid'], $flexFormData['settings']['dfolder']) &&
            ($flexFormData['switchableControllerActions'] === 'Manager->filesearch;Manager->download' ||
                $flexFormData['switchableControllerActions'] === 'Manager->filesearch')) {
            $newEntry['CType'] = 'reintdownloadmanager_dmfilesearch';
            $newEntry['pi_flexform'] = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="element">
            <language index="lDEF">
                <field index="settings.searchplaceholder">
                    <value index="vDEF">' . $flexFormData['settings']['searchplaceholder'] . '</value>
                </field>
                <field index="settings.lbpid">
                    <value index="vDEF">' . $flexFormData['settings']['lbpid'] . '</value>
                </field>
                <field index="settings.dfolder">
                    <value index="vDEF">' . $flexFormData['settings']['dfolder'] . '</value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>';
        }
        return $newEntry;
    }
}
