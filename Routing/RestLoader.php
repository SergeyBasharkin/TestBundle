<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 11.01.18
 * Time: 17:40
 */

namespace Test\TestBundle\Routing;


use Psr\Log\LoggerInterface;
use SensioLabs\Security\Exception\RuntimeException;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RestLoader extends Loader
{

    private $loaded = false;
    private $container;
    private  $logger;
    private $locator;

    /**
     * RestLoader constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container, LoggerInterface $logger, FileLocatorInterface $fileLocator)
    {
        $this->logger=$logger;
        $this->container = $container;
        $this->locator = $fileLocator;
    }


    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "rest" loader twice');
        }
        try {
            $this->resolve($resource, $type);
        } catch (FileLoaderLoadException $e) {
            dump($e);
            $this->logger->error("test");
        }
        $routes = new RouteCollection();
        $path = '/{entity}/{id}';
        $listPath='/{entity}/';
        $requirements = array(
            'parameter' => '\d+',
        );
        $route = new Route($path, array('_controller' => 'Test\TestBundle\Controller\DefaultController::indexAction'), $requirements);
        $routeList = new Route($listPath,array('_controller' => 'Test\TestBundle\Controller\DefaultController::listEntities'));
        $routes->add('entityRoute', $route);
        $routes->add('listEntitiesRoute', $routeList);

        $this->loaded = true;

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