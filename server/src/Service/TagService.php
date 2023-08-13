<?php

namespace App\Service;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Script;
use App\Dto\ScriptDto;

class TagService
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $params
     * @return array
     */
    public function handleGetTags(array $params): array
    {
        $result = array();
        if (count($params) == 0){
            $tags = $this->entityManager->getRepository(Tag::class)->findAll();
            foreach($tags as $tag){
                /* @var Tag $tag*/
                $tagResult['id'] = $tag->getId();
                $tagResult['name'] = $tag->getName();
                $tagResult['color'] = $tag->getColor();
                $result[] = $tagResult;
            }
            return $result;
        }
        return $result;
    }

    public function handlePostTags(Tag $tag): array
    {
        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        $tagResult['id'] = $tag->getId();
        $tagResult['name'] = $tag->getName();
        $tagResult['color'] = $tag->getColor();
        $result[] = $tagResult;

        return $result;
    }
}