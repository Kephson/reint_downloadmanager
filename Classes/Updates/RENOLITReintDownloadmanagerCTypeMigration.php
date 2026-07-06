<?php

declare(strict_types=1);

namespace RENOLIT\ReintDownloadmanager\Updates;

use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Upgrades\AbstractListTypeToCTypeUpdate;

#[UpgradeWizard('renolitReintDownloadmanagerCTypeMigration')]
final class RENOLITReintDownloadmanagerCTypeMigration extends AbstractListTypeToCTypeUpdate
{
    public function getTitle(): string
    {
        return 'Migrate "RENOLIT ReintDownloadmanager" plugins to content elements.';
    }

    public function getDescription(): string
    {
        return 'The "RENOLIT ReintDownloadmanager" plugins are now registered as content element. Update migrates existing records and backend user permissions.';
    }

    /**
     * This must return an array containing the "list_type" to "CType" mapping
     *
     *  Example:
     *
     *  [
     *      'pi_plugin1' => 'pi_plugin1',
     *      'pi_plugin2' => 'new_content_element',
     *  ]
     *
     * @return array<string, string>
     */
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'reintdownloadmanager_dmfilesearch' => 'reintdownloadmanager_dmfilesearch',
            'reintdownloadmanager_dmtopdownloads' => 'reintdownloadmanager_dmtopdownloads',
            'reintdownloadmanager_dmlist' => 'reintdownloadmanager_dmlist',
        ];
    }
}
