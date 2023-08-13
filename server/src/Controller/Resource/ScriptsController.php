<?php

namespace App\Controller\Resource;

use App\Controller\BaseController;
use App\Factory\TagFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ScriptService;
use App\Factory\ScriptFactory;

class ScriptsController extends BaseController
{
    // Define default return fields
    const RETURN_FIELD = array(
        "name","description","path","properties","created_at","updated_at","tags"
    );

    private ScriptService $scriptService;
    private ScriptFactory $scriptFactory;
    private TagFactory $tagFactory;

    public function __construct(
        ScriptService $scriptService,
        ScriptFactory $scriptFactory,
        TagFactory $tagFactory
    )
    {
        $this->scriptService = $scriptService;
        $this->scriptFactory = $scriptFactory;
        $this->tagFactory = $tagFactory;
    }

    /**
     * @Route("/scripts", name="get scripts", methods={"GET"})
     * @throws \Exception
     */
    public function executeGet(Request $request): Response
    {
        $response = new Response();

        $returnFields = $request->query->get("return_fields") ?? self::RETURN_FIELD;
        $id = $request->query->get("id") ?? null;
        $ids = $request->query->get("ids") ?? null;
        $key = $request->query->get("key") ?? null;

        // validate params
        // ...

        $queryParams = array();
        if ($id) {
            $queryParams['id'] = $id;
        }
        if ($ids) {
            $queryParams['ids'] = $ids;
        }
        if ($key) {
            $queryParams['key'] = $key;
        }

        // processing
        $resultArray['data'] = $this->scriptService->handleGetScripts($queryParams, $returnFields);

        $response->setContent(json_encode($resultArray));
        return $response;
    }

    /**
     * @Route("/scripts", name="保存一个新的脚本", methods={"POST"})
     * @throws \Exception
     */
    public function executePost(Request $request): Response
    {
        $response = new Response();
        $resultArray = array();

        $params = json_decode($request->getContent(), true);
        $this->validateNecessaryParameters($params, ['data' => self::OBJECT_TYPE]);
        $data = $params['data'];

        // validate params
        // example ...
        $this->validateNecessaryParameters($data, [
            'name' => self::STRING_TYPE,
            "path" => self::STRING_TYPE,
            "tags" => self::ARRAY_TYPE
        ]);

        // processing
        $script = $this->scriptFactory->create(
            $data['name'],
            $data['description']?? null,
            $data['path'],
            $data['properties']?? null
        );

        $tagObjIds = array();
        $newTags = array();
        foreach ($data['tags'] as $paramTag){
            // 有id的，是已经存在的tag，将直接保存关联关系
            $tagId = $paramTag['id'] ?? null;
            if ($tagId){
                $tagObjIds[] =  $tagId;
            }else{
                // 没有id的默认创建
                $newTags[] = $this->tagFactory->create($paramTag['name'], $paramTag['color']);
            }
        }

        // processing
        $resultArray['data'] = $this->scriptService->handlePostScripts($script, $tagObjIds, $newTags, self::RETURN_FIELD);

        $response->setContent(json_encode($resultArray));
        return $response;
    }

    /**
     * @Route("/scripts", name="更新脚本", methods={"PUT"})
     * @throws \Exception
     */
    public function executePut(Request $request): Response
    {
        $response = new Response();

        $params = json_decode($request->getContent(), true);
        $this->validateNecessaryParameters($params, ['data' => self::OBJECT_TYPE]);
        $data = $params['data'];

        // processing
        $resultArray['data'] = $this->scriptService->handlePutScripts($data,  self::RETURN_FIELD);

        $response->setContent(json_encode($resultArray));
        return $response;
    }

    /**
     * @Route("/scripts", name="删除一个脚本", methods={"DELETE"})
     * @throws \Exception
     */
    public function executeDelete(Request $request): Response
    {
        $response = new Response();

        $params = json_decode($request->getContent(), true);
        $this->validateNecessaryParameters($params, ['data' => self::OBJECT_TYPE]);
        $data = $params['data'];

        // validate params
        // ...

        // processing
        $resultArray['data'] = $this->scriptService->handleDeleteScripts($data, self::RETURN_FIELD);

        $response->setContent(json_encode($resultArray));
        return $response;
    }
}