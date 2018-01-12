<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 11.01.18
 * Time: 17:40
 */

namespace Test\TestBundle\Routing;


use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

class RestLoader extends Loader
{

    private $loaded = false;
    private $container;
    private  $logger;

    /**
     * RestLoader constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->logger=$logger;
        $this->container = $container;
    }


    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "rest" loader twice');
        }

        /** @var Router $router */
//        $router = $this->container->get('router');
//
//        $routes = $router->getRouteCollection();
//        dump($routes);

        //        // prepare a new route
        $path = '/extra/';
//        $defaults = array(
//            '_controller' => 'App\Controller\ExtraController::extra',
//        );
//        $requirements = array(
//            'id' => '\d+',
//        );
        $route = new Route($path, array('_controller' => 'Test\TestBundle\Controller\DefaultController::indexAction'));
//
        $routes = new RouteCollection();
//        // add the new route to the route collection
        $routeName = 'extraRoute';
        $routes->add($routeName, $route);
//
        $this->loaded = true;
//        $this->logger->error($this->container->get('doctrine'));

        return $routes;
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
}