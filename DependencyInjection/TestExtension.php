<?php

namespace Test\TestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Test\TestBundle\Exception\MultipleRouteException;
use Test\TestBundle\Helper\Utils;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class TestExtension extends Extension
{
    private $router;
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('test_rest', $config);
        $this->router = $container->get('router');
        $routes = $this->router->getRouteCollection()->all();
        $entitiesNames = $this->getListEntitiesNames($container);
        if (!empty($entitiesNames)) {
            $routeCollisions = array();
            foreach ($routes as $key => $route) {
                if ($key !== 'listEntitiesRoute' && $key !== 'entityRoute') {
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
        }
        if (!empty($routeCollisions)) {
            throw  new MultipleRouteException($routeCollisions);
        }
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function getListEntitiesNames(ContainerBuilder $container){
        $metadata = $container->get('doctrine')->getManager()->getMetadataFactory()->getAllMetadata();
        $names = array();
        foreach ($metadata as $classMetadata) {
            $pathArray = explode("\\", $classMetadata->getName());
            if (!in_array($classMetadata->getName(),$this->blacklist)) $names[] = $pathArray[count($pathArray) - 1];
        }
        return $names;
    }
}
