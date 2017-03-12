<?php namespace Inovasi;

use Ifsnop\Mysqldump\Mysqldump;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Twistor\FlysystemStreamWrapper;

class Backup
{
    private $configurations;
    private $fs;

    public function __construct(BackupConfigurations $backupConfigurations)
    {
        $this->configurations = $backupConfigurations;

        $this->fs = new MountManager([
            'local' => new Filesystem(new Local($backupConfigurations->getApplicationDirectory())),
            'archive' => new Filesystem(new ZipArchiveAdapter($backupConfigurations->getBackupArchivePath()))
        ]);

        FlysystemStreamWrapper::register('archive', $this->fs->getFilesystem('archive'));
    }

    /**
     *
     */
    public function doBackup()
    {
        return $this->backupDb() && $this->backupFile();
    }

    /**
     * @param BackupConfigurations|null $configurations
     * @return bool
     */
    public function backupDb (BackupConfigurations $configurations = null)
    {
        if (!$configurations) $configurations = $this->getConfigurations();

        try {
            $dump = new Mysqldump($configurations->getPdoDsn(), $configurations->getDbUser(), $configurations->getDbPass());
            $dump->start('archive://db/dump.sql');
            return true;
        } catch (\Exception $e) {
            echo 'mysqldump-php error: ' . $e->getMessage();
            return false;
        }
    }

    /**
     * @param BackupConfigurations|null $configurations
     * @return bool
     */
    public function backupFile (BackupConfigurations $configurations = null)
    {
        if (!$configurations) $configurations = $this->getConfigurations();

        $contents = $this->fs->listContents('local://', true);

        foreach ($contents as $entry) {
            if (array_count_values($configurations->getIgnoreList()))
                if (preg_match('/' . implode('|', $configurations->getIgnoreList()) . '/i', $entry['path'])) {
                    continue;
                }

            if ($this->fs->getMetadata('local://' . $entry['path'])['type'] === 'dir')
                $this->fs->createDir('archive://files/' . $entry['path']);
            else if ($this->fs->getMetadata('local://' . $entry['path'])['type'] === 'file')
                $this->fs->put('archive://files/' . $entry['path'], $this->fs->read('local://' . $entry['path']));

        }

        $this->fs->getFilesystem('archive')->getAdapter()->getArchive()->close();
        return true;
    }

    /**
     * @return BackupConfigurations
     */
    public function getConfigurations()
    {
        return $this->configurations;
    }
}