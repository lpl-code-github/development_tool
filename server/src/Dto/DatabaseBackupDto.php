<?php

namespace App\Dto;

use App\Entity\DatabaseBackup;

class DatabaseBackupDto
{
    private $id;

    private $name;

    private $description;

    private $db_name;

    private $created_at;

    private $path;

    public function __construct(DatabaseBackup $databaseBackup)
    {
        $this->id = $databaseBackup->getId();
        $this->name = $databaseBackup->getName();
        $this->description = $databaseBackup->getDescription();
        $this->db_name = $databaseBackup->getDbName();
        $this->created_at = $databaseBackup->getCreatedAt();
        $this->path = $databaseBackup->getPath();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function getDbName()
    {
        return $this->db_name;
    }

    public function setDbName($db_name)
    {
        $this->db_name = $db_name;
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }
    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function toArray(array $fields = []): array
    {
        $resultArray = [];
        $resultArray["id"] = $this->getId();
        if (in_array("id", $fields)) {
            $resultArray["id"] = $this->getId();
        }
        if (in_array("name", $fields)) {
            $resultArray["name"] = $this->getName();
        }
        if (in_array("description", $fields)) {
            $resultArray["description"] = $this->getDescription();
        }
        if (in_array("db_name", $fields)) {
            $resultArray["db_name"] = $this->getDbName();
        }
        if (in_array("created_at", $fields)) {
            $resultArray["created_at"] = $this->getCreatedAt()->format('Y-m-d H:i:s');
        }
        if (in_array("path", $fields)) {
            $resultArray["path"] = $this->getPath();
        }
        return $resultArray;
    }
}