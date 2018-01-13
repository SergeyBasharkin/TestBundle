<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 13.01.18
 * Time: 14:42
 */

namespace Test\TestBundle\EventListener;


use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Router;
use Test\TestBundle\Exception\MultipleRouteException;
use Test\TestBundle\Helper\Utils;
use Test\TestBundle\Service\EntityService;

class RouteMatchListener
{

    private $router;
    private $entityService;

    /**
     * RouteMatchListener constructor.
     */
    public function __construct(Router $router, EntityService $entityService)
    {
        $this->router = $router;
        $this->entityService = $entityService;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $routes = $this->router->getRouteCollection()->all();
        dump($routes);
        $entitiesNames = $this->entityService->getListEntitiesNames();
        $routeCollisions = array();
        foreach ($routes as $key => $route) {
            if ($key !== 'listEntitiesRoute' && $key !== 'entityRoute') {
                dump($route);
                $explodedPath = Utils::parsePath($route->getPath());
                if (count($explodedPath) >= 3) continue;
                if (in_array($explodedPath[0], $entitiesNames)) {
                    $routeCollisions[] = $route;
                }
                if (preg_match('/\{.*\}/', $explodedPath[0])) {
                    $routeCollisions[] = $route;
                }
            }
        }
        if (!empty($routeCollisionsr)) throw  new MultipleRouteException($routeCollisions);
    }
}