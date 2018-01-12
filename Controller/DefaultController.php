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
        dump($request->getMethod());
        dump($id);
        dump($entity);
    }

    public function listEntities($entity)
    {
        $repository = $this->initRepository($entity);
        return new Response($this->serializer->serialize($repository->findAll(), 'json'));
    }

    private function initRepository($entity)
    {
        $metadata = $this->getDoctrine()->getManager()->getMetadataFactory()->getAllMetadata();
        $repository = null;
        foreach ($metadata as $classMetadata) {
            $pathArray = explode("\\", $classMetadata->getName());
            if ($entity === $pathArray[count($pathArray) - 1]) {
                $repository = $this->getDoctrine()->getRepository($classMetadata->getName());
            }
        }
        return $repository;
    }
}
