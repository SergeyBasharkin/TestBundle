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

    public function indexAction()
    {
        $metadata = $this->getDoctrine()->getManager()->getMetadataFactory()->getAllMetadata();

        $choices = [];
        foreach ($metadata as $classMeta) {
            $choices[] = $classMeta->getName();// Entity FQCN
            $choices["filelds"] = $this->getDoctrine()->getManager()->getClassMetadata($classMeta->getName())->getFieldNames();
        }

        // replace this example code with whatever you need
        return new Response(json_encode($choices));
    }

    public function listEntities($entity)
    {
        $metadata = $this->getDoctrine()->getManager()->getMetadataFactory()->getAllMetadata();
        $repository = null;
        foreach ($metadata as $classMetadata) {
            $pathArray = explode("\\", $classMetadata->getName());
            if ($entity === $pathArray[count($pathArray) - 1]){
                $repository = $this->getDoctrine()->getRepository($classMetadata->getName());
            }
        }

        return new Response($this->serializer->serialize($repository->findAll(), 'json'));
    }
}
