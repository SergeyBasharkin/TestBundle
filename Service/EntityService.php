<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 13.01.18
 * Time: 13:14
 */

namespace Test\TestBundle\Service;


use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Router;

class EntityService
{
    private $container;

    /**
     * EntityService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function getListEntitiesNames()
    {
        $metadata = $this->container->get('doctrine')->getManager()->getMetadataFactory()->getAllMetadata();
        $names = array();
        foreach ($metadata as $classMetadata) {
            $pathArray = explode("\\", $classMetadata->getName());
            $names[] = $pathArray[count($pathArray) -1];
        }
        return $names;
    }

    public function testRouter(){

        /** @var Router $router */
        $router = $this->container->get('router');
        dump($router->getRouteCollection());
    }
}