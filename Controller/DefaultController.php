<?php

namespace Test\TestBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
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
            dump($pathArray);
            if ($entity === $pathArray[count($pathArray) - 1]){
                $repository = $this->getDoctrine()->getRepository($classMetadata->getName());
                dump($repository);
            }
        }

        dump($repository->findAll());
        return new Response("hi");
    }
}
