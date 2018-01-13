<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 13.01.18
 * Time: 13:14
 */

namespace Test\TestBundle\Service;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Router;

class EntityService
{
    private $doctrine;

    /**
     * EntityService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }


    public function getListEntitiesNames()
    {
        $metadata = $this->doctrine->getManager()->getMetadataFactory()->getAllMetadata();
        $names = array();
        foreach ($metadata as $classMetadata) {
            $pathArray = explode("\\", $classMetadata->getName());
            $names[] = $pathArray[count($pathArray) -1];
        }
        return $names;
    }
}