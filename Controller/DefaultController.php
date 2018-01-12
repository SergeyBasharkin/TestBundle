<?php

namespace Test\TestBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends Controller
{

    private $serializer;

    /**
     * DefaultController constructor.
     */
    public function __construct()
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function indexAction($entity, $id, Request $request)
    {
        $response = new Response("some error");
        switch ($request->getMethod()) {
            case "GET":
                $response = $this->getById($entity, $id);
                break;
            case "PATCH":
                $response = $this->saveOrUpdate($entity,json_decode($request->getContent(), true), $id);
                break;
            case "DELETE":
                $response = $this->delete($entity, $id);
        }
        return $response;
    }

    public function listEntities($entity, Request $request)
    {
        $response = new Response("some error");
        switch ($request->getMethod()) {
            case "GET":
                $response = $this->findAll($entity);
                break;
            case "POST":
                $response = $this->saveOrUpdate($entity, json_decode($request->getContent(), true));
                break;
        }
        return $response;
    }

    private function initRepository($entity)
    {
        return $this->getDoctrine()->getRepository($this->getEntityClassName($entity));
    }

    private function getEntityClassName($entity){
        $metadata = $this->getDoctrine()->getManager()->getMetadataFactory()->getAllMetadata();
        $name = null;
        foreach ($metadata as $classMetadata) {
            $pathArray = explode("\\", $classMetadata->getName());
            if ($entity === $pathArray[count($pathArray) - 1]) {
                $name = $classMetadata->getName();
            }
        }
        return $name;
    }

    private function getById($entity, $id)
    {
        $repository = $this->initRepository($entity);
        return new Response($this->serializer->serialize($repository->findBy(array("id" => $id)), 'json'));
    }

    private function findAll($entity)
    {
        $repository = $this->initRepository($entity);
        return new Response($this->serializer->serialize($repository->findAll(), 'json'));
    }

    private function saveOrUpdate($entity, $body, $id = null)
    {
        $repository = $this->initRepository($entity);
        $className = $repository->getClassName();
        $cl = $this->getDoctrine()->getManager()->getMetadataFactory()->getMetadataFor($repository->getClassName());
        $entityClass = null;
        if (is_null($id)){
            $entityClass = new $className;
        }else{
            $entityClass = $repository->findOneBy(array("id" => $id));
        }
        foreach ($cl->getFieldNames() as $field) {
            if ($cl->hasField($field) && !$cl->isIdentifier($field)) {
                $set = 'set' . ucfirst($field);
                $entityClass->$set($body[$field]);
            }
        }
        $this->getDoctrine()->getManager()->persist($entityClass);
        $this->getDoctrine()->getManager()->flush();
        return new Response("ok");
    }

    private function delete($entity, $id)
    {
        $repository = $this->initRepository($entity);
        $entityClass = $repository->findOneBy(array(array("id" => $id)));
        $em = $this->getDoctrine()->getManager();
        $em->remove($entityClass);
        $em->flush();
        return new Response("ok");
    }
}
