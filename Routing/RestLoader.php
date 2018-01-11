<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 11.01.18
 * Time: 17:40
 */

namespace Test\TestBundle\Routing;


use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;

class RestLoader extends Loader
{

    private $loaded = false;
    private $container;

    /**
     * RestLoader constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "rest" loader twice');
        }

        $routes = new RouteCollection();

        dump($this->container->get('doctrine'));
//        // prepare a new route
//        $path = '/extra/{id}';
//        $defaults = array(
//            '_controller' => 'App\Controller\ExtraController::extra',
//        );
//        $requirements = array(
//            'id' => '\d+',
//        );
//        $route = new Route($path, $defaults, $requirements);
//
//        // add the new route to the route collection
//        $routeName = 'extraRoute';
//        $routes->add($routeName, $route);
//
//        $this->loaded = true;

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