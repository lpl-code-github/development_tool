<?php

namespace App\Dto;

use App\Entity\Script;
use App\Entity\ScriptTag;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
class ScriptDto
{
    private $id;

    private $name;

    private $description;

    private $path;

    private $properties;

    private $created_at;

    private $updated_at;

    private $scriptTag;

    private EntityManagerInterface $entityManager;

    public function __construct(Script $script, EntityManagerInterface $entityManager)
    {
        $this->id = $script->getId();
        $this->name = $script->getName();
        $this->description = $script->getDescription();
        $this->path = $script->getPath();
        $this->properties = $script->getProperties();
        $this->created_at = $script->getCreatedAt();
        $this->updated_at = $script->getUpdatedAt();
        $this->scriptTag = $script->getScriptTag();
        $this->entityManager = $entityManager;

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
    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }
    public function getProperties()
    {
        return $this->properties;
    }

    public function setProperties($properties)
    {
        $this->properties = $properties;
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }
    public function getScriptTag()
    {
        return $this->scriptTag;
    }

    public function setScriptTag($scriptTag)
    {
        $this->scriptTag = $scriptTag;
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
        if (in_array("path", $fields)) {
            $resultArray["path"] = $this->getPath();
        }
        if (in_array("properties", $fields)) {
            $resultArray["properties"] = $this->getProperties();
        }
        if (in_array("created_at", $fields)) {
            $resultArray["created_at"] = $this->getCreatedAt()->format('Y-m-d H:i:s');
        }
        if (in_array("updated_at", $fields)) {
            $resultArray["updated_at"] = $this->getUpdatedAt()->format('Y-m-d H:i:s');
        }
        if (in_array("scriptTag", $fields)) {
            $resultArray["scriptTag"] = $this->getScriptTag();
        }
        if (in_array("tags", $fields)) {
            $scriptTags = $this->entityManager->getRepository(ScriptTag::class)->findByScriptId($this->getId());
            if (!$scriptTags){
                $scriptTags = array();
            }
            $tagsResult = array();
            foreach ($scriptTags as $scriptTag){
                /* @var ScriptTag $scriptTag */
                $tag = $scriptTag->getTag();
                $tagResult['id'] =  $tag->getId();
                $tagResult['name'] = $tag->getName();
                $tagResult['color'] = $tag->getColor();
                $tagsResult[] = $tagResult;
            }
            $resultArray["tags"] = $tagsResult;
        }
        return $resultArray;
    }
}