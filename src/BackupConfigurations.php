<?php namespace Inovasi;

class BackupConfigurations
{
    private $backupArchiveName = '';
    private $applicationDirectory = '';
    private $backupDirectory = '';

    private $ignoreList = [];

    private $dbType = 'mysql';
    private $dbHost = 'localhost';
    private $dbPort = '3306';
    private $dbName = '';
    private $dbUser = '';
    private $dbPass = '';

    /**
     * Configurations constructor.
     * @param array $configurations
     */
    public function __construct($configurations)
    {
        // Default configurations
        $this->setBackupArchiveName(
            isset($configurations['backup_file_name']) ?
                $configurations['backup_file_name'] : 'slims-backup_' . date("YmdHis") . '.zip');
        $this->setBackupDirectory(
            isset($configurations['backup_dir']) ?
                $configurations['backup_dir'] : getcwd() . '/..');
        $this->setApplicationDirectory(
            isset($configurations['app_dir']) ?
                $configurations['backup_dir'] : getcwd());

        $this->setIgnoreList(
            isset($configurations['ignore'])?
                $configurations['ignore'] : []
        );

        // Set configurations
        $this->setDatabase(
            $configurations['db']['name'],
            $configurations['db']['user'],
            $configurations['db']['pass'],
            isset($configurations['db']['host']) ? $configurations['db']['host'] : $this->getDbHost(),
            isset($configurations['db']['port']) ? $configurations['db']['port'] : $this->getDbPort()
        );
    }

    /**
     * @param string $backupArchiveName
     */
    public function setBackupArchiveName($backupArchiveName)
    {
        $this->backupArchiveName = $backupArchiveName;
    }

    /**
     * @return string
     */
    public function getBackupArchiveName()
    {
        return $this->backupArchiveName;
    }

    /**
     * @param string $backupDirectory
     */
    public function setBackupDirectory($backupDirectory)
    {
        $this->backupDirectory = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $backupDirectory);
    }

    /**
     * @return string
     */
    public function getBackupDirectory()
    {
        return $this->backupDirectory;
    }

    /**
     * @return string
     */
    public function getBackupArchivePath()
    {
        return $this->getBackupDirectory() . DIRECTORY_SEPARATOR . $this->backupArchiveName;
    }

    /**
     * @param string $applicationDirectory
     */
    public function setApplicationDirectory($applicationDirectory)
    {
        $this->applicationDirectory = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $applicationDirectory);
    }

    /**
     * @return string
     */
    public function getApplicationDirectory()
    {
        return $this->applicationDirectory;
    }

    /**
     * @param array $ignoreList
     */
    public function setIgnoreList($ignoreList)
    {
        $this->ignoreList = array_map(function ($value) {
            $value = preg_replace('/^\/(.+)/i', '^$1', $value);
            $value = str_replace('*', '.+', $value);
            $value = str_replace('\\', '/', $value);
            return $value;
        }, $ignoreList);
    }

    /**
     * @return array
     */
    public function getIgnoreList()
    {
        return $this->ignoreList;
    }

    /**
     * @param string $databaseHost
     * @param string $databaseName
     * @param string $databaseUsername
     * @param string $databasePassword
     * @param string $databasePort
     */
    public function setDatabase($databaseName, $databaseUsername, $databasePassword, $databaseHost = 'localhost', $databasePort = '3306')
    {
        $this->dbHost = $databaseHost;
        $this->dbPort = $databasePort;
        $this->dbName = $databaseName;
        $this->dbUser = $databaseUsername;
        $this->dbPass = $databasePassword;
    }

    /**
     * @return string
     */
    public function getDbType()
    {
        return $this->dbType;
    }

    /**
     * @return string
     */
    public function getDbHost()
    {
        return $this->dbHost;
    }

    /**
     * @return string
     */
    public function getDbPort()
    {
        return $this->dbPort;
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @return string
     */
    public function getDbUser()
    {
        return $this->dbUser;
    }

    /**
     * @return string
     */
    public function getDbPass()
    {
        return $this->dbPass;
    }

    public function getPdoDsn()
    {
        return $this->getDbType() . ':host=' . $this->getDbHost() . ';port=' . $this->getDbPort() . ';dbname=' .
            $this->getDbName();
    }
}