# inovasi/backup
Backup your files and database into zip archive.

''' php
use Inovasi\Backup;
use Inovasi\BackupConfigurations;

require 'vendor/autoload.php';

$backup = new Backup(new BackupConfigurations([
    'db' => [
        'name' => 'dbname',
        'user' => 'dbuser',
        'pass' => 'dbpass'
    ]
]));

$backup->doBackup();
