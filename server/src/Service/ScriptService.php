<?php

namespace App\Service;

use App\Entity\ScriptTag;
use App\Entity\Tag;
use App\Factory\ExceptionFactory;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Script;
use Symfony\Component\Process\Process;
use App\Dto\ScriptDto;
use Symfony\Component\Filesystem\Filesystem;

class ScriptService
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
     * @param array $returnFields
     * @return array
     */
    public function handleGetScripts(array $params, array $returnFields): array
    {
        $result = array();
        if (count($params) == 0) {
            $scripts = $this->entityManager->getRepository(Script::class)->findAllScript();
            foreach ($scripts as $script) {
                $scriptDto = new ScriptDto($script, $this->entityManager);
                $result[] = $scriptDto->toArray($returnFields);
            }
            return $result;
        }
        if (array_key_exists('key', $params)) {
            $scripts = $this->entityManager->getRepository(Script::class)->findLikeNameOrDesc($params['key']);
            foreach ($scripts as $script) {
                $scriptDto = new ScriptDto($script, $this->entityManager);
                $result[] = $scriptDto->toArray($returnFields);
            }
            return $result;
        }


        if (array_key_exists("id", $params)) {
            $script = $this->entityManager->getRepository(Script::class)->findOneById($params['id']);
            if ($script) {
                $scriptDto = new ScriptDto($script, $this->entityManager);
                $result[] = $scriptDto->toArray($returnFields);
            }
            return $result;
        }

        if (array_key_exists("ids", $params)) {
            $scripts = $this->entityManager->getRepository(Script::class)->findByIds($params['ids']);
            foreach ($scripts as $script) {
                $scriptDto = new ScriptDto($script, $this->entityManager);
                $result[] = $scriptDto->toArray($returnFields);
            }
            return $result;
        }

        return $result;
    }

    /**
     * @param Script $script
     * @param array $tagObjIds
     * @param array $newTags
     * @param array $returnFields
     * @return array
     * @throws \Exception
     */
    public function handlePostScripts(Script $script, array $tagObjIds, array $newTags, array $returnFields): array
    {
        $this->entityManager->beginTransaction();
        try {
            // 保存$script
            $this->entityManager->persist($script);
            $this->entityManager->flush();

            // 所有需要设置关联的tags
            $tags = array();

            // 对新的tag创建 $tags
            foreach ($newTags as $newTag) {
                $this->entityManager->persist($newTag);
                $this->entityManager->flush();
                $tags[] = $newTag;
            }
            // 对已经存在的tag
            foreach ($tagObjIds as $tagObjId) {
                $existTag = $this->entityManager->getRepository(Tag::class)->find($tagObjId);
                $tags[] = $existTag;
            }

            // 保存关联关系
            foreach ($tags as $tag) {
                $scriptTag = new ScriptTag();
                $scriptTag->setScript($script);
                $scriptTag->setTag($tag);
                $this->entityManager->persist($scriptTag);
                $this->entityManager->flush();
            }

            $this->entityManager->commit();
        } catch (\Exception $exception) {
            $this->entityManager->rollback();
            throw ExceptionFactory::InternalServerException($exception->getMessage());
        }


        $scriptDto = new ScriptDto($script, $this->entityManager);
        $result[] = $scriptDto->toArray($returnFields);
        return $result;
    }

    /**
     * @param array $params
     * @param array $returnFields
     * @return array
     */
    public function handlePutScripts(array $params, array $returnFields): array
    {
        /**
         * @var Script $script
         */
        $script = $this->entityManager->getRepository(Script::class)->findOneById($params['id']);

        $this->entityManager->beginTransaction();
        try {
            if (array_key_exists('name', $params)) {
                $script->setName($params["name"]);
            }
            if (array_key_exists('description', $params)) {
                $script->setDescription($params["description"]);
            }
            if (array_key_exists('properties', $params)) {
                $script->setProperties($params["properties"]);
            }
            if (array_key_exists('path', $params)) {
                $script->setPath($params["path"]);
            }
            // 如果是更新tags 去要前端传现有的tags
            if (array_key_exists('tags', $params)) {
                $paramsTags = $params['tags'];
                // 删除所有有关联的tags
                $scriptTags = $this->entityManager->getRepository(ScriptTag::class)->findByScriptId(
                    $script->getId()
                );
                foreach ($scriptTags as $scriptTag) {
                    $this->entityManager->getRepository(ScriptTag::class)->remove($scriptTag, true);
                }

                // 设置新的
                foreach ($paramsTags as $paramsTag) {
                    if (array_key_exists('id', $paramsTag)) {
                        $tag = $this->entityManager->getRepository(Tag::class)->find($paramsTag['id']);
                        if ($tag) {
                            $scriptTag = $this->entityManager->getRepository(ScriptTag::class)->findByScriptIdAndTagId(
                                $script->getId(), $paramsTag['id'],
                            );
                            if ($scriptTag) {
                                continue;
                            }
                            $newScriptTag = new ScriptTag();
                            $newScriptTag->setScript($script);
                            $newScriptTag->setTag($tag);
                            $this->entityManager->persist($newScriptTag);
                            $this->entityManager->flush();
                        } else {
                            $tag = new Tag();
                            $tag->setName($paramsTag['name']);
                            $tag->setColor($paramsTag['color']);
                            $this->entityManager->persist($tag);
                            $this->entityManager->flush();

                            $newScriptTag = new ScriptTag();
                            $newScriptTag->setScript($newScriptTag);
                            $newScriptTag->setTag($tag);
                        }
                        $this->entityManager->persist($script);
                        $this->entityManager->flush();
                    }
                }
            }
            $this->entityManager->persist($script);
            $this->entityManager->flush();

            $this->entityManager->commit();
        } catch (\Exception $exception) {
            $this->entityManager->rollback();
            throw ExceptionFactory::InternalServerException($exception->getMessage());
        }


        $scriptDto = new ScriptDto($script, $this->entityManager);
        $result[] = $scriptDto->toArray($returnFields);
        return $result;
    }

    /**
     * @param array $params
     * @param array $returnFields
     * @return array
     * @throws \Exception
     */
    public function handleDeleteScripts(array $params, array $returnFields): array
    {
        $result = [];

        // example
        if (array_key_exists('id', $params)) {
            $script = $this->entityManager->getRepository(Script::class)->findOneById($params['id']);
            $result[] = $this->deleteScript($script, $returnFields);
        }

        if (array_key_exists('ids', $params)) {
            $scripts = $this->entityManager->getRepository(Script::class)->findByIds($params['ids']);
            foreach ($scripts as $script) {
                $result[] = $this->deleteScript($script, $returnFields);
            }
        }

        return $result;
    }

    /**
     * @param Script $script
     * @param array $returnFields
     * @return array
     * @throws \Exception
     */
    private function deleteScript(Script $script, array $returnFields): array
    {
        $this->entityManager->beginTransaction();
        try {
            $scriptDto = new ScriptDto($script, $this->entityManager);

            $filesystem = new Filesystem();
            $path = $script->getPath();

            /**
             * 先删除数据库
             */
            // 删除Script与Tags的关联
            $scriptTagsByScriptId = $this->entityManager->getRepository(ScriptTag::class)->findByScriptId($script->getId());
            foreach ($scriptTagsByScriptId as $scriptTag) {
                $this->entityManager->getRepository(ScriptTag::class)->remove($scriptTag, true);
            }
            // 删除Script
            $this->entityManager->getRepository(Script::class)->remove($script, true);
            $this->entityManager->commit();

            /**
             * 再删除文件
             */
            $filesystem->remove($path);

            return $scriptDto->toArray($returnFields);
        } catch (\Exception $exception) {
            $this->entityManager->rollback();
            throw ExceptionFactory::InternalServerException("删除失败：" . $exception->getMessage());
        }
    }

    /**
     * 运行一个脚本
     * @throws \Exception
     */
    public function handleRunScript($scriptId)
    {
        $command = "bash ";

        /* @var Script $script */
        $script = $this->entityManager->getRepository(Script::class)->find($scriptId);
        if (!$script) {
            throw ExceptionFactory::NotFoundException("未找到script");
        }

        $scriptPath = $script->getPath();
        $command = $command . $scriptPath . ' ';

        $properties = $script->getProperties();
        foreach ($properties as $property) {
            $command = $command . $property . " ";
        }

        $process = Process::fromShellCommandline($command);
        $process->run();

        if (!$process->isSuccessful()) {
            return $process->getErrorOutput();
        }

        return $process->getOutput();
    }
}