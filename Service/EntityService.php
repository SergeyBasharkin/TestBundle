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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EntityService
{
    private $doctrine;
    private $serializer;
    private $blacklist;


    public function __construct(ContainerInterface $container,Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $this->blacklist = $container->get('rest_test.blacklist');
        $this->serializer = new Serializer($normalizers, $encoders);
    }


    public function getListEntitiesNames()
    {
        $metadata = $this->doctrine->getManager()->getMetadataFactory()->getAllMetadata();
        $names = array();
        foreach ($metadata as $classMetadata) {
            $pathArray = explode("\\", $classMetadata->getName());
            if (!in_array($classMetadata->getName(),$this->blacklist)) $names[] = $pathArray[count($pathArray) - 1];
        }
        return $names;
    }

    public function initRepository($entity)
    {
        return $this->doctrine->getRepository($this->getEntityClassName($entity));
    }

    public function getEntityClassName($entity)
    {
        $metadata = $this->doctrine->getManager()->getMetadataFactory()->getAllMetadata();
        $name = null;
        foreach ($metadata as $classMetadata) {
            $pathArray = explode("\\", $classMetadata->getName());
            if ($entity === $pathArray[count($pathArray) - 1]) {
                $name = $classMetadata->getName();
            }
        }
        return $name;
    }

    public function getById($entity, $id)
    {
        $repository = $this->initRepository($entity);
        return new Response($this->serializer->serialize($repository->findBy(array("id" => $id)), 'json'));
    }

    public function findAll($entity)
    {
        $repository = $this->initRepository($entity);
        return new Response($this->serializer->serialize($repository->findAll(), 'json'));
    }

    public function saveOrUpdate($entity, $body, $id = null)
    {
        $repository = $this->initRepository($entity);
        $className = $repository->getClassName();
        $cl = $this->doctrine->getManager()->getMetadataFactory()->getMetadataFor($repository->getClassName());
        $entityClass = null;
        if (is_null($id)) {
            $entityClass = new $className;
        } else {
            $entityClass = $repository->findOneBy(array("id" => $id));
        }
        foreach ($cl->getFieldNames() as $field) {
            if ($cl->hasField($field) && !$cl->isIdentifier($field)) {
                $set = 'set' . ucfirst($field);
                $entityClass->$set($body[$field]);
            }
        }
        $this->doctrine->getManager()->persist($entityClass);
        $this->doctrine->getManager()->flush();
        return new Response("ok");
    }

    public function delete($entity, $id)
    {
        $repository = $this->initRepository($entity);
        $entityClass = $repository->findOneBy(array("id" => $id));
        $em = $this->doctrine->getManager();
        $em->remove($entityClass);
        $em->flush();
        return new Response("ok");
    }
}