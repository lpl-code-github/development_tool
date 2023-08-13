<?php

namespace App\Controller\Resource;

use App\Controller\BaseController;
use App\Entity\Tag;
use App\Service\TagService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class
TagController extends BaseController
{
    private TagService $tagService;

    public function __construct(
        TagService $tagService
    )
    {
        $this->tagService = $tagService;
    }

    /**
     * @Route("/tags", name="get tags", methods={"GET"})
     * @throws \Exception
     */
    public function executeGet(Request $request): Response
    {
        $response = new Response();

        $queryParams = array();

        // processing
        $resultArray['data'] = $this->tagService->handleGetTags($queryParams);

        $response->setContent(json_encode($resultArray));
        return $response;
    }

    /**
     * @Route("/tags", name="保存一个新的标签", methods={"POST"})
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
            "color" => self::STRING_TYPE,
        ]);

        // processing
        $tag = new Tag();
        $tag->setName($data['name']);
        $tag->setColor($data['color']);

        // processing
        $resultArray['data'] = $this->tagService->handlePostTags($tag);

        $response->setContent(json_encode($resultArray));
        return $response;
    }
}