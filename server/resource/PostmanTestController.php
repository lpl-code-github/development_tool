<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PostmanTestController extends AbstractController
{

    /**
     * @Route("/backup", name="backup", methods={"GET"})
     */
    public function backup()
    {
        return new JsonResponse([]);
    }
}