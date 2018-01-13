<?php

namespace Test\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Test\TestBundle\Service\EntityService;

class DefaultController extends Controller
{

    private $entityService;

    /**
     * DefaultController constructor.
     */
    public function __construct(EntityService $entityService)
    {
        $this->entityService = $entityService;
    }

    public function indexAction($entity, $id, Request $request)
    {
        $response = new Response("some error");
        switch ($request->getMethod()) {
            case "GET":
                $response = $this->entityService->getById($entity, $id);
                break;
            case "PATCH":
                $response = $this->entityService->saveOrUpdate($entity,json_decode($request->getContent(), true), $id);
                break;
            case "DELETE":
                $response = $this->entityService->delete($entity, $id);
        }
        return $response;
    }

    public function listEntities($entity, Request $request)
    {
        $response = new Response("some error");
        switch ($request->getMethod()) {
            case "GET":
                $response = $this->entityService->findAll($entity);
                break;
            case "POST":
                $response = $this->entityService->saveOrUpdate($entity, json_decode($request->getContent(), true));
                break;
        }
        return $response;
    }

    public function entities(){
        return new Response(json_encode($this->entityService->getListEntitiesNames()));
    }
}
