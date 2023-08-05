<?php

namespace App\Factory;

use App\Entity\DatabaseBackup;

class DatabaseBackupFactory
{
    /**
     * Create an instance
     */
    public function create($name, $description, $dbName, $path): ?DatabaseBackup
    {
        // Perform some verification
        // ...

        $databaseBackup = new DatabaseBackup();
        $databaseBackup->setName($name);
        $databaseBackup->setDescription($description);
        $databaseBackup->setDbName($dbName);
        $databaseBackup->setPath($path);
        return $databaseBackup;
    }
}