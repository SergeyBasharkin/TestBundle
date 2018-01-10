<?php

namespace Test\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
         $metadata = $this->getDoctrine()->getManager()->getMetadataFactory()->getAllMetadata();

        $choices = [];
        foreach($metadata as $classMeta) {
            $choices[] = $classMeta->getName(); // Entity FQCN
        }

        // replace this example code with whatever you need
        return new Response(json_encode($choices));
    }
}
