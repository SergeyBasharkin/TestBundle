<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 11.01.18
 * Time: 17:40
 */

namespace Test\TestBundle\Routing;


use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Test\TestBundle\Service\EntityService;

class RestLoader extends Loader
{

    private $loaded = false;
    private  $logger;
    private $entityService;

    /**
     * RestLoader constructor.
     * @param $container
     */
    public function __construct(EntityService $entityService, LoggerInterface $logger)
    {
        $this->logger=$logger;
        $this->entityService = $entityService;
    }


    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "rest" loader twice');
        }
        $routes = new RouteCollection();
        $entityNames = $this->entityService->getListEntitiesNames();
        if (empty($entityNames)) {
            $regexpNames = $this->entityNamesToRequirements($entityNames);

            $pathRUD = '/{entity}/{id}';
            $pathCR = '/{entity}/';
            $requirements = array(
                'id' => '\d+',
                'entity' => $regexpNames
            );
            $defaultsRUD = array(
                '_controller' => 'Test\TestBundle\Controller\DefaultController::indexAction'
            );
            $defaultsCR = array(
                '_controller' => 'Test\TestBundle\Controller\DefaultController::listEntities'
            );

            $routeRUD = new Route($pathRUD, $defaultsRUD, $requirements);
            $routeCR = new Route($pathCR, $defaultsCR, $requirements);
            $routes->add('entityRoute', $routeRUD);
            $routes->add('listEntitiesRoute', $routeCR);

            $this->loaded = true;

            return $routes;
        }
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return 'rest' == $type;
    }

    private function entityNamesToRequirements(array $names)
    {
        $requiremets = '';
        foreach ($names as $name){
            $requiremets.=$name.'|';
        }
        return substr($requiremets, 0, -1);
    }
}